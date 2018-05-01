<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup backup zip model.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Model_Backup_Zip {
	/** @var string Beginning of central directory record. */
	private $_centralDirHeader	= "\x50\x4b\x01\x02";

	/** @var string End of central directory record. */
	private $_centralDirEnd		= "\x50\x4b\x05\x06";

	/** @var string Begining of file contents. */
	private $_fileHeader		= "\x50\x4b\x03\x04";

	/** @var string The name of the temporary file holding the ZIP's Central Directory */
	private $_centralDirFileName;

	/** @var string The name of file holding the ZIP's data, which becomes the final archive */
	private $_dataFileName;

	/** @var integer The total number of files and directories stored in the ZIP archive */
	private $_totalFileEntries;

	/** @var integer The total size of data in the archive. */
	private $_totalDataSize		= 0;

	/** @var bool Should I use Split ZIP? */
	private $_useSplitZip		= false;

	/** @var integer Maximum fragment size, in bytes */
	private $_fragmentSize		= 0;

	/** @var integer Current fragment number */
	private $_currentFragment	= 1;

	/** @var integer Total number of fragments */
	private $_totalFragments	= 1;

	/** @var string Archive full path without extension */
	private $_dataFileNameBase	= '';

	/** @var integer How much data to read at once when finalizing ZIP archives */
	private $_readChunk			= 1048756;

	/** @var integer */
	private $_threshold			= 0;

	protected $fp;
	protected $cdfp;
	protected $_comment			= '';

	/** @var MageBackup_MageBackup_Model_Backup $backup */
	protected $backup;

	public function getDataFileName() {
		return $this->_dataFileName;
	}

	public function getTotalDataSize() {
		return $this->_totalDataSize;
	}

	public function getTotalFragments() {
		return $this->_totalFragments;
	}
	
	/**
	 * Get string lenth.
	 */
	protected function _strlen($string) {
		return function_exists('mb_strlen') ? mb_strlen($string, '8bit') : strlen($string);
	}

	/**
	 * Get CRC-32 of a file.
	 */
	protected function _crc32_file($fileName) {
		static $mustInvert	= null;

		if (is_null($mustInvert)) {
			$testCrc	= @hash('crc32b', 'test', false);
			$mustInvert	= strtolower($testCrc) == 'oc7e7fd8';
		}

		$crc	= @hash_file('crc32b', $fileName, false);

		if ($mustInvert) {
			$crc	= substr($crc, 6, 2) . substr($crc, 4, 2) . substr($crc, 2, 2) . substr($crc, 0, 2);
		}

		return hexdec($crc);
	}

	/**
	 * Get DOS Time from Unix Time.
	 */
	protected function _unix2DosTime($unixtime) {
		$timearray	= is_null($unixtime) ? getdate() : getdate($unixtime);

		if ($timearray['year'] < 1980) {
			$timearray['year']		= 1980;
			$timearray['mon']		= 1;
			$timearray['mday']		= 1;
			$timearray['hours']		= 0;
			$timearray['minutes']	= 0;
			$timearray['seconds']	= 0;
		}

		return (($timearray['year'] - 1980) << 25) |
			($timearray['mon'] << 21) |
			($timearray['mday'] << 16) |
			($timearray['hours'] << 11) |
			($timearray['minutes'] << 5) |
			($timearray['seconds'] >> 1)
		;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->backup	= Mage::registry('magebackup/backup');
		$log			= $this->backup->getLog();

		$log->debug(__CLASS__ . ' :: New instance');

		if (($readChunk = $this->backup->getProfile()->getValue('zip_read_chunk', 0)) > 0) {
			$this->_readChunk	= $readChunk * 1048576;
		} else {
			$this->_readChunk	= 524288;
		}

		$log->debug('Chunk size is now ' . $this->_readChunk . ' bytes');

		$fragmentSize	= $this->backup->getProfile()->getValue('zip_fragment_size', 0) * 1048576;

		if ($fragmentSize >= 65536) {
			$this->_useSplitZip		= true;
			$this->_fragmentSize	= $fragmentSize;
		}

		//
		$this->_threshold		= $this->backup->getProfile()->getValue('zip_threshold', 0) * 1048576;
	}

	/**
	 * Initialize.
	 */
	public function initialize($targetArchivePath) {
		$this->_dataFileName	= $targetArchivePath;
		$log					= $this->backup->getLog();

		if ($this->_useSplitZip) {
			$log->info(__CLASS__ . ' :: Split Zip creation enabled');

			$this->_dataFileNameBase	= dirname($targetArchivePath) . '/' . basename($targetArchivePath, '.zip');
			$this->_dataFileName		= $this->_dataFileNameBase . '.z01';
		}

		$this->_centralDirFileName		= $this->backup->getProfile()->getTmpDir();
		$this->_centralDirFileName		= $this->_centralDirFileName . '/' . time() . '.tmp';

		$fp	= fopen($this->_centralDirFileName, 'wb');
		fclose($fp);

		if (!@touch($this->_centralDirFileName)) {
			return;
		}

		if (function_exists('chmod')) {
			chmod($this->_centralDirFileName, 666);
		}

		// Try to kill the archive if it exists
		$fp	= fopen($this->_dataFileName, 'wb');

		if (!($fp === false)) {
			ftruncate($fp, 0);
			fclose($fp);
		} else {
			@unlink($this->_dataFileName);
		}

		if (!@touch($this->_dataFileName)) {
			$log->error('Could not open archive file for Zip archiver. Please check your output directory\'s permission');
			return;
		}

		if (function_exists('chmod')) {
			chmod($this->_centralDirFileName, 0666);
		}

		if ($this->_useSplitZip) {
			file_put_contents($this->_dataFileName, "\x50\x4b\x07\x08");
		}
	}

	/**
	 * Finalize.
	 */
	public function finalize() {
		clearstatcache();

		$log					= $this->backup->getLog();
		$cdOffset				= @filesize($this->_dataFileName);
		$this->_totalDataSize	+= $cdOffset;
		$cdSize					= @filesize($this->_centralDirFileName);

		if (!is_null($this->fp)) {
			fclose($this->fp);
		}

		if (!is_null($this->cdfp)) {
			fclose($this->cdfp);
		}

		$this->fp	= fopen($this->_dataFileName, 'ab');
		$this->cdfp	= fopen($this->_centralDirFileName, 'rb');

		if ($this->fp === false) {
			$log->error('Could not open ZIP data file ' . $this->_dataFileName . ' for reading');
			return;
		}

		if ($this->cdfp === false) {
			fclose($this->fp);
			$this->fp	= null;
			$this->cdfp	= null;

			return;
		}

		if (!$this->_useSplitZip) {
			while (!feof($this->cdfp)) {
				$chunk	= fread($this->cdfp, $this->_readChunk);
				fwrite($this->fp, $chunk);
			}

			unset($chunk);

			fclose($this->cdfp);
		} else {
			$comment_length		= $this->_strlen($this->_comment);
			$total_cd_eocd_size	= $cdSize + 22 + $comment_length;

			clearstatcache();

			$current_part_size	= @filesize($this->_dataFileName);
			$free_space			= $this->_fragmentSize - ($current_part_size === false ? 0 : $current_part_size);

			if ($free_space < $total_cd_eocd_size && $total_cd_eocd_size > 65636) {
				if (!$this->_createNewPart(true)) {
					$log->error('Could not create new ZIP part file ' . basename($this->_dataFileName));
					return;
				}

				fclose($this->fp);

				$this->fp = @fopen($this->_dataFileName, 'ab');

				if ($this->fp === false) {
					$this->fp = null;
					$log->error('Could not open archive file ' . $this->_dataFileName . ' for append');

					return;
				}

				while (!feof($this->cdfp)) {
					$chunk	= fread($this->cdfp, $this->_readChunk);
					fwrite($this->fp, $chunk);
				}

				unset($chunk);

				fclose($this->cdfp);
				$this->cdfp	= null;
			} else {
				while (!feof($this->cdfp)) {
					$chunk	= fread($this->cdfp, $this->_readChunk);
					fwrite($this->fp, $chunk);
				}

				unset($chunk);

				fclose($this->cdfp);
				$this->cdfp	= null;
			}
		}

		fclose($this->fp);
		$this->fp	= null;

		clearstatcache();

		$this->fp	= fopen($this->_dataFileName, 'ab');

		if ($this->fp === false) {
			$log->error('Could not open archive file ' . $this->_dataFileName . ' for append');

			return;
		}

		fwrite($this->fp, $this->_centralDirEnd);

		if ($this->_useSplitZip) {
			fwrite($this->fp, pack('v', $this->_totalFragments - 1));	// Number of this disk.
			fwrite($this->fp, pack('v', $this->_totalFragments - 1));	// Disk with central directory start.
		} else {
			fwrite($this->fp, pack('V', 0));
		}

		fwrite($this->fp, pack('v', $this->_totalFileEntries));		// Total number of entries on this disk.
		fwrite($this->fp, pack('v', $this->_totalFileEntries));		// Total number of entries overall.
		fwrite($this->fp, pack('V', $cdSize));						// Size of central directory.
		fwrite($this->fp, pack('V', $cdOffset));					// Offset to start of central dir.

		$sizeOfComment = $comment_length = $this->_strlen($this->_comment);

		fwrite($this->fp, pack('v', $sizeOfComment));
		fwrite($this->fp, $this->_comment);
		fclose($this->fp);

		if ($this->_useSplitZip) {
			$extension	= substr($this->_dataFileName, -3);

			if ($extension != '.zip') {
				$log->debug('Renaming last ZIP part to .ZIP extension');

				$newName	= $this->_dataFileNameBase . '.zip';

				if (!@rename($this->_dataFileName, $newName)) {
					$log->error('Could not rename last ZIP part to .ZIP extension.');
					return;
				}

				$this->_dataFileName	= $newName;
			}
		}

		if ($this->_useSplitZip && $this->_totalFragments == 1) {
			$this->fp	= fopen($this->_dataFileName, 'r+b');
			fwrite($this->fp, "\x50\x4b\x30\x30");
		}

		if (function_exists('chmod')) {
			chmod($this->_dataFileName, 0755);
		}
	}

	/**
	 * Creates a new part for the spanned archive.
	 */
	protected function _createNewPart($finalPart = false) {
		if (is_resource($this->fp)) {
			fclose($this->fp);
		}

		if (is_resource($this->cdfp)) {
			fclose($this->cdfp);
		}

		//

		$this->fp	= null;
		$this->cdfp	= null;

		clearstatcache();
		$this->_totalDataSize	+= filesize($this->_dataFileName);
		$this->_totalFragments++;
		$this->_currentFragment	= $this->_totalFragments;

		if ($finalPart) {
			$this->_dataFileName	= $this->_dataFileNameBase . '.zip';
		} else {
			$this->_dataFileName	= $this->_dataFileNameBase . '.z' . sprintf('%02d', $this->_currentFragment);
		}

		$this->backup->getLog()->info('Creating new ZIP part #' . $this->_currentFragment . ', file ' . $this->_dataFileName);

		@unlink($this->_dataFileName);

		$result	= @touch($this->_dataFileName);

		if (function_exists('chmod')) {
			chmod($this->_dataFileName, 0666);
		}

		return $result;
	}

	/**
	 * Add file to archive.
	 */
	public function addFile($sourceName, $targetName) {
		$log	= $this->backup->getLog();
		$zdata	= false;

		if ($this->_useSplitZip) {
			$starting_disk_number	= $this->_currentFragment - 1;
		} else {
			$starting_disk_number	= 0;
		}

		if (is_null($this->fp) || !is_resource($this->fp)) {
			$this->fp	= @fopen($this->_dataFileName, 'ab');
		}

		if ($this->fp === false) {
			$log->error('Could not open archive file ' . $this->_dataFileName . ' for append');
			return false;
		}

		$isDir		= is_dir($sourceName);
		$isSymlink	= false;

		if (false) {
			$isSymlink = is_link($sourceName);
		}

		if ($isSymlink) {
			$fileSize	= $this->_strlen(readlink($sourceName));
		} else {
			$fileSize	= $isDir ? 0 : filesize($sourceName);
		}

		$ftime	= @filemtime($sourceName);

		if ($isDir || $isSymlink) {
			$compressionMethod	= 0; // don't compress directories...
		} else {
			if ($this->_threshold > 0 && $fileSize > $this->_threshold) {
				$compressionMethod = 0;
			} else {
				$compressionMethod	= 8;
			}
		}

		$compressionMethod	= function_exists('gzcompress') ? $compressionMethod : 0;
		$storedName			= $targetName;

		$unc_len			= $fileSize;

		if (!$isDir) {
			$crc			= $this->_crc32_file($sourceName);

			if ($crc === false) {
				$log->warning('Could not calculate CRC32 for ' . $sourceName);
				return false;
			}
		} else if ($isSymlink) {
			$crc	= crc32(@readlink($sourceName));
		} else {
			$crc		= 0;
			$storedName	.= '/';
			$unc_len	= 0;
		}

		if ($crc) {
			$log->debug('File: ' . $sourceName . ' - CRC32: ' . dechex($crc) . ' - Size: ' . $fileSize . ' (' . Mage::helper('magebackup')->fileSize($fileSize) . ')');
		} else {
			$log->debug('Directory: ' . $sourceName);
		}

		if ($compressionMethod == 8) {
			$udata	= @file_get_contents($sourceName);

			if ($udata === false) {
				$log->warning('Unreadable file ' . $sourceName . '. Check permissions');

				return false;
			} else {
				$zdata	= @gzcompress($udata);

				if ($zdata === false) {
					$c_len				= $unc_len;
					$compressionMethod	= 0;
				} else {
					unset($udata);
					$zdata	= substr(substr($zdata, 0, -4), 2);
					$c_len	= $this->_strlen($zdata);
				}
			}
		} else {
			$c_len		= $unc_len;
		}

		$dtime	= dechex($this->_unix2DosTime($ftime));

		if ($this->_strlen($dtime) < 8) {
			$dtime	= '00000000';
		}

		$hexdtime	= chr(hexdec($dtime[6] . $dtime[7]))
			. chr(hexdec($dtime[4] . $dtime[5]))
			. chr(hexdec($dtime[2] . $dtime[3]))
			. chr(hexdec($dtime[0] . $dtime[1]))
		;

		if ($this->_useSplitZip) {
			$header_size		= 30 + $this->_strlen($storedName);

			clearstatcache();

			$current_part_size	= @filesize($this->_dataFileName);
			$free_space			= $this->_fragmentSize - ($current_part_size === false ? 0 : $current_part_size);

			if ($free_space <= $header_size) {
				if (!$this->_createNewPart()) {
					$log->error('Could not create new Zip part file ' . basename($this->_dataFileName));
					return false;
				}

				$this->fp	= @fopen($this->_dataFileName, 'ab');

				if ($this->fp === false) {
					$log->error('Could not open archive file ' . $this->_dataFileName . ' for append');
					return false;
				}
			}
		}

	//		$old_offset	= @ftell($this->fp);
		$old_offset	= @filesize($this->_dataFileName);

		if ($this->_useSplitZip && $old_offset == 0) {
			@fseek($this->fp, 4);
			$old_offset	= @ftell($this->fp);
		}

		$fn_length	= $this->_strlen($storedName);

		fwrite($this->fp, $this->_fileHeader);	// Begin creating the ZIP data.

		if (!$isSymlink) {
			fwrite($this->fp, "\x14\x00");		// Version needed to extract.
		} else {
			fwrite($this->fp, "\x0a\x03");
		}

		fwrite($this->fp, pack('v', 2048));		// General purpose bit flag. Bit 11 set = use UTF-8 encoding for filenames & comments.
		fwrite($this->fp, $compressionMethod == 8 ? "\x08\x00" : "\x00\x00");	// Compression method.
		fwrite($this->fp, $hexdtime);			// Last modification time/date.
		fwrite($this->fp, pack('V', $crc));		// CRC 32 Information.

		fwrite($this->fp, pack('V', $c_len));	// Compressed filesize.
		fwrite($this->fp, pack('V', $unc_len));	// Uncompressed filesize.
		fwrite($this->fp, pack('v', $fn_length));	// Length of filename.
		fwrite($this->fp, pack('v', 0));		// Extra field length.
		fwrite($this->fp, $storedName);			// File name.


		// ???
		if ($compressionMethod == 8) {
			if (!$this->_useSplitZip) {
				fwrite($this->fp, $zdata);

			} else {
				clearstatcache();

				$current_part_size	= @filesize($this->_dataFileName);
				$free_space			= $this->_fragmentSize - ($current_part_size === false ? 0 : $current_part_size);

				if ($free_space >= $this->_strlen($zdata)) {
					fwrite($this->fp, $zdata);
				} else {
					$bytes_left	= $this->_strlen($zdata);

					while ($bytes_left > 0) {
						clearstatcache();

						$current_part_size	= @filesize($this->_dataFileName);
						$free_space			= $this->_fragmentSize - ($current_part_size === false ? 0 : $current_part_size);

						// split between parts - write first part
						fwrite($this->fp, $zdata, min($this->_strlen($zdata), $free_space));

						// get the rest of the data
						$bytes_left			= $this->_strlen($zdata) - $free_space;

						if ($bytes_left > 0) {
							fclose($this->fp);
							$this->fp	= null;

							if (!$this->_createNewPart()) {
								$log->error('Could not create new ZIP part file ' . basename($this->_dataFileName));
								return false;
							}

							$this->fp	= @fopen($this->_dataFileName, 'ab');

							if ($this->fp === false) {
								$log->error('Could not open archive file ' . $this->_dataFileName . ' for append');
								return false;
							}

							// get the rest of the compressed data
							$zdata	= substr($zdata, -$bytes_left);
						}
					}
				}

				unset($zdata);
			}
		} else if (!($isDir || $isSymlink)) {
			$zdatafp	= @fopen($sourceName, 'rb');

			if ($zdatafp === false) {
				return false;
			} else {
				if (!$this->_useSplitZip) {
					// For non Split Zip, just dump the file very fast
					while (!feof($zdatafp) && $unc_len > 0) {
						$zdata		= fread($zdatafp, $this->_readChunk);
						fwrite($this->fp, $zdata, min($this->_strlen($zdata), $this->_readChunk));
						$unc_len	-= $this->_readChunk;
					}
				} else {
					clearstatcache();

					$current_part_size	= @filesize($this->_dataFileName);
					$free_space			= $this->_fragmentSize - ($current_part_size === false ? 0 : $current_part_size);

					if ($free_space >= $unc_len) {
						while (!feof($zdatafp) && $unc_len > 0) {
							$zdata		= fread($zdatafp, $this->_readChunk);
							fwrite($this->fp, $zdata, min($this->_strlen($zdata), $this->_readChunk));
							$unc_len	-= $this->_readChunk;
						}
					} else {
						while (!feof($zdatafp)) {
							clearstatcache();

							$current_part_size	= @filesize($this->_dataFileName);
							$free_space			= $this->_fragmentSize - ($current_part_size === false ? 0 : $current_part_size);
							$chunk_size_primary	= min($this->_readChunk, $free_space);

							if ($chunk_size_primary <= 0) {
								$chunk_size_primary	= max($this->_readChunk, $free_space);
							}

							$chunk_size_secondary	= $free_space % $chunk_size_primary;
							$loop_times				= ($free_space - $chunk_size_secondary) / $chunk_size_primary;

							for ($i = 1; $i <= $loop_times; $i++) {
								$zdata		= fread($zdatafp, $chunk_size_primary);
								fwrite($this->fp, $zdata, min($this->_strlen($zdata), $chunk_size_primary));
								$unc_len	-= $chunk_size_secondary;
							}

							if ($chunk_size_secondary > 0) {
								$zdata		= fread($zdatafp, $chunk_size_secondary);
								fwrite($this->fp, $zdata, min($this->_strlen($zdata), $chunk_size_secondary));
								$unc_len	-= $chunk_size_secondary;
							}

							if (!feof($zdatafp) && $unc_len > 0) {
								if (!$this->_createNewPart()) {
									return false;
								}

								fclose($this->fp);
								$this->fp	= null;

								$this->fp	= @fopen($this->_dataFileName, 'ab');

								if ($this->fp === false) {
									return false;
								}
							}
						}
					}

					@fclose($zdatafp);
				}
			}
		} else if ($isSymlink) {
			fwrite($this->fp, @readlink($sourceName));
		}

		if (is_null($this->cdfp) || !is_resource($this->cdfp)) {
			$this->cdfp	= @fopen($this->_centralDirFileName, 'ab');
		}

		if ($this->cdfp === false) {
			return false;
		}

		fwrite($this->cdfp, $this->_centralDirHeader);

		if (!$isSymlink) {
			fwrite($this->cdfp, "\x14\x00");		// Version made by (always set to 2.0).
			fwrite($this->cdfp, "\x14\x00");		// Version needed to extract
			fwrite($this->cdfp, pack('v', 2048));	// General purpose bit flag
			fwrite($this->cdfp, $compressionMethod == 8 ? "\x08\x00" : "\x00\x00");	// Compression Method.
		} else {
			fwrite($this->cdfp, "\x14\x03");		// Version made by (version 2.0 with UNIX extensions).
			fwrite($this->cdfp, "\x0a\x03");		// Version needed to extract
			fwrite($this->cdfp, pack('v', 2048));	// General purpose bit flag
			fwrite($this->cdfp, "\x00\x00");		// Compression Method.
		}

		fwrite($this->cdfp, $hexdtime);			// Last mod time/date.
		fwrite($this->cdfp, pack('V', $crc));	// CRC 32 information.
		fwrite($this->cdfp, pack('V', $c_len));	// Compressed filesize.

		if ($compressionMethod == 0) {
			fwrite($this->cdfp, pack('V', $c_len));		// Uncompressed filesize.
		} else {
			fwrite($this->cdfp, pack('V', $unc_len));	// Uncompressed filesize.
		}

		fwrite($this->cdfp, pack('v', $fn_length));	// Lenth of filename.
		fwrite($this->cdfp, pack('v', 0));			// Extra field length.
		fwrite($this->cdfp, pack('v', 0));			// File comment length.
		fwrite($this->cdfp, pack('v', $starting_disk_number));	// Disk number start.
		fwrite($this->cdfp, pack('v', 0));			// Internal file attributes.

		if (!$isSymlink) {
			fwrite($this->cdfp, pack('V', $isDir ? 0x41FF0010 : 0xFE49FFE0)); // External file attributes - 'archive' bit set.
		} else {
			fwrite($this->cdfp, "\x20\x80\xFF\xA1");	// External file attributes for Symlink.
		}

		fwrite($this->cdfp, pack('V', $old_offset));	// Relative offset of local header.
		fwrite($this->cdfp, $storedName);				// File name.

		$this->_totalFileEntries++;

		return true;
	}
}
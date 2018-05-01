<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup data helper
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Helper_Data extends Mage_Core_Helper_Abstract {
	/**
	 * Unlimit PHP execution.
	 */
	public function unlimit() {
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '2048M');
		set_time_limit(0);

		$write	= Mage::getSingleton('core/resource')->getConnection('core_write');
		$write->query('SET SESSION wait_timeout = 28800');
		$write->query('SET SESSION interactive_timeout = 28800');
	}

	/**
	 * Make file size in bytes to human readable.
	 *
	 * @param	int	$size	Size in bytes
	 * @return	string		Human readable size
	 */
	public function fileSize($size) {
		$unit	= $this->__('B');

		if ($size > 1024) {
			$size	/= 1024;
			$unit	= $this->__('KB');
		}

		if ($size > 1024) {
			$size	/= 1024;
			$unit	= $this->__('MB');
		}

		if ($size > 1024) {
			$size	/= 1024;
			$unit	= $this->__('GB');
		}

		if ($size > 1024) {
			$size	/= 1024;
			$unit	= $this->__('TB');
		}

		$size	= round(number_format($size, 2), 2);

		return $size . ' ' . $unit;
	}

	/**
	 * Get first key of an array.
	 *
	 * @param	array	$array		An array
	 * @return	int|string|false	First key of the array, false if array is empty
	 */
	public function getFirstKey($array) {
		$keys	= array_keys($array);

		return count($keys) ? $keys[0] : false;
	}

	/**
	 * Get next key of a specific key in an array.
	 *
	 * @param	array		$array	An array
	 * @param	int|string	$key	A specific key
	 * @return	int|string|false	The next key, false if can't find the key
	 */
	public function getNextKey($array, $key) {
		$keys		= array_keys($array);
		$position	= array_search($key, $keys);

		return $position !== false && isset($keys[$position + 1]) ? $keys[$position + 1] : false;
	}

	/**
	 * Get part file name of a multipart zip file.
	 *
	 * @param	string	$file	The base zip file name
	 * @param	int		$index	The current part index
	 * @return	string			The current part file name
	 */
	public function getZipPart($file, $index = 0) {
		if ($index == 0) {
			return $file;
		} else {
			$dirname	= dirname($file);
			$base		= ($dirname != '.' ? $dirname . '/' : '') . basename($file, '.zip');

			return $base . '.z' . sprintf('%02d', $index);
		}
	}

	/**
	 * Clean path and transform to unix path.
	 *
	 * @param	string	$path
	 * @return	string
	 */
	public function cleanPath($path) {
		// transform to unix path
		$path	= str_replace('\\', '/', $path);

		// remove multiple splashes
		$path	= str_replace('///', '/', $path);
		$path	= str_replace('//', '/', $path);

		return $path;
	}

	/**
	 * Convert error reporting to string
	 */
	public function error_reporting() {
		if (function_exists('error_reporting')) {
			$value	= error_reporting();
		} else {
			return 'Not applicable; host too restrictive';
		}

		$levelNames	= array(
			E_ERROR				=> 'E_ERROR',
			E_WARNING			=> 'E_WARNING',
			E_PARSE				=> 'E_PARSE',
			E_NOTICE			=> 'E_NOTICE',
			E_CORE_ERROR		=> 'E_CORE_ERROR',
			E_CORE_WARNING		=> 'E_CORE_WARNING',
			E_COMPILE_ERROR		=> 'E_COMPILE_ERROR',
			E_COMPILE_WARNING	=> 'E_COMPILE_WARNING',
			E_USER_ERROR		=> 'E_USER_ERROR',
			E_USER_WARNING		=> 'E_USER_WARNING',
			E_USER_NOTICE		=> 'E_USER_NOTICE'
		);

		if (defined('E_STRICT')) {
			$levelNames[E_STRICT]	= 'E_STRICT';
		}

		$levels	= array();

		if ($value & E_ALL == E_ALL) {
			$levels[]	= 'E_ALL';
			$value &= ~E_ALL;
		}

		foreach ($levelNames as $level => $name) {
			if ($value && $level == $level) {
				$levels[]	= $name;
			}
		}

		return implode(' | ', $levels);
	}
}
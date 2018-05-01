<?php

/**
 * BENCHMARK
 * 100 mo file
 * compressed in 3.2233 sec
 * uncompressed in 0.8543 sec
 * size gain ratio : x28 (3.5 mo)
 */

class Bms_Compression_Engines_ZipArchive extends Bms_Compression_Engines_Base
{
    const TYPE = 'zip';
    const EXTENSION = 'zip';
    const CAN_ARCHIVE = true;
    const DELETE_OUT_FILE = true;

    public $mimeTypes = [
        'application/zip',
        'application/x-zip',
        'application/x-zip-compressed',
        'multipart/x-zip'
    ];

    public function isAvailable($forMode)
    {
        return (function_exists('zip_open') && class_exists('\ZipArchive'));
    }

    public function compressFile($inFilepath, $outFilepath = null, $deleteInFile = false)
    {
        $this->init(static::MODE_COMPRESS, $inFilepath, $outFilepath, $deleteInFile);

        $zip = new \ZipArchive();
        $errorCode = $zip->open($this->outFilepath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        if ($errorCode !== true) {
            throw new Bms_Compression_Exceptions_Error(strtoupper($this->getMode()) . "_KO ZipArchive error : " . (isset($this->getZipArchiveErrors()[$errorCode]) ? $this->getZipArchiveErrors()[$errorCode] : 'Unknown error.') . " (" . $this->inFilepath . ")");
        }
        $zip->addFile($this->inFilepath, pathinfo($this->inFilepath)['basename']);
        $zip->close();

        $this->postProcess();
        return $this->outFilepath;
    }

    public function compressFiles($inFilepath, $outFilepath = null, $deleteInFile = false)
    {
        $this->init(static::MODE_COMPRESS, $inFilepath, $outFilepath, $deleteInFile);

        if (!is_dir($this->inFilepath)) {
            throw new Bms_Compression_Exceptions_Error(strtoupper($this->getMode()) . "_KO folder does not exists (" . $this->inFilepath . ")");
        }

        $zip = new \ZipArchive();
        $errorCode = $zip->open($this->outFilepath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        if ($errorCode !== true) {
            throw new Bms_Compression_Exceptions_Error(strtoupper($this->getMode()) . "_KO ZipArchive error : " . (isset($this->getZipArchiveErrors()[$errorCode]) ? $this->getZipArchiveErrors()[$errorCode] : 'Unknown error.') . " (" . $this->inFilepath . ")");
        }

        $files = scandir($this->inFilepath);
        foreach ($files as $filename) {
            if (in_array($filename, array('.', '..'))) {
                continue;
            }
            $zip->addFile($this->inFilepath . DS . $filename, $filename);
        }

        $zip->close();

        $this->postProcess();
        return $this->outFilepath;
    }

    public function uncompressFile($inFilepath, $outFilepath = null, $deleteInFile = false)
    {
        $this->init(static::MODE_UNCOMPRESS, $inFilepath, $outFilepath, $deleteInFile);

        $zip = new \ZipArchive();
        $errorCode = $zip->open($this->inFilepath);
        if ($errorCode !== true) {
            throw new Bms_Compression_Exceptions_Error(strtoupper($this->getMode()) . "_KO ZipArchive error : " . (isset($this->getZipArchiveErrors()[$errorCode]) ? $this->getZipArchiveErrors()[$errorCode] : 'Unknown error.') . " (" . $this->inFilepath . ")");
        }

        if ($zip->numFiles == 0) {
            throw new Bms_Compression_Exceptions_Error(strtoupper($this->getMode()) . "_KO ZipArchive error : no file into archive (" . $this->inFilepath . ")");
        }

        $containFolders = (strpos($zip->getNameIndex(0), DIRECTORY_SEPARATOR) !== false ? true : false);
        if (!$containFolders && $zip->numFiles == 1) {
            $zip->extractTo(pathinfo($this->outFilepath)['dirname']);
        } else {
            $zip->extractTo($this->outFilepath);
        }
        $zip->close();

        $this->postProcess();
        return $this->outFilepath;
    }

    protected function getZipArchiveErrors()
    {
        return [
            \ZipArchive::ER_EXISTS => 'File already exists.',
            \ZipArchive::ER_INCONS => 'Zip archive inconsistent.',
            \ZipArchive::ER_INVAL => 'Invalid argument.',
            \ZipArchive::ER_MEMORY => 'Malloc failure.',
            \ZipArchive::ER_NOENT => 'No such file.',
            \ZipArchive::ER_NOZIP => 'Not a zip archive.',
            \ZipArchive::ER_OPEN => "Can't open file.",
            \ZipArchive::ER_READ => 'Read error.',
            \ZipArchive::ER_SEEK => 'Seek error.',
        ];
    }
}
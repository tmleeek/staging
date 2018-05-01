<?php

/**
 * BENCHMARK
 * 100 mo file
 * compressed in 3.2233 sec
 * uncompressed in 0.8543 sec
 * size gain ratio : x28 (3.5 mo)
 */

class Bms_Compression_Engines_Zip extends Bms_Compression_Engines_Base
{
    const TYPE = 'zip';
    const EXTENSION = 'zip';
    const CAN_ARCHIVE = true;

    public $mimeTypes = [
        'application/zip',
        'application/x-zip',
        'application/x-zip-compressed',
        'multipart/x-zip'
    ];

    public function isAvailable($forMode)
    {
        if($forMode == static::MODE_COMPRESS) {
            exec("zip --help", $cmdResults);
            if (!is_array($cmdResults) || count($cmdResults) == 0) {
                return false;
            }
        }

        if($forMode == static::MODE_UNCOMPRESS) {
            exec("unzip --help", $cmdResults);
            if (!is_array($cmdResults) || count($cmdResults) == 0) {
                return false;
            }
        }

        return true;
    }

    public function compressFile($inFilepath, $outFilepath = null, $deleteInFile = false)
    {
        $this->init(static::MODE_COMPRESS, $inFilepath, $outFilepath, $deleteInFile);

        list($inDirname, $inBasename, $inExtension) = array_values(pathinfo($this->inFilepath));
        list($outDirname, $outBasename, $outExtension) = array_values(pathinfo($this->outFilepath));
        $cmd = 'cd ' . $inDirname . ';zip ' . $outBasename . ' '.$inBasename;
        exec($cmd);

        $this->postProcess();
        return $this->outFilepath;
    }

    public function compressFiles($inFilepath, $outFilepath = null, $deleteInFile = false)
    {
        $this->init(static::MODE_COMPRESS, $inFilepath, $outFilepath, $deleteInFile);
        $cmd = 'cd ' . $this->inFilepath . ';zip -r ' . $this->outFilepath . ' *';
        exec($cmd);

        $this->postProcess();
        return $this->outFilepath;
    }

    public function uncompressFile($inFilepath, $outFilepath = null, $deleteInFile = false)
    {
        $this->init(static::MODE_UNCOMPRESS, $inFilepath, $outFilepath, $deleteInFile);
        $archiveData = $this->getArchiveData($inFilepath);

        if($archiveData['has_single_file']) {
            exec('unzip -p ' . $inFilepath.' > '.$this->outFilepath);
        } else {
            exec('unzip '.$inFilepath.' -d ' . $this->outFilepath);
        }

        $this->postProcess();
        return $this->outFilepath;
    }

    public function getArchiveData($inFilepath)
    {
        exec("unzip -l $inFilepath", $cmdResults);

        $containFolders = false;
        $files = array();
        for($i = 3; $i < count($cmdResults) - 2; $i++) {
            preg_match("@\s*([0-9]+)\s+([0-9-]{10} [0-9:]{5})\s+(.+)@", $cmdResults[$i], $match);
            list(,$size,$datetime,$name) = $match;

            if(strpos($name, DIRECTORY_SEPARATOR) !== false) {
                $containFolders = true;
            }

            $files[] = array(
                'size' => trim($size),
                'datetime' => trim($datetime).':00',
                'name' => trim($name),
            );
        }

        return array(
            'has_single_file' => !$containFolders && count($files) == 1,
            'files' => $files
        );
    }
}
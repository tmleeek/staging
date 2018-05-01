<?php

/**
 * BENCHMARK
 * 100 mo file
 * compressed in 3.2233 sec
 * uncompressed in 0.8543 sec
 * size gain ratio : x28 (3.5 mo)
 */

class Bms_Compression_Engines_Tgz extends Bms_Compression_Engines_Base
{
    const TYPE = 'tgz';
    const EXTENSION = 'tgz';
    const CAN_ARCHIVE = true;

    public $mimeTypes = [
        'application/x-gzip',
        'application/tar+gzip',
        'application/gzip',
        'application/gzip-compressed',
        'application/gzipped',
        'gzip/document',
    ];

    public function isAvailable($forMode)
    {
        exec("tar --help", $cmdResults);
        if (!is_array($cmdResults) || count($cmdResults) == 0) {
            return false;
        }

        return true;
    }

    public function compressFile($inFilepath, $outFilepath = null, $deleteInFile = false)
    {
        $this->init(static::MODE_COMPRESS, $inFilepath, $outFilepath, $deleteInFile);

        list($inDirname, $inBasename, $inExtension) = array_values(pathinfo($this->inFilepath));
        list($outDirname, $outBasename, $outExtension) = array_values(pathinfo($this->outFilepath));
        $cmd = 'cd ' . $inDirname . ';tar -cvzf ' . $outBasename . ' ' . $inBasename;
        exec($cmd);

        $this->postProcess();
        return $this->outFilepath;
    }

    public function compressFiles($inFilepath, $outFilepath = null, $deleteInFile = false)
    {
        $this->init(static::MODE_COMPRESS, $inFilepath, $outFilepath, $deleteInFile);
        $cmd = 'cd ' . $this->inFilepath . ';tar -cvzf ' . $this->outFilepath . ' *';
        exec($cmd);

        $this->postProcess();
        return $this->outFilepath;
    }

    public function uncompressFile($inFilepath, $outFilepath = null, $deleteInFile = false)
    {
        $this->init(static::MODE_UNCOMPRESS, $inFilepath, $outFilepath, $deleteInFile);
        $archiveData = $this->getArchiveData($inFilepath);

        if($archiveData['has_single_file']) {
            exec('tar -xOvzf ' . $inFilepath.' > '.$this->outFilepath);
        } else {
            exec('mkdir -p '.$this->outFilepath.' && tar -xf '.$inFilepath.' -C ' . $this->outFilepath);
        }

        $this->postProcess();
        return $this->outFilepath;
    }

    public function getArchiveData($inFilepath)
    {
        exec("tar -tvf $inFilepath", $cmdResults);

        $containFolders = false;
        $files = array();
        for($i = 0; $i < count($cmdResults); $i++) {
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
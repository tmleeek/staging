<?php

/**
 * BENCHMARK
 * 100 mo file
 * compressed in 33.2654 sec
 * uncompressed in 5?1344 sec
 * size gain ratio : x43 (2.5 mo)
 */

class Bms_Compression_Engines_Bz2 extends Bms_Compression_Engines_Base
{
    const TYPE = 'bz2';
    const EXTENSION = 'bz2';
    const CAN_ARCHIVE = false;

    const BUFFER_LENGTH = 4096;

    public $mimeTypes = [
        'application/x-bzip2',
        'application/bzip2',
        'application/x-bz2',
        'application/x-bzip'
    ];

    public function isAvailable($forMode)
    {
        return function_exists('bzcompress');
    }

    public function compressFile($inFilepath, $outFilepath = null, $deleteInFile = false)
    {
        $this->init(static::MODE_COMPRESS, $inFilepath, $outFilepath, $deleteInFile);

        $inFile = fopen ($this->inFilepath, "rb");
        $outFile = bzopen ($this->outFilepath, "w");
        while (!feof ($inFile)) {
            $buffer = fgets ($inFile, SELF::BUFFER_LENGTH);
            bzwrite ($outFile, $buffer, SELF::BUFFER_LENGTH);
        }
        fclose ($inFile);
        bzclose ($outFile);

        $this->postProcess();
        return $this->outFilepath;
    }

    public function uncompressFile($inFilepath, $outFilepath = null, $deleteInFile = false)
    {
        $this->init(static::MODE_UNCOMPRESS, $inFilepath, $outFilepath, $deleteInFile);

        $inFile = bzopen ($this->inFilepath, "r");
        $outFile = fopen($this->outFilepath, "wb");
        while ($buffer = bzread ($inFile, SELF::BUFFER_LENGTH)) {
            fwrite ($outFile, $buffer, SELF::BUFFER_LENGTH);
        }
        bzclose ($inFile);
        fclose ($outFile);

        $this->postProcess();
        return $this->outFilepath;
    }
}
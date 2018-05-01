<?php

/**
 * BENCHMARK
 * 100 mo file
 * compressed in 4.4267 sec
 * uncompressed in 0.5896 sec
 * size gain ratio : x27 (4 mo)
*/

class Bms_Compression_Engines_Gz extends Bms_Compression_Engines_Base
{
    const TYPE = 'gz';
    const EXTENSION = 'gz';
    const CAN_ARCHIVE = false;

    public $mimeTypes = [
        'gzip/document',
    ];

    const BUFFER_LENGTH = 4096;

    public function isAvailable($forMode)
    {
        return function_exists('gzencode');
    }

    public function compressFile($inFilepath, $outFilepath = null, $deleteInFile = false)
    {
        $this->init(static::MODE_COMPRESS, $inFilepath, $outFilepath, $deleteInFile);

        $inFile = fopen ($this->inFilepath, "rb");
        $outFile = gzopen ($this->outFilepath, "w");
        while (!feof ($inFile)) {
            $buffer = fgets ($inFile, self::BUFFER_LENGTH);
            gzwrite ($outFile, $buffer, self::BUFFER_LENGTH);
        }
        fclose ($inFile);
        gzclose ($outFile);

        $this->postProcess();
        return $this->outFilepath;
    }

    public function uncompressFile($inFilepath, $outFilepath = null, $deleteInFile = false)
    {
        $this->init(static::MODE_UNCOMPRESS, $inFilepath, $outFilepath, $deleteInFile);

        $inFile = gzopen ($this->inFilepath, "r");
        $outFile = fopen ($this->outFilepath, "wb");
        while (!gzeof ($inFile)) {
            $buffer = gzread ($inFile, self::BUFFER_LENGTH);
            fwrite ($outFile, $buffer, self::BUFFER_LENGTH);
        }
        gzclose ($inFile);
        fclose ($outFile);

        $this->postProcess();
        return $this->outFilepath;
    }
}
<?php

class Bms_Compression
{
    public $engines = [
        'Bms_Compression_Engines_Tgz',
        'Bms_Compression_Engines_Zip',
        'Bms_Compression_Engines_ZipArchive',
        'Bms_Compression_Engines_Bz2'
    ];

    public $engine = null;

    public function compressFile($inFilepath, $outFilepath = null, $deleteInFile = false, $forceType = null)
    {
        if(is_dir($inFilepath)) {
            return $this->compressFiles($inFilepath, $outFilepath, $deleteInFile, $forceType);
        }

        $this->engine = $this->detectCompressEngine($canArchive = false, $forceType);
        return $this->engine->compressFile($inFilepath, $outFilepath, $deleteInFile);
    }

    public function compressFiles($inFilepath, $outFilepath = null, $deleteInFile = false, $forceType = null)
    {
        $this->engine = $this->detectCompressEngine($canArchive = true, $forceType);
        return $this->engine->compressFiles($inFilepath, $outFilepath, $deleteInFile);
    }

    public function canArchive($forceType = null)
    {
        try {
            $this->detectCompressEngine($canArchive = true, $forceType);
        } catch(Bms_Compression_Exceptions_UnknownCompressionType $e) {
            return false;
        }
        return true;
    }

    public function uncompressFile($inFilepath, $outFilepath = null, $deleteInFile = false)
    {
        $this->engine = $this->detectUncompressEngine($inFilepath);
        return $this->engine->uncompressFile($inFilepath, $outFilepath, $deleteInFile);
    }

    protected function detectCompressEngine($canArchive = false, $type = null)
    {
        foreach($this->engines as $engine) {
            $class = new $engine();

            if($type !== null && $type != $class::TYPE) {
                continue;
            }

            if(!$class->isAvailable(Bms_Compression_Engines_Base::MODE_COMPRESS)) {
                continue;
            }

            if($canArchive && !$class::CAN_ARCHIVE) {
                continue;
            }

            return $class;
        }

        throw new Bms_Compression_Exceptions_UnknownCompressionType ("no available compression engine ".($canArchive? "with archive" : "")." ".($type !== null ? ' for type '. $type : ''));
    }

    protected function detectUncompressEngine($filepath)
    {
        $mimeType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $filepath);

        foreach($this->engines as $engine) {
            $class = new $engine();

            $mimeTypeDetected = false;
            foreach($class->getMimeTypes() as $availableMimeType) {
                if($mimeType == $availableMimeType) {
                    $mimeTypeDetected = true;
                    break;
                }
            }
            if(!$mimeTypeDetected) {
                continue;
            }


            if(!$class->isAvailable(Bms_Compression_Engines_Base::MODE_UNCOMPRESS)) {
                continue;
            }

            return $class;
        }

        throw new Bms_Compression_Exceptions_UnknownCompressionType ("no available uncompression engine for mime type $mimeType ($filepath)");
    }
}
<?php

abstract class Bms_Compression_Engines_Base
{
    const MODE_COMPRESS = 'compress';
    const MODE_UNCOMPRESS = 'uncompress';
    const DELETE_OUT_FILE = false;

    public $inFilepath = null;
    public $outFilepath = null;
    public $deleteInFile = false;

    abstract public function isAvailable($forMode);

    abstract public function compressFile($inFilepath, $outFilepath = null, $deleteInFile = false);

    abstract public function uncompressFile($inFilepath, $outFilepath = null, $deleteInFile = false);

    public function init($mode, $inFilepath, $outFilepath = null, $deleteInFile = false)
    {
        $this->setMode($mode);
        $this->setInFilepath($inFilepath);
        $this->setOutFilepath($outFilepath);
        $this->deleteInFile = $deleteInFile;
        $this->checkFilesAndPermissions();
    }

    public function postProcess()
    {
        if ($this->deleteInFile()) {
            $this->deleteFile($this->inFilepath);
        }
    }

    public function setMode($mode)
    {
        $this->mode = $mode;
        return $this;
    }

    public function getMode()
    {
        return $this->mode;
    }

    public function getExtension()
    {
        return static::EXTENSION;
    }

    public function getMimeTypes()
    {
        return $this->mimeTypes;
    }

    public function getDefaultMimeType()
    {
        $mimeTypes = $this->getMimeTypes();
        return isset($mimeTypes[0]) ? $mimeTypes[0] : 'undefined';
    }

    public function deleteOutFile()
    {
        return static::DELETE_OUT_FILE;
    }

    public function deleteInFile()
    {
        return $this->deleteInFile;
    }

    protected function setInFilepath($inFilepath)
    {
        $this->inFilepath = $inFilepath;
        return $this;
    }

    protected function setOutFilepath($outFilepath = null)
    {
        if ($outFilepath) {
            $this->outFilepath = $outFilepath;
            return $this;
        }

        $this->buildOutFilepath();
        return $this;
    }

    protected function buildOutFilepath()
    {
        list($dirname, $basename, $extension) = array_values(pathinfo($this->inFilepath));
        $filename = str_replace('.' . $extension, '', $basename);

        if ($this->getMode() == static::MODE_COMPRESS) {
            $this->outFilepath = $dirname . '/' . $filename . ($extension == $filename ? '' : '.' . $extension) . '.' . $this->getExtension();
        } else {
            $this->outFilepath = $dirname . '/' . $filename;
        }

        return $this;
    }

    protected function checkFilesAndPermissions()
    {
        if (!is_dir($this->inFilepath) && !file_exists($this->inFilepath)) {
            throw new Bms_Compression_Exceptions_Error (strtoupper($this->getMode()) . "_KO : file does not exist (" . $this->inFilepath . ")");
        }

        if (!is_readable($this->inFilepath)) {
            throw new Bms_Compression_Exceptions_Error (strtoupper($this->getMode()) . "_KO : file is not readable (" . $this->inFilepath . ")");
        }

        if (!file_exists($this->outFilepath) && !is_writeable(dirname($this->outFilepath)) || (file_exists($this->outFilepath) && !is_writable($this->outFilepath))) {
            throw new Bms_Compression_Exceptions_Error (strtoupper($this->getMode()) . "_KO : file is not writable (" . $this->outFilepath . ")");
        }

        if ($this->deleteOutFile()) {
            $this->deleteFile($this->outFilepath);
        }
    }

    protected function deleteFile($filepath)
    {
        if(is_dir($filepath) && !empty($filepath) && $filepath != DIRECTORY_SEPARATOR) {
            shell_exec("rm -rf $filepath");
        }
        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }
}
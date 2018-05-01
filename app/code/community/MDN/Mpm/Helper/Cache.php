<?php

class MDN_Mpm_Helper_Cache extends Mage_Core_Helper_Abstract {

    protected function dirname(){

        return Mage::getBaseDir('var').'/';

    }

    public function isEnabled(){

        return true;

    }

    public function load($file, $ttl = 7200){

        $content = false;

        if($this->isEnabled()) {
            $filename = $this->dirname() . str_replace('_', '/', $file) . '.cache';

            if (file_exists($filename)) {

                if (filemtime($filename) > time() - $ttl) {

                    $content = file_get_contents($filename);

                }

            }
        }

        return $content;

    }

    public function add($file, $content){

        $tmp = explode('_', $file);
        $name = array_pop($tmp);
        $dir = (count($tmp) > 0) ? $this->dirname().implode('/',$tmp) : $this->dirname();

        if(!empty($dir)) {
            if (!file_exists($dir))
                mkdir($dir, 0755, true);

            file_put_contents($dir . '/' . $name.'.cache', $content);
        }

    }

    public function flush($file){

        $filename = str_replace('_','/',$this->dirname().$file).'.cache';
        if(file_exists($filename)){
            unlink($filename);
        }

    }

}
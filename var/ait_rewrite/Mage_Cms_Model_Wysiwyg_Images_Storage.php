<?php
/* DO NOT MODIFY THIS FILE! THIS IS TEMPORARY FILE AND WILL BE RE-GENERATED AS SOON AS CACHE CLEARED. */


class Aitoc_Aitmanufacturers_Model_Rewrite_WysiwygImagesStorage extends Mage_Cms_Model_Wysiwyg_Images_Storage
{
    private $s3Helper = null;

    public function getDirsCollection($path)
    {
        if ($this->getS3Helper()->checkS3Usage()) {
            /** @var Thai_S3_Model_Core_File_Storage_S3 $storageModel */
            $storageModel = $this->getS3Helper()->getStorageDatabaseModel();
            $subdirectories = $storageModel->getSubdirectories($path);

            foreach ($subdirectories as $directory) {
                $fullPath = rtrim($path, '/') . '/' . $directory['name'];
                if (!file_exists($fullPath)) {
                    mkdir($fullPath, 0777, true);
                }
            }
        }
        return parent::getDirsCollection($path);
    }

    public function getFilesCollection($path, $type = null)
    {
        if ($this->getS3Helper()->checkS3Usage()) {
            /** @var Thai_S3_Model_Core_File_Storage_S3 $storageModel */
            $storageModel = $this->getS3Helper()->getStorageDatabaseModel();
            $files = $storageModel->getDirectoryFiles($path);

            /** @var Mage_Core_Model_File_Storage_File $fileStorageModel */
            $fileStorageModel = Mage::getModel('core/file_storage_file');
            foreach ($files as $file) {
                $fileStorageModel->saveFile($file);
            }
        }
        return parent::getFilesCollection($path, $type);
    }

    public function resizeFile($source, $keepRatio = true)
    {
        if ($dest = parent::resizeFile($source, $keepRatio)) {
            if ($this->getS3Helper()->checkS3Usage()) {
                /** @var Thai_S3_Model_Core_File_Storage_S3 $storageModel */
                $storageModel = $this->getS3Helper()->getStorageDatabaseModel();

                $filePath = ltrim(str_replace(Mage::getConfig()->getOptions()->getMediaDir(), '', $dest), DS);

                $storageModel->saveFile($filePath);
            }
        }
        return $dest;
    }

    public function getThumbsPath($filePath = false)
    {
        $mediaRootDir = Mage::getConfig()->getOptions()->getMediaDir();
        $thumbnailDir = $this->getThumbnailRoot();

        if ($filePath && strpos($filePath, $mediaRootDir) === 0) {
            $thumbnailDir .= DS . ltrim(dirname(substr($filePath, strlen($mediaRootDir))), DS);
        }

        return $thumbnailDir;
    }

    /**
     * @return Thai_S3_Helper_Core_File_Storage_Database
     */
    protected function getS3Helper()
    {
        if (is_null($this->s3Helper)) {
            $this->s3Helper = Mage::helper('thai_s3/core_file_storage_database');
        }
        return $this->s3Helper;
    }
}


/**
 * Shop By Brands
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitmanufacturers
 * @version      3.3.1
 * @license:     zAuKpf4IoBvEYeo5ue8Cll0eto0di8JUzOnOWiuiAF
 * @copyright:   Copyright (c) 2014 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 * @copyright  Copyright (c) 2011 AITOC, Inc.
 *
 * @author lyskovets
 */

    class Thai_S3_Model_Cms_Wysiwyg_Images_Storage extends Aitoc_Aitmanufacturers_Model_Rewrite_WysiwygImagesStorage
    {
        public function getDirsCollection($path)
        {
            $currVersion = Mage::getVersion();
            if (version_compare($currVersion, '1.4.2.0', 'gt')&& version_compare($currVersion, '1.5.1.0', 'le'))
            {
                if (Mage::helper('core/file_storage_database')->checkDbUsage()) {
                    $subDirectories = Mage::getModel('core/file_storage_directory_database')->getSubdirectories($path);
                    foreach ($subDirectories as $directory) {
                        $fullPath = rtrim($path, DS) . DS . $directory['name'];
                        if (!file_exists($fullPath)) {
                            mkdir($fullPath, 0777, true);
                        }
                    }
                }

                $conditions = array('reg_exp' => array(), 'plain' => array());

                foreach ($this->getConfig()->dirs->exclude->children() as $dir) {
                    $conditions[$dir->getAttribute('regexp') ? 'reg_exp' : 'plain'][(string) $dir] = true;
                }
                // "include" section takes precedence and can revoke directory exclusion
                foreach ($this->getConfig()->dirs->include->children() as $dir) {
                    unset($conditions['regexp'][(string) $dir], $conditions['plain'][(string) $dir]);
                }

                $regExp = $conditions['reg_exp'] ? ('~' . implode('|', array_keys($conditions['reg_exp'])) . '~i') : null;
                $collection = $this->getCollection($path)
                    ->setCollectDirs(true)
                    ->setCollectFiles(false)
                    ->setCollectRecursively(false);
                $storageRootLength = strlen($this->getHelper()->getStorageRoot());

                foreach ($collection as $key => $value) {
                    $rootChildParts = explode(DIRECTORY_SEPARATOR, substr($value->getFilename(), $storageRootLength));

                    if (array_key_exists($rootChildParts[0], $conditions['plain'])
                        || ($regExp && preg_match($regExp, $value->getFilename()))) {
                        $collection->removeItemByKey($key);
                    }
                }

                return $collection;
            }
            else
            {
                return parent::getDirsCollection($path);
            }    
            
        }
        
        public function deleteDirectory($path)
        {
            $currVersion = Mage::getVersion();
            if (version_compare($currVersion, '1.4.2.0', 'gt')&& version_compare($currVersion, '1.5.1.0', 'le'))
            {
                // prevent accidental root directory deleting
                $rootCmp = rtrim($this->getHelper()->getStorageRoot(), DS);
                $pathCmp = rtrim($path, DS);

                if ($rootCmp == $pathCmp) {
                    Mage::throwException(Mage::helper('cms')->__('Cannot delete root directory %s.', $path));
                }

                $io = new Varien_Io_File();

                if (Mage::helper('core/file_storage_database')->checkDbUsage()) {
                    Mage::getModel('core/file_storage_directory_database')->deleteDirectory($path);
                }
                if (!$io->rmdir($path, true)) {
                    Mage::throwException(Mage::helper('cms')->__('Cannot delete directory %s.', $path));
                }

                if (strpos($pathCmp, $rootCmp) === 0) {
                    $io->rmdir($this->getThumbnailRoot() . DS . ltrim(substr($pathCmp, strlen($rootCmp)), '\\/'), true);
                }
            }
            else
            {
                return parent::deleteDirectory($path);
            }
            
        }
    
        public function createDirectory($name, $path)
        {
            $currVersion = Mage::getVersion();
            if (version_compare($currVersion, '1.4.2.0', 'gt')&& version_compare($currVersion, '1.5.1.0', 'le'))
            {
                if (!preg_match(self::DIRECTORY_NAME_REGEXP, $name)) {
                    Mage::throwException(Mage::helper('cms')->__('Invalid folder name. Please, use alphanumeric characters, underscores and dashes.'));
                }
                if (!is_dir($path) || !is_writable($path)) {
                    $path = $this->getHelper()->getStorageRoot();
                }

                $newPath = $path . DS . $name;

                if (file_exists($newPath)) {
                    Mage::throwException(Mage::helper('cms')->__('A directory with the same name already exists. Please try another folder name.'));
                }

                $io = new Varien_Io_File();
                if ($io->mkdir($newPath)) {
                    if (Mage::helper('core/file_storage_database')->checkDbUsage()) {
                        $relativePath = Mage::helper('core/file_storage_database')->getMediaRelativePath($newPath);
                        Mage::getModel('core/file_storage_directory_database')->createRecursive($relativePath);
                    }

                    $result = array(
                        'name'          => $name,
                        'short_name'    => $this->getHelper()->getShortFilename($name),
                        'path'          => $newPath,
                        'id'            => $this->getHelper()->convertPathToId($newPath)
                    );
                    return $result;
                }
                Mage::throwException(Mage::helper('cms')->__('Cannot create new directory.'));
            }
            else
            {
                return parent::createDirectory($name, $path);
            }

        } 
    }


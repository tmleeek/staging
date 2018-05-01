<?php
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

    class Aitoc_Aitmanufacturers_Model_Rewrite_WysiwygImagesStorage extends Mage_Cms_Model_Wysiwyg_Images_Storage
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
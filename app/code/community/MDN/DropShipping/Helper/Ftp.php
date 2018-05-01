<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_DropShipping_Helper_Ftp extends Mage_Core_Helper_Abstract {

    private $_host = null;
    private $_port = null;
    private $_login = null;
    private $_password = null;

    /**
     * INIT
     * @param type $host
     * @param type $port
     * @param type $login
     * @param type $password 
     */
    public function setCredentials($host, $port, $login, $password) {
        $this->_host = $host;
        $this->_port = $port;
        $this->_login = $login;
        $this->_password = $password;
    }

    /**
     *
     * @return type
     * @throws Exception 
     */
    protected function getConnection() {

        $connId = ftp_connect($this->_host, $this->_port);
        if (!$connId || !$this->_host)
            throw new Exception('Unable to connect to ftp server, please check ftp settings');

        $loginResult = ftp_login($connId, $this->_login, $this->_password);

        return $connId;
    }

    /**
     * Upload files
     *
     */
    public function uploadFiles($files, $directory, $tempPrefix = null) {
        $debug = '';
        $connId = $this->getConnection();

        //upload files
        foreach ($files as $file) {
            //upload file
            $fp = fopen($file, 'r');
            $remoteFilePath = $directory . $tempPrefix . basename($file);
            if (!ftp_fput($connId, $remoteFilePath, $fp, FTP_BINARY))
                throw new Exception('Unable to upload ' . $remoteFilePath);
            $debug .= 'Upload file ' . $remoteFilePath . "\n";
            fclose($fp);

            //rename file if tempPrefix set
            if ($tempPrefix != null) {
                $oldFileName = $tempPrefix . basename($file);
                $newFileName = basename($file);

                ftp_rename($connId, $directory . $oldFileName, $directory . $newFileName);
            }
        }

        //close connexion
        ftp_close($connId);
    }

    /**
     * Search for files matching pattern on ftp
     *
     */
    public function downloadFilesMatchingPattern($remoteDirectory, $patterns, $runDirectory, $fileName = NULL) {

        $debug = '';
        $connId = $this->getConnection();
        $matchingFiles = array();
        
        //list files
        $remoteFiles = ftp_nlist($connId, $remoteDirectory);
        foreach ($remoteFiles as $remoteFile) {
            $remoteFile = str_replace("\\", "/", $remoteFile);

            $debug .= 'Check file ' . $remoteFile . "\n";
            //check if matches to any pattern
            foreach ($patterns as $key => $pattern) {
                if ($remoteFile == $pattern) {
                    $debug .= 'File matches : ' . $remoteFile . "\n";
                    $matchingFiles[] = $remoteFile;
                }
                else
                    $debug .= 'File doesnt matches : ' . $remoteFile . "\n";
            }
        }
        
        //download files
        $downloadedFiles = array();
        $downloadDirectory = $runDirectory;

        foreach ($matchingFiles as $key => $remoteFilePath) {
            //download file with the same name or the giving name passed by parameter
            if( $fileName == NULL) {
                $fileName = basename($remoteFilePath);
                $localPath = $downloadDirectory . $fileName;
            }
            else {
                $localPath = $downloadDirectory . $fileName;
            }
            
            if (!ftp_get($connId, $localPath, $remoteFilePath, FTP_BINARY))
                throw new Exception('Unable to download file ' . $remoteFilePath . ' to ' . $localPath);

            $downloadedFiles[$key] = array('key' => $key, 'localpath' => $localPath, 'remotepath' => $remoteFilePath);
        }

        return $downloadedFiles;
    }

    /**
     * Delete remote file
     *
     * @param unknown_type $remotePath
     */
    public function deleteRemoteFile($remotePath) {
        $debug = '';
        $connId = $this->getConnection();

        try {
            ftp_delete($connId, $remotePath);
            $debug = 'File ' . $remotePath . ' deleted' . "\n";
        } catch (Exception $ex) {
            $debug = 'An error occured deleting ftp file ' . $remotePath . ' : ' . $ex->getMessage() . "\n";
        }

        //close connexion
        ftp_close($connId);

        return $debug;
    }

    /**
     *
     * @param type $currentRemotePath
     * @param type $newRemotePath
     * @return string 
     */
    public function moveRemoteFile($currentRemotePath, $newRemotePath) {

        $debug = '';
        $connId = $this->getConnection();

        try {
            if (ftp_rename($connId, './' . $currentRemotePath, './' . $newRemotePath)) {
                $debug .= 'File ' . $currentRemotePath . ' has been moved to ' . $newRemotePath;
            } else {
                $debug = 'An error occured moving ftp file from ' . $currentRemotePath . ' to ' . $newRemotePath . "\n";
            }
        } catch (Exception $e) {
            $debug .= 'An error occured moving ftp file from ' . $currentRemotePath . ' to ' . $newRemotePath . ' : ' . $e->getMessage() . "\n";
        }

        ftp_close($connId);

        return $debug;
    }

    /* public function checkBalise($remotePath){

      $debug = '';
      $connId = $this->getConnection();

      try{



      }catch(Exception $e){
      $debug .= 'An error occured trying to check balise file : '.$e->getMessage()."\n";
      }

      ftp_close($connId);

      return $debug;

      } */

    /**
     * rename the file
     * @param type $directory
     * @param type $oldFileName
     * @param type $newFileName
     */
    public function renameFile($directory, $oldFileName, $newFileName) {

        $connId = $this->getConnection();
        
        // try to rename !! cant bear blank space, must be erased before
        $resultRename = ftp_rename($connId, $directory.$oldFileName, $directory.$newFileName);
        if ($resultRename == FALSE)
            throw new Exception(Mage::Helper('DropShipping')->__("Unable to rename the file %s into %s",$oldFileName,$newFileName));

        // close the connection
        ftp_close($connId);
    }

}
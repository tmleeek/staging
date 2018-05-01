<?php

if (!defined('DS')) define('DS','/');
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);
date_default_timezone_set('Europe/Paris');


/**
* Generate MagentoConnect Package
* 
* Based on xml config File of the Module (this file in save by magento in var/connect 
* when create the package manually)
* 
* Can be used with a modman config file in order to generate contents in the xml and
* grab the version from the config.xml file in the module
* 
*/
class Jetpulp_MagentoConnect
{
    private $verbose=true;

    private $packageName='Addonline_SoColissimo';

    private $packageData;
    
	/**
	 * Main method
	 * 
	 * Need a valid magento path as parameter
	 * 
	 * @param unknown $argv
	 */
    public function main($argv)
    {
    	$this_script = array_shift($argv);
    	$magentoDir = array_shift($argv);
    	if (!$magentoDir) {
    		exit('You must specify magento directory as first parameter');
    	}
    	if (!is_dir($magentoDir . DS . 'downloader') && !is_dir($magentoDir . DS . 'app' . DS . 'code' . DS . 'core') ) {
    		exit('You must specify a valid magento directory as first parameter');
    	}
    	
    	//init magento
    	require $magentoDir . DS . 'app/Mage.php';
    	Mage::app();
    	
     	$this->output(
     			$this->buildExtension($magentoDir)
     	);
    
    }
        
    private function output($string, $newline="\n")
    {
        if(!$this->verbose)
        {
            return;
        }
        echo $string,$newline;
    }
    
    private function error($string)
    {
        $this->output("ERROR: " . $string);
        $this->output("Execution halted at " . __FILE__ . '::' . __LINE__);
        exit;
    }
    
    private function getModuleVersion($file)
    {
        if (file_exists($file)) {
            $xml = simplexml_load_file($file);
            $version_strings = $xml->xpath('//version');
            foreach($version_strings as $version)
            {
                $version = (string) $version;
                if(!empty($version)) 
                {
                    return (string)$version;
                }
            }
        } else {
            $this->error('Module config file '.$file. ' doesn\'t exists');
        }
    }
    
    /**
     * Generate the package file
     * 
     * @param string $magentoDir
     */
    private function buildExtension($magentoDir)
    {
        ob_start();
        
        # check var/connect directory in the magento path
        ###--------------------------------------------------
        $path_output = $magentoDir . DS . 'var' . DS . 'connect';
        if (!file_exists($path_output)) {
            mkdir($path_output);
        }

        # Copy the xml config package file in the var/connect magento path 
        # and load its data
        ###--------------------------------------------------
        $sourcefile = dirname(__FILE__). DS . $this->packageName . '.xml';
        $destfile = $magentoDir . DS . 'var' . DS . 'connect' . DS . $this->packageName . '.xml';
        if (!copy( $sourcefile, $destfile) ) {
			$this->error('Impossible to copy '.$sourcefile. ' to ' . $destfile);
			exit();
        }
        
        $this->packageData = Mage::helper('connect')->loadLocalPackage($this->packageName);
        
        # enrich $this->packageData with contents from modman file   
        ###-------------------------------------------------------
        if (file_exists(dirname(__FILE__). '/modman')) {

            $this->packageData['contents']['target'] = array();
            $this->packageData['contents']['path'] = array();
            $this->packageData['contents']['type'] = array();
            $this->packageData['contents']['include'] = array();
            $this->packageData['contents']['ignore'] = array();

            $this->addContent('magelocal', ''); //an empty first line is necessary ?
            
            $files = array();
	        $modmanfile = file_get_contents(dirname(__FILE__). '/modman');
			foreach (explode("\n", $modmanfile) as $modmanline) {
				 $line = explode(" ", $modmanline);
				 $file =  dirname(__FILE__) . DS . $line[0];
				 $targetAndPath = $this->getTargetAndPath($line[0]);
				 if (file_exists($file)) {
				 	if (is_dir($file)) {
				 	    $type = 'dir';
				 	    if (file_exists(dirname(__FILE__).DS.$line[0].DS.'etc/config.xml')) {
				 	        $configFile = $line[0].DS.'etc/config.xml';
				 	    }
				 	} else {
				 	    $type = 'file';
				 	}
				 	$this->addContent($targetAndPath[0], $targetAndPath[1], $type);
				 } else {
				 	$this->error("File or directory is missing $file");
				 }
			}
        	$extensionVersion = $this->getModuleVersion($configFile);
        }
		
        # set version from the config.xml file
        ###-------------------------------------------------------
        if ($extensionVersion) {        
        	$this->packageData['version']= $extensionVersion;
        }
        
        //var_dump($this->packageData);
		//die(); 
		       
        # use magento Connect module to save package.xml file et create package file
        ###-------------------------------------------------------------------------
        $ext = Mage::getModel('connect/extension');
        /** @var $ext Mage_Connect_Model_Extension */
        $ext->setData($this->packageData);
        if ($ext->savePackage()) {
        	$this->output(Mage::helper('connect')->__('The package data has been saved.'));
        	if ($ext->createPackage()) {
        	    $this->output(Mage::helper('connect')->__('The package has been created.'));

                # Report on what we did
                $this->output('');
                $this->output('Build Complete');
                $this->output('--------------------------------------------------');
                $this->output( "Built $this->packageName-$extensionVersion.tgz in $path_output\n");

        	} else {
        	    $this->output(Mage::helper('connect')->__('There was a problem creating package'));
        	}
        	//$ext->createPackageV1x();
        } else {
        	$this->output(Mage::helper('connect')->__('There was a problem saving package data'));
        }

        ###--------------------------------------------------
        
        return ob_get_clean();
    }
    
    /**
     * Add a content in the package datas
     * 
     * @param string $target
     * @param string $path
     * @param string $type
     * @param string $include
     * @param string $exclude
     */
    private function addContent($target, $path, $type = 'file', $include = '', $exclude = '')
    {
        $this->packageData['contents']['target'][] = $target;
        $this->packageData['contents']['path'][] = $path;
        $this->packageData['contents']['type'][] = $type;
        $this->packageData['contents']['include'][] = $include;
        $this->packageData['contents']['ignore'][] = $exclude;
    }

    /**
     * Gets the target and the path from a filename 
     *  
     * @param string $filename
     * @return array
     */
    private function getTargetAndPath($filename)
    {
		if (strpos($filename, 'app/code/local/') === 0) {
		    return array('magelocal', str_replace( 'app/code/local/' ,'', $filename));
		} elseif (strpos($filename, 'app/code/community/') === 0) {
		    return array('magecommunity', str_replace( 'app/code/community/' ,'', $filename));
		} elseif (strpos($filename, 'app/code/core/') === 0) {
		    return array('magecore', str_replace( 'app/code/core/' ,'', $filename));
		} elseif (strpos($filename, 'app/design/') === 0) {
		    return array('magedesign', str_replace( 'app/design/' ,'', $filename));
		} elseif (strpos($filename, 'app/etc/') === 0) {
		    return array('mageetc', str_replace( 'app/etc/' ,'', $filename));
		} elseif (strpos($filename, 'app/locale/') === 0) {
		    return array('magelocale', str_replace( 'app/locale/' ,'', $filename));
		} elseif (strpos($filename, 'lib/') === 0) {
		    return array('magelib', str_replace( 'lib/' ,'', $filename));
		} elseif (strpos($filename, 'media/') === 0) {
		    return array('magemedia', str_replace( 'media/' ,'', $filename));
		} elseif (strpos($filename, 'skin/') === 0) {
		    return array('mageskin', str_replace( 'skin/' ,'', $filename));
		} elseif (strpos($filename, 'js/') === 0 || strpos($filename, 'errors/') === 0 ) {
		    return array('mageweb', $filename);
		} else {
		    return array('mage', $filename);
		}
    }
    
 
}
if(isset($argv))
{
    $connect = new Jetpulp_MagentoConnect();
    $connect->main($argv);
}

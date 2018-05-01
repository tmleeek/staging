<?php
class Extendware_EWCore_Model_Observer
{
	static public function refreshModules()
    {
		Mage::getResourceModel('ewcore/module_summary')->refreshAll();
    }
    
    static public function updateLicensesAndSerials()
    {
    	if (Mage::helper('ewcore/config')->isUpdateLicensesOnCronjobEnabled() === false) {
    		return;
    	}
    	
    	if (isset($_SERVER['REQUEST_TIME']) === false or !$_SERVER['REQUEST_TIME']) {
    		$_SERVER['REQUEST_TIME'] = time();
    	}
    	
    	$maxTime = 60*60*30;
    	$maxTimeToWait = 60*15;
    	if (ini_get('max_execution_time') > 0 and ini_get('max_execution_time') <= $maxTime) {
    		ini_set('max_execution_time', $maxTime);
    		$maxTimeToWait = min(ini_get('max_execution_time') - 120, $maxTimeToWait);
    	}
    	
    	// we wait so that all installs do not access our server at the same time
    	// please do not change this
    	$waitUntilTime = time() + mt_rand(0, $maxTimeToWait); // now + 0s second to 15 minutes from now
    	Mage::helper('ewcore/licensing')->setIsCron(true);
    	$modules = Mage::getSingleton('ewcore/module')->getCollection()->getItems();
    	shuffle($modules);
		foreach ($modules as $module) {
			if ($module->isActive() === true) {
				if (Mage::helper('ewcore/licensing')->getIsTimeForUpdate($module->getId()) === true) {
					if (Mage::helper('ewcore/environment')->isDevelopmentServer() === false) {
						// close connection to prevent mysql connection error
						try { Mage::getSingleton('core/resource')->getConnection('core_read')->closeConnection(); }
	    				catch (Exception $e) { Mage::logException($e); }
						while (time() < $waitUntilTime) {
			    			sleep(5);
			    			// this is done because the way some hosts have mysql configured.
			    			// this prevents the connection from closing
			    			// Mage::getModel('customer/customer')->load(1);
						}
			    	}
    	
    				$module->updateLicensesAndSerial();
				}
			}
    	}
    }
    
	static public function cleanupSystemMessages()
    {
    	$collection = Mage::getModel('ewcore/system_message')->getCollection();
    	$collection->setOrder('system_message_id', 'DESC');
    	$collection->getSelect()->limit(null, Mage::helper('ewcore/config')->getSystemMessagesMaxNum());
    	$collection->delete();
    }
    
    static public function disableIllegalExtensions()
    {
    	$config = Mage::helper('ewcore/config');
    	if ($config->isViolationDisablingEnabled() === false) {
    		return;
    	}
    	
    	$modulesToDisable = array();
    	$moduleCollection = Mage::getSingleton('ewcore/module')->getCollection();
		foreach ($moduleCollection as $module) {
    		if ($module->isActive() === true and $module->isExtendware() === true and $module->isForMainSite() === false) {
    			if ($module->isLicensed() === false) continue;
				
    			$license = $module->getLicense();
    			if ($license->isRespected() === false) {
    				Mage::helper('ewcore/system')->log(Mage::helper('ewcore')->__('Disabled %s because license is not respected', $module->getIdentifier()), true);
    				$modulesToDisable[$module->getIdentifier()] = $module;
    				continue;
    			}
    			
    			if ($config->isPreemptiveExpirationDisablingEnabled() === true) {
    				$minExpirationTime = time() + 60*60*24*999999;
    				if ($module->getLicense()->getExpiry()> 0) $minExpirationTime = min($minExpirationTime, $module->getLicense()->getExpiry());
		    		if ($module->getEncoderLicense()->getExpiry() > 0) $minExpirationTime = min($minExpirationTime, $module->getEncoderLicense()->getExpiry());
		    		
	    			if ($minExpirationTime > 0) {
		    			$hours = ($minExpirationTime - time()) / (60*60);
		    			if ($config->getPreemptiveExpirationDisablingNumHours() > $hours) {
		    				Mage::helper('ewcore/system')->log(Mage::helper('ewcore')->__('Disabled %s because license is about to expire', $module->getIdentifier()), true);
		    				$modulesToDisable[$module->getIdentifier()] = $module;
		    				continue;
		    			}
	    			}
    			}
    		}
    	}
		
    	if (empty($modulesToDisable) === false) {
	    	// turn off compilation
			Mage::getModel('compiler/process')->registerIncludePath(false);
			
			$configTools = Mage::helper('ewcore/config_tools');
			foreach ($modulesToDisable as $module) {
				try {
					$configTools->disableModule($module->getIdentifier());
				} catch (Exception $e) { Mage::logException($e); }
			}
    	}
    }
}

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
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

class Aitoc_Aitmanufacturers_Controller_Router extends Mage_Core_Controller_Varien_Router_Standard
{
    const MODULE     = 'Aitoc_Aitmanufacturers';
    const CONTROLLER = 'index';
     
    public function initControllerRouters($observer)
    {   
        $front = $observer->getEvent()->getFront();

        $router = new Aitoc_Aitmanufacturers_Controller_Router();
        $front->addRouter('aitmanufacturers', $router);
    }

    public function match(Zend_Controller_Request_Http $request)
    {
        $identifier = trim($request->getPathInfo(), '/');
        $attributeCode = '';
        $d = explode('/', $identifier, 3);
        
        if (isset($_GET['___from_store'])) {
            return $this->_storeRewriteRedirrector($request);
        } 
        $attributeCode = Mage::helper('aitmanufacturers')->checkUrlPrefix($d[0], Mage::app()->getStore()->getId());
        
        if (!$attributeCode)
        {
            return false;
        } else {
            if (!Mage::registry('shopby_attribute'))
            {
                Mage::register('shopby_attribute', $attributeCode);
            }
        }

        $action = 'index';

        $request->setModuleName(self::MODULE)
            ->setControllerName(self::CONTROLLER)
            ->setActionName($action);
            
		$request->setAlias(
			Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
			$identifier
		);
		
		$front = $this->getFront();
		$controllerClassName = $this->_validateControllerClassName(self::MODULE, self::CONTROLLER);
		$controllerInstance = new $controllerClassName($request, $front->getResponse());
		$request->setDispatched(true);
        $controllerInstance->dispatch($action);
        
        return true;
    }
    
    protected function _storeRewriteRedirrector(Zend_Controller_Request_Http $request)
    {
        $identifier = trim($request->getPathInfo(), '/');
        $attributeCode = '';
        $d = explode('/', $identifier, 3);
        try {
             $fromStoreId = Mage::app()->getStore($_GET['___from_store'])->getId();
             $attributeCode = Mage::helper('aitmanufacturers')->checkUrlPrefix($d[0], $fromStoreId);
             if (isset($_GET['___store']))
             {
                 $toStoreId = Mage::app()->getStore($_GET['___store'])->getId();
             } else {
                 $toStoreId = Mage::app()->getStore()->getId();
             }             
             $targetUrl = Mage::helper('aitmanufacturers')->getManufacturersUrl($attributeCode, $toStoreId);
        }
        catch (Exception $e) {
             return false;
        }
        if ($attributeCode) {
             $front = $this->getFront();
             $front->getResponse()->setRedirect($targetUrl);
      		 $request->setDispatched(true);
        }
        else 
        {
            //redirect for attributes options pages in Magento 1.8.0.0 and greater 
            //when url key for options pages is different on different store views
            if (version_compare(Mage::getVersion(), '1.8.0.0', 'ge'))
            {
                Mage::getModel('core/url_rewrite')->rewrite();
            }
        }
        return true;
    }
    
    /**
     * Generating and validating class file name,
     * class and if evrything ok do include if needed and return of class name
     *
     * @return mixed
     */
    protected function _validateControllerClassName($realModule, $controller)
    {
        $controllerFileName = $this->getControllerFileName($realModule, $controller);
        if (!$this->validateControllerFileName($controllerFileName)) {
            return false;
        }

        $controllerClassName = $this->getControllerClassName($realModule, $controller);
        if (!$controllerClassName) {
            return false;
        }

//         include controller file if needed
        if (!$this->_inludeControllerClass($controllerFileName, $controllerClassName)) {
            return false;
        }

        return $controllerClassName;
    }
    
    /**
     * Including controller class if checking of existense class before include
     *
     * @param string $controllerFileName
     * @param string $controllerClassName
     * @return bool
     */
    protected function _inludeControllerClass($controllerFileName, $controllerClassName)
    {
        if (!class_exists($controllerClassName, false)) {
            if (!file_exists($controllerFileName)) {
                return false;
            }
            include $controllerFileName;

            if (!class_exists($controllerClassName, false)) {
                throw Mage::exception('Mage_Core', Mage::helper('core')->__('Controller file was loaded but class does not exist'));
            }
        }
        return true;
    }
}
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

class Aitoc_Aitmanufacturers_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        if (!Mage::registry('shopby_attribute'))
        {
            /**
             * 
             * If you get the /brands url and you have manufacturer to brands 
             * conversion disabled, then you should forward to 404
             */
            $shopByAttribute = Mage::helper('aitmanufacturers')->checkUrlPrefix(Aitoc_Aitmanufacturers_Helper_Data::URLPREFIX_BRANDS, Mage::app()->getStore()->getId());
            if (!$shopByAttribute)
            {
                return $this->_forward('noRoute');
            }
            Mage::register('shopby_attribute', $shopByAttribute);
        }
        
        $this->loadLayout(array('default', 'aitmanufacturers_index_list'));
        $this->renderLayout();
    }
    
    public function viewAction()
    {
        $session = Mage::getSingleton('core/session');
        $helper = Mage::helper('aitmanufacturers');


        if ($id = $this->getRequest()->getParam('id'))
        {
            try {
                $brandId = Mage::getModel('aitmanufacturers/aitmanufacturers')->load($this->getRequest()->getParam('id'))->getManufacturerId();
               
                if (!Mage::registry('shopby_attribute'))
                {
                    Mage::register('shopby_attribute', Mage::getModel('aitmanufacturers/config')->getAttributeCodeByOption($brandId));
                }
                $session->setAitocManufacturersCurrentManufacturerId($brandId);
                //Mage::app()->getRequest()->setParam('shopby_attribute', $brandId);

            }
            catch (Exception $e) {}
        }
        
        $session->setLayeredNavigationUsedFromAitocManufacturersModule(true);

        if (!Mage::helper('aitmanufacturers/manufacturer')->renderPage($this, $id, Mage::getModel('aitmanufacturers/config')->getAttributeId(Mage::registry('shopby_attribute'))))
        {   
            $this->_forward('index', 'index', 'aitmanufacturers');
        }
    }
}
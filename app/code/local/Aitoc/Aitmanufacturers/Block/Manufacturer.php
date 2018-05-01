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

class Aitoc_Aitmanufacturers_Block_Manufacturer extends Mage_Core_Block_Template
{
    protected $_manufacturer = null;
    
    public function __construct()
    {
        if (!$this->_manufacturer)
            $this->_manufacturer = Mage::getModel('aitmanufacturers/aitmanufacturers')->load($this->getRequest()->getParam('id'));
            
        $processor = Mage::getModel('core/email_template_filter');
        $html = $processor->filter(nl2br($this->_manufacturer->getContent()));
        $this->_manufacturer->setContent($html);
    }

    protected function _prepareLayout()
    {
        $config = Mage::getModel('aitmanufacturers/config');
        
        $attributeCode = $config->getAttributeCodeById($config->getAttributeIdByOption($this->_manufacturer->getManufacturerId()));
        $attributeName = Mage::getModel('aitmanufacturers/config')->getAttributeName($attributeCode);
        
        if (!in_array($this->getAction()->getFullActionName(), array('adjnav_ajax_category')))
        {         
            $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
            if ($breadcrumbs)
            {
                $breadcrumbs->addCrumb('home', array('label'=>Mage::helper('cms')->__('Home'), 'title'=>Mage::helper('cms')->__('Go to Home Page'), 'link'=>Mage::getBaseUrl()));
                $breadcrumbs->addCrumb('manufacturers', array('label'=>Mage::helper('aitmanufacturers')->__('All '.$attributeName.'s'), 'title'=>Mage::helper('aitmanufacturers')->__('Go to All '.$attributeName.'s List'), 'link' => Mage::helper('aitmanufacturers')->getManufacturersUrl($attributeCode, Mage::app()->getStore()->getId())));
                $breadcrumbs->addCrumb('manufacturer', array('label'=>$this->_manufacturer->getManufacturer(), 'title'=>$this->_manufacturer->getManufacturer()));
            }

            if ($root = $this->getLayout()->getBlock('root')) {
                $template = (string)Mage::getConfig()->getNode('global/aitmanufacturers/layouts/'.$this->_manufacturer->getRootTemplate().'/template');
                $root->setTemplate($template);
                $root->addBodyClass('aitmanufacturers-'.$this->_manufacturer->getUrlKey());
            }
        }

        if ($head = $this->getLayout()->getBlock('head')) {
            $head->setTitle($this->_manufacturer->getTitle());
            if ($this->_manufacturer->getMetaKeywords())
                $head->setKeywords($this->_manufacturer->getMetaKeywords());
            if ($this->_manufacturer->getMetaDescription())
                $head->setDescription($this->_manufacturer->getMetaDescription());
            if ($this->helper('aitmanufacturers')->canUseCanonicalTag())
                $head->addLinkRel('canonical', $this->getManufacturer()->getUrl());
        }
    }
    
    public function getManufacturer()
    {
        return $this->_manufacturer;
    }
}
<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_XSitemap_Model_Adminhtml_System_Config_Source_Common_Slash
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'default', 'label' => Mage::helper('xsitemap')->__('Default')),
            array('value' => 'add', 'label' => Mage::helper('xsitemap')->__('Add')),
            array('value' => 'crop', 'label' => Mage::helper('xsitemap')->__('Crop')),
        );
    }

}

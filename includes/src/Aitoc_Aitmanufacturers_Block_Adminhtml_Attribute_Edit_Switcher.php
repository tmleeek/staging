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

class Aitoc_Aitmanufacturers_Block_Adminhtml_Attribute_Edit_Switcher extends Mage_Adminhtml_Block_Store_Switcher
{
    protected $_storeIds;
    
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('aitmanufacturers/attribute/edit/store_switcher.phtml');
        $this->setUseConfirm(false);
        $this->setUseAjax(false);
        $this->setDefaultStoreName($this->__('All Store Views'));
    }

    /* dirty hack below - we should always render this template cause there is a script. moving the script to outer file is a good idea for refactoring */
    public function isShow()
    {
        return true;
    }

    protected function _toHtml()
    {
        Mage::dispatchEvent('adminhtml_block_html_before', array('block' => $this));
        if (!$this->getTemplate()) {
            return '';
        }
        $html = $this->renderView();
        return $html;
    }
}
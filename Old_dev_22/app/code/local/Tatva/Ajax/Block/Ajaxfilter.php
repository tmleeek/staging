<?php

class Tatva_Ajax_Block_Ajaxfilter extends Mage_Catalog_Block_Product_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('ajax/ajax_filter.phtml');
    }
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

}

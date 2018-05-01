<?php

class Tatva_Ajax_Block_Ajaxcartdelete extends Mage_Catalog_Block_Product_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('ajax/ajaxcart.phtml');
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function TopCartLink()
    {
            $count = $this->helper('checkout/cart')->getSummaryCount();

            $empty_string = ($empty_string) ? $empty_string : 'My Cart';
            $one_string = ($one_string) ? $one_string : 'My Cart (%s item)';
            $multiple_string = ($multiple_string) ? $multiple_string : 'My Cart (%s items)';

            if( $count == 1 ) {
                $text = $this->__($one_string, $count);
            } elseif( $count > 0 ) {
                $text = $this->__($multiple_string, $count);
            } else {
                $text = $this->__($empty_string, $count);
            }
        return $text;
    }
}

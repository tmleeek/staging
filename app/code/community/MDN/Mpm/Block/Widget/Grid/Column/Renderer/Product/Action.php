<?php

class MDN_Mpm_Block_Widget_Grid_Column_Renderer_Product_Action extends
    Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {

        $html = "<select onchange='productGridAction(this);'><option></option>";

        $magentoId = $this->getIdMagento($row->getProductId());
        if(!empty($magentoId)) {
            $url = Mage::getUrl('adminhtml/catalog_product/edit', array('id' => $this->getIdMagento($row->getProductId())));
            $html .= "<option value='edit' data-url='$url'>" . Mage::helper('catalog')->__('Edit') ."</option>";
        }

        $html .= "<option value='reprice' >" . Mage::helper('catalog')->__('Reprice') ."</option>";
        $html .= "</select>";
        return $html;
    }

    protected function getIdMagento($sku)
    {
        if($product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku)) {
            return $product->getId();
        }

        return;
    }
}
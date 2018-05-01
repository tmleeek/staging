<?php

class MDN_AdvancedStock_Block_Adminhtml_Catalog_Product_Edit extends Mage_Adminhtml_Block_Catalog_Product_Edit {

    protected function _prepareLayout() {

        parent::_prepareLayout();

        if ($this->getProduct()->getId()) {
            $this->setChild('erp_view',
                            $this->getLayout()->createBlock('adminhtml/widget_button')
                            ->setData(array(
                                'label' => Mage::helper('AdvancedStock')->__('ERP View'),
                                'onclick' => 'setLocation(\'' . $this->getUrl('AdvancedStock/Products/Edit', array('product_id' => $this->getProduct()->getId())) . '\')'
                            ))
            );
        }

        return $this;
    }

    public function getSaveButtonHtml() {
        return $this->getChildHtml('erp_view') . $this->getChildHtml('save_button');
    }

}

<?php
class Tatva_Customerattributes_Adminhtml_ProductController  extends Mage_Adminhtml_Controller_Action
{
    public function addAttributeAction()
    {
        $this->_getSession()->addNotice(
            Mage::helper('Customerattributes')->__('Please click on the Close Window button if it is not closed automatically.')
        );
        $this->loadLayout('popup');
        $this->_initProduct();
        $this->_addContent(
            $this->getLayout()->createBlock('customerattributes/adminhtml_customerattributes_new_product_created')
        );
        $this->renderLayout();
    }
}
<?php

class MDN_Mpm_Adminhtml_Mpm_PricerController extends Mage_Adminhtml_Controller_Action
{

    public function applyAction()
    {
        $productId = $this->getRequest()->getParam('product_id');
        $channel = $this->getRequest()->getParam('channel');
        $product = Mage::getModel('catalog/product')->load($productId);
        try
        {
            Mage::getSingleton('Mpm/Pricer')->processProduct($product, ($channel ? $channel : null));
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('Mpm')->__('Price updated'));
        }
        catch(Exception $ex)
        {
            Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());
        }


        $this->_redirect('adminhtml/catalog_product/edit', array('id' => $productId,  'tab' => 'product_info_tabs_mpm_carl'));
    }

    public function ErrorsAction()
    {
        if (!Mage::helper('Mpm/Carl')->checkCredentials())
        {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('Mpm')->__('Please configure Carl credentials first'));
            $this->_redirect('adminhtml/system_config/edit', array('section' => 'mpm'));
        }
        else {
            $this->loadLayout();
            $this->renderLayout();
        }
    }

    public function PriceAllAction()
    {
        try
        {
            Mage::helper('Mpm/Product')->repriceAll();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('Mpm')->__('Prices updated'));
        }
        catch(Exception $ex)
        {
            Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());
        }

        $this->_redirect('adminhtml/system_config/edit', array('section' => 'mpm'));
    }

    protected function _isAllowed()
    {
        return true;
    }

}

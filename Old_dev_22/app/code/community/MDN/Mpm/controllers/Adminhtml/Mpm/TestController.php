<?php

class MDN_Mpm_Adminhtml_Mpm_TestController extends Mage_Core_Controller_Front_Action
{

    public function testAction()
    {
        $report = Mage::helper('Mpm/Commission')->Synchronize('amazon_fr_default');
   }

    public function productAction()
    {
        $productId = $this->getRequest()->getParam('id');
        $channel = $this->getRequest()->getParam('channel');
        $product = Mage::getModel('catalog/product')->load($productId);

        $model = Mage::getModel('Mpm/Pricer');

        try
        {
            $model->calculatePrice($product, $channel);
        }
        catch(Exception $ex)
        {
            Mage::helper('Mpm')->log(date('H:i:s')." product action test error ".$ex->getMessage());
        }

        Mage::helper('Mpm')->log(date('H:i:s')." product action test ".json_encode($model->_debug));
    }

    public function tokenInputAction(){

        try {
            $response = Mage::Helper('Mpm/Seller')->searchFromTokenInput($this->getRequest()->getParam('q'));
        }catch(Exception $e){
            $response = '';
        }
        $this->getResponse()->setBody($response);

    }

}
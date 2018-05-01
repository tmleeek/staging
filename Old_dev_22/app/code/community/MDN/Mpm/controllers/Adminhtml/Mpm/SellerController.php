<?php

class MDN_Mpm_Adminhtml_Mpm_SellerController extends Mage_Adminhtml_Controller_Action {

    public function tokenInputAction(){

        try {
            $response = Mage::Helper('Mpm/Seller')->searchFromTokenInput($this->getRequest()->getParam('q'));
        }catch(Exception $e){
            $response = '';
        }
        $this->getResponse()->setBody($response);

    }

    protected function _isAllowed()
    {
        return true;
    }

}
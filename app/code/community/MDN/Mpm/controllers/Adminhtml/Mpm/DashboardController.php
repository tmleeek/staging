<?php

class MDN_Mpm_Adminhtml_Mpm_DashboardController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        if (!Mage::helper('Mpm/Carl')->checkCredentials()) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('Mpm')->__('Please configure Carl credentials first'));
            $this->_redirect('adminhtml/system_config/edit', array('section' => 'mpm'));
        } else {
            Mage::helper('Mpm/Product')->pricingInProgress();
            $this->loadLayout();
            $this->renderLayout();
        }
    }

    public function channelBLockAction()
    {
        $channel = $this->getRequest()->getParam('channel');
        Mage::register('mpm_channel', $channel);

        $this->loadLayout();

        $block = $this->getLayout()->createBlock('Mpm/Dashboard_Tabs_Channel');
        $this->getResponse()->setBody($block->toHtml());
    }


    protected function _isAllowed()
    {
        return true;
    }
}

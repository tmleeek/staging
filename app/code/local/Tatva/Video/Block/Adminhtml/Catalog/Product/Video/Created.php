<?php

class Tatva_Video_Block_Adminhtml_Catalog_Product_Video_Created extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product/video/created.phtml');
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'close_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'   => Mage::helper('catalog')->__('Close Window'),
                    'onclick' => 'addVideo(true)'
                ))
        );

    }

    public function getCloseButtonHtml()
    {
        return $this->getChildHtml('close_button');
    }

    public function getVideoBlockJson()
    {
        $result = array(
            $this->getRequest()->getParam('tab') => $this->getChildHtml('tab_video')
        );

        return Zend_Json::encode($result);
    }
}
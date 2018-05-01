<?php

class Tatva_Video_Block_Adminhtml_Catalog_Product_Edit_Tab_Video_Create extends Mage_Adminhtml_Block_Widget_Button
{
    /**
     * Config of create new attribute
     *
     * @var Varien_Object
     */
    protected $_config = null;

    /**
     * Retrive config of new attribute creation
     *
     * @return Varien_Object
     */
    public function getConfig()
    {
        if (is_null($this->_config)) {
           $this->_config = new Varien_Object();
        }

        return $this->_config;
    }

    protected function _beforeToHtml()
    {
        $this->setId('create_video')
            ->setOnClick($this->getJsObjectName() . '.create();')
            ->setType('button')
            ->setClass('add')
            ->setLabel(Mage::helper('tatvavideo')->__('Create New Video'));

        $this->getConfig()
            ->setUrl($this->getUrl(
                '*/*/videoEdit',
                array(
                    'popup'     => 1,
                	'product_id' => $this->getRequest()->getParam('id')
                )
            ));

        return parent::_beforeToHtml();
    }

    public function _toHtml()
    {
        $this->setCanShow(true);
 
        if (!$this->getCanShow()) {
            return '';
        }

        $html = parent::_toHtml();
        $html .= Mage::helper('adminhtml/js')->getScript(
            "var {$this->getJsObjectName()} = new Product.Video('{$this->getId()}');\n"
            . "{$this->getJsObjectName()}.setConfig(" . Zend_Json::encode($this->getConfig()->getData()) . ");\n"
        ); 

        return $html;
    }

    public function getJsObjectName()
    {
        return $this->getId() . 'JsObject';
    }
}
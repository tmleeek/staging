<?php
class Phpro_Translate_Block_Adminhtml_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('translate_default_tabs');
        $this->setDestElementId('container-content');
        $this->setTitle(Mage::helper('translate')->__('Translate'));
    }

    protected function _beforeToHtml() {
        $this->addTab('search_string', array(
            'label' => Mage::helper('translate')->__('Search &amp; translate'),
            'title' => Mage::helper('translate')->__('Search &amp; translate'),
            'content' => $this->getLayout()->createBlock("translate/adminhtml_form")->toHtml(),
        ));

        $this->addTab('list_untranslated', array(
            'label' => Mage::helper('translate')->__('Untranslated strings'),
            'title' => Mage::helper('translate')->__('Untranslated strings'),
            'content' => $this->getLayout()->createBlock("translate/adminhtml_grid")->toHtml(),
        ));
        
        $this->addTab('statistics', array(
            'label' => Mage::helper('translate')->__('Translation stats'),
            'title' => Mage::helper('translate')->__('Translation stats'),
            'content' => $this->getLayout()->createBlock("translate/adminhtml_stats")->toHtml(),
        ));
        
        $this->addTab('about', array(
            'label' => Mage::helper('translate')->__('About PHPro'),
            'title' => Mage::helper('translate')->__('About PHPro'),
            'content' => $this->getLayout()->createBlock("translate/adminhtml_about")->toHtml(),
        ));

        return parent::_beforeToHtml();
    }

    public function getHeaderText() {
        return Mage::helper('translate')->__('Test');
    }

}
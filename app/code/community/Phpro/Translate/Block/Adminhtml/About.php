<?php
class Phpro_Translate_Block_Adminhtml_About extends Mage_Adminhtml_Block_Widget_View_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'about_id';
        $this->_blockGroup = 'about';
        $this->_controller = 'adminhtml';
        $this->setTemplate('translate/about.phtml');
        
        $this->removebutton('edit');
    }

    public function getHeaderText() {
        return Mage::helper('translate')->__("About");
    }

}
<?php
class Phpro_Translate_Block_Adminhtml_Translate extends Mage_Adminhtml_Block_Widget_View_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'test_id';
        $this->_blockGroup = 'translate';
        $this->_controller = 'adminhtml';
        $this->setTemplate('translate/container.phtml');
        
        $this->removebutton('edit');
        
//        TODO: buttons to truncate and remove duplicates
        $this->_addButton('truncate', array(
            'label'     => Mage::helper('adminhtml')->__('Truncate log table'),
            'onclick'   => 'window.location.href=\'' . $this->getUrl('*/*/truncate/') . '\'',
            'class'     => 'delete',
            'title'     => Mage::helper('adminhtml')->__('Remove all the records from the &quot;untransalated strings&quot; table'),
        ));

        $this->_addButton('remove_duplicate', array(
            'label'     => Mage::helper('adminhtml')->__('Remove duplicates in log table'),
            'class'     => 'go',
            'onclick'   => 'window.location.href=\'' . $this->getUrl('*/*/removeDuplicate/') . '\'',
            'title'     => Mage::helper('adminhtml')->__('Remove duplicate strings that are in the system and &quot;untranslated strings&quot; table'),
        ));
    }

    public function getHeaderText() {
        return Mage::helper('translate')->__("PHPro Translate module");
    }

}
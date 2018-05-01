<?php
class Zealousweb_WhoAlsoView_Block_Adminhtml_Whoalsoview extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'whoalsoview';
        $this->_controller = 'adminhtml_whoalsoview';
        $this->_headerText = $this->__('Also Bought');
 
        parent::__construct();
        $this->_removeButton('add');
    }
}
<?php

class MDN_AdvancedStock_Block_Inventory_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    private $_product = null;
    private $_inventory = null;

    public function __construct() {

        $this->_objectId = 'id';
        $this->_controller = 'inventory';
        $this->_blockGroup = 'AdvancedStock';

        $this->_inventory = Mage::registry('current_inventory');

        parent::__construct();

        if ($this->_inventory->getId() && $this->_inventory->getei_status() == MDN_AdvancedStock_Model_Inventory::kStatusOpened) {

            $msg = $this->__('Are you sure ? This will erase previous stock picture if exists');
            $this->_addButton(
                    'stock_picture', array(
                'label' => Mage::helper('AdvancedStock')->__('Update stock picture'),
                'onclick' => "if (confirm('".addslashes($msg)."')) { window.location.href='" . $this->getUrl('AdvancedStock/Inventory/UpdateStockPicture', array('ei_id' => $this->_inventory->getId())) . "'};",
                'level' => -1
                    )
            );

            $this->_addButton(
                    'scan', array(
                'label' => Mage::helper('AdvancedStock')->__('Scan products'),
                'onclick' => "window.location.href='" . $this->getUrl('AdvancedStock/Inventory/Scan', array('ei_id' => $this->_inventory->getId())) . "'",
                'level' => -1
                    )
            );
            
        }
        
        
        if ($this->_inventory->getei_status() == MDN_AdvancedStock_Model_Inventory::kStatusClosed)
        {
            if (Mage::getStoreConfig('advancedstock/stock_take/prevent_to_edit_a_close_stocktake'))
            {
                $this->_removeButton('save');
            }
        }
    }

    public function getHeaderText() {
        if ($this->_inventory->getId())
            return $this->__('Edit stock take - %s (%s)', $this->_inventory->getei_name(), $this->_inventory->getWarehouse()->getstock_name());
        else
            return $this->__('New stock take');
    }

    public function getBackUrl() {
        return mage::helper('adminhtml')->getUrl('AdvancedStock/Inventory/Grid');
    }

    public function getSaveUrl() {
        return mage::helper('adminhtml')->getUrl('AdvancedStock/Inventory/Save');
    }

}

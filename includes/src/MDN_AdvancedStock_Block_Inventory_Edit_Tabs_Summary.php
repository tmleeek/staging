<?php

class MDN_AdvancedStock_Block_Inventory_Edit_Tabs_Summary extends Mage_Adminhtml_Block_Widget_Form
{
    
    public function initForm()
    {
        $form = new Varien_Data_Form();

        $inventory = Mage::registry('current_inventory');

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend'=>Mage::helper('AdvancedStock')->__('General'))
        );

        $fieldset->addField('ei_id', 'hidden', array(
            'name'=>'ei_id',
            'label' => Mage::helper('AdvancedStock')->__('Id')
        ));
        
        $fieldset->addField('ei_date', 'label', array(
            'name'=>'ei_date',
            'label' => Mage::helper('AdvancedStock')->__('Created at')
        ));

        $fieldset->addField('ei_stock_picture_date', 'label', array(
            'name'=>'ei_stock_picture_date',
            'label' => Mage::helper('AdvancedStock')->__('Stock picture date')
        ));
        
        $fieldset->addField('ei_name', 'text', array(
            'name'=>'ei_name',
            'label' => Mage::helper('AdvancedStock')->__('Name'),
            'required' => true
        ));

        $fieldset->addField('ei_warehouse_id', 'select', array(
            'name'=>'ei_warehouse_id',
            'label' => Mage::helper('AdvancedStock')->__('Warehouse'),
            'required' => true,
            'values'=> $this->getWarehouses()
        ));

        $fieldset->addField('ei_status', 'select', array(
            'name'=>'ei_status',
            'label' => Mage::helper('AdvancedStock')->__('Status'),
            'required' => true,
            'values'=> Mage::getModel('AdvancedStock/Inventory')->getStatuses()
        ));

        $fieldset->addField('ei_comments', 'textarea', array(
            'name'=>'ei_comments',
            'label' => Mage::helper('AdvancedStock')->__('Comments')
        ));

        $form->setValues($inventory->getData());
        $this->setForm($form);
        
        return $this;
    }

    /**
     * 
     */
    protected function getWarehouses()
    {
       $localWarehouses = array();
       
       $collection = Mage::getModel('AdvancedStock/Warehouse')->getCollection();
       foreach($collection as $item)
       {
           $localWarehouses[] = array('value' => $item->getId() ,'label' => $item->getstock_name());
       }
       
       return $localWarehouses;
    }
    
}

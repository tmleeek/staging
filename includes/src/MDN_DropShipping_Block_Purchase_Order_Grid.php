<?php

class MDN_DropShipping_Block_Purchase_Order_Grid extends MDN_Purchase_Block_Order_Grid
{

   /**
     * 
     *
     * @return unknown
     */
    protected function _prepareColumns()
    {
        
        parent::_prepareColumns();
                                                                                       
        $this->addColumn('is_drop_ship', array(
            'header'=> Mage::helper('purchase')->__('Dropship ?'),
            'index' => 'is_drop_ship',
            'type' => 'options',
            'options' => array(
                '1' => Mage::helper('catalog')->__('Yes'),
                '0' => Mage::helper('catalog')->__('No'),
            ),
            'align' => 'center'
        ));
                                                                                                      
        return $this;
    }

    
}

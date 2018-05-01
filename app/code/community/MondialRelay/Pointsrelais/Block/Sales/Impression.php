<?php

class MondialRelay_Pointsrelais_Block_Sales_Impression extends Mage_Adminhtml_Block_Widget_Grid_Container
{


    public function __construct()
    {
        $this->_blockGroup = 'pointsrelais';
        $this->_controller = 'sales_shipment';
        $this->_headerText = Mage::helper('pointsrelais')->__('Impressions des étiquettes Mondial Relay');
        parent::__construct();
        $this->_removeButton('add');
    }

}
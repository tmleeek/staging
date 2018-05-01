<?php

class TBT_Milestone_Model_System_Config_Source_Orders_Status
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'create',
                'label' => Mage::helper('tbtmilestone')->__("Order Is Created")
            ),
            array(
                'value' => 'payment',
                'label' => Mage::helper('tbtmilestone')->__("Order Is Paid For")
            ),
            array(
                'value' => 'shipment',
                'label' => Mage::helper('tbtmilestone')->__("Order Is Shipped")
            )
        );
    }
}

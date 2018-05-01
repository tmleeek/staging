<?php
class MondialRelay_Pointsrelais_Model_System_Config_Backend_Shipping_Pointsrelaislds extends Mage_Core_Model_Config_Data
{
    public function _afterSave()
    {
		Mage::getResourceModel('pointsrelais/carrier_pointsrelaislds')->uploadAndImport($this);
    }
}

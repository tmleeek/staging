<?php
class MondialRelay_Pointsrelais_Model_System_Config_Backend_Shipping_Pointsrelais extends Mage_Core_Model_Config_Data
{
    public function _afterSave()
    {
		Mage::getResourceModel('pointsrelais/carrier_pointsrelais')->uploadAndImport($this);
    }
}

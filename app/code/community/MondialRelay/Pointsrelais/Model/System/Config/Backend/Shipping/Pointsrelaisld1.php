<?php
class MondialRelay_Pointsrelais_Model_System_Config_Backend_Shipping_Pointsrelaisld1 extends Mage_Core_Model_Config_Data
{
    public function _afterSave()
    {
		Mage::getResourceModel('pointsrelais/carrier_pointsrelaisld1')->uploadAndImport($this);
    }
}

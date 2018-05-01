<?php

class Infortis_Brands_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getCfg($optionString)
    {
        return Mage::getStoreConfig('brands/' . $optionString);
    }
}

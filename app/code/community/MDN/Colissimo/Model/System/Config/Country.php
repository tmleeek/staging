<?php

class MDN_Colissimo_Model_System_Config_Country extends Mage_Core_Model_Abstract
{
    /**
     * Returns array of countries and codes for configuration
     * @author Arnaud P <arnaud@boostmyshop.com>
     * @version 1.0.0
     * @param void
     * @return array Array of countries for configuration
     */
    public function toOptionArray()
    {
        return Mage::getModel('directory/country')->getCollection()->toOptionArray();
    }
}
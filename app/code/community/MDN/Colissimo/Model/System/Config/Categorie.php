<?php

class MDN_Colissimo_Model_System_Config_Categorie extends Mage_Core_Model_Abstract
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
        return array(
            1 => 'Gift',
            2 => 'Sample',
            3 => 'Commercial shipment',
            4 => 'Document',
            5 => 'Other',
            6 => 'Return of goods'
        );
    }
}
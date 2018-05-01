<?php

class MDN_Colissimo_Model_System_Config_DeliveryMode extends Mage_Core_Model_Abstract
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
            'DOM' => 'DOM',
            'RDV' => 'RDV',
            'BPR' => 'BPR',
            'ACP' => 'ACP',
            'CDI' => 'CDI',
            'A2P' => 'A2P',
            'MRL' => 'MRL',
            'CIT' => 'CIT',
            'DOS' => 'DOS',
            'CMT' => 'CMT',
            'BDP' => 'BDP'
        );
    }
}
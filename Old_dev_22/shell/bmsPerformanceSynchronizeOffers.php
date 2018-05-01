<?php

require_once 'abstract.php';

class MDN_Shell_bmsPerformanceSynchronizeOffers extends Mage_Shell_Abstract
{

    /**
     * Run script
     *
     */
    public function run()
    {
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

        Mage::helper('Mpm')->log(date('H:i:s')." : Start bms performance sync");
        Mage::helper('Mpm/Product')->synchronizeAllOffers();
        Mage::helper('Mpm')->log(date('H:i:s')." : End of bms performance sync");

        return true;
    }
}

$shell = new MDN_Shell_bmsPerformanceSynchronizeOffers();
$shell->run();

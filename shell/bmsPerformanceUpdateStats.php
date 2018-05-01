<?php

require_once 'abstract.php';

class MDN_Shell_UpdateStats extends Mage_Shell_Abstract
{

    /**
     * Run script
     *
     */
    public function run()
    {
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

        ini_set('display_errors', 1);

        Mage::helper('Mpm')->log(date('H:i:s')." : Start mpm update stats");
        Mage::getSingleton('Mpm/Stat')->run();
        Mage::helper('Mpm')->log(date('H:i:s')." : End of mpm update stats");

        return true;
    }
}


$shell = new MDN_Shell_UpdateStats();
$shell->run();

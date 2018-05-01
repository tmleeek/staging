<?php

require_once 'abstract.php';

class MDN_Shell_RepriceAll extends Mage_Shell_Abstract
{

    /**
     * Run script
     *
     */
    public function run()
    {
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

        Mage::helper('Mpm')->log(date('H:i:s')." : Start repriceAll");
        Mage::helper('Mpm/Product')->repriceAll();
        Mage::helper('Mpm')->log(date('H:i:s')." : End of repriceAll");

        return true;
    }
}

$shell = new MDN_Shell_RepriceAll();
$shell->run();

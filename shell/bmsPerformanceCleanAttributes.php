<?php

require_once 'abstract.php';

class MDN_Shell_CleanAttributes extends Mage_Shell_Abstract
{

    /**
     * Run script
     *
     */
    public function run()
    {
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

        Mage::helper('Mpm')->log(date('H:i:s')." : Start cleaning attributes");
        Mage::getModel('Mpm/Observer')->cleanAttributes();
        Mage::helper('Mpm')->log(date('H:i:s')." : End of cleaning attributes");

        return true;
    }
}

$shell = new MDN_Shell_CleanAttributes();
$shell->run();

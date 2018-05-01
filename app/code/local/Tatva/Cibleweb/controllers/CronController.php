<?php
class Tatva_Cibleweb_CronController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        //echo "test";die();
        //Mage::getModel('cibleweb/Beezupxml')->runSaveCatalog();
        Mage::getModel('cibleweb/Exportxml')->runSaveCatalog();
    }
}
?>
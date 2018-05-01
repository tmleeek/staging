<?php
class Tatva_Checkcron_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	Mage::getModel("checkcron/testmail")->checkmail();
    }
}
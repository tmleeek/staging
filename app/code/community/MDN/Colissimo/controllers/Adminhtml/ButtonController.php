<?php
class MDN_Colissimo_Adminhtml_ButtonController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Return some checking result
     *
     * @return void
     */

    public function checkAction()
    {

        $result = Mage::helper('colissimo')->supervision();
        Mage::app()->getResponse()->setBody($result);
    }
}
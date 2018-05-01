<?php

require_once(Mage::getModuleDir('controllers', 'TBT_Common') . DS . 'Front' . DS . 'AbstractController.php');
abstract class TBT_Rewards_Front_AbstractController extends TBT_Common_Front_AbstractController
{
    public function getModuleKey()
    {
        return 'rewards';
    }
}

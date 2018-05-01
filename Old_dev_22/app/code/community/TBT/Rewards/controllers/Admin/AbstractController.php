<?php

require_once(Mage::getModuleDir('controllers', 'TBT_Common') . DS . 'Admin' . DS . 'AbstractController.php');
abstract class TBT_Rewards_Admin_AbstractController extends TBT_Common_Admin_AbstractController
{
    public function getModuleKey()
    {
        return 'rewards';
    }
}

<?php

include_once(Mage::getModuleDir('controllers', 'TBT_Rewards') . DS . 'Admin' . DS . 'AbstractController.php');
class TBT_Rewards_Manage_PlatformController extends TBT_Rewards_Admin_AbstractController 
{
    public function billingAction()
    {
        $client = Mage::getSingleton('rewards/platform_instance');
        $account = $client->account()->get();
        
        $login_url = $account['login_url'];
        $this->getResponse()->setRedirect($login_url);
    }
}

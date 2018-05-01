<?php

class TBT_Rewards_Manage_Dashboard_UsageController extends Mage_Adminhtml_Controller_Action
{
    public function checkUsageAction()
    {
        $result = $this->getLayout()->createBlock('rewards/manage_dashboard_usage')->toHtml();

        $this->getResponse()->setHeader('Content-Type', 'application/json', true);
        $this->getResponse()->setBody(Zend_Json::encode($result));

        return $this;
    }
}
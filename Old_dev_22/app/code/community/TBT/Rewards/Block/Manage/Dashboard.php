<?php

class TBT_Rewards_Block_Manage_Dashboard extends Mage_Adminhtml_Block_Template
{
    const CACHE_TAG = 'rewards_dashboard';

    protected $_account = null;

    protected function _construct()
    {
        parent::_construct();

        $this->setTemplate('rewards/dashboard/dashboard.phtml');
        $this->addData(array(
            'cache_lifetime' => false,
            'cache_tags'     => array(self::CACHE_TAG)
        ));

        return $this;
    }

    /**
     * Checks if Sweet Tooth panel from Dashboard is enabled/disabled
     *
     * @return boolean
     */
    public function displayRewardsDashboard()
    {
        return Mage::helper('rewards/config')->displayRewardsDashboard();
    }

}

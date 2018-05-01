<?php
/**
 * Customer Reward Notification Preferences
 *
 * @category   TBT
 * @package    TBT_Rewards
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */
class TBT_Rewards_Block_Customer_Preferences extends TBT_Rewards_Block_Customer_Abstract 
{
	
	protected function _prepareLayout() 
	{
		parent::_prepareLayout ();
	}
    
    /*
     * Check if Customer Points Summary email is allowed
     */
    public function showPreferences()
    {
        return Mage::getStoreConfigFlag('rewards/pointSummaryEmails/allow_points_summary_email');
    }
           
}
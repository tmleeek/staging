<?php

/**
 * Manage Customer Edit Tab Preferences
 *
 */
class TBT_Rewards_Block_Manage_Customer_Edit_Tab_Preferences extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface 
{
	protected function _construct() 
	{
		parent::_construct ();
	}
	
	protected function _prepareLayout() 
	{
		parent::_prepareLayout ();
	}
	
	/**
	 * Fetches the rewards customer for this session
	 *
	 * @return TBT_Rewards_Model_Customer
	 */
	
	/**
	 * Retrieve available customer
	 *
	 * @return Mage_Model_Customer
	 */
	public function getCustomer() 
	{
	    if ($this->hasCustomer ()) {
			return Mage::getModel('rewards/customer')->getRewardsCustomer($this->getData ( 'customer' ));
		}
		if (Mage::registry ( 'current_customer' )) {
			return Mage::getModel('rewards/customer')->getRewardsCustomer(Mage::registry ( 'current_customer' ));
		}
		if (Mage::registry ( 'customer' )) {
			return Mage::getModel('rewards/customer')->getRewardsCustomer(Mage::registry ( 'customer' ));
		}
		Mage::throwException ( Mage::helper ( 'customer' )->__ ( 'Can\'t get customer instance' ) );
	}
	
	/*
     * Check if Customer Points Summary email is allowed
     */
    public function showPreferences()
    {
        return Mage::getStoreConfigFlag('rewards/pointSummaryEmails/allow_points_summary_email');
    }
	
	/**
	 * ######################## TAB settings #################################
	 */
	public function getTabLabel() 
	{
		return $this->__ ( "Points & Rewards" );
	}
	
	public function getTabTitle() 
	{
		return $this->__ ( "Points & Rewards" );
	}
	
	public function canShowTab() 
	{
		return true;
	}
	
	public function isHidden() 
	{
		return false;
	}

}

?>
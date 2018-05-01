<?php

class TBT_Rewards_Model_Poll_Poll extends TBT_Rewards_Model_Special_Configabstract
{
    const ACTION_CODE = 'customer_poll';

    public function _construct()
    {
        $this->setCaption( "Vote Poll" );
        $this->setDescription( "Customer will get points when they voting poll." );
        $this->setCode( "customer_poll" );
        return parent::_construct();
    }

    public function getNewCustomerConditions()
    {
        return array(self::ACTION_CODE => Mage::helper( 'rewards' )->__( 'Voting Poll' ) );
    }

    public function getNewActions()
    {
        return array();
    }
}
<?php

class TBT_Rewards_Model_Poll_Decorators_Poll extends Varien_Object
{
    /**
     * @var Mage_Poll_Model_Poll
     */
    protected $_poll = null;

    /**
     * @var TBT_Rewards_Model_Customer
     */
    protected $_customer = null;

    /**
     * @param  Mage_Poll_Model_Poll $poll
     * @return TBT_Rewards_Model_Poll_Decorators_Poll
     */
    public function decorate(Mage_Poll_Model_Poll $poll)
    {
        $this->_poll = $poll;
        return $this;
    }

    /**
     * @return Mage_Poll_Model_Poll
     */
    public function getOriginalModel()
    {
        return $this->_poll;
    }

    public function getCustomerSession()
    {
        return Mage::getSingleton( 'customer/session', array('name' => 'frontend') );
    }

    public function isCustomerLoggedIn()
    {
        return $this->getCustomerSession()->isLoggedIn();
    }

    /**
     * Fetches the rewards customer details
     *
     * @return TBT_Rewards_Model_Customer
     */
    public function getRewardsCustomer()
    {
        if ($this->isCustomerLoggedIn()) {
            $this->_customer = Mage::getModel( 'rewards/customer' )->load( $this->getCustomerSession()->getCustomerId() );
        }
        return $this->_customer;
    }

    public function getCustomerId()
    {
        if($this->isCustomerLoggedIn()) {
            return $this->getCustomerSession()->getCustomerId();
        }

        return false;
    }

    /**
     * True if the customer has received points for the voting poll
     * @return boolean
     */
    public function isCustomerHasPointsForPoll()
    {
        $hasReceivedPoints = ! $this->getCustomerPollTransfers()->sumPoints()->isNoPoints();
        return $hasReceivedPoints;
    }

    public function getCustomerPollTransfers()
    {
        $customer = $this->getRewardsCustomer();
        $transfers = $customer->getTransfers()
                              ->addAllReferences()
                              ->addFilter( 'reference_type', TBT_Rewards_Model_Transfer_Reference::REFERENCE_POLL )
                              ->addFilter( 'reference_id', $this->_poll->getId() );
        return $transfers;
    }

    public function getPollId()
    {
        if($this->_poll) {
            return $this->_poll->getId();
        }

        return false;
    }
}
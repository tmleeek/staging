<?php

class TBT_Rewards_Model_Poll_Transfer extends TBT_Rewards_Model_Transfer
{
    public function _construct()
    {
        parent::_construct();
    }

    /**
     * Creates a customer point-transfer of any amount or currency.
     *
     * @param  TBT_Rewards_Model_Poll_Decorators_Poll $rpoll
     * @param  $rule Special Rule
     * @return boolean  : whether or not the point-transfer succeeded
     */
    public function createPollPoints(TBT_Rewards_Model_Poll_Decorators_Poll $rpoll, $rule)
    {

        $num_points = $rule->getPointsAmount();
        $currency_id = $rule->getPointsCurrencyId();
        $rule_id = $rule->getId();
        $transfer = $this->initTransfer( $num_points, $currency_id, $rule_id );
        $customer = $rpoll->getRewardsCustomer();
        $store_id = $customer->getStore()->getId();

        if (! $transfer) {
            return false;
        }

        $initial_status = Mage::helper( 'rewards/poll_config' )->getInitialTransferStatusAfterPoll( $store_id );

        if (! $transfer->setStatus( null, $initial_status )) {
            return false;
        }

        $initial_transfer_msg = Mage::getStoreConfig( 'rewards/transferComments/pollEarned', $store_id );
        $comments = Mage::helper( 'rewards' )->__( $initial_transfer_msg );

        $customer_id = $rpoll->getCustomerId();

        $this->setPollId( $rpoll->getPollId() )
             ->setComments( $comments )
             ->setCustomerId( $customer_id )
             ->save();

        return true;
    }
}
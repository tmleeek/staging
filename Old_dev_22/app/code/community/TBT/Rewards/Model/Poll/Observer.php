<?php

class TBT_Rewards_Model_Poll_Observer extends Varien_Object
{

    protected $_points_transferred  = false;

    /**
      * Event: poll_vote_add
      *
      * @param Varien_Event_Observer $o
      */
     public function afterPollVoteAdd16($o)
     {
        if($this->_points_transferred) {
            return $this;
        }

        $poll = $o->getPoll();
        $this->afterPollVoteAdd($poll);
        return $this;
     }

     /**
      * Event: controller_action_postdispatch_poll_vote_add
      * Prior version of magento < 16
      *
      * @param Varien_Event_Observer $o
      */
     public function afterPollVoteAddPrior16($o)
     {
         if (!Mage::helper ( 'rewards/version' )->isMageVersionBetween ( '1.4.0.0', '1.6.0.0' )) {
             return $this;
         }

         $pollId = Mage::getSingleton('core/session')->getJustVotedPoll();

         if($pollId) {
             $poll = Mage::getModel("poll/poll")->load($pollId);
             $this->afterPollVoteAdd($poll);
         }

         return $this;
     }

    /**
     * Give rewards point to customer
     *
     * @param Mage_Poll_Model_Poll $poll
     */
    public function afterPollVoteAdd(Mage_Poll_Model_Poll $poll)
    {
        try{
            $rPoll = Mage::getModel("rewards/poll_decorators_poll")->decorate($poll); // using decorator design pattern

            if($rPoll->isCustomerLoggedIn() && !$rPoll->isCustomerHasPointsForPoll()) {
                Mage::dispatchEvent( 'rewards_poll_new_vote', array ('poll' => $poll ) );
                $this->initReward($rPoll);
            }
        } catch(Exception $e) {
            Mage::helper('rewards')->log($e->getMessage());
        }

        return $this;
    }

    /**
     * Do points transfer
     *
     * @param TBT_Rewards_Model_Poll_Decorators_Poll $rPoll
     */
    public function initReward(TBT_Rewards_Model_Poll_Decorators_Poll $rPoll)
    {
        $ruleCollection = Mage::getSingleton( 'rewards/poll_validator' )->getApplicableRulesOnPoll();

        foreach ( $ruleCollection as $rule ) {
            if (! $rule->getId()) {
                continue;
            }

            try {
                //Create the transfer
                $is_transfer_successful = Mage::getModel( 'rewards/poll_transfer' )->createPollPoints($rPoll, $rule);
            } catch ( Exception $ex ) {
                Mage::getSingleton( 'core/session' )->addError( $ex->getMessage() );
            }

            if ($is_transfer_successful) {
                $this->_points_transferred = true;
                //Alert the customer on the distributed points
                Mage::getSingleton( 'core/session' )
                ->addSuccess( Mage::helper ( 'rewards' )
                ->__( 'You received %s for voting', (string)Mage::getModel( 'rewards/points' )->set( $rule ) ) );
            }
        }

        return $this;
    }
}
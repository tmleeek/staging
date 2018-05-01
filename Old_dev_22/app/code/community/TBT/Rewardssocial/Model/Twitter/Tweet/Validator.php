<?php

class TBT_Rewardssocial_Model_Twitter_Tweet_Validator extends TBT_Rewards_Model_Special_Validator
{
    /**
     * Loops through each Special rule. If the rule applies and the customer didn't
     * already earn points for this tweet, then create (a) new points transfer(s) for the tweet.
     */
    public function initReward($customerId, $url, $tweetId = null, $twitterUserId = null)
    {
        try {
            $tweetModel = $this->fetchTweet($customerId, $url);
            if (!$tweetModel->getId()) {
                throw new Exception(
                        "TWEET model was not saved for some reason. Customer ID {$customerId}, TWEET url: {$url}.");
            }

            $ruleCollection = $this->getApplicableRules();
            $count = count($ruleCollection);

            $this->_transferTweetPoints($ruleCollection, $customerId, $tweetModel);

        } catch (Exception $e) {
            Mage::helper('rewards')->logException($e);
            throw new Exception(
                Mage::helper('rewardssocial')->__("Could not reward you for your Twitter mention."),
                null, $e
            );
        }

        return $this;
    }


    /**
     * @param TBT_Rewards_Model_Customer
     * @return boolean
     */
    public function hasTweeted($customerId, $url)
    {
        $hasTweeted = Mage::getModel('rewardssocial/twitter_tweet')
            ->getCollection()->containsEntry($customerId, $url);
        return $hasTweeted;
    }

    public function fetchTweet($customerId, $url)
    {
        return Mage::getModel('rewardssocial/twitter_tweet')->loadByCustomerAndUrl($customerId, $url);
    }


    /**
     * Goes through an already validated rule collection and transfers rule points to the customer specified
     * with the tweet model as the reference.
     * @param array(TBT_Rewards_Model_Special) $ruleCollection
     * @param TBT_Rewards_Model_Customer $customer
     * @param TBT_Rewardssocial_Model_Twitter_Tweet $tweetModel
     */
    protected function _transferTweetPoints($ruleCollection, $customerId, $tweetModel)
    {
        foreach ($ruleCollection as $rule) {
            if (!$rule->getId()) {
                continue;
            }

            $transfer = Mage::getModel('rewardssocial/twitter_tweet_transfer');
            $is_transfer_successful = $transfer->createTwitterTweetPoints(
                    $customerId,
                    $tweetModel->getId(),
                    $rule
            );

            if (!$is_transfer_successful) {
                throw new Exception("Failed to reward for tweeting.");
            }
        }

        return $this;
    }

    /**
     * Returns all rules that apply when a customer tweets something.
     * @return array(TBT_Rewards_Model_Special)
     */
    public function getApplicableRules($action = null, $orAction = null)
    {
        if ($action === null) {
            $action = TBT_Rewardssocial_Model_Twitter_Tweet_Special_Config::ACTION_CODE;
        }

        return parent::getApplicableRules($action, $orAction);
    }

    /**
     * Returns all rules that apply wehn a customer tweets something on twitter
     * @deprecated  see getApplicableRules()
     * @return array(TBT_Rewards_Model_Special)
     */
    public function getApplicableRulesOnTwitterTweet()
    {
        return $this->getApplicableRules(
                TBT_Rewardssocial_Model_Twitter_Tweet_Special_Config::ACTION_CODE
        );
    }


    /**
     * Returns an array outlining the number of points they will receive for liking the item
     *
     * @return array
     */
    public function getPredictedTwitterTweetPoints($page=null)
    {

        Varien_Profiler::start("TBT_Rewardssocial:: Predict Twitter Tweet Points");
        $ruleCollection = $this->getApplicableRules();

        $predictArray = array();
        foreach ($ruleCollection as $rule) {
            // TODO: shoud this be += ? I think so.
            // ksteffen: I think so, too. Changed code to do so.
            $currencyId = $rule->getPointsCurrencyId();
            if (!isset($predictArray[$currencyId])) {
                $predictArray[$currencyId] = 0;
            }
            $predictArray[$currencyId] += $rule->getPointsAmount();
        }

        Varien_Profiler::stop("TBT_Rewardssocial:: Predict Twitter Tweet Points");
        return $predictArray;
    }

}
?>

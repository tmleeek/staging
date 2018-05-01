<?php

class TBT_Rewardssocial_Model_Facebook_Share_Transfer extends TBT_Rewards_Model_Transfer
{

    public function _construct()
    {
        parent::_construct();
    }

    /**
     * Facebook Share in this context refers to a tuple in the rewardssocial_facebook_share table which contains
     * information about the product share action and product
     *
     * @param int $id
     * @return $this
     */
    public function setFacebookShareId($id)
    {
        $this->clearReferences();
        $this->setReferenceType(TBT_Rewardssocial_Model_Facebook_Share_Reference::REFERENCE_TYPE_ID);
        $this->setReferenceId($id);
        $this->setReasonId(TBT_Rewardssocial_Model_Facebook_Share_Reason::REASON_TYPE_ID);
        $this->setData(TBT_Rewardssocial_Model_Facebook_Share_Reference::REFERENCE_KEY, $id);

        return $this;
    }

    /**
     * Creates customer points transfer for a Facebook product share action.
     * @param  Mage_Customer_Model_Customer $customer The customer that shared the product.
     * @param  int $productShareId                    The ID of the product that was shared on Facebook.
     * @param  TBT_Rewards_Model_Special $rule        The rule that awards points to the customer for this action.
     * @return boolean True, if transfer succeded or false otherwise.
     */
    public function createFacebookProductSharePoints($customer, $productShareId, $rule)
    {

        $pointsAmount = $rule->getPointsAmount();
        $currencyId = $rule->getPointsCurrencyId();
        $ruleId = $rule->getId();
        $transfer = $this->initTransfer($pointsAmount, $currencyId, $ruleId);
        $store = $customer->getStore();

        if (!$transfer) {
            return false;
        }

        //get On-Hold initial status override
        if ($rule->getOnholdDuration() > 0) {
            $transfer->setEffectiveStart(date('Y-m-d H:i:s', strtotime("+{$rule->getOnholdDuration()} days")))
                ->setStatus(null, TBT_Rewards_Model_Transfer_Status::STATUS_PENDING_TIME);
        } else {
            //get the default starting status
            $initial_status = Mage::getStoreConfig('rewards/InitialTransferStatus/AfterFacebookProductShare', $store);
            if (!$transfer->setStatus(null, $initial_status)) {
                return false;
            }
        }

        // Translate the message through the core translation engine (not the store view system) in case people want to use that instead
        // This is not normal, but we found that a lot of people preferred to use the standard translation system insteaed of the
        // store view system so this lets them use both.
        $initial_transfer_msg = Mage::getStoreConfig('rewards/transferComments/facebookProductShare', $store);
        $comments = Mage::helper('rewardssocial')->__($initial_transfer_msg);

        $this->setFacebookShareId($productShareId)
            ->setComments($comments)
            ->setCustomerId($customer->getId())
            ->save();

        return true;

    }

}

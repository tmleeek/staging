<?php

class TBT_Rewardssocial_Model_Purchase_Share_Transfer extends TBT_Rewards_Model_Transfer
{

    public function trigger($customer, $purchaseId, $rule, $type)
    {
        $pointsAmount = $rule->getPointsAmount();
        $currencyId   = $rule->getPointsCurrencyId();
        $ruleId       = $rule->getId();
        $store        = Mage::app()->getStore();

        $transfer = $this->initTransfer($pointsAmount, $currencyId, $ruleId);

        if (!$transfer) {
            return false;
        }

        //get On-Hold initial status override
        if ($rule->getOnholdDuration() > 0) {
            $transfer->setEffectiveStart(date('Y-m-d H:i:s', strtotime("+{$rule->getOnholdDuration()} days")))
                ->setStatus(null, TBT_Rewards_Model_Transfer_Status::STATUS_PENDING_TIME);
        } else {
            //get the default starting status
            $initialStatus = Mage::helper('rewardssocial/purchase_share')->getInitialStatusByType($type, $store);
            if (!$transfer->setStatus(null, $initialStatus)) {
                return false;
            }
        }

        $initialTransferMsg    = Mage::helper('rewardssocial/purchase_share')->getTransferCommentsByType($type, $store);
        $comments = Mage::helper('rewardssocial')->__($initialTransferMsg);

        $this->setPurchaseShareId($purchaseId, $type)
            ->setComments($comments)
            ->setCustomerId($customer->getId())
            ->save();

        return true;
    }

    /**
     * Twitter Tweet in this context refers to a tuple
     * in the rewardssocial_twitter_tweet table which
     * contains information about the tweet action and
     * article (product, category, cms page, etc).
     *
     * @param unknown_type $id
     * @return unknown
     */
    public function setPurchaseShareId($purchaseId, $type)
    {
        $referenceClass = Mage::helper('rewardssocial/purchase_share')->getReferenceClassByType($type);

        $this->clearReferences();
        $this->setReferenceType(constant($referenceClass.'::REFERENCE_TYPE_ID'));
        $this->setReferenceId($purchaseId);
        $this->setReasonId(TBT_Rewards_Model_Transfer_Reason::REASON_CUSTOMER_DISTRIBUTION);
        $this->setData(constant($referenceClass.'::REFERENCE_KEY'), $purchaseId);

        return $this;
    }
}

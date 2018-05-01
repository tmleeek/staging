<?php

class TBT_Milestone_Model_Rule_Action_Grantpoints extends TBT_Milestone_Model_Rule_Action
{
    public function execute($customerId)
    {
        try {
            // save this data for the rule log
            $milestone['condition']['message']           = "Reached a " . $this->getRuleCondition()->getMilestoneDescription();
            $milestone['condition']['reference_type_id'] = $this->getRuleCondition()->getPointsReferenceTypeId();
            $milestone['action']['points']               = $this->getPointsAmount();
            $this->getRule()->setMilestoneDetails($milestone);

            $transfer = Mage::getModel('rewards/transfer');

            $transfer->setCurrencyId(1)                     // Sweet Tooth only supports 1 currency Id as of this writing
                     ->setQuantity($this->getPointsAmount())
                     ->setCustomerId($customerId)
                     ->setComments($this->getTransferComment())
                     ->setReasonId(TBT_Rewards_Model_Transfer_Reason_Distribution::REASON_TYPE_ID)
                     ->setReferenceType($this->getRuleCondition()->getPointsReferenceTypeId())
                     ->setReferenceId($this->getRule()->getId());

            $statusChanged = $transfer->setStatus(null, $this->getTransferStatus());
            if (!$statusChanged){
                throw new Exception ("Unable to change the transfer status.");
            }

            $transfer->save();
            $this->notifySuccess();



        } catch (Exception $e){
            Mage::helper('rewards')->logException($e);
        }

        return $this;
    }

    protected function _getFrontendSuccessMessage()
    {
        return Mage::helper ( 'rewards' )->__("You have earned %s by reaching a %s.", $this->getPointsObject(), $this->getRuleCondition()->getMilestoneDescription());
    }

    protected function _getBackendSuccessMessage()
    {
        return Mage::helper ( 'rewards' )->__("Customer #%s was rewarded %s by reaching a %s.", $this->getCustomerId(), $this->getPointsObject(), $this->getRuleCondition()->getMilestoneDescription());
    }

    protected function _getEmailSuccessMessage()
    {
        return Mage::helper ( 'rewards' )->__("You have been rewarded with %s by reaching a %s.", $this->getPointsObject(), $this->getRuleCondition()->getMilestoneDescription());
    }

    /**
     * @return string. Transfer comment loaded from system config
     */
    public function getTransferComment()
    {
        $storeComment = Mage::getStoreConfig("rewards/transferComments/milestone");
        return Mage::helper('tbtmilestone')->__($storeComment, $this->getRuleCondition()->getMilestoneDescription());
    }

    /**
     * @return int. Transfer status loaded from system config
     */
    public function getTransferStatus()
    {
        return Mage::getStoreConfig("rewards/InitialTransferStatus/milestone");
    }

    /**
     * Given a points amount, will return a points model object
     *
     * @param int|null $pointsAmount. Optional. If not supplied, will call $this->getPointsAmount()
     * @return TBT_Rewards_Model_Points
     */
    public function getPointsObject($pointsAmount = null)
    {
        $pointsAmount = !is_null($pointsAmount) ? $pointsAmount : $this->getPointsAmount();
        return Mage::getModel('rewards/points')->setPoints(1, $pointsAmount);
    }

    public function validateSave()
    {
        if (!$this->getPointsAmount()) {
            throw new Exception("Please specify a points amount in the Actions tab.");
        }

        return $this;
    }
}

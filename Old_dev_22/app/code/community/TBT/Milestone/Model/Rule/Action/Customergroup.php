<?php

class TBT_Milestone_Model_Rule_Action_Customergroup extends TBT_Milestone_Model_Rule_Action
{
    public function execute($customerId)
    {
        try {
            // save this data for the rule log
            $customer                                    = Mage::getModel('customer/customer')->load($customerId);
            $milestone['condition']['message']           = "Reached a " . $this->getRuleCondition()->getMilestoneDescription();
            $milestone['condition']['reference_type_id'] = $this->getRuleCondition()->getPointsReferenceTypeId();
            $milestone['action']['from']                 = $customer->getGroupId();
            $milestone['action']['to']                   = $this->getCustomerGroupId();
            $this->getRule()->setMilestoneDetails($milestone);

            $customer->setGroupId($this->getCustomerGroupId())
                     ->save();

            $this->notifySuccess();

        } catch (Exception $e){
            Mage::helper('rewards')->logException($e);
        }

        return $this;
    }

    protected function _getFrontendSuccessMessage()
    {
        return Mage::helper ( 'rewards' )->__("Congratulations! You've reached a new %s.", $this->getRuleCondition()->getMilestoneDescription());
    }

    protected function _getBackendSuccessMessage()
    {
        return Mage::helper ( 'rewards' )->__("Customer #%s has been moved to a new customer group by reaching a %s.", $this->getCustomerId(), $this->getRuleCondition()->getMilestoneDescription());
    }

    protected function _getEmailSuccessMessage()
    {
        return Mage::helper ( 'rewards' )->__("By reaching a %s, you are now eligible for special offers at our store.", $this->getRuleCondition()->getMilestoneDescription());
    }

    public function validateSave()
    {
        if (!$this->getCustomerGroupId()) {
            throw new Exception("Customer Group is a required field.");
        }

        return $this;
    }
}

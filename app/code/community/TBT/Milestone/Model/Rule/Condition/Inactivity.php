<?php

class TBT_Milestone_Model_Rule_Condition_Inactivity extends TBT_Milestone_Model_Rule_Condition
{
    // @deprecated use TBT_Milestone_Model_Rule_Condition_Inactivity_Reference::REFERENCE_TYPE_ID
    const POINTS_REFERENCE_TYPE_ID = 603;

    protected $_notification_email = true;
    protected $_notification_frontend = false;
    protected $_notification_backend = false;

    public function getMilestoneName()
    {
        return Mage::helper('tbtmilestone')->__("Period of Inactivity");
    }

    public function getMilestoneDescription()
    {
        return Mage::helper('tbtmilestone')->__("%s day period of inactivity", $this->getThreshold());
    }

    public function isSatisfied($customerId)
    {
        /**
         * This type of condition comes with a prequalifier
         * which would have already ensured that the rule
         * is satisfied for the customer.
         *
         * @see TBT_Milestone_Model_Rule_Condition_Inactivity_Prequalifier::getCollection()
         */

        return true;
    }

    public function validateSave()
    {
        if (!$this->getThreshold()) {
            throw new Exception("The milestone threshold is a required field.");
        }

        return $this;
    }

    /**
     * @return int. The Transfer Refrence Type ID used to identify this type of rule.
     * @see TBT_Milestone_Model_Rule_Condition::getPointsReferenceTypeId()
     */
    public function getPointsReferenceTypeId()
    {
        return TBT_Milestone_Model_Rule_Condition_Inactivity_Reference::REFERENCE_TYPE_ID;
    }
}

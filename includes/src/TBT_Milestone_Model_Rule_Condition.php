<?php


class TBT_Milestone_Model_Rule_Condition extends Varien_Object
{
    /**
     * @var int Generic ID for Milestone Rules. Subclasses should overwrite this with more specific Ids
     */
    const POINTS_GENERIC_REFERENCE_TYPE_ID = 600;

    /**
     * @var boolean. Should we email the customer if milestone is reached?
     */
    protected $_notification_email = false;

    /**
     * @var boolean. Should we email the customer if milestone is reached and customer is not logged in to get frontend
     * notification?
     */
    protected $_notification_email_not_logged_in = true;

    /**
     * @var boolean. Should we notify the cutomer on the front-end if milestone is reached?
     */
    protected $_notification_frontend = true;

    /**
     * @var boolean. Should we notify the admin in the admin-panel if milestone is reached?
     */
    protected $_notification_backend = true;

    /**
     *
     * @var null|TBT_Milestone_Model_Rule
     * @see TBT_Milestone_Model_Rule_Condition::setRule()
     */
    protected $_rule = null;

    /**
     * A name to identify this kind of rule by.
     * @return String.
     */
    public function getMilestoneName()
    {
        return "";    
    }
    
    /**
     * A description for this type of rule.
     * Sentence should be appropriate to comeplete the following sentence:
     * 'Points received by reaching a ...'; 
     * 
     * @example "milestone for placing 2 orders"
     * @return string
     */
    public function getMilestoneDescription()
    {
        return "";
    }
    
    
    
    /**
     * @return int. The Transfer Refrence Type ID used to identify this type of rule.
     */
    public function getPointsReferenceTypeId()
    {
        return self::POINTS_GENERIC_REFERENCE_TYPE_ID;
    }

    /**
     * @return TBT_Milestone_Helper_Data
     */
    protected function _getHelper($type = null)
    {
        $helper = is_null($type) ? "data" : $type;
        return Mage::helper("tbtmilestone/{$helper}");
    }

    /**
     * @return array. Notification settings for this class.
     */
    public function getNotificationSettings()
    {
        return array(
            'frontend'          => $this->_notification_frontend,
             'backend'          => $this->_notification_backend,
             'email'            => $this->_notification_email,
             'email_not_logged' => $this->_notification_email_not_logged_in
        );
    }

    /**
     * Will look for the from_date field in this object, if none found, will use the last update date
     * @return string from date in mysql format in UTC timezone
     */
    public function getFromDate()
    {
        $fromDate = $this->getData('from_date');
        if (empty($fromDate)) {
            $updatedAt = $this->_getHelper()->getLocalTimestamp($this->getRule()->getUpdatedAt()); // Local Timestamp
            $updatedAt = $this->_getHelper()->getNormalizedDateString($updatedAt);                       // Local Timestamp normalized
            $fromDate = $this->_getHelper()->getUtcTimestamp($updatedAt);                          // UTC Timestamp
        }

        return $this->_getHelper()->getMySqlDateString($fromDate);
    }

    /**
     * @return string from date in mysql format in UTC timezone
     */
    public function getToDate()
    {
        $toDate = $this->getData('to_date');
        if (!empty($toDate)){
            return $this->_getHelper()->getMySqlDateString($toDate);
        } else {
            return $toDate;
        }
    }
    /**
     * Pass in the rule object by refrence.
     * @param TBT_Milestone_Model_Rule $rule
     * @return self
     */
    public function setRule(&$rule)
    {
        $this->_rule = $rule;

        return $this;
    }

    /**
     * Once this class is insatiated by the Rule object, it's reference becomes available.
     * @return TBT_Milestone_Model_Rule
     */
    public function getRule()
    {
        return $this->_rule;
    }
}

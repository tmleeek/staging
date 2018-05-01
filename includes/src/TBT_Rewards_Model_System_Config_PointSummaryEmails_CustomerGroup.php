<?php

class TBT_Rewards_Model_System_Config_PointSummaryEmails_CustomerGroup extends Mage_Core_Model_Config_Data
{
    protected function _beforeSave()
    {
        $customerGroups = $this->getValue();

        if (!$customerGroups) {
            Mage::throwException(Mage::helper('rewards')->__('Please make sure you select at least one Customer Group for Points Summary Emails.'));
            return $this;
        }

        return parent::_beforeSave();
    }
}
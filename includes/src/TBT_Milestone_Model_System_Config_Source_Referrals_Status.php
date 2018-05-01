<?php

class TBT_Milestone_Model_System_Config_Source_Referrals_Status
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'signup',
                'label' => Mage::helper('tbtmilestone')->__("Referral Signs-up")
            ),
            array(
                'value' => 'order',
                'label' => Mage::helper('tbtmilestone')->__("Referral Places First Order")
            ),
        );
    }
}

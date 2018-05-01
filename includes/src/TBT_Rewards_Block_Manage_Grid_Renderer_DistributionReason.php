<?php

class TBT_Rewards_Block_Manage_Grid_Renderer_DistributionReason extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * This renders a column which holds a value that is a combination of 'reference_type' and 'reason_id' like this:
     *         'reference_type' . '_' . 'reason_id'
     * Based on this, it renders proper caption that identifies the reason for this points transfer.
     *
     * @param  Varien_Object $row [description]
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $referenceTypeReason = $this->_getValue($row);
        if (!$referenceTypeReason) {
            return $referenceTypeReason;
        }
        $parts = explode('_', $referenceTypeReason);
        if (isset($parts[1])) {
            $referenceTypeId = $parts[0];
            $reasonId        = $parts[1];
        } else {
            $reasonId = array_shift($parts);
        }

        // if we can identify caption by transfer's 'reference_type' use this, except if it's a referral transfer in
        // which case refine by it's reason
        if (isset($referenceTypeId) && ($captionByReference = Mage::getModel('rewards/transfer_reference')->getReferenceCaption($referenceTypeId))
            && $referenceTypeId != 20) {
            return $captionByReference;
        }

        if (isset($reasonId) && $captionByReason = Mage::getModel('rewards/transfer_reason')->getReasonCaption($reasonId)) {
            return $captionByReason;
        }

        return Mage::helper('rewards')->__('Other');
    }
}

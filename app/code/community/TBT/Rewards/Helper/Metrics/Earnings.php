<?php

class TBT_Rewards_Helper_Metrics_Earnings extends TBT_Rewards_Helper_Metrics_Chart_Abstract
{

    /**
     * Initializes series of data.
     *
     * @return TBT_Rewards_Helper_Metrics_Earnings
     */
    protected function _initSeries()
    {
        $this->_initCollection();

        $results = $this->_collection
            ->load()
            ->getData();

        $this->_prepareSeries($results);

        return $this;
    }

    protected function _initCollection()
    {
        if (!is_null($this->_collection)) {
            return $this;
        }

        $period         = $this->getParam('period_type');
        $from           = $this->getParam('from');
        $to             = $this->getParam('to');
        $transferStatus = $this->getParam('transfer_statuses');
        $storeIds       = $this->_getStoreIds();

        $this->_collection = Mage::getResourceSingleton('rewards/metrics_earnings_collection')
            ->prepareSummary($period, $storeIds, $from, $to, $transferStatus);

        return $this;
    }

    protected function _prepareSeries($series = array())
    {
        if (is_null($series)) {
            return $this;
        }

        foreach ($series as $key => &$value) {
            $value['distribution_reason'] = $this->getReasonCaption($value['distribution_reason']);
        }

        $this->setAllSeries($series);

        return $this;
    }

    /**
     * This accepts a parameter like 'reference_type' . '_' . 'reason_id' and based on this returns a caption that
     * clearly outlines the reason for this points transfer.
     *
     * @param  string $referenceTypeReasonId
     * @return string
     */
    public function getReasonCaption($referenceTypeReasonId)
    {
        if (!$referenceTypeReasonId) {
            return $referenceTypeReasonId;
        }
        $parts = explode('_', $referenceTypeReasonId);
        if (isset($parts[1])) {
            $referenceTypeId = $parts[0];
            $reasonId        = $parts[1];
        } else {
            $reasonId = array_shift($parts);
        }

        // if we can identify caption by transfer's 'reference_type' use this, except if it's a referral transfer in
        // which case refine by it's reason
        if (isset($referenceTypeId) && ($captionByReference = Mage::getModel('rewards/transfer_reference')->getReferenceCaption($referenceTypeId))
            && $referenceTypeId != TBT_RewardsReferral_Model_Transfer_Reference_Referral::REFERENCE_TYPE_ID) {
            return $captionByReference;
        }

        if (isset($reasonId) && $captionByReason = Mage::getModel('rewards/transfer_reason')->getReasonCaption($reasonId)) {
            return $captionByReason;
        }

        return Mage::helper('rewards')->__('Other');
    }
}

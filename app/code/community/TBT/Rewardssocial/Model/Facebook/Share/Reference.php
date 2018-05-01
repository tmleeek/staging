<?php

class TBT_Rewardssocial_Model_Facebook_Share_Reference extends TBT_Rewards_Model_Transfer_Reference_Abstract
{
    const REFERENCE_TYPE_ID = 74;
    const REFERENCE_KEY     = 'facebook_share_id';

    public function clearReferences(&$transfer)
    {
        if ($transfer->hasData(self::REFERENCE_KEY)) {
            $transfer->unsetData(self::REFERENCE_KEY);
        }

        return $this;
    }

    public function getReferenceOptions()
    {
        $referenceOptions = array(self::REFERENCE_TYPE_ID => Mage::helper('rewardssocial')->__('Facebook Product Share'));
        return $referenceOptions;
    }

    public function loadReferenceInformation(&$transfer)
    {
        $this->_loadTransferId($transfer);
        return $this;
    }

    protected function _loadTransferId($transfer)
    {
        $id = $transfer->getReferenceId();
        $transfer->setReferenceType(self::REFERENCE_TYPE_ID);
        $transfer->setReferenceId($id);
        $transfer->setData(self::REFERENCE_KEY, $id);

        return $this;
    }
}
<?php

class TBT_Rewardssocial_Helper_Purchase_Share extends Mage_Core_Helper_Abstract
{
    /**
     * This will load JS for purchase share rewarding only if needed, currently
     * if option is enabled in admin.
     *
     * @return file JS file to load in layout, or null
     */
    public function getJs()
    {
        // DO:  we should check for rules instead of this
        if (Mage::helper('rewardssocial/purchase_config')->isSharePurchaseFeatureEnabled()) {
            return 'tbt/rewardssocial/purchase/share/reward.js';
        }

        return null;
    }

    /**
     * Will load CSS for Twitter Tweet button only if config option to show this
     * button is enabled.
     *
     * @return file CSS file to be loaded in layout, or null if option not enabled
     */
    public function getTweetCss()
    {
        if (Mage::helper('rewardssocial/purchase_config')->isSharePurchaseOnTwitter()) {
            return 'css/rewardssocial/twitter/tweet.css';
        }

        return null;
    }

    public function getValidator($type)
    {
        if (!$type) {
            return null;
        }
        $type = strtolower($type);
        $validatorClass = "rewardssocial/purchase_share_{$type}_validator";

        return Mage::getSingleton($validatorClass);
    }

    public function getInitialStatusByType($type, $store)
    {
        if (!$type) {
            return null;
        }

        $type = ucfirst($type);
        $configPath    = "rewards/InitialTransferStatus/afterPurchaseShareOn{$type}";
        $initialStatus = Mage::getStoreConfig($configPath, $store);

        return $initialStatus;
    }

    public function getTransferCommentsByType($type, $store)
    {
        if (!$type) {
            return null;
        }

        $type = ucfirst($type);
        $configPath    = "rewards/transferComments/purchaseShareOn{$type}";
        $transferComments = Mage::getStoreConfig($configPath, $store);

        return $transferComments;
    }

    public function getReferenceClassByType($type)
    {
        if (!$type) {
            return null;
        }

        $type = ucfirst($type);
        $className = "TBT_Rewardssocial_Model_Purchase_Share_{$type}_Reference";

        return $className;
    }
}

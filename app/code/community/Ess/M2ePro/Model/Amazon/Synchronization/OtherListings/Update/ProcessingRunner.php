<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Amazon_Synchronization_OtherListings_Update_ProcessingRunner
    extends Ess_M2ePro_Model_Connector_Command_Pending_Processing_Runner_Partial
{
    const LOCK_ITEM_PREFIX = 'synchronization_amazon_other_listings_update';

    // ##################################

    protected function setLocks()
    {
        parent::setLocks();

        $params = $this->getParams();

        /** @var $lockItem Ess_M2ePro_Model_Lock_Item_Manager */
        $lockItem = Mage::getModel('M2ePro/Lock_Item_Manager');
        $lockItem->setNick(self::LOCK_ITEM_PREFIX.'_'.$params['account_id']);
        $lockItem->setMaxInactiveTime(self::MAX_LIFETIME);
        $lockItem->create();

        /** @var Ess_M2ePro_Model_Account $account */
        $account = Mage::helper('M2ePro/Component_Amazon')->getCachedObject('Account', $params['account_id']);

        $account->addProcessingLock(NULL, $this->getProcessingObject()->getId());
        $account->addProcessingLock('synchronization', $this->getProcessingObject()->getId());
        $account->addProcessingLock('synchronization_amazon', $this->getProcessingObject()->getId());
        $account->addProcessingLock(self::LOCK_ITEM_PREFIX, $this->getProcessingObject()->getId());
    }

    protected function unsetLocks()
    {
        parent::unsetLocks();

        $params = $this->getParams();

        /** @var $lockItem Ess_M2ePro_Model_Lock_Item_Manager */
        $lockItem = Mage::getModel('M2ePro/Lock_Item_Manager');
        $lockItem->setNick(self::LOCK_ITEM_PREFIX.'_'.$params['account_id']);
        $lockItem->remove();

        /** @var Ess_M2ePro_Model_Account $account */
        $account = Mage::helper('M2ePro/Component_Amazon')->getCachedObject('Account', $params['account_id']);

        $account->deleteProcessingLocks(NULL, $this->getProcessingObject()->getId());
        $account->deleteProcessingLocks('synchronization', $this->getProcessingObject()->getId());
        $account->deleteProcessingLocks('synchronization_amazon', $this->getProcessingObject()->getId());
        $account->deleteProcessingLocks(self::LOCK_ITEM_PREFIX, $this->getProcessingObject()->getId());
    }

    // ##################################
}
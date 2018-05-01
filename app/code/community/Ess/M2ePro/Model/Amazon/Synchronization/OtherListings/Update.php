<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

final class Ess_M2ePro_Model_Amazon_Synchronization_OtherListings_Update
    extends Ess_M2ePro_Model_Amazon_Synchronization_OtherListings_Abstract
{
    //########################################

    protected function getNick()
    {
        return '/update/';
    }

    protected function getTitle()
    {
        return 'Update';
    }

    // ---------------------------------------

    protected function getPercentsStart()
    {
        return 30;
    }

    protected function getPercentsEnd()
    {
        return 100;
    }

    // ---------------------------------------

    protected function intervalIsEnabled()
    {
        return true;
    }

    protected function intervalIsLocked()
    {
        if ($this->getInitiator() == Ess_M2ePro_Helper_Data::INITIATOR_USER ||
            $this->getInitiator() == Ess_M2ePro_Helper_Data::INITIATOR_DEVELOPER) {
            return false;
        }

        if (!in_array(Ess_M2ePro_Model_Synchronization_Task_Component_Abstract::LISTINGS_PRODUCTS,
            $this->getAllowedTasksTypes())) {
            return parent::intervalIsLocked();
        }

        $operationHistory = $this->getActualOperationHistory()->getParentObject('synchronization_amazon');
        if (is_null($operationHistory)) {
            return parent::intervalIsLocked();
        }

        $synchronizationStartTime = $operationHistory->getData('start_date');
        $updateListingsProductsLastTime = $this->getConfigValue(
            '/amazon/listings_products/update/', 'last_time'
        );

        return strtotime($synchronizationStartTime) > strtotime($updateListingsProductsLastTime);
    }

    //########################################

    protected function performActions()
    {
        /** @var $accountsCollection Mage_Core_Model_Mysql4_Collection_Abstract */
        $accountsCollection = Mage::helper('M2ePro/Component_Amazon')->getCollection('Account');
        $accountsCollection->addFieldToFilter('other_listings_synchronization',
            Ess_M2ePro_Model_Amazon_Account::OTHER_LISTINGS_SYNCHRONIZATION_YES);

        $accounts = $accountsCollection->getItems();

        if (count($accounts) <= 0) {
            return;
        }

        $iteration = 0;
        $percentsForOneStep = $this->getPercentsInterval() / count($accounts);

        foreach ($accounts as $account) {

            /** @var $account Ess_M2ePro_Model_Account **/

            $this->getActualOperationHistory()->addText('Starting Account "'.$account->getTitle().'"');
            // M2ePro_TRANSLATIONS
            // The "3rd Party Listings" Action for Amazon Account: "%account_title%" is started. Please wait...
            $status = 'The "3rd Party Listings" Action for Amazon Account: "%account_title%" is started. ';
            $status .= 'Please wait...';
            $this->getActualLockItem()->setStatus(Mage::helper('M2ePro')->__($status, $account->getTitle()));

            if (!$this->isLockedAccount($account)) {

                $this->getActualOperationHistory()->addTimePoint(
                    __METHOD__.'process'.$account->getId(),
                    'Process Account '.$account->getTitle()
                );

                try {

                    $params = array();
                    if (!$this->isFullItemsDataAlreadyReceived($account)) {
                        $params['full_items_data'] = true;

                        $additionalData = (array)Mage::helper('M2ePro')->jsonDecode($account->getAdditionalData());
                        $additionalData['is_amazon_other_listings_full_items_data_already_received'] = true;
                        $account->setSettings('additional_data', $additionalData)->save();
                    }

                    $dispatcherObject = Mage::getModel('M2ePro/Amazon_Connector_Dispatcher');
                    $connectorObj = $dispatcherObject->getCustomConnector(
                        'Amazon_Synchronization_OtherListings_Update_Requester',
                        $params, $account
                    );

                    $dispatcherObject->process($connectorObj);

                } catch (Exception $exception) {

                    $message = Mage::helper('M2ePro')->__(
                        'The "3rd Party Listings" Action for Amazon Account "%account%" was completed with error.',
                        $account->getTitle()
                    );

                    $this->processTaskAccountException($message, __FILE__, __LINE__);
                    $this->processTaskException($exception);
                }

                $this->getActualOperationHistory()->saveTimePoint(__METHOD__.'process'.$account->getId());
            }

            // M2ePro_TRANSLATIONS
            // The "3rd Party Listings" Action for Amazon Account: "%account_title%" is finished. Please wait...
            $status = 'The "3rd Party Listings" Action for Amazon Account: "%account_title%" is finished. ';
            $status .= 'Please wait...';
            $this->getActualLockItem()->setStatus(Mage::helper('M2ePro')->__($status, $account->getTitle()));
            $this->getActualLockItem()->setPercents($this->getPercentsStart() + $iteration * $percentsForOneStep);
            $this->getActualLockItem()->activate();

            $iteration++;
        }
    }

    //########################################

    private function isLockedAccount(Ess_M2ePro_Model_Account $account)
    {
        /** @var $lockItem Ess_M2ePro_Model_Lock_Item_Manager */
        $lockItem = Mage::getModel('M2ePro/Lock_Item_Manager');
        $lockItem->setNick(
            Ess_M2ePro_Model_Amazon_Synchronization_OtherListings_Update_ProcessingRunner::LOCK_ITEM_PREFIX
            .'_'.$account->getId()
        );
        $lockItem->setMaxInactiveTime(Ess_M2ePro_Model_Processing_Runner::MAX_LIFETIME);

        return $lockItem->isExist();
    }

    private function isFullItemsDataAlreadyReceived(Ess_M2ePro_Model_Account $account)
    {
        $additionalData = (array)Mage::helper('M2ePro')->jsonDecode($account->getAdditionalData());
        return !empty($additionalData['is_amazon_other_listings_full_items_data_already_received']);
    }

    //########################################
}
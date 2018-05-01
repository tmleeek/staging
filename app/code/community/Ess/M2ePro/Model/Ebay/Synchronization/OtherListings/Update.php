<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

final class Ess_M2ePro_Model_Ebay_Synchronization_OtherListings_Update
    extends Ess_M2ePro_Model_Ebay_Synchronization_OtherListings_Abstract
{
    const LOCK_ITEM_PREFIX = 'synchronization_ebay_other_listings_update';

    //########################################

    /**
     * @return string
     */
    protected function getNick()
    {
        return '/update/';
    }

    /**
     * @return string
     */
    protected function getTitle()
    {
        return 'Update';
    }

    // ---------------------------------------

    /**
     * @return int
     */
    protected function getPercentsStart()
    {
        return 0;
    }

    /**
     * @return int
     */
    protected function getPercentsEnd()
    {
        return 40;
    }

    // ---------------------------------------

    protected function intervalIsEnabled()
    {
        return true;
    }

    //########################################

    protected function performActions()
    {
        /** @var $accountsCollection Mage_Core_Model_Mysql4_Collection_Abstract */
        $accountsCollection = Mage::helper('M2ePro/Component_Ebay')->getCollection('Account');
        $accountsCollection->addFieldToFilter('other_listings_synchronization',
            Ess_M2ePro_Model_Ebay_Account::OTHER_LISTINGS_SYNCHRONIZATION_YES);

        $accounts = $accountsCollection->getItems();

        if (count($accounts) <= 0) {
            return;
        }

        $iteration = 0;
        $percentsForOneStep = ($this->getPercentsInterval()/2) / count($accounts);

        foreach ($accounts as $account) {

            /** @var $account Ess_M2ePro_Model_Account **/

            $this->getActualOperationHistory()->addText('Starting Account "'.$account->getTitle().'"');
            // M2ePro_TRANSLATIONS
            // The "Update 3rd Party Listings" Action for eBay Account: "%account_title%" is started. Please wait...
            $status = 'The "Update 3rd Party Listings" Action for eBay Account: "%account_title%" is started. ';
            $status .= 'Please wait...';
            $this->getActualLockItem()->setStatus(Mage::helper('M2ePro')->__($status, $account->getTitle()));

            if (!$this->isLockedAccount($account)) {

                $this->getActualOperationHistory()->addTimePoint(
                    __METHOD__.'process'.$account->getId(),
                    'Process Account '.$account->getTitle()
                );

                try {

                    $this->executeUpdateInventoryDataAccount($account);

                } catch (Exception $exception) {

                    $message = Mage::helper('M2ePro')->__(
                        'The "Update 3rd Party Listings" Action for eBay Account "%account%" was completed with error.',
                        $account->getTitle()
                    );

                    $this->processTaskAccountException($message, __FILE__, __LINE__);
                    $this->processTaskException($exception);
                }

                $this->getActualOperationHistory()->saveTimePoint(__METHOD__.'process'.$account->getId());
            }

            // M2ePro_TRANSLATIONS
            // The "Update 3rd Party Listings" Action for eBay Account: "%account_title%" is finished. Please wait...
            $status = 'The "Update 3rd Party Listings" Action for eBay Account: "%account_title%" is finished. ';
            $status .= 'Please wait...';
            $this->getActualLockItem()->setStatus(Mage::helper('M2ePro')->__($status, $account->getTitle()));
            $this->getActualLockItem()->setPercents($this->getPercentsStart() + $iteration * $percentsForOneStep);
            $this->getActualLockItem()->activate();

            $iteration++;
        }
    }

    //########################################

    private function executeUpdateInventoryDataAccount(Ess_M2ePro_Model_Account $account)
    {
        $sinceTime = $account->getData('other_listings_last_synchronization');

        if (empty($sinceTime)) {

            $marketplaceCollection = Mage::helper('M2ePro/Component_Ebay')->getCollection('Marketplace');
            $marketplaceCollection->addFieldToFilter('status',Ess_M2ePro_Model_Marketplace::STATUS_ENABLE);
            $marketplace = $marketplaceCollection->getFirstItem();

            if (!$marketplace->getId()) {
                $marketplace = Ess_M2ePro_Helper_Component_Ebay::MARKETPLACE_US;
            }

            $dispatcherObject = Mage::getModel('M2ePro/Ebay_Connector_Dispatcher');
            $connectorObj = $dispatcherObject->getCustomConnector(
                'Ebay_Synchronization_OtherListings_Update_Requester',
                array(), $marketplace, $account
            );
            $dispatcherObject->process($connectorObj);
            return;
        }

        $sinceTime = $this->prepareSinceTime($sinceTime);
        $changes = $this->getChangesByAccount($account, $sinceTime);

        /** @var $updatingModel Ess_M2ePro_Model_Ebay_Listing_Other_Updating */
        $updatingModel = Mage::getModel('M2ePro/Ebay_Listing_Other_Updating');
        $updatingModel->initialize($account);
        $updatingModel->processResponseData($changes);
    }

    //########################################

    private function getChangesByAccount(Ess_M2ePro_Model_Account $account, $sinceTime)
    {
        $nextSinceTime = new DateTime($sinceTime, new DateTimeZone('UTC'));

        $operationHistory = $this->getActualOperationHistory()->getParentObject('synchronization');
        if (!is_null($operationHistory)) {
            $toTime = new DateTime($operationHistory->getData('start_date'), new DateTimeZone('UTC'));
        } else {
            $toTime = new DateTime('now', new DateTimeZone('UTC'));
        }

        $toTime->modify('-1 hour');

        if ((int)$nextSinceTime->format('U') >= (int)$toTime->format('U')) {
            $nextSinceTime = $toTime;
            $nextSinceTime->modify('-1 minute');
        }

        $response = $this->receiveChangesFromEbay(
            $account,
            array('since_time'=>$nextSinceTime->format('Y-m-d H:i:s'), 'to_time' => $toTime->format('Y-m-d H:i:s'))
        );

        if ($response) {
            return (array)$response;
        }

        $previousSinceTime = $nextSinceTime;

        $nextSinceTime = new DateTime('now', new DateTimeZone('UTC'));
        $nextSinceTime->modify('-1 day');

        if ($previousSinceTime->format('U') < $nextSinceTime->format('U')) {

            $response = $this->receiveChangesFromEbay(
                $account,
                array('since_time'=>$nextSinceTime->format('Y-m-d H:i:s'), 'to_time' => $toTime->format('Y-m-d H:i:s'))
            );

            if ($response) {
                return (array)$response;
            }

            $previousSinceTime = $nextSinceTime;
        }

        $nextSinceTime = new DateTime('now', new DateTimeZone('UTC'));
        $nextSinceTime->modify('-2 hours');

        if ($previousSinceTime->format('U') < $nextSinceTime->format('U')) {

            $response = $this->receiveChangesFromEbay(
                $account,
                array('since_time'=>$nextSinceTime->format('Y-m-d H:i:s'), 'to_time' => $toTime->format('Y-m-d H:i:s'))
            );

            if ($response) {
                return (array)$response;
            }
        }

        return array();
    }

    private function receiveChangesFromEbay(Ess_M2ePro_Model_Account $account, array $paramsConnector = array())
    {
        $dispatcherObj = Mage::getModel('M2ePro/Ebay_Connector_Dispatcher');
        $connectorObj = $dispatcherObj->getVirtualConnector('item','get','changes',
                                                            $paramsConnector,NULL,
                                                            NULL,$account->getId());

        $dispatcherObj->process($connectorObj);
        $responseData = $connectorObj->getResponseData();

        $this->processResponseMessages($connectorObj->getResponseMessages());

        if (!isset($responseData['items']) || !isset($responseData['to_time'])) {
            return NULL;
        }

        return $responseData;
    }

    private function processResponseMessages(array $messages)
    {
        /** @var Ess_M2ePro_Model_Connector_Connection_Response_Message_Set $messagesSet */
        $messagesSet = Mage::getModel('M2ePro/Connector_Connection_Response_Message_Set');
        $messagesSet->init($messages);

        foreach ($messagesSet->getEntities() as $message) {

            if (!$message->isError() && !$message->isWarning()) {
                continue;
            }

            $logType = $message->isError() ? Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR
                : Ess_M2ePro_Model_Log_Abstract::TYPE_WARNING;

            $this->getLog()->addMessage(
                Mage::helper('M2ePro')->__($message->getText()),
                $logType,
                Ess_M2ePro_Model_Log_Abstract::PRIORITY_HIGH
            );
        }
    }

    //########################################

    private function prepareSinceTime($sinceTime)
    {
        $minTime = new DateTime('now', new DateTimeZone('UTC'));
        $minTime->modify("-1 month");

        if (empty($sinceTime) || strtotime($sinceTime) < (int)$minTime->format('U')) {
            $sinceTime = new DateTime('now', new DateTimeZone('UTC'));
            $sinceTime = $sinceTime->format('Y-m-d H:i:s');
        }

        return $sinceTime;
    }

    private function isLockedAccount(Ess_M2ePro_Model_Account $account)
    {
        /** @var $lockItem Ess_M2ePro_Model_Lock_Item_Manager */
        $lockItem = Mage::getModel('M2ePro/Lock_Item_Manager');
        $lockItem->setNick(self::LOCK_ITEM_PREFIX.'_'.$account->getId());
        $lockItem->setMaxInactiveTime(Ess_M2ePro_Model_Processing_Runner::MAX_LIFETIME);
        return $lockItem->isExist();
    }

    //########################################
}
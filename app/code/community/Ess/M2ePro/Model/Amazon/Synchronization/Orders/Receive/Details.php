<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

final class Ess_M2ePro_Model_Amazon_Synchronization_Orders_Receive_Details
    extends Ess_M2ePro_Model_Amazon_Synchronization_Orders_Abstract
{
    //########################################

    protected function getNick()
    {
        return '/receive_details/';
    }

    protected function getTitle()
    {
        return 'Receive Details';
    }

    // ---------------------------------------

    protected function getPercentsStart()
    {
        return 0;
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

    //########################################

    protected function performActions()
    {
        $permittedAccounts = $this->getPermittedAccounts();
        if (empty($permittedAccounts)) {
            return;
        }

        $iteration = 0;
        $percentsForOneStep = $this->getPercentsInterval() / count($permittedAccounts);

        foreach ($permittedAccounts as $account) {

            /** @var Ess_M2ePro_Model_Account $account */

            // ---------------------------------------
            $this->getActualOperationHistory()->addText('Starting account "'.$account->getTitle().'"');
            // M2ePro_TRANSLATIONS
            // The "Receive Details" action for Amazon account: "%account_title%" is started. Please wait...
            $status = 'The "Receive Details" action for Amazon account: "%account_title%" is started. Please wait...';
            $this->getActualLockItem()->setStatus(Mage::helper('M2ePro')->__($status, $account->getTitle()));
            // ---------------------------------------

            // ---------------------------------------
            $this->getActualOperationHistory()->addTimePoint(
                __METHOD__.'process'.$account->getId(),
                'Process account '.$account->getTitle()
            );
            // ---------------------------------------

            try {

                $this->processAccount($account);

            } catch (Exception $exception) {

                $message = Mage::helper('M2ePro')->__(
                    'The "Receive Details" Action for Amazon Account "%account%" was completed with error.',
                    $account->getTitle()
                );

                $this->processTaskAccountException($message, __FILE__, __LINE__);
                $this->processTaskException($exception);
            }

            // ---------------------------------------
            $this->getActualOperationHistory()->saveTimePoint(__METHOD__.'process'.$account->getId());
            // ---------------------------------------

            // ---------------------------------------
            // M2ePro_TRANSLATIONS
            // The "Receive Details" action for Amazon account: "%account_title%" is finished. Please wait...
            $status = 'The "Receive Details" action for Amazon account: "%account_title%" is finished. Please wait...';
            $this->getActualLockItem()->setStatus(Mage::helper('M2ePro')->__($status, $account->getTitle()));
            $this->getActualLockItem()->setPercents($this->getPercentsStart() + $iteration * $percentsForOneStep);
            $this->getActualLockItem()->activate();
            // ---------------------------------------

            $iteration++;
        }
    }

    //########################################

    private function getPermittedAccounts()
    {
        /** @var $accountsCollection Mage_Core_Model_Mysql4_Collection_Abstract */
        $accountsCollection = Mage::helper('M2ePro/Component_Amazon')->getCollection('Account');
        return $accountsCollection->getItems();
    }

    // ---------------------------------------

    private function processAccount(Ess_M2ePro_Model_Account $account)
    {
        $fromDate = $this->getFromDate($account);

        /** @var Ess_M2ePro_Model_Mysql4_Order_Collection $orderCollection */
        $orderCollection = Mage::helper('M2ePro/Component_Amazon')->getCollection('Order');
        $orderCollection->addFieldToFilter('account_id', $account->getId());
        $orderCollection->addFieldToFilter('is_afn_channel', 1);
        $orderCollection->addFieldToFilter('create_date', array('gt' => $fromDate));

        $amazonOrdersIds = $orderCollection->getColumnValues('amazon_order_id');
        if (empty($amazonOrdersIds)) {
            return;
        }

        $dispatcherObject = Mage::getModel('M2ePro/Amazon_Connector_Dispatcher');
        $connectorObj = $dispatcherObject->getCustomConnector(
            'Amazon_Synchronization_Orders_Receive_Details_Requester', array('items' => $amazonOrdersIds), $account
        );
        $dispatcherObject->process($connectorObj);

        $this->setFromDate($account);
    }

    //########################################

    private function getFromDate(Ess_M2ePro_Model_Account $account)
    {
        $accountAdditionalData = Mage::helper('M2ePro')->jsonDecode($account->getAdditionalData());
        return !empty($accountAdditionalData['amazon_last_receive_fulfillment_details_date']) ?
                   $accountAdditionalData['amazon_last_receive_fulfillment_details_date']
                   : Mage::helper('M2ePro')->getCurrentGmtDate();
    }

    private function setFromDate(Ess_M2ePro_Model_Account $account)
    {
        $fromDate = Mage::helper('M2ePro')->getCurrentGmtDate();

        $accountAdditionalData = Mage::helper('M2ePro')->jsonDecode($account->getAdditionalData());
        $accountAdditionalData['amazon_last_receive_fulfillment_details_date'] = $fromDate;
        $account->setSettings('additional_data', $accountAdditionalData)->save();
    }

    //########################################
}

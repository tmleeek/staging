<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

final class Ess_M2ePro_Model_Amazon_Actions_Processor
{
    const PENDING_REQUEST_MAX_LIFE_TIME = 86400;

    /** @var Ess_M2ePro_Model_Lock_Item_Manager|null */
    private $lockItem = null;

    //####################################

    public function getLockItem()
    {
        return $this->lockItem;
    }

    public function setLockItem(Ess_M2ePro_Model_Lock_Item_Manager $lockItem)
    {
        $this->lockItem = $lockItem;
        return $this;
    }

    //####################################

    public function process()
    {
        $this->removeMissedProcessingActions();
        $this->completeExpiredActions();
        $this->completeNeedSynchRulesCheckActions();

        $this->executeCompletedRequestsPendingSingle();

        /** @var Ess_M2ePro_Model_Mysql4_Account_Collection $accountCollection */
        $accountCollection = Mage::helper('M2ePro/Component_Amazon')->getCollection('Account');

        /** @var Ess_M2ePro_Model_Account[] $accounts */
        $accounts = $accountCollection->getItems();

        foreach ($accounts as $account) {
            $this->executeNotProcessedSingleAccountActions($account);
        }

        $groupedAccounts = array();

        foreach ($accounts as $account) {
            /** @var $account Ess_M2ePro_Model_Account */

            $merchantId = $account->getChildObject()->getMerchantId();
            if (!isset($groupedAccounts[$merchantId])) {
                $groupedAccounts[$merchantId] = array();
            }

            $groupedAccounts[$merchantId][] = $account;
        }

        foreach ($groupedAccounts as $accountsGroup) {
            $this->executeNotProcessedMultipleAccountsActions($accountsGroup);
        }
    }

    //####################################

    private function removeMissedProcessingActions()
    {
        $actionCollection = Mage::getResourceModel('M2ePro/Amazon_Processing_Action_Collection');
        $actionCollection->getSelect()->joinLeft(
            array('p' => Mage::getResourceModel('M2ePro/Processing')->getMainTable()),
            'p.id = main_table.processing_id',
            array()
        );
        $actionCollection->addFieldToFilter('p.id', array('null' => true));

        /** @var Ess_M2ePro_Model_Amazon_Processing_Action[] $actions */
        $actions = $actionCollection->getItems();

        foreach ($actions as $action) {
            $action->deleteInstance();
        }
    }

    private function completeExpiredActions()
    {
        /** @var Ess_M2ePro_Model_Mysql4_Amazon_Processing_Action_Collection $actionCollection */
        $actionCollection = Mage::getResourceModel('M2ePro/Amazon_Processing_Action_Collection');
        $actionCollection->addFieldToFilter('request_pending_single_id', array('notnull' => true));
        $actionCollection->getSelect()->joinLeft(
            array('rps' => Mage::getResourceModel('M2ePro/Request_Pending_Single')->getMainTable()),
            'rps.id = main_table.request_pending_single_id',
            array()
        );
        $actionCollection->addFieldToFilter('rps.id', array('null' => true));

        /** @var Ess_M2ePro_Model_Amazon_Processing_Action[] $actions */
        $actions = $actionCollection->getItems();

        $message = Mage::getModel('M2ePro/Connector_Connection_Response_Message');
        $message->initFromPreparedData(
            'Request wait timeout exceeded.',
            Ess_M2ePro_Model_Connector_Connection_Response_Message::TYPE_ERROR
        );

        foreach ($actions as $action) {
            $this->completeAction($action, array('messages' => array($message->asArray())));
        }
    }

    private function completeNeedSynchRulesCheckActions()
    {
        $actionCollection = Mage::getResourceModel('M2ePro/Amazon_Processing_Action_Collection');
        $actionCollection->getSelect()->joinLeft(
            array('lp' => Mage::getResourceModel('M2ePro/Listing_Product')->getMainTable()),
            'lp.id = main_table.related_id',
            'need_synch_rules_check'
        );
        $actionCollection->addFieldToFilter('need_synch_rules_check', true);
        $actionCollection->addFieldToFilter('request_pending_single_id', array('null' => true));

        /** @var Ess_M2ePro_Model_Amazon_Processing_Action[] $actions */
        $actions = $actionCollection->getItems();
        if (empty($actions)) {
            return;
        }

        $message = Mage::getModel('M2ePro/Connector_Connection_Response_Message');
        $message->initFromPreparedData(
            'The scheduled Action was skipped as some of the parameters which determine this Action
             were changed in Magento or directly on the Channel. Now the revision of this Action is required.',
            Ess_M2ePro_Model_Connector_Connection_Response_Message::TYPE_ERROR
        );

        foreach ($actions as $action) {
            $this->completeAction($action, array('messages' => array($message->asArray())));
        }
    }

    private function executeCompletedRequestsPendingSingle()
    {
        $requestIds = Mage::getResourceModel('M2ePro/Amazon_Processing_Action')
            ->getUniqueRequestPendingSingleIds();
        if (empty($requestIds)) {
            return;
        }

        $requestPendingSingleCollection = Mage::getResourceModel('M2ePro/Request_Pending_Single_Collection');
        $requestPendingSingleCollection->addFieldToFilter('id', array('in' => $requestIds));
        $requestPendingSingleCollection->addFieldToFilter('is_completed', 1);

        /** @var Ess_M2ePro_Model_Request_Pending_Single[] $requestPendingSingleObjects */
        $requestPendingSingleObjects = $requestPendingSingleCollection->getItems();
        if (empty($requestPendingSingleObjects)) {
            return;
        }

        foreach ($requestPendingSingleObjects as $requestId => $requestPendingSingle) {
            $actionCollection = Mage::getResourceModel('M2ePro/Amazon_Processing_Action_Collection');
            $actionCollection->setRequestPendingSingleIdFilter($requestId);
            $actionCollection->setInProgressFilter();

            /** @var Ess_M2ePro_Model_Amazon_Processing_Action[] $actions */
            $actions = $actionCollection->getItems();

            $resultData     = $requestPendingSingle->getResultData();
            $resultMessages = $requestPendingSingle->getResultMessages();

            foreach ($actions as $action) {

                $relatedId = $action->getRelatedId();

                $resultActionData = $this->getResponseData($resultData, $relatedId);
                $resultActionData['messages'] = $this->getResponseMessages($resultData, $resultMessages, $relatedId);

                $this->completeAction($action, $resultActionData, $requestPendingSingle->getData('create_date'));
            }

            $requestPendingSingle->deleteInstance();
        }
    }

    private function executeNotProcessedSingleAccountActions(Ess_M2ePro_Model_Account $account)
    {
        foreach ($this->getSingleAccountActionTypes() as $actionType) {
            while ($this->isNeedExecuteAction($actionType, array($account))) {
                $this->executeAction($actionType, array($account));
            }
        }
    }

    private function executeNotProcessedMultipleAccountsActions(array $accounts)
    {
        foreach ($this->getMultipleAccountsActionTypes() as $actionType) {
            while ($this->isNeedExecuteAction($actionType, $accounts)) {
                $this->executeAction($actionType, $accounts);
            }
        }
    }

    //####################################

    /**
     * @param $actionType
     * @param Ess_M2ePro_Model_Account[] $accounts
     * @return bool
     */
    private function isNeedExecuteAction($actionType, array $accounts)
    {
        /** @var Ess_M2ePro_Model_Mysql4_Amazon_Processing_Action_Collection $actionCollection */
        $actionCollection = Mage::getResourceModel('M2ePro/Amazon_Processing_Action_Collection');
        $actionCollection->setNotProcessedFilter();
        $actionCollection->addFieldToFilter('type', $actionType);
        $actionCollection->setAccountsFilter($accounts);

        if ($actionCollection->getSize() > $this->getMaxAllowedWaitingActionsCount($actionType)) {
            return true;
        }

        $actionCollection = Mage::getResourceModel('M2ePro/Amazon_Processing_Action_Collection');
        $actionCollection->setNotProcessedFilter();
        $actionCollection->addFieldToFilter('type', $actionType);
        $actionCollection->setAccountsFilter($accounts);
        $actionCollection->setStartedBeforeFilter($this->getMaxAllowedMinutesDelay($actionType));

        return (bool)$actionCollection->getSize();
    }

    /**
     * @param $actionType
     * @param Ess_M2ePro_Model_Account[] $accounts
     */
    private function executeAction($actionType, array $accounts)
    {
        /** @var Ess_M2ePro_Model_Mysql4_Amazon_Processing_Action_Collection $actionCollection */
        $actionCollection = Mage::getResourceModel('M2ePro/Amazon_Processing_Action_Collection');
        $actionCollection->setNotProcessedFilter();
        $actionCollection->addFieldToFilter('type', $actionType);
        $actionCollection->setAccountsFilter($accounts);
        $actionCollection->setPageSize($this->getMaxItemsCountInRequest($actionType));
        $actionCollection->setOrder('start_date', Varien_Data_Collection::SORT_ORDER_ASC);

        if ($actionCollection->getSize() <= 0) {
            return;
        }

        $dispatcherObject = Mage::getModel('M2ePro/Amazon_Connector_Dispatcher');

        $command = $this->getCommand($actionType);

        /** @var Ess_M2ePro_Model_Amazon_Processing_Action[] $actions */
        $actions = $actionCollection->getItems();

        $requestData = $this->getRequestData($actions, $actionType);

        if ($this->isMultipleAccountsActionType($actionType)) {
            foreach ($accounts as $account) {
                $requestData['accounts'][] = $account->getChildObject()->getServerHash();
            }
        } else {
            $requestData['account'] = reset($accounts)->getChildObject()->getServerHash();
        }

        $connectorObj = $dispatcherObject->getVirtualConnector(
            $command[0], $command[1], $command[2],
            $requestData, null, null
        );

        try {
            $dispatcherObject->process($connectorObj);
        } catch (Exception $exception) {
            Mage::helper('M2ePro/Module_Exception')->process($exception);

            $message = Mage::getModel('M2ePro/Connector_Connection_Response_Message');
            $message->initFromException($exception);

            foreach ($actions as $action) {
                $this->completeAction($action, array('messages' => array($message->asArray())));
            }

            return;
        }

        $responseData = $connectorObj->getResponseData();
        $responseMessages = $connectorObj->getResponseMessages();

        if (empty($responseData['processing_id'])) {
            foreach ($actions as $action) {
                $messages = $this->getResponseMessages($responseData, $responseMessages, $action->getRelatedId());
                $this->completeAction(
                    $action,
                    array('messages' => $messages)
                );
            }

            return;
        }

        Mage::getResourceModel('M2ePro/Amazon_Processing_Action')->markAsInProgress(
            $actionCollection->getColumnValues('id'),
            $this->buildRequestPendingSingle($responseData['processing_id'])
        );
    }

    //####################################

    private function getMaxItemsCountInRequest($actionType)
    {
        if ($this->isProductActionType($actionType)) {
            return 10000;
        }

        return 1000;
    }

    private function getMaxAllowedWaitingActionsCount($actionType)
    {
        if ($this->isProductActionType($actionType)) {
            return 10000;
        }

        return 10000;
    }

    private function getMaxAllowedMinutesDelay($actionType)
    {
        if (!Mage::helper('M2ePro/Module')->isProductionEnvironment()) {
            return 1;
        }

        if ($this->isProductActionType($actionType)) {
            return 60;
        }

        return 60;
    }

    //####################################

    private function getCommand($actionType)
    {
        switch ($actionType) {
            case Ess_M2ePro_Model_Amazon_Processing_Action::TYPE_PRODUCT_ADD:
                return array('product', 'add', 'entities');

            case Ess_M2ePro_Model_Amazon_Processing_Action::TYPE_PRODUCT_UPDATE:
                return array('product', 'update', 'entities');

            case Ess_M2ePro_Model_Amazon_Processing_Action::TYPE_PRODUCT_DELETE:
                return array('product', 'delete', 'entities');

            case Ess_M2ePro_Model_Amazon_Processing_Action::TYPE_ORDER_UPDATE:
                return array('orders', 'update', 'entities');

            case Ess_M2ePro_Model_Amazon_Processing_Action::TYPE_ORDER_CANCEL:
                return array('orders', 'cancel', 'entities');

            case Ess_M2ePro_Model_Amazon_Processing_Action::TYPE_ORDER_REFUND:
                return array('orders', 'refund', 'entities');

            default:
                throw new Ess_M2ePro_Model_Exception_Logic('Unknown action type.');
        }
    }

    //####################################

    /**
     * @param $serverHash
     * @return Ess_M2ePro_Model_Request_Pending_Single
     */
    private function buildRequestPendingSingle($serverHash)
    {
        $requestPendingSingle = Mage::getModel('M2ePro/Request_Pending_Single');
        $requestPendingSingle->setData(array(
            'component'       => Ess_M2ePro_Helper_Component_Amazon::NICK,
            'server_hash'     => $serverHash,
            'expiration_date' => Mage::helper('M2ePro')->getDate(
                Mage::helper('M2ePro')->getCurrentGmtDate(true)+self::PENDING_REQUEST_MAX_LIFE_TIME
            )
        ));
        $requestPendingSingle->save();

        return $requestPendingSingle;
    }

    //####################################

    private function getSingleAccountActionTypes()
    {
        return array(
            Ess_M2ePro_Model_Amazon_Processing_Action::TYPE_PRODUCT_ADD,
            Ess_M2ePro_Model_Amazon_Processing_Action::TYPE_PRODUCT_UPDATE,
            Ess_M2ePro_Model_Amazon_Processing_Action::TYPE_PRODUCT_DELETE,
            Ess_M2ePro_Model_Amazon_Processing_Action::TYPE_ORDER_CANCEL,
            Ess_M2ePro_Model_Amazon_Processing_Action::TYPE_ORDER_REFUND,
        );
    }

    // ---------------------------------------

    private function getMultipleAccountsActionTypes()
    {
        return array(
            Ess_M2ePro_Model_Amazon_Processing_Action::TYPE_ORDER_UPDATE,
        );
    }

    private function isMultipleAccountsActionType($actionType)
    {
        return in_array($actionType, $this->getMultipleAccountsActionTypes());
    }

    // ---------------------------------------

    private function getProductActionTypes()
    {
        return array(
            Ess_M2ePro_Model_Amazon_Processing_Action::TYPE_PRODUCT_ADD,
            Ess_M2ePro_Model_Amazon_Processing_Action::TYPE_PRODUCT_UPDATE,
            Ess_M2ePro_Model_Amazon_Processing_Action::TYPE_PRODUCT_DELETE,
        );
    }

    private function isProductActionType($actionType)
    {
        return in_array($actionType, $this->getProductActionTypes());
    }

    //####################################

    private function getResponseData(array $responseData, $relatedId)
    {
        $data = array();

        if (!empty($responseData['asins'][$relatedId.'-id'])) {
            $data['asins'] = $responseData['asins'][$relatedId.'-id'];
        }

        return $data;
    }

    private function getResponseMessages(array $responseData, array $responseMessages, $relatedId)
    {
        $messages = $responseMessages;

        if (!empty($responseData['messages'][0])) {
            $messages = array_merge($messages, $responseData['messages']['0']);
        }

        if (!empty($responseData['messages']['0-id'])) {
            $messages = array_merge($messages, $responseData['messages']['0-id']);
        }

        if (!empty($responseData['messages'][$relatedId.'-id'])) {
            $messages = array_merge($messages, $responseData['messages'][$relatedId.'-id']);
        }

        return $messages;
    }

    // ---------------------------------------

    /**
     * @param Ess_M2ePro_Model_Amazon_Processing_Action[] $actions
     * @param $actionType
     * @return array
     */
    private function getRequestData(array $actions, $actionType)
    {
        $requestData = array();

        foreach ($actions as $action) {
            $requestData[$action->getRelatedId()] = $action->getRequestData();
        }

        $dataKey = 'items';
        if (in_array($actionType, array(Ess_M2ePro_Model_Amazon_Processing_Action::TYPE_ORDER_CANCEL,
            Ess_M2ePro_Model_Amazon_Processing_Action::TYPE_ORDER_REFUND))
        ) {
            $dataKey = 'orders';
        }

        return array($dataKey => $requestData);
    }

    //####################################

    private function completeAction(Ess_M2ePro_Model_Amazon_Processing_Action $action, array $data, $requestTime = NULL)
    {
        $processing = $action->getProcessing();

        $data['start_processing_date'] = $action->getStartDate();

        $processing->setSettings('result_data', $data);
        $processing->setData('is_completed', 1);

        if (!is_null($requestTime)) {
            $processingParams = $processing->getParams();
            $processingParams['request_time'] = $requestTime;
            $processing->setSettings('params', $processingParams);
        }

        $processing->save();

        $action->deleteInstance();
    }

    //####################################
}
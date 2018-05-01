<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

final class Ess_M2ePro_Model_Ebay_Actions_Processor
{
    const ACTION_MAX_LIFE_TIME = 86400;

    /**
     * Maximum actions that we can execute during MAX_TOTAL_EXECUTION_TIME (180 sec).
     * Fastest action (STOP (1 sec)), executed in parallel in packs of MAX_PARALLEL_EXECUTION_PACK_SIZE (10)
     * considering with ONE_SERVER_CALL_INCREASE_TIME (1 sec), calculated as following:
     *
     * MAX_TOTAL_EXECUTION_TIME /
     * (STOP_COMMAND_REQUEST_TIME + ONE_SERVER_CALL_INCREASE_TIME) * MAX_PARALLEL_EXECUTION_PACK_SIZE + 100 (buffer)
     */
    const MAX_SELECT_ACTIONS_COUNT = 1000;

    const MAX_PARALLEL_EXECUTION_PACK_SIZE = 10;

    const ONE_SERVER_CALL_INCREASE_TIME = 1;
    const MAX_TOTAL_EXECUTION_TIME      = 180;

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
        $this->completeNeedSynchRulesCheckActions();
        $this->completeExpiredActions();

        $actions = $this->getActionsForExecute();

        if ($this->calculateSerialExecutionTime($actions) <= self::MAX_TOTAL_EXECUTION_TIME) {
            $this->executeSerial($actions);
        } else {
            $this->executeParallel($actions);
        }
    }

    //####################################

    private function removeMissedProcessingActions()
    {
        $actionCollection = Mage::getResourceModel('M2ePro/Ebay_Processing_Action_Collection');
        $actionCollection->getSelect()->joinLeft(
            array('p' => Mage::getResourceModel('M2ePro/Processing')->getMainTable()),
            'p.id = main_table.processing_id',
            array()
        );
        $actionCollection->addFieldToFilter('p.id', array('null' => true));

        /** @var Ess_M2ePro_Model_Ebay_Processing_Action[] $actions */
        $actions = $actionCollection->getItems();
        if (empty($actions)) {
            return;
        }

        foreach ($actions as $action) {
            $action->deleteInstance();
        }
    }

    private function completeNeedSynchRulesCheckActions()
    {
        $actionCollection = Mage::getResourceModel('M2ePro/Ebay_Processing_Action_Collection');
        $actionCollection->getSelect()->joinLeft(
            array('lp' => Mage::getResourceModel('M2ePro/Listing_Product')->getMainTable()),
            'lp.id = main_table.related_id',
            'need_synch_rules_check'
        );
        $actionCollection->addFieldToFilter('need_synch_rules_check', true);

        /** @var Ess_M2ePro_Model_Ebay_Processing_Action[] $actions */
        $actions = $actionCollection->getItems();
        if (empty($actions)) {
            return;
        }

        foreach ($actions as $action) {
            $this->completeAction(
                $action, array(), array($this->getNeedSynchRulesCheckActionMessage())
            );
        }
    }

    private function completeExpiredActions()
    {
        $minimumAllowedDate = new DateTime('now', new DateTimeZone('UTC'));
        $minimumAllowedDate->modify('- '.self::ACTION_MAX_LIFE_TIME.' seconds');

        $actionCollection = Mage::getResourceModel('M2ePro/Ebay_Processing_Action_Collection');
        $actionCollection->addFieldToFilter('create_date', array('lt' => $minimumAllowedDate->format('Y-m-d H:i:s')));

        /** @var Ess_M2ePro_Model_Ebay_Processing_Action[] $expiredActions */
        $expiredActions = $actionCollection->getItems();
        if (empty($expiredActions)) {
            return;
        }

        $expiredMessage = Mage::getModel('M2ePro/Connector_Connection_Response_Message');
        $expiredMessage->initFromPreparedData(
            'Request wait timeout exceeded.',
            Ess_M2ePro_Model_Connector_Connection_Response_Message::TYPE_ERROR
        );
        $expiredMessage = $expiredMessage->asArray();

        foreach ($expiredActions as $expiredAction) {
            $this->completeAction($expiredAction, array(), array($expiredMessage));
        }
    }

    //####################################

    /**
     * @return Ess_M2ePro_Model_Ebay_Processing_Action[]
     */
    private function getActionsForExecute()
    {
        $actionCollection = Mage::getResourceModel('M2ePro/Ebay_Processing_Action_Collection');
        $actionCollection->getSelect()->order('priority DESC');
        $actionCollection->getSelect()->order('start_date ASC');
        $actionCollection->getSelect()->limit(self::MAX_SELECT_ACTIONS_COUNT);

        /** @var $connRead Varien_Db_Adapter_Pdo_Mysql */
        $connRead = Mage::getSingleton('core/resource')->getConnection('core_read');

        $statement = $connRead->query($actionCollection->getSelect());

        $actions = array();

        while (($actionData = $statement->fetch()) !== false) {
            $action = Mage::getModel('M2ePro/Ebay_Processing_Action');
            $action->setData($actionData);

            if ($this->isActionCanBeAdded($action, $actions)) {
                $actions[] = $action;
            }

            if ($this->isActionsSetFull($actions)) {
                break;
            }
        }

        return $actions;
    }

    //-----------------------------------------

    /**
     * @param Ess_M2ePro_Model_Ebay_Processing_Action[] $actions
     */
    private function executeSerial(array $actions)
    {
        /** @var Ess_M2ePro_Model_Ebay_Connector_Dispatcher $dispatcher */
        $dispatcher = Mage::getModel('M2ePro/Ebay_Connector_Dispatcher');

        foreach ($actions as $action) {
            $this->getLockItem()->activate();

            $listingProduct = Mage::getModel('M2ePro/Listing_Product');
            $listingProduct->load($action->getRelatedId());

            if ($listingProduct->getId() && $listingProduct->needSynchRulesCheck()) {
                $this->completeAction($action, array(), array($this->getNeedSynchRulesCheckActionMessage()));
                continue;
            }

            $command = $this->getCommand($action);

            /** @var Ess_M2ePro_Model_Connector_Command_RealTime_Virtual $connector */
            $connector = $dispatcher->getVirtualConnector(
                $command[0], $command[1], $command[2],
                $action->getRequestData(), NULL,
                $action->getMarketplaceId(), $action->getAccountId(),
                $action->getRequestTimeOut()
            );

            $dispatcher->process($connector);

            $this->completeAction(
                $action,
                $connector->getResponseData(), $connector->getResponseMessages(),
                $connector->getRequestTime()
            );
        }
    }

    /**
     * @param Ess_M2ePro_Model_Ebay_Processing_Action[] $actions
     * @throws Ess_M2ePro_Model_Exception
     */
    private function executeParallel(array $actions)
    {
        /** @var Ess_M2ePro_Model_Ebay_Actions_Processor_Connector_Multiple_Dispatcher $dispatcher */
        $dispatcher = Mage::getModel('M2ePro/Ebay_Actions_Processor_Connector_Multiple_Dispatcher');

        foreach ($this->groupForParallelExecution($actions, true) as $actionsPacks) {
            foreach ($actionsPacks as $actionsPack) {
                /** @var Ess_M2ePro_Model_Ebay_Processing_Action[] $actionsPack */

                $this->getLockItem()->activate();

                $listingsProducts = $this->getListingsProducts($actionsPack);

                $connectors = array();

                foreach ($actionsPack as $action) {
                    if (isset($listingsProducts[$action->getRelatedId()]) &&
                        $listingsProducts[$action->getRelatedId()]->needSynchRulesCheck()) {
                        $this->completeAction($action, array(), array($this->getNeedSynchRulesCheckActionMessage()));
                        continue;
                    }

                    $command = $this->getCommand($action);

                    $connectors[$action->getId()] = $dispatcher->getCustomVirtualConnector(
                        'Ebay_Actions_Processor_Connector_Multiple_Command_VirtualWithoutCall',
                        $command[0], $command[1], $command[2],
                        $action->getRequestData(), NULL,
                        $action->getMarketplaceId(), $action->getAccountId(),
                        $action->getRequestTimeOut()
                    );
                }

                if (empty($connectors)) {
                    continue;
                }

                $dispatcher->processMultiple($connectors, true);

                $systemErrorsMessages = array();
                $isServerInMaintenanceMode = NULL;

                foreach ($connectors as $actionId => $connector) {
                    foreach ($actionsPack as $action) {
                        if ($action->getId() != $actionId) {
                            continue;
                        }

                        $response = $connector->getResponse();

                        if ($response->getMessages()->hasSystemErrorEntity()) {
                            $systemErrorsMessages[] = $response->getMessages()->getCombinedSystemErrorsString();

                            if (is_null($isServerInMaintenanceMode) && $response->isServerInMaintenanceMode()) {
                                $isServerInMaintenanceMode = true;
                            }
                            continue;
                        }

                        $this->completeAction(
                            $action,
                            $connector->getResponseData(), $connector->getResponseMessages(),
                            $connector->getRequestTime()
                        );

                        break;
                    }
                }

                if (!empty($systemErrorsMessages)) {
                    throw new Ess_M2ePro_Model_Exception(Mage::helper('M2ePro')->__(
                        "Internal Server Error(s) [%error_message%]",
                        $this->getCombinedErrorMessage($systemErrorsMessages)
                    ), array(), 0, !$isServerInMaintenanceMode);
                }
            }
        }
    }

    //-----------------------------------------

    private function getCombinedErrorMessage(array $systemErrorsMessages)
    {
        $combinedErrorMessages = array();
        foreach ($systemErrorsMessages as $systemErrorMessage) {
            $key = md5($systemErrorMessage);

            if (isset($combinedErrorMessages[$key])) {
                $combinedErrorMessages[$key]["count"] += 1;
                continue;
            }

            $combinedErrorMessages[$key] = array(
                "message" => $systemErrorMessage,
                "count" => 1
            );
        }

        $message = "";
        foreach ($combinedErrorMessages as $combinedErrorMessage) {
            $message .= sprintf("%s (%s)<br>",
                $combinedErrorMessage["message"],
                $combinedErrorMessage["count"]
            );
        }

        return $message;
    }

    //####################################

    /**
     * @param Ess_M2ePro_Model_Ebay_Processing_Action $action
     * @param Ess_M2ePro_Model_Ebay_Processing_Action[] $actions
     * @return bool
     */
    private function isActionCanBeAdded(Ess_M2ePro_Model_Ebay_Processing_Action $action, array $actions)
    {
        if ($this->calculateParallelExecutionTime($actions) < self::MAX_TOTAL_EXECUTION_TIME) {
            return true;
        }

        $groupedActions     = $this->groupForParallelExecution($actions, false);
        $commandRequestTime = $this->getCommandRequestTime($this->getCommand($action));

        if (empty($groupedActions[$commandRequestTime])) {
            return false;
        }

        foreach ($groupedActions[$commandRequestTime] as $actionsGroup) {
            if (count($actionsGroup) < self::MAX_PARALLEL_EXECUTION_PACK_SIZE) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Ess_M2ePro_Model_Ebay_Processing_Action[] $actions
     * @return bool
     */
    private function isActionsSetFull(array $actions)
    {
        if ($this->calculateParallelExecutionTime($actions) < self::MAX_TOTAL_EXECUTION_TIME) {
            return false;
        }

        foreach($this->groupForParallelExecution($actions, false) as $actionsGroups) {
            foreach ($actionsGroups as $actionsGroup) {
                if (count($actionsGroup) < self::MAX_PARALLEL_EXECUTION_PACK_SIZE) {
                    return false;
                }
            }
        }

        return true;
    }

    //-----------------------------------------

    /**
     * @param Ess_M2ePro_Model_Ebay_Processing_Action[] $actions
     * @return int
     */
    private function calculateSerialExecutionTime(array $actions)
    {
        $totalTime = 0;

        foreach ($actions as $action) {
            $commandRequestTime = $this->getCommandRequestTime($this->getCommand($action));
            $totalTime += $commandRequestTime + self::ONE_SERVER_CALL_INCREASE_TIME;
        }

        return $totalTime;
    }

    /**
     * @param Ess_M2ePro_Model_Ebay_Processing_Action[] $actions
     * @return int
     */
    private function calculateParallelExecutionTime(array $actions)
    {
        $totalTime = 0;

        foreach ($this->groupForParallelExecution($actions, false) as $commandRequestTime => $actionsPacks) {
            $actionsPacksCount = count($actionsPacks);
            $totalTime += $actionsPacksCount * ($commandRequestTime + self::ONE_SERVER_CALL_INCREASE_TIME);
        }

        return $totalTime;
    }

    //-----------------------------------------

    /**
     * @param Ess_M2ePro_Model_Ebay_Processing_Action[] $actions
     * @param bool $needDistribute
     * @return array
     */
    private function groupForParallelExecution(array $actions, $needDistribute = false)
    {
        $groupedByTimeActions = array();

        foreach ($actions as $action) {
            $commandRequestTime = $this->getCommandRequestTime($this->getCommand($action));
            $groupedByTimeActions[$commandRequestTime][] = $action;
        }

        $resultGroupedActions = array();

        $totalSerialExecutionTime = $this->calculateSerialExecutionTime($actions);

        foreach ($groupedByTimeActions as $commandRequestTime => $groupActions) {

            $packSize = self::MAX_PARALLEL_EXECUTION_PACK_SIZE;

            if ($needDistribute) {
                $groupSerialExecutionTime  = $this->calculateSerialExecutionTime($groupActions);
                $groupAllowedExecutionTime = (int)(
                    self::MAX_TOTAL_EXECUTION_TIME * $groupSerialExecutionTime / $totalSerialExecutionTime
                );
                if ($groupAllowedExecutionTime < $commandRequestTime) {
                    $groupAllowedExecutionTime = $commandRequestTime;
                }

                $packsCount = ceil(
                    $groupAllowedExecutionTime / ($commandRequestTime + self::ONE_SERVER_CALL_INCREASE_TIME)
                );
                $packSize   = ceil(count($groupActions) / $packsCount);
            }

            $resultGroupedActions[$commandRequestTime] = array_chunk($groupActions, $packSize);
        }

        return $resultGroupedActions;
    }

    /**
     * @param Ess_M2ePro_Model_Ebay_Processing_Action[] $actions
     * @return Ess_M2ePro_Model_Listing_Product[]
     */
    public function getListingsProducts(array $actions)
    {
        $listingsProductsIds = array();
        foreach ($actions as $action) {
            $listingsProductsIds[] = $action->getRelatedId();
        }

        $listingProductCollection = Mage::helper('M2ePro/Component_Ebay')->getCollection('Listing_Product');
        $listingProductCollection->addFieldToFilter('id', array('in' => $listingsProductsIds));

        return $listingProductCollection->getItems();
    }

    //####################################

    private function getCommand(Ess_M2ePro_Model_Ebay_Processing_Action $action)
    {
        switch ($action->getType()) {
            case Ess_M2ePro_Model_Ebay_Processing_Action::TYPE_LISTING_PRODUCT_LIST:
                return array('item', 'add', 'single');

            case Ess_M2ePro_Model_Ebay_Processing_Action::TYPE_LISTING_PRODUCT_RELIST:
                return array('item', 'update', 'relist');

            case Ess_M2ePro_Model_Ebay_Processing_Action::TYPE_LISTING_PRODUCT_REVISE:
                return array('item', 'update', 'revise');

            case Ess_M2ePro_Model_Ebay_Processing_Action::TYPE_LISTING_PRODUCT_STOP:
                return array('item', 'update', 'end');

            default:
                throw new Ess_M2ePro_Model_Exception_Logic('Unknown action type.');
        }
    }

    private function getCommandRequestTime($command)
    {
        switch ($command) {
            case array('item', 'add', 'single'):
            case array('item', 'update', 'relist'):
                return 3;

            case array('item', 'update', 'revise'):
                return 4;

            case array('item', 'update', 'end'):
                return 1;

            default:
                throw new Ess_M2ePro_Model_Exception_Logic('Unknown command.');
        }
    }

    //-----------------------------------------

    private function getNeedSynchRulesCheckActionMessage()
    {
        $message = Mage::getModel('M2ePro/Connector_Connection_Response_Message');
        $message->initFromPreparedData(
            'The scheduled Action was skipped as some of the parameters which determine this Action
             were changed in Magento or directly on the Channel. Now the revision of this Action is required.',
            Ess_M2ePro_Model_Connector_Connection_Response_Message::TYPE_ERROR
        );

        return $message->asArray();
    }

    private function completeAction(
        Ess_M2ePro_Model_Ebay_Processing_Action $action, array $data, array $messages, $requestTime = NULL
    ) {
        $processing = $action->getProcessing();

        $data['start_processing_date'] = $action->getStartDate();

        $processing->setSettings('result_data', $data);
        $processing->setSettings('result_messages', $messages);
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
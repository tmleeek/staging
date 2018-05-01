<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

abstract class Ess_M2ePro_Model_Amazon_Connector_Product_Requester
    extends Ess_M2ePro_Model_Amazon_Connector_Command_Pending_Requester
{
    /**
     * @var Ess_M2ePro_Model_Listing_Product $listingProduct
     */
    protected $listingProduct = NULL;

    // ---------------------------------------

    /**
     * @var Ess_M2ePro_Model_Amazon_Listing_Product_Action_Logger
     */
    protected $logger = NULL;

    // ---------------------------------------

    /**
     * @var Ess_M2ePro_Model_Amazon_Listing_Product_Action_Type_Validator $validatorObject
     */
    protected $validatorObject = NULL;

    /**
     * @var Ess_M2ePro_Model_Amazon_Listing_Product_Action_Type_Request $requestObject
     */
    protected $requestObject = NULL;

    /**
     * @var Ess_M2ePro_Model_Amazon_Listing_Product_Action_RequestData $requestDataObject
     */
    protected $requestDataObject = NULL;

    // ########################################

    public function __construct(array $params = array())
    {
        if (!isset($params['logs_action_id']) || !isset($params['status_changer'])) {
            throw new Ess_M2ePro_Model_Exception('Product Connector has not received some params');
        }

        parent::__construct($params);
    }

    // ########################################

    public function setListingProduct(Ess_M2ePro_Model_Listing_Product $listingProduct)
    {
        if (!is_null($listingProduct->getActionConfigurator())) {
            $actionConfigurator = $listingProduct->getActionConfigurator();
        } else {
            $actionConfigurator = Mage::getModel('M2ePro/Amazon_Listing_Product_Action_Configurator');
        }

        $this->listingProduct = $listingProduct->loadInstance($listingProduct->getId());

        if ($this->listingProduct->needSynchRulesCheck()) {
            $this->listingProduct->setData('need_synch_rules_check', 0);
            $this->listingProduct->save();
        }

        $this->listingProduct->setActionConfigurator($actionConfigurator);

        $this->account = $this->listingProduct->getAccount();
    }

    // ########################################

    protected function getProcessingRunnerModelName()
    {
        return 'Amazon_Connector_Product_ProcessingRunner';
    }

    protected function getProcessingParams()
    {
        $configuratorParams = $this->listingProduct->getActionConfigurator()->getParams();

        $startDate = Mage::helper('M2ePro')->getCurrentGmtDate();
        if (!empty($configuratorParams['start_processing_date'])) {
            $startDate = $configuratorParams['start_processing_date'];
        }

        return array_merge(
            parent::getProcessingParams(),
            array(
                'request_data'       => $this->getRequestData(),
                'listing_product_id' => $this->listingProduct->getId(),
                'lock_identifier'    => $this->getLockIdentifier(),
                'action_type'        => $this->getActionType(),
                'start_date'         => $startDate,
            )
        );
    }

    // ########################################

    abstract protected function getLogsAction();

    // ----------------------------------------

    protected function getLockIdentifier()
    {
        return strtolower($this->getOrmActionType());
    }

    // ########################################

    public function process()
    {
        try {
            $this->getLogger()->setStatus(Ess_M2ePro_Helper_Data::STATUS_SUCCESS);

            if ($this->isListingProductLocked()) {
                return;
            }

            /** @var Ess_M2ePro_Model_Amazon_Listing_Product $amazonListingProduct */
            $amazonListingProduct = $this->listingProduct->getChildObject();

            if ($amazonListingProduct->getVariationManager()->isRelationParentType() &&
                $this->validateAndProcessParentListingProduct()
            ) {
                return;
            }

            $this->lockListingProduct();

            if (!$this->validateListingProduct()) {
                $this->unlockListingProduct();
                return;
            }

            $this->eventBeforeExecuting();
            $this->getProcessingRunner()->start();

        } catch (Exception $exception) {
            $this->unlockListingProduct();
            throw $exception;
        }

        $this->unlockListingProduct();
    }

    // ########################################

    public function getStatus()
    {
        return $this->getLogger()->getStatus();
    }

    // ########################################

    protected function validateListingProduct()
    {
        $validator = $this->getValidatorObject();

        $validationResult = $validator->validate();

        foreach ($validator->getMessages() as $messageData) {

            $message = Mage::getModel('M2ePro/Connector_Connection_Response_Message');
            $message->initFromPreparedData($messageData['text'], $messageData['type']);

            $this->getLogger()->logListingProductMessage(
                $this->listingProduct,
                $message,
                Ess_M2ePro_Model_Log_Abstract::PRIORITY_MEDIUM
            );
        }

        return $validationResult;
    }

    // ########################################

    protected function validateAndProcessParentListingProduct()
    {
        /** @var Ess_M2ePro_Model_Amazon_Listing_Product $amazonListingProduct */
        $amazonListingProduct = $this->listingProduct->getChildObject();

        if (!$amazonListingProduct->getVariationManager()->isRelationParentType()) {
            return false;
        }

        if (!$amazonListingProduct->getGeneralId()) {
            return false;
        }

        $childListingsProducts = $amazonListingProduct->getVariationManager()
            ->getTypeModel()
            ->getChildListingsProducts();

        $childListingsProducts = $this->filterChildListingProductsByStatus($childListingsProducts);
        $childListingsProducts = $this->filterLockedChildListingProducts($childListingsProducts);

        if (empty($childListingsProducts)) {
            $this->listingProduct->setData('no_child_for_processing', true);
            return false;
        }

        $dispatcherParams = array_merge($this->params, array('is_parent_action' => true));

        $dispatcherObject = Mage::getModel('M2ePro/Amazon_Connector_Product_Dispatcher');
        $processStatus = $dispatcherObject->process(
            $this->getActionType(), $childListingsProducts, $dispatcherParams
        );

        if ($processStatus == Ess_M2ePro_Helper_Data::STATUS_ERROR) {
            $this->getLogger()->setStatus(Ess_M2ePro_Helper_Data::STATUS_ERROR);
        }

        return true;
    }

    /**
     * @param Ess_M2ePro_Model_Listing_Product[] $listingProducts
     * @return Ess_M2ePro_Model_Listing_Product[]
     */
    abstract protected function filterChildListingProductsByStatus(array $listingProducts);

    /**
     * @param Ess_M2ePro_Model_Listing_Product[] $listingProducts
     * @return Ess_M2ePro_Model_Listing_Product[]
     */
    protected function filterLockedChildListingProducts(array $listingProducts)
    {
        $lockItem = Mage::getModel('M2ePro/Lock_Item_Manager');

        $resultListingProducts = array();
        foreach ($listingProducts as $listingProduct) {
            $lockItem->setNick(
                Ess_M2ePro_Helper_Component_Amazon::NICK.'_listing_product_'.$listingProduct->getId()
            );

            if ($listingProduct->isSetProcessingLock('in_action') || $lockItem->isExist()) {
                continue;
            }

            $resultListingProducts[] = $listingProduct;
        }

        return $resultListingProducts;
    }

    // ########################################

    protected function isListingProductLocked()
    {
        $lockItem = Mage::getModel('M2ePro/Lock_Item_Manager');
        $lockItem->setNick(Ess_M2ePro_Helper_Component_Amazon::NICK.'_listing_product_'.$this->listingProduct->getId());

        if ($this->listingProduct->isSetProcessingLock('in_action') || $lockItem->isExist()) {

            // M2ePro_TRANSLATIONS
            // Another Action is being processed. Try again when the Action is completed.
            $message = Mage::getModel('M2ePro/Connector_Connection_Response_Message');
            $message->initFromPreparedData(
                'Another Action is being processed. Try again when the Action is completed.',
                Ess_M2ePro_Model_Connector_Connection_Response_Message::TYPE_ERROR
            );

            $this->getLogger()->logListingProductMessage(
                $this->listingProduct,
                $message,
                Ess_M2ePro_Model_Log_Abstract::PRIORITY_MEDIUM
            );

            return true;
        }

        return false;
    }

    // ########################################

    protected function lockListingProduct()
    {
        $lockItem = Mage::getModel('M2ePro/Lock_Item_Manager');
        $lockItem->setNick(Ess_M2ePro_Helper_Component_Amazon::NICK.'_listing_product_'.$this->listingProduct->getId());

        $lockItem->create();
        $lockItem->makeShutdownFunction();
    }

    protected function unlockListingProduct()
    {
        $lockItem = Mage::getModel('M2ePro/Lock_Item_Manager');
        $lockItem->setNick(Ess_M2ePro_Helper_Component_Amazon::NICK.'_listing_product_'.$this->listingProduct->getId());

        $lockItem->remove();
    }

    // ########################################

    protected function getRequestData()
    {
        $requestObject  = $this->getRequestObject();
        $requestDataRaw = $requestObject->getData();

        foreach ($requestObject->getWarningMessages() as $messageText) {

            $message = Mage::getModel('M2ePro/Connector_Connection_Response_Message');
            $message->initFromPreparedData(
                $messageText,
                Ess_M2ePro_Model_Connector_Connection_Response_Message::TYPE_WARNING
            );

            $this->getLogger()->logListingProductMessage($this->listingProduct,
                                                         $message,
                                                         Ess_M2ePro_Model_Log_Abstract::PRIORITY_MEDIUM);
        }

        $this->buildRequestDataObject($requestDataRaw);

        return array_merge($requestDataRaw, array('id' => $this->listingProduct->getId()));
    }

    protected function getResponserParams()
    {
        $product = array(
            'request'      => $this->getRequestDataObject()->getData(),
            'configurator' => $this->listingProduct->getActionConfigurator()->getData(),
            'id'           => $this->listingProduct->getId(),
        );

        return array(
            'account_id'      => $this->account->getId(),
            'action_type'     => $this->getActionType(),
            'lock_identifier' => $this->getLockIdentifier(),
            'logs_action'     => $this->getLogsAction(),
            'logs_action_id'  => $this->getLogger()->getActionId(),
            'status_changer'  => $this->params['status_changer'],
            'params'          => $this->params,
            'product'         => $product,
        );
    }

    // ########################################

    /**
     * @return Ess_M2ePro_Model_Amazon_Listing_Product_Action_Logger
     */
    protected function getLogger()
    {
        if (is_null($this->logger)) {

            /** @var Ess_M2ePro_Model_Amazon_Listing_Product_Action_Logger $logger */

            $logger = Mage::getModel('M2ePro/Amazon_Listing_Product_Action_Logger');

            $logger->setActionId((int)$this->params['logs_action_id']);
            $logger->setAction($this->getLogsAction());

            switch ($this->params['status_changer']) {
                case Ess_M2ePro_Model_Listing_Product::STATUS_CHANGER_UNKNOWN:
                    $initiator = Ess_M2ePro_Helper_Data::INITIATOR_UNKNOWN;
                    break;
                case Ess_M2ePro_Model_Listing_Product::STATUS_CHANGER_USER:
                    $initiator = Ess_M2ePro_Helper_Data::INITIATOR_USER;
                    break;
                default:
                    $initiator = Ess_M2ePro_Helper_Data::INITIATOR_EXTENSION;
                    break;
            }

            $logger->setInitiator($initiator);

            $this->logger = $logger;
        }

        return $this->logger;
    }

    // ########################################

    /**
     * @return Ess_M2ePro_Model_Amazon_Listing_Product_Action_Type_Validator
     */
    protected function getValidatorObject()
    {
        if (is_null($this->validatorObject)) {

            /** @var $validator Ess_M2ePro_Model_Amazon_Listing_Product_Action_Type_Validator */
            $validator = Mage::getModel(
                'M2ePro/Amazon_Listing_Product_Action_Type_'.$this->getOrmActionType().'_Validator'
            );

            $validator->setParams($this->params);
            $validator->setListingProduct($this->listingProduct);
            $validator->setConfigurator($this->listingProduct->getActionConfigurator());

            $this->validatorObject = $validator;
        }

        return $this->validatorObject;
    }

    /**
     * @return Ess_M2ePro_Model_Amazon_Listing_Product_Action_Type_Request
     */
    protected function getRequestObject()
    {
        if (is_null($this->requestObject)) {

            /* @var $request Ess_M2ePro_Model_Amazon_Listing_Product_Action_Type_Request */
            $request = Mage::getModel(
                'M2ePro/Amazon_Listing_Product_Action_Type_'.$this->getOrmActionType().'_Request'
            );

            $request->setParams($this->params);
            $request->setListingProduct($this->listingProduct);
            $request->setConfigurator($this->listingProduct->getActionConfigurator());
            $request->setValidatorsData($this->getValidatorObject()->getData());

            $this->requestObject = $request;
        }

        return $this->requestObject;
    }

    // ----------------------------------------

    /**
     * @return Ess_M2ePro_Model_Amazon_Listing_Product_Action_RequestData
     */
    protected function getRequestDataObject()
    {
        return $this->requestDataObject;
    }

    /**
     * @param array $data
     * @return Ess_M2ePro_Model_Amazon_Listing_Product_Action_RequestData
     */
    protected function buildRequestDataObject(array $data)
    {
        if (is_null($this->requestDataObject)) {

            /** @var Ess_M2ePro_Model_Amazon_Listing_Product_Action_RequestData $requestData */
            $requestData = Mage::getModel('M2ePro/Amazon_Listing_Product_Action_RequestData');

            $requestData->setData($data);
            $requestData->setListingProduct($this->listingProduct);

            $this->requestDataObject = $requestData;
        }

        return $this->requestDataObject;
    }

    // ########################################

    private function getOrmActionType()
    {
        switch ($this->getActionType()) {
            case Ess_M2ePro_Model_Listing_Product::ACTION_LIST:
                return 'List';
            case Ess_M2ePro_Model_Listing_Product::ACTION_RELIST:
                return 'Relist';
            case Ess_M2ePro_Model_Listing_Product::ACTION_REVISE:
                return 'Revise';
            case Ess_M2ePro_Model_Listing_Product::ACTION_STOP:
                return 'Stop';
            case Ess_M2ePro_Model_Listing_Product::ACTION_DELETE:
                return 'Delete';
        }

        throw new Ess_M2ePro_Model_Exception('Wrong Action type');
    }

    abstract protected function getActionType();

    // ########################################
}
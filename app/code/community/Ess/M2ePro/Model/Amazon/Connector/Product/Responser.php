<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

abstract class Ess_M2ePro_Model_Amazon_Connector_Product_Responser
    extends Ess_M2ePro_Model_Amazon_Connector_Command_Pending_Responser
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

    /**
     * @var Ess_M2ePro_Model_Amazon_Listing_Product_Action_Configurator $configurator
     */
    protected $configurator = NULL;

    // ---------------------------------------

    /**
     * @var Ess_M2ePro_Model_Amazon_Listing_Product_Action_Type_Response $responseObject
     */
    protected $responseObject = NULL;

    /**
     * @var Ess_M2ePro_Model_Amazon_Listing_Product_Action_RequestData $requestDataObject
     */
    protected $requestDataObject = NULL;

    // ---------------------------------------

    protected $isSuccess = false;

    // ########################################

    public function __construct(array $params = array(), Ess_M2ePro_Model_Connector_Connection_Response $response)
    {
        parent::__construct($params, $response);

        $listingProductId = $this->params['product']['id'];
        $this->listingProduct = Mage::helper('M2ePro/Component_Amazon')
            ->getObject('Listing_Product', $listingProductId);
    }

    // ########################################

    public function failDetected($messageText)
    {
        parent::failDetected($messageText);

        $message = Mage::getModel('M2ePro/Connector_Connection_Response_Message');
        $message->initFromPreparedData(
            $messageText,
            Ess_M2ePro_Model_Connector_Connection_Response_Message::TYPE_ERROR
        );

        $this->getLogger()->logListingProductMessage(
            $this->listingProduct,
            $message,
            Ess_M2ePro_Model_Log_Abstract::PRIORITY_HIGH
        );
    }

    public function eventAfterExecuting()
    {
        parent::eventAfterExecuting();

        $this->processParentProcessor();
        $this->inspectProduct();
    }

    protected function inspectProduct()
    {
        if (!$this->isSuccess && !$this->listingProduct->needSynchRulesCheck()) {
            return;
        }

        /** @var Ess_M2ePro_Model_Amazon_Listing_Product $amazonListingProduct */
        $amazonListingProduct = $this->listingProduct->getChildObject();
        if ($amazonListingProduct->getVariationManager()->isRelationParentType()) {
            return;
        }

        $runner = Mage::getModel('M2ePro/Synchronization_Templates_Synchronization_Runner');
        $runner->setConnectorModel('Amazon_Connector_Product_Dispatcher');
        $runner->setMaxProductsPerStep(100);

        $inspector = Mage::getModel('M2ePro/Amazon_Synchronization_Templates_Synchronization_Inspector');

        $responseData = $this->getPreparedResponseData();

        if (empty($responseData['request_time']) && $this->listingProduct->needSynchRulesCheck()) {
            $configurator = $this->getConfigurator();
        } else {
            $configurator = Mage::getModel('M2ePro/Amazon_Listing_Product_Action_Configurator');
        }

        if (empty($responseData['request_time']) && !empty($responseData['start_processing_date'])) {
            $configurator->setParams(array('start_processing_date' => $responseData['start_processing_date']));
        }

        if ($this->listingProduct->isListed()) {

            if ($inspector->isMeetStopRequirements($this->listingProduct)) {
                $runner->addProduct(
                    $this->listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_STOP, $configurator
                );
                $runner->execute();
                return;
            }

            $configurator->reset();

            $needRevise = false;

            if ($inspector->isMeetReviseQtyRequirements($this->listingProduct)) {
                $configurator->allowQty();
                $needRevise = true;
            }

            if ($inspector->isMeetReviseRegularPriceRequirements($this->listingProduct)) {
                $configurator->allowRegularPrice();
                $needRevise = true;
            }

            if ($inspector->isMeetReviseBusinessPriceRequirements($this->listingProduct)) {
                $configurator->allowBusinessPrice();
                $needRevise = true;
            }

            if ($needRevise) {
                $runner->addProduct(
                    $this->listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_REVISE, $configurator
                );
                $runner->execute();
            }

            return;
        }

        if ($this->listingProduct->isStopped()) {

            /** @var Ess_M2ePro_Model_Listing_Product $listingProduct */

            if (!$inspector->isMeetRelistRequirements($this->listingProduct)) {
                return;
            }

            /** @var Ess_M2ePro_Model_Amazon_Listing_Product $amazonListingProduct */
            $amazonListingProduct = $this->listingProduct->getChildObject();

            $synchronizationTemplate = $amazonListingProduct->getAmazonSynchronizationTemplate();

            if (!$synchronizationTemplate->isRelistSendData()) {
                $configurator->reset();
                $configurator->allowQty();
            }

            $runner->addProduct(
                $this->listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_RELIST, $configurator
            );
            $runner->execute();

            return;
        }

        if ($this->listingProduct->isUnknown()) {

            $configurator->reset();

            $needRevise = false;

            if ($inspector->isMeetReviseRegularPriceRequirements($this->listingProduct)) {
                $configurator->allowRegularPrice();
                $needRevise = true;
            }

            if ($inspector->isMeetReviseBusinessPriceRequirements($this->listingProduct)) {
                $configurator->allowBusinessPrice();
                $needRevise = true;
            }

            if ($needRevise) {
                $runner->addProduct(
                    $this->listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_REVISE, $configurator
                );
                $runner->execute();
            }

            return;
        }
    }

    protected function processParentProcessor()
    {
        if (!$this->isSuccess) {
            return;
        }

        /** @var Ess_M2ePro_Model_Amazon_Listing_Product $amazonListingProduct */
        $amazonListingProduct = $this->listingProduct->getChildObject();

        $variationManager = $amazonListingProduct->getVariationManager();

        if (!$variationManager->isRelationMode()) {
            return;
        }

        if ($variationManager->isRelationParentType()) {
            $parentListingProduct = $this->listingProduct;
        } else {
            $parentListingProduct = $variationManager->getTypeModel()->getParentListingProduct();
        }

        /** @var Ess_M2ePro_Model_Amazon_Listing_Product $amazonParentListingProduct */
        $amazonParentListingProduct = $parentListingProduct->getChildObject();

        $parentTypeModel = $amazonParentListingProduct->getVariationManager()->getTypeModel();
        $parentTypeModel->getProcessor()->process();
    }

    // ########################################

    protected function validateResponse()
    {
        $responseData = $this->getResponse()->getData();
        return isset($responseData['messages']) && is_array($responseData['messages']);
    }

    protected function processResponseData()
    {
        $responseMessages = array();

        $responseData = $this->getPreparedResponseData();

        foreach ($responseData['messages'] as $messageData) {
            $message = Mage::getModel('M2ePro/Connector_Connection_Response_Message');
            $message->initFromResponseData($messageData);

            $responseMessages[] = $message;
        }

        if (!$this->processMessages($responseMessages)) {
            return;
        }

        $successParams = $this->getSuccessfulParams();
        $this->processSuccess($successParams);
    }

    //----------------------------------------

    protected function processMessages(array $messages)
    {
        $hasError = false;

        foreach ($messages as $message) {

            /** @var Ess_M2ePro_Model_Connector_Connection_Response_Message $message */

            !$hasError && $hasError = $message->isError();

            $this->getLogger()->logListingProductMessage(
                $this->listingProduct, $message
            );
        }

        return !$hasError;
    }

    protected function processSuccess(array $params = array())
    {
        $this->getResponseObject()->processSuccess($params);

        $message = Mage::getModel('M2ePro/Connector_Connection_Response_Message');
        $message->initFromPreparedData(
            $this->getSuccessfulMessage(),
            Ess_M2ePro_Model_Connector_Connection_Response_Message::TYPE_SUCCESS
        );

        $this->getLogger()->logListingProductMessage(
            $this->listingProduct, $message
        );

        $this->isSuccess = true;
    }

    //----------------------------------------

    protected function getSuccessfulParams()
    {
        return array();
    }

    //----------------------------------------

    /**
     * @return string
     */
    abstract protected function getSuccessfulMessage();

    // ########################################

    /**
     * @return Ess_M2ePro_Model_Amazon_Listing_Product_Action_Logger
     */
    protected function getLogger()
    {
        if (is_null($this->logger)) {

            /** @var Ess_M2ePro_Model_Amazon_Listing_Product_Action_Logger $logger */

            $logger = Mage::getModel('M2ePro/Amazon_Listing_Product_Action_Logger');

            $logger->setActionId($this->getLogsActionId());
            $logger->setAction($this->getLogsAction());

            switch ($this->getStatusChanger()) {
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

    protected function getConfigurator()
    {
        if (is_null($this->configurator)) {

            $configurator = Mage::getModel('M2ePro/Amazon_Listing_Product_Action_Configurator');
            $configurator->setData($this->params['product']['configurator']);

            $this->configurator = $configurator;
        }

        return $this->configurator;
    }

    // ########################################

    /**
     * @return Ess_M2ePro_Model_Amazon_Listing_Product_Action_Type_Response
     */
    protected function getResponseObject()
    {
        if (is_null($this->responseObject)) {

            /* @var $response Ess_M2ePro_Model_Amazon_Listing_Product_Action_Type_Response */
            $response = Mage::getModel(
                'M2ePro/Amazon_Listing_Product_Action_Type_'.$this->getOrmActionType().'_Response'
            );

            $response->setParams($this->params['params']);
            $response->setListingProduct($this->listingProduct);
            $response->setConfigurator($this->getConfigurator());
            $response->setRequestData($this->getRequestDataObject());

            $this->responseObject = $response;
        }

        return $this->responseObject;
    }

    /**
     * @return Ess_M2ePro_Model_Amazon_Listing_Product_Action_RequestData
     */
    protected function getRequestDataObject()
    {
        if (is_null($this->requestDataObject)) {

            /** @var Ess_M2ePro_Model_Amazon_Listing_Product_Action_RequestData $requestData */
            $requestData = Mage::getModel('M2ePro/Amazon_Listing_Product_Action_RequestData');

            $requestData->setData($this->params['product']['request']);
            $requestData->setListingProduct($this->listingProduct);

            $this->requestDataObject = $requestData;
        }

        return $this->requestDataObject;
    }

    // ########################################

    /**
     * @return Ess_M2ePro_Model_Account
     */
    protected function getAccount()
    {
        return $this->getObjectByParam('Account','account_id');
    }

    /**
     * @return Ess_M2ePro_Model_Marketplace
     */
    protected function getMarketplace()
    {
        return $this->getAccount()->getChildObject()->getMarketplace();
    }

    //---------------------------------------

    protected function getActionType()
    {
        return $this->params['action_type'];
    }

    protected function getLockIdentifier()
    {
        return $this->params['lock_identifier'];
    }

    //---------------------------------------

    protected function getLogsAction()
    {
        return $this->params['logs_action'];
    }

    protected function getLogsActionId()
    {
        return (int)$this->params['logs_action_id'];
    }

    //---------------------------------------

    protected function getStatusChanger()
    {
        return (int)$this->params['status_changer'];
    }

    // ########################################

    protected function getOrmActionType()
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

    // ########################################
}
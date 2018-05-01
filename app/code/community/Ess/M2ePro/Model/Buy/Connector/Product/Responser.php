<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

abstract class Ess_M2ePro_Model_Buy_Connector_Product_Responser
    extends Ess_M2ePro_Model_Buy_Connector_Command_Pending_Responser
{
    /**
     * @var Ess_M2ePro_Model_Listing_Product[]
     */
    protected $listingsProducts = array();

    /**
     * @var Ess_M2ePro_Model_Listing_Product[]
     */
    protected $successfulListingProducts = array();

    // ---------------------------------------

    /**
     * @var Ess_M2ePro_Model_Buy_Listing_Product_Action_Logger
     */
    protected $logger = NULL;

    /**
     * @var Ess_M2ePro_Model_Buy_Listing_Product_Action_Configurator[]
     */
    protected $configurators = array();

    // ---------------------------------------

    /**
     * @var Ess_M2ePro_Model_Buy_Listing_Product_Action_Type_Response[]
     */
    protected $responsesObjects = array();

    /**
     * @var Ess_M2ePro_Model_Buy_Listing_Product_Action_RequestData[]
     */
    protected $requestsDataObjects = array();

    protected $isResponseFailed = false;

    // ########################################

    public function __construct(array $params = array(), Ess_M2ePro_Model_Connector_Connection_Response $response)
    {
        parent::__construct($params, $response);

        $listingsProductsIds = array_keys($this->params['products']);

        /** @var Ess_M2ePro_Model_Mysql4_Listing_Product_Collection $listingProductCollection */
        $listingProductCollection = Mage::helper('M2ePro/Component_Buy')->getCollection('Listing_Product');
        $listingProductCollection->addFieldToFilter('id', array('in' => $listingsProductsIds));

        $this->listingsProducts = $listingProductCollection->getItems();
    }

    // ########################################

    protected function processResponseMessages()
    {
        parent::processResponseMessages();

        foreach ($this->listingsProducts as $listingProduct) {
            $this->processMessages($listingProduct, $this->getResponse()->getMessages()->getEntities());
        }
    }

    protected function isNeedProcessResponse()
    {
        if (!parent::isNeedProcessResponse()) {
            return false;
        }

        $responseData = $this->getResponse()->getData();
        if ($this->getResponse()->getMessages()->hasErrorEntities() && !isset($responseData['messages'])) {
            return false;
        }

        return true;
    }

    // ########################################

    public function failDetected($messageText)
    {
        parent::failDetected($messageText);

        $this->isResponseFailed = true;

        $message = Mage::getModel('M2ePro/Connector_Connection_Response_Message');
        $message->initFromPreparedData(
            $messageText,
            Ess_M2ePro_Model_Connector_Connection_Response_Message::TYPE_ERROR
        );

        foreach ($this->listingsProducts as $listingProduct) {
            $this->getLogger()->logListingProductMessage(
                $listingProduct,
                $message,
                Ess_M2ePro_Model_Log_Abstract::PRIORITY_HIGH
            );
        }
    }

    // ----------------------------------------

    public function eventAfterExecuting()
    {
        parent::eventAfterExecuting();

        if (!$this->isResponseFailed) {
            $this->inspectProducts();
        }
    }

    protected function inspectProducts()
    {
        $listingsProductsByStatus = array(
            Ess_M2ePro_Model_Listing_Product::STATUS_LISTED  => array(),
            Ess_M2ePro_Model_Listing_Product::STATUS_STOPPED => array(),
        );

        foreach ($this->successfulListingProducts as $listingProduct) {
            $listingsProductsByStatus[$listingProduct->getStatus()][$listingProduct->getId()] = $listingProduct;
        }

        $runner = Mage::getModel('M2ePro/Synchronization_Templates_Synchronization_Runner');
        $runner->setConnectorModel('Buy_Connector_Product_Dispatcher');
        $runner->setMaxProductsPerStep(100);

        $inspector = Mage::getModel('M2ePro/Buy_Synchronization_Templates_Synchronization_Inspector');

        foreach ($listingsProductsByStatus[Ess_M2ePro_Model_Listing_Product::STATUS_LISTED] as $listingProduct) {

            /** @var Ess_M2ePro_Model_Listing_Product $listingProduct */

            $configurator = Mage::getModel('M2ePro/Buy_Listing_Product_Action_Configurator');

            if ($inspector->isMeetStopRequirements($listingProduct)) {
                $runner->addProduct(
                    $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_STOP, $configurator
                );

                continue;
            }

            if ($inspector->isMeetReviseQtyRequirements($listingProduct)) {
                $configurator->reset();
                $configurator->allowQty();

                $runner->addProduct(
                    $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_REVISE, $configurator
                );
            }

            if ($inspector->isMeetRevisePriceRequirements($listingProduct)) {
                $configurator->reset();
                $configurator->allowPrice();

                $runner->addProduct(
                    $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_REVISE, $configurator
                );
            }
        }

        foreach ($listingsProductsByStatus[Ess_M2ePro_Model_Listing_Product::STATUS_STOPPED] as $listingProduct) {
            if (!$inspector->isMeetRelistRequirements($listingProduct)) {
                continue;
            }

            $configurator = Mage::getModel('M2ePro/Buy_Listing_Product_Action_Configurator');

            $runner->addProduct(
                $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_LIST, $configurator
            );
        }

        $runner->execute();
    }

    // ########################################

    protected function validateResponse()
    {
        $responseData = $this->getResponse()->getData();
        return isset($responseData['messages']) && is_array($responseData['messages']);
    }

    protected function processResponseData()
    {
        $responseData = $this->getPreparedResponseData();

        $responseMessages = array();

        foreach ($responseData['messages'] as $key => $value) {
            $responseMessages[(int)$key] = $value;
        }

        $globalMessages = array();

        if (isset($responseMessages[0]) && is_array($responseMessages[0])) {

            foreach ($responseMessages[0] as $messageData) {
                $message = Mage::getModel('M2ePro/Connector_Connection_Response_Message');
                $message->initFromPreparedData($messageData['text'], $messageData['type']);

                $globalMessages[] = $message;
            }

            unset($responseMessages[0]);
        }

        foreach ($this->listingsProducts as $listingProduct) {

            $messages = $globalMessages;

            if (isset($responseMessages[(int)$listingProduct->getId()]) &&
                is_array($responseMessages[(int)$listingProduct->getId()])) {

                foreach ($responseMessages[(int)$listingProduct->getId()] as $messageData) {
                    $message = Mage::getModel('M2ePro/Connector_Connection_Response_Message');
                    $message->initFromPreparedData($messageData['text'], $messageData['type']);

                    $messages[] = $message;
                }
            }

            if (!$this->processMessages($listingProduct, $messages) ||
                $this->getResponse()->getMessages()->hasErrorEntities()
            ) {
                continue;
            }

            $successParams = $this->getSuccessfulParams($listingProduct);
            $this->processSuccess($listingProduct, $successParams);

            $this->successfulListingProducts[] = $listingProduct;
        }
    }

    //----------------------------------------

    protected function processMessages(Ess_M2ePro_Model_Listing_Product $listingProduct, array $messages = array())
    {
        $hasError = false;

        foreach ($messages as $message) {

            /** @var Ess_M2ePro_Model_Connector_Connection_Response_Message $message */

            !$hasError && $hasError = $message->isError();

            $this->getLogger()->logListingProductMessage(
                $listingProduct, $message
            );
        }

        return !$hasError;
    }

    protected function processSuccess(Ess_M2ePro_Model_Listing_Product $listingProduct, array $params = array())
    {
        $this->getResponseObject($listingProduct)->processSuccess($params);

        $message = Mage::getModel('M2ePro/Connector_Connection_Response_Message');
        $message->initFromPreparedData(
            $this->getSuccessfulMessage($listingProduct),
            Ess_M2ePro_Model_Connector_Connection_Response_Message::TYPE_SUCCESS
        );

        $this->getLogger()->logListingProductMessage(
            $listingProduct, $message
        );

        $this->successfulListingProducts[$listingProduct->getId()] = $listingProduct;
    }

    //----------------------------------------

    protected function getSuccessfulParams(Ess_M2ePro_Model_Listing_Product $listingProduct)
    {
        return array();
    }

    //----------------------------------------

    /**
     * @param Ess_M2ePro_Model_Listing_Product $listingProduct
     * @return string
     */
    abstract protected function getSuccessfulMessage(Ess_M2ePro_Model_Listing_Product $listingProduct);

    // ########################################

    /**
     * @return Ess_M2ePro_Model_Buy_Listing_Product_Action_Logger
     */
    protected function getLogger()
    {
        if (is_null($this->logger)) {

            /** @var Ess_M2ePro_Model_Buy_Listing_Product_Action_Logger $logger */

            $logger = Mage::getModel('M2ePro/Buy_Listing_Product_Action_Logger');

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

    // ########################################

    public function getStatus()
    {
        return $this->getLogger()->getStatus();
    }

    // ########################################

    protected function getConfigurator(Ess_M2ePro_Model_Listing_Product $listingProduct)
    {
        if (empty($this->configurators[$listingProduct->getId()])) {

            $configurator = Mage::getModel('M2ePro/Buy_Listing_Product_Action_Configurator');
            $configurator->setData($this->params['products'][$listingProduct->getId()]['configurator']);

            $this->configurators[$listingProduct->getId()] = $configurator;
        }

        return $this->configurators[$listingProduct->getId()];
    }

    // ########################################

    /**
     * @param Ess_M2ePro_Model_Listing_Product $listingProduct
     * @return Ess_M2ePro_Model_Buy_Listing_Product_Action_Type_Response
     */
    protected function getResponseObject(Ess_M2ePro_Model_Listing_Product $listingProduct)
    {
        if (!isset($this->responsesObjects[$listingProduct->getId()])) {

            /* @var $response Ess_M2ePro_Model_Buy_Listing_Product_Action_Type_Response */
            $response = Mage::getModel(
                'M2ePro/Buy_Listing_Product_Action_Type_'.$this->getOrmActionType().'_Response'
            );

            $response->setParams($this->params['params']);
            $response->setListingProduct($listingProduct);
            $response->setConfigurator($this->getConfigurator($listingProduct));
            $response->setRequestData($this->getRequestDataObject($listingProduct));

            $this->responsesObjects[$listingProduct->getId()] = $response;
        }

        return $this->responsesObjects[$listingProduct->getId()];
    }

    /**
     * @param Ess_M2ePro_Model_Listing_Product $listingProduct
     * @return Ess_M2ePro_Model_Buy_Listing_Product_Action_RequestData
     */
    protected function getRequestDataObject(Ess_M2ePro_Model_Listing_Product $listingProduct)
    {
        if (!isset($this->requestsDataObjects[$listingProduct->getId()])) {

            /** @var Ess_M2ePro_Model_Buy_Listing_Product_Action_RequestData $requestData */
            $requestData = Mage::getModel('M2ePro/Buy_Listing_Product_Action_RequestData');

            $requestData->setData($this->params['products'][$listingProduct->getId()]['request']);
            $requestData->setListingProduct($listingProduct);

            $this->requestsDataObjects[$listingProduct->getId()] = $requestData;
        }

        return $this->requestsDataObjects[$listingProduct->getId()];
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
        return Mage::helper('M2ePro/Component_Buy')->getMarketplace();
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
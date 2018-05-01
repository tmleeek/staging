<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

abstract class Ess_M2ePro_Model_Buy_Connector_Product_Requester
    extends Ess_M2ePro_Model_Buy_Connector_Command_Pending_Requester
{
    /**
     * @var Ess_M2ePro_Model_Listing_Product[]
     */
    protected $listingsProducts = array();

    // ---------------------------------------

    /**
     * @var Ess_M2ePro_Model_Buy_Listing_Product_Action_Logger
     */
    protected $logger = NULL;

    // ---------------------------------------

    /**
     * @var Ess_M2ePro_Model_Buy_Listing_Product_Action_Type_Validator[]
     */
    protected $validatorsObjects = array();

    /**
     * @var Ess_M2ePro_Model_Buy_Listing_Product_Action_Type_Request[]
     */
    protected $requestsObjects = array();

    /**
     * @var Ess_M2ePro_Model_Buy_Listing_Product_Action_RequestData[]
     */
    protected $requestsDataObjects = array();

    // ########################################

    /**
     * @param array $params
     * @throws Ess_M2ePro_Model_Exception
     */
    public function __construct(array $params = array())
    {
        if (!isset($params['logs_action_id']) || !isset($params['status_changer'])) {
            throw new Ess_M2ePro_Model_Exception('Product Connector has not received some params');
        }

        parent::__construct($params);
    }

    // ########################################

    public function setListingsProducts(array $listingsProducts)
    {
        if (empty($listingsProducts)) {
            throw new Ess_M2ePro_Model_Exception_Logic('Product connector receive empty products list.');
        }

        /** @var Ess_M2ePro_Model_Account $account */
        $account = reset($listingsProducts)->getListing()->getAccount();

        $listingProductIds   = array();
        $actionConfigurators = array();

        foreach ($listingsProducts as $listingProduct) {

            if (!($listingProduct instanceof Ess_M2ePro_Model_Listing_Product)) {
                throw new Ess_M2ePro_Model_Exception('Product Connector has received invalid Product data type');
            }

            if ($account->getId() != $listingProduct->getListing()->getAccountId()) {
                throw new Ess_M2ePro_Model_Exception('Product Connector has received Products from different Accounts');
            }

            $listingProductIds[] = $listingProduct->getId();

            if (!is_null($listingProduct->getActionConfigurator())) {
                $actionConfigurators[$listingProduct->getId()] = $listingProduct->getActionConfigurator();
            } else {
                $actionConfigurators[$listingProduct->getId()] = Mage::getModel(
                    'M2ePro/Buy_Listing_Product_Action_Configurator'
                );
            }
        }

        /** @var Ess_M2ePro_Model_Mysql4_Listing_Product_Collection $listingProductCollection */
        $listingProductCollection = Mage::helper('M2ePro/Component_Buy')->getCollection('Listing_Product');
        $listingProductCollection->addFieldToFilter('id', array('in' => array_unique($listingProductIds)));

        /** @var Ess_M2ePro_Model_Listing_Product[] $actualListingsProducts */
        $actualListingsProducts = $listingProductCollection->getItems();

        if (empty($actualListingsProducts)) {
            throw new Ess_M2ePro_Model_Exception('All products were removed before connector processing');
        }

        foreach ($actualListingsProducts as $actualListingProduct) {
            $actualListingProduct->setActionConfigurator($actionConfigurators[$actualListingProduct->getId()]);
            $this->listingsProducts[$actualListingProduct->getId()] = $actualListingProduct;
        }

        $this->account = $account;

        return $this;
    }

    // ########################################

    protected function getProcessingRunnerModelName()
    {
        return 'Buy_Connector_Product_ProcessingRunner';
    }

    protected function getProcessingParams()
    {
        return array_merge(
            parent::getProcessingParams(),
            array(
                'request_data'        => $this->getRequestData(),
                'listing_product_ids' => array_keys($this->listingsProducts),
                'lock_identifier'     => $this->getLockIdentifier(),
                'action_type'         => $this->getActionType(),
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

            $this->prepareListingsProducts();

            if (empty($this->listingsProducts)) {
                return;
            }

            parent::process();

            if ($this->getResponser()->getStatus() != Ess_M2ePro_Helper_Data::STATUS_SUCCESS) {
                $this->getLogger()->setStatus($this->getResponser()->getStatus());
            }

        } catch (Exception $exception) {
            $this->unlockListingsProducts();
            throw $exception;
        }

        $this->unlockListingsProducts();
    }

    // ########################################

    public function getStatus()
    {
        return $this->getLogger()->getStatus();
    }

    // ########################################

    private function prepareListingsProducts()
    {
        $this->filterLockedListingsProducts();
        $this->lockListingsProducts();
        $this->validateAndFilterListingsProducts();
    }

    // ########################################

    protected function validateAndFilterListingsProducts()
    {
        foreach ($this->listingsProducts as $listingProduct) {

            $validator = $this->getValidatorObject($listingProduct);

            $validationResult = $validator->validate();

            foreach ($validator->getMessages() as $messageData) {

                $message = Mage::getModel('M2ePro/Connector_Connection_Response_Message');
                $message->initFromPreparedData($messageData['text'], $messageData['type']);

                $this->getLogger()->logListingProductMessage(
                    $listingProduct,
                    $message,
                    Ess_M2ePro_Model_Log_Abstract::PRIORITY_MEDIUM
                );
            }

            if ($validationResult) {
                continue;
            }

            $this->removeAndUnlockListingProduct($listingProduct);
        }
    }

    // ########################################

    protected function filterLockedListingsProducts()
    {
        foreach ($this->listingsProducts as $listingProduct) {

            $lockItem = Mage::getModel('M2ePro/Lock_Item_Manager');
            $lockItem->setNick(Ess_M2ePro_Helper_Component_Buy::NICK.'_listing_product_'.$listingProduct->getId());

            if ($listingProduct->isSetProcessingLock('in_action') || $lockItem->isExist()) {

                // M2ePro_TRANSLATIONS
                // Another Action is being processed. Try again when the Action is completed.
                $message = Mage::getModel('M2ePro/Connector_Connection_Response_Message');
                $message->initFromPreparedData(
                    'Another Action is being processed. Try again when the Action is completed.',
                    Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR
                );

                $this->getLogger()->logListingProductMessage(
                    $listingProduct,
                    $message,
                    Ess_M2ePro_Model_Log_Abstract::PRIORITY_MEDIUM
                );

                unset($this->listingsProducts[$listingProduct->getId()]);
            }
        }
    }

    protected function removeAndUnlockListingProduct(Ess_M2ePro_Model_Listing_Product $listingProduct)
    {
        $lockItem = Mage::getModel('M2ePro/Lock_Item_Manager');
        $lockItem->setNick(Ess_M2ePro_Helper_Component_Buy::NICK.'_listing_product_'.$listingProduct->getId());
        $lockItem->remove();

        unset($this->listingsProducts[$listingProduct->getId()]);
    }

    // ########################################

    protected function lockListingsProducts()
    {
        foreach ($this->listingsProducts as $listingProduct) {
            $lockItem = Mage::getModel('M2ePro/Lock_Item_Manager');
            $lockItem->setNick(Ess_M2ePro_Helper_Component_Buy::NICK.'_listing_product_'.$listingProduct->getId());

            $lockItem->create();
            $lockItem->makeShutdownFunction();
        }
    }

    protected function unlockListingsProducts()
    {
        foreach ($this->listingsProducts as $listingProduct) {

            /** @var $listingProduct Ess_M2ePro_Model_Listing_Product */

            $lockItem = Mage::getModel('M2ePro/Lock_Item_Manager');
            $lockItem->setNick(Ess_M2ePro_Helper_Component_Buy::NICK.'_listing_product_'.$listingProduct->getId());

            $lockItem->remove();
        }
    }

    // ########################################

    protected function getRequestData()
    {
        $data = array(
            'items' => array()
        );

        foreach ($this->listingsProducts as $listingProduct) {

            /** @var $listingProduct Ess_M2ePro_Model_Listing_Product */

            $requestObject = $this->getRequestObject($listingProduct);
            $requestDataRaw = $requestObject->getData();

            foreach ($requestObject->getWarningMessages() as $messageData) {

                $message = Mage::getModel('M2ePro/Connector_Connection_Response_Message');
                $message->initFromPreparedData($messageData['text'], Ess_M2ePro_Model_Log_Abstract::TYPE_WARNING);

                $this->getLogger()->logListingProductMessage($listingProduct,
                                                             $message,
                                                             Ess_M2ePro_Model_Log_Abstract::PRIORITY_MEDIUM);
            }

            $this->buildRequestDataObject($listingProduct,$requestDataRaw);

            $data['items'][$listingProduct->getId()] = $requestDataRaw;
            $data['items'][$listingProduct->getId()]['id'] = $listingProduct->getId();
        }

        return $data;
    }

    protected function getResponserParams()
    {
        $products = array();

        foreach ($this->listingsProducts as $listingProduct) {
            $products[$listingProduct->getId()] = array(
                'request'      => $this->getRequestDataObject($listingProduct)->getData(),
                'configurator' => $listingProduct->getActionConfigurator()->getData(),
            );
        }

        return array(
            'account_id'      => $this->account->getId(),
            'action_type'     => $this->getActionType(),
            'lock_identifier' => $this->getLockIdentifier(),
            'logs_action'     => $this->getLogsAction(),
            'logs_action_id'  => $this->getLogger()->getActionId(),
            'status_changer'  => $this->params['status_changer'],
            'params'          => $this->params,
            'products'        => $products
        );
    }

    // ########################################

    /**
     * @return Ess_M2ePro_Model_Buy_Listing_Product_Action_Logger
     */
    protected function getLogger()
    {
        if (is_null($this->logger)) {

            /** @var Ess_M2ePro_Model_Buy_Listing_Product_Action_Logger $logger */

            $logger = Mage::getModel('M2ePro/Buy_Listing_Product_Action_Logger');

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
     * @param Ess_M2ePro_Model_Listing_Product $listingProduct
     * @return Ess_M2ePro_Model_Buy_Listing_Product_Action_Type_Validator
     */
    protected function getValidatorObject(Ess_M2ePro_Model_Listing_Product $listingProduct)
    {
        if (!isset($this->validatorsObjects[$listingProduct->getId()])) {

            /** @var $validator Ess_M2ePro_Model_Buy_Listing_Product_Action_Type_Validator */
            $validator = Mage::getModel(
                'M2ePro/Buy_Listing_Product_Action_Type_'.$this->getOrmActionType().'_Validator'
            );

            $validator->setParams($this->params);
            $validator->setListingProduct($listingProduct);
            $validator->setConfigurator($listingProduct->getActionConfigurator());

            $this->validatorsObjects[$listingProduct->getId()] = $validator;
        }

        return $this->validatorsObjects[$listingProduct->getId()];
    }

    /**
     * @param Ess_M2ePro_Model_Listing_Product $listingProduct
     * @return Ess_M2ePro_Model_Buy_Listing_Product_Action_Type_Request
     */
    protected function getRequestObject(Ess_M2ePro_Model_Listing_Product $listingProduct)
    {
        if (!isset($this->requestsObjects[$listingProduct->getId()])) {

            /* @var $request Ess_M2ePro_Model_Buy_Listing_Product_Action_Type_Request */
            $request = Mage::getModel(
                'M2ePro/Buy_Listing_Product_Action_Type_'.$this->getOrmActionType().'_Request'
            );

            $request->setParams($this->params);
            $request->setListingProduct($listingProduct);
            $request->setConfigurator($listingProduct->getActionConfigurator());
            $request->setValidatorsData($this->getValidatorObject($listingProduct)->getData());

            $this->requestsObjects[$listingProduct->getId()] = $request;
        }

        return $this->requestsObjects[$listingProduct->getId()];
    }

    // ----------------------------------------

    /**
     * @param Ess_M2ePro_Model_Listing_Product $listingProduct
     * @return Ess_M2ePro_Model_Buy_Listing_Product_Action_RequestData
     */
    protected function getRequestDataObject(Ess_M2ePro_Model_Listing_Product $listingProduct)
    {
        return $this->requestsDataObjects[$listingProduct->getId()];
    }

    /**
     * @param Ess_M2ePro_Model_Listing_Product $listingProduct
     * @param array $data
     * @return Ess_M2ePro_Model_Buy_Listing_Product_Action_RequestData
     */
    protected function buildRequestDataObject(Ess_M2ePro_Model_Listing_Product $listingProduct, array $data)
    {
        if (!isset($this->requestsDataObjects[$listingProduct->getId()])) {

            /** @var Ess_M2ePro_Model_Buy_Listing_Product_Action_RequestData $requestData */
            $requestData = Mage::getModel('M2ePro/Buy_Listing_Product_Action_RequestData');

            $requestData->setData($data);
            $requestData->setListingProduct($listingProduct);

            $this->requestsDataObjects[$listingProduct->getId()] = $requestData;
        }

        return $this->requestsDataObjects[$listingProduct->getId()];
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
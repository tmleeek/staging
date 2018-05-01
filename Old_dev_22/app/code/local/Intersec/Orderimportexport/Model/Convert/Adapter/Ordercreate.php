<?php
/**
 * Ordercreate.php
 * CommerceExtensions @ InterSEC Solutions LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.commerceextensions.com/LICENSE-M1.txt
 *

 * @category   Orders
 * @package    Ordercreate
 * @copyright  Copyright (c) 2003-2009 CommerceExtensions @ InterSEC Solutions LLC. (http://www.commerceextensions.com)
 * @license    http://www.commerceextensions.com/LICENSE-M1.txt
 */ 

class Intersec_Orderimportexport_Model_Convert_Adapter_Ordercreate extends Varien_Object //Intersec_Orderimportexport_Model_Convert_Adapter_Importorders
{
    /**
     * Quote session object
     *
     * @var Mage_Adminhtml_Model_Session_Quote
     */
    protected $_session;

    /**
     * Re-collect quote flag
     *
     * @var boolean
     */
    protected $_needCollect;

    /**
     * Customer instance
     *
     * @var Mage_Customer_Model_Customer
     */
    protected $_customer;

    /**
     * Customer Address Form instance
     *
     * @var Mage_Customer_Model_Form
     */
    protected $_customerAddressForm;

    /**
     * Customer Form instance
     *
     * @var Mage_Customer_Model_Form
     */
    protected $_customerForm;

    /**
     * Array of validate errors
     *
     * @var array
     */
    protected $_errors = array();

    public function __construct()
    {
        $this->_session = Mage::getModel("intersec_orderimportexport/convert_adapter_sessionquote");
    }

    /**
     * Initialize data for price rules
     *
     * @return Mage_Adminhtml_Model_Sales_Order_Create
     */
    public function initRuleData()
    {
        Mage::register('rule_data', new Varien_Object(array(
            'store_id'  => $this->_session->getStore()->getId(),
            'website_id'  => $this->_session->getStore()->getWebsiteId(),
            'customer_group_id' => $this->getCustomerGroupId(),
        )));
        return $this;
    }

    /**
     * Set collect totals flag for quote
     *
     * @param   bool $flag
     * @return  Mage_Adminhtml_Model_Sales_Order_Create
     */
    public function setRecollect($flag)
    {
        $this->_needCollect = $flag;
        return $this;
    }


    /**
     * Quote saving
     *
     * @return Mage_Adminhtml_Model_Sales_Order_Create
     */
    public function saveQuote()
    {
        if (!$this->getQuote()->getId()) {
            return $this;
        }

        if ($this->_needCollect) {
            $this->getQuote()->collectTotals();
        }

        $this->getQuote()->save();
        return $this;
    }

    /**
     * Retrieve session model object of quote
     *
     * @return Mage_Adminhtml_Model_Session_Quote
     */
    public function getSession()
    {
        return $this->_session;
    }

    /**
     * Retrieve quote object model
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getSession()->getQuote();
    }

    public function getCustomerGroupId()
    {
        $groupId = $this->getQuote()->getCustomerGroupId();
        if (!$groupId) {
            $groupId = $this->getSession()->getCustomerGroupId();
        }
        return $groupId;
    }

    /**
     * Add product to current order quote
     * $product can be either product id or product model
     * $config can be either buyRequest config, or just qty
     *
     * @param   int|Mage_Catalog_Model_Product $product
     * @param   float|array|Varien_Object $config
     * @return  Mage_Adminhtml_Model_Sales_Order_Create
     */
    public function addProduct($product, $config = 1)
    {
        if (!is_array($config) && !($config instanceof Varien_Object)) {
            $config = array('qty' => $config);
        }
        $config = new Varien_Object($config);

        if (!($product instanceof Mage_Catalog_Model_Product)) {
            $productId = $product;
            $product = Mage::getModel('catalog/product')
                ->setStore($this->getSession()->getStore())
                ->setStoreId($this->getSession()->getStoreId())
                ->load($product);
            if (!$product->getId()) {
                Mage::throwException(
                    Mage::helper('adminhtml')->__('Failed to add a product to cart by id "%s".', $productId)
                );
            }
        }

        $stockItem = $product->getStockItem();
        if ($stockItem && $stockItem->getIsQtyDecimal()) {
            $product->setIsQtyDecimal(1);
        }

        $product->setCartQty($config->getQty());


        if($this->is14()) {
            $item = $this->getQuote()->addProduct($product, $config);
        } else {
            $item = $this->getQuote()->addProductAdvanced(
                $product,
                $config,
                Mage_Catalog_Model_Product_Type_Abstract::PROCESS_MODE_FULL
            );
            if (is_string($item)) {
                if ($product->getTypeId() != Mage_Catalog_Model_Product_Type_Grouped::TYPE_CODE) {
                    $item = $this->getQuote()->addProductAdvanced(
                        $product,
                        $config,
                        Mage_Catalog_Model_Product_Type_Abstract::PROCESS_MODE_LITE
                    );
                }
                if (is_string($item)) {
                    Mage::throwException($item);
                }
            }
        }
        $item->checkData();

        $this->setRecollect(true);
        return $this;
    }

    /**
     * Add multiple products to current order quote
     *
     * @param   array $products
     * @return  Mage_Adminhtml_Model_Sales_Order_Create|Exception
     */
    public function addProducts(array $products)
    {
        foreach ($products as $productId => $config) {
            $config['qty'] = isset($config['qty']) ? (float)$config['qty'] : 1;
            try {
                $this->addProduct($productId, $config);
            }
            catch (Mage_Core_Exception $e){
                $this->getSession()->addError($e->getMessage());
            }
            catch (Exception $e){
                return $e;
            }
        }
        return $this;
    }

    /**
     * Prepare options array for info buy request
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return array
     */
    protected function _prepareOptionsForRequest($item)
    {
        $newInfoOptions = array();
        if ($optionIds = $item->getOptionByCode('option_ids')) {
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                $option = $item->getProduct()->getOptionById($optionId);
                $optionValue = $item->getOptionByCode('option_'.$optionId)->getValue();

                $group = Mage::getSingleton('catalog/product_option')->groupFactory($option->getType())
                    ->setOption($option)
                    ->setQuoteItem($item);

                $newInfoOptions[$optionId] = $group->prepareOptionValueForRequest($optionValue);
            }
        }
        return $newInfoOptions;
    }

    /**
     * Retrieve oreder quote shipping address
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getShippingAddress()
    {
        return $this->getQuote()->getShippingAddress();
    }

    /**
     * Return Customer (Checkout) Form instance
     *
     * @return Mage_Customer_Model_Form
     */
    protected function _getCustomerForm()
    {
        if (is_null($this->_customerForm)) {
            $this->_customerForm = Mage::getModel('customer/form')
                ->setFormCode('adminhtml_checkout')
                ->ignoreInvisible(false);
        }
        return $this->_customerForm;
    }

    /**
     * Return Customer Address Form instance
     *
     * @return Mage_Customer_Model_Form
     */
    protected function _getCustomerAddressForm()
    {
        if (is_null($this->_customerAddressForm)) {
            $this->_customerAddressForm = Mage::getModel('customer/form')
                ->setFormCode('adminhtml_customer_address')
                ->ignoreInvisible(false);
        }
        return $this->_customerAddressForm;
    }

    /**
     * Set and validate Quote address
     * All errors added to _errors
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @param array $data
     * @return Mage_Adminhtml_Model_Sales_Order_Create
     */
    protected function _setQuoteAddress(Mage_Sales_Model_Quote_Address $address, array $data)
    {
        if ($this->is14()) {
            $address->addData($data);
        } else {
            $addressForm    = $this->_getCustomerAddressForm()
                ->setEntity($address)
                ->setEntityType(Mage::getSingleton('eav/config')->getEntityType('customer_address'))
                ->setIsAjaxRequest(true);

            // prepare request
            // save original request structure for files
            if ($address->getAddressType() == Mage_Sales_Model_Quote_Address::TYPE_SHIPPING) {
                $requestData  = array('order' => array('shipping_address' => $data));
                $requestScope = 'order/shipping_address';
            } else {
                $requestData = array('order' => array('billing_address' => $data));
                $requestScope = 'order/billing_address';
            }
            $request        = $addressForm->prepareRequest($requestData);
            $addressData    = $addressForm->extractData($request, $requestScope);
            $addressForm->restoreData($addressData);
        }
        return $this;
    }

    public function setShippingAddress($address)
    {
        if (is_array($address)) {
            $address['save_in_address_book'] = isset($address['save_in_address_book'])
                && !empty($address['save_in_address_book']);
            $shippingAddress = Mage::getModel('sales/quote_address')
                ->setData($address)
                ->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING);
            if (!$this->getQuote()->isVirtual()) {
                $this->_setQuoteAddress($shippingAddress, $address);
            }
            $shippingAddress->implodeStreetAddress();
        }
        if ($address instanceof Mage_Sales_Model_Quote_Address) {
            $shippingAddress = $address;
        }

        $this->setRecollect(true);
        $this->getQuote()->setShippingAddress($shippingAddress);
        return $this;
    }

    public function is14() {
        if (substr(Mage::getVersion(), 0, 3) === "1.4") {
            return true;
        }

        return false;
    }

    public function setShippingAsBilling($flag)
    {
        if ($flag) {
            $tmpAddress = clone $this->getBillingAddress();
            $tmpAddress->unsAddressId()
                ->unsAddressType();
            $data = $tmpAddress->getData();
            $data['save_in_address_book'] = 0; // Do not duplicate address (billing address will do saving too)
            $this->getShippingAddress()->addData($data);
        }
        $this->getShippingAddress()->setSameAsBilling($flag);
        $this->setRecollect(true);
        return $this;
    }

    /**
     * Retrieve quote billing address
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getBillingAddress()
    {
        return $this->getQuote()->getBillingAddress();
    }

    public function setBillingAddress($address)
    {
        if (is_array($address)) {
            $address['save_in_address_book'] = isset($address['save_in_address_book']) ? 1 : 0;
            $billingAddress = Mage::getModel('sales/quote_address')
                ->setData($address)
                ->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_BILLING);
            $this->_setQuoteAddress($billingAddress, $address);
            $billingAddress->implodeStreetAddress();
        }

        if ($this->getShippingAddress()->getSameAsBilling()) {
            $shippingAddress = clone $billingAddress;
            $shippingAddress->setSameAsBilling(true);
            $shippingAddress->setSaveInAddressBook(false);
            $address['save_in_address_book'] = 0;
            $this->setShippingAddress($address);
        }

        $this->getQuote()->setBillingAddress($billingAddress);
        return $this;
    }

    public function setShippingMethod($method)
    {
//        if ($method == 'flatrate_flatrate'){
        $this->getShippingAddress()->setShippingMethod('flatrate_flatrate');

        $this->setRecollect(true);
        return $this;
    }

    public function resetShippingMethod()
    {
        $this->getShippingAddress()->setShippingMethod(false);
        $this->getShippingAddress()->removeAllShippingRates();
        return $this;
    }

    /**
     * Collect shipping data for quote shipping address
     */
    public function collectShippingRates()
    {
        $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
        $this->collectRates();
        return $this;
    }

    public function collectRates()
    {
        $this->getQuote()->collectTotals();
    }

//    public function setPaymentMethod($method)
//    {
//        $this->getQuote()->getPayment()->setMethod($method);
//        return $this;
//    }

    public function setPaymentData($data)
    {
        if (!isset($data['method'])) {
            $data['method'] = $this->getQuote()->getPayment()->getMethod();
        }

        if ($data['method'] == 'purchaseorder') {
		    $myotherdata = $this->getQuote()->getPayment();
            $this->paymentImportData($myotherdata, $data);
        } else {
			$datapaymentarray = array(
			   'method' => 'imported',
			   'additional_information' => $data['method']
		   );
		   $myotherdata = $this->getQuote()->getPayment();
           $this->paymentImportData($myotherdata,$datapaymentarray);
        }

        return $this;
    }

    public function paymentImportData(&$payment, array $data)
    {
        $data = new Varien_Object($data);
		//COMMENTED OUT FOR 1.11.x EE or any EE with customer balance solves 
		//Fatal error: Call to a member function getQuote() on a non-object in app/code/core/Enterprise/CustomerBalance/Model/Observer.php on line 89
		/*
        Mage::dispatchEvent(
            'sales_quote_payment_import_data_before',
            array(
                $payment->_eventObject=>$payment,
                'input'=>$payment,
            )
        );
		*/

        $payment->setMethod($data->getMethod());
        $method = $payment->getMethodInstance();

        $method->assignData($data);

        return $payment;
    }


    public function applyCoupon($code)
    {
        $code = trim((string)$code);
        $this->getQuote()->setCouponCode($code);
        $this->setRecollect(true);
        return $this;
    }

    public function setAccountData($accountData)
    {

        $customer   = $this->getQuote()->getCustomer();

        if ($this->is14()) {
            $customer->addData($accountData);
        } else {
            $form       = $this->_getCustomerForm();
            $form->setEntity($customer);

            // emulate request
            $request = $form->prepareRequest($accountData);
            $data    = $form->extractData($request);
            $form->restoreData($data);

            $data = array();
            foreach ($form->getAttributes() as $attribute) {
                $code = sprintf('customer_%s', $attribute->getAttributeCode());
                $data[$code] = $customer->getData($attribute->getAttributeCode());
            }

            if (isset($data['customer_group_id'])) {
                $groupModel = Mage::getModel('customer/group')->load($data['customer_group_id']);
                $data['customer_tax_class_id'] = $groupModel->getTaxClassId();
                $this->setRecollect(true);
            }
        }
        $this->getQuote()->addData($data);
        return $this;
    }

    /**
     * Parse data retrieved from request
     *
     * @param   array $data
     * @return  Mage_Adminhtml_Model_Sales_Order_Create
     */
    public function importPostData($data)
    {
        if (is_array($data)) {
            $this->addData($data);
        } else {
            return $this;
        }

        if (isset($data['account'])) {
            $this->setAccountData($data['account']);
        }

        if (isset($data['comment'])) {
            $this->getQuote()->addData($data['comment']);
            if (empty($data['comment']['customer_note_notify'])) {
                $this->getQuote()->setCustomerNoteNotify(false);
            } else {
                $this->getQuote()->setCustomerNoteNotify(true);
            }
        }

        if (isset($data['billing_address'])) {
            $this->setBillingAddress($data['billing_address']);
        }

        if (isset($data['shipping_address'])) {
            $this->setShippingAddress($data['shipping_address']);
        }

        if (isset($data['shipping_method'])) {
            $this->setShippingMethod($data['shipping_method']);
        }

//        if (isset($data['payment_method'])) {
//            $this->setPaymentMethod($data['payment_method']);
//        }

        if (isset($data['coupon']['code'])) {
            $this->applyCoupon($data['coupon']['code']);
        }
        return $this;
    }

    /**
     * Check whether we need to create new customer (for another website) during order creation
     *
     * @param   Mage_Core_Model_Store $store
     * @return  boolean
     */
    protected function _customerIsInStore($store)
    {
        $customer = $this->getSession()->getCustomer();
        if ($customer->getWebsiteId() == $store->getWebsiteId()) {
            return true;
        }
        return $customer->isInStore($store);
    }

    /**
     * Set and validate Customer data
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return Mage_Adminhtml_Model_Sales_Order_Create
     */
    protected function _setCustomerData(Mage_Customer_Model_Customer $customer)
    {
        if ($this->is14()) return $this;

        $form = $this->_getCustomerForm();
        $form->setEntity($customer);

        // emulate request
        $request = $form->prepareRequest(array('order' => $this->getData()));
        $data    = $form->extractData($request, 'order/account');
        $form->restoreData($data);

        return $this;
    }

    /**
     * Prepare quote customer
     */
    public function _prepareCustomer()
    {
        $quote = $this->getQuote();
        if ($quote->getCustomerIsGuest()) {
            return $this;
        }

        $customer           = $this->getSession()->getCustomer();
        $store              = $this->getSession()->getStore();
        $customerIsInStore  = $this->_customerIsInStore($store);
        $billingAddress     = null;
        $shippingAddress    = null;

        if ($customer->getId()) {
            if (!$customerIsInStore) {
                $customer->setId(null)
                    ->setStore($store)
                    ->setDefaultBilling(null)
                    ->setDefaultShipping(null)
                    ->setPassword($customer->generatePassword());
                $this->_setCustomerData($customer);
            }
            if ($this->getBillingAddress()->getSaveInAddressBook() || !$customerIsInStore) {
                $billingAddress = $this->getBillingAddress()->exportCustomerAddress();
                $customerAddressId = $this->getBillingAddress()->getCustomerAddressId();
                if ($customerAddressId && $customer->getId()) {
                    $customer->getAddressItemById($customerAddressId)->addData($billingAddress->getData());
                } else {
                    $customer->addAddress($billingAddress);
                }
            }
            if (!$this->getQuote()->isVirtual() && ($this->getShippingAddress()->getSaveInAddressBook()
                || !$customerIsInStore)
            ) {
                $shippingAddress = $this->getShippingAddress()->exportCustomerAddress();
                $customerAddressId = $this->getShippingAddress()->getCustomerAddressId();
                if ($customerAddressId && $customer->getId()) {
                    $customer->getAddressItemById($customerAddressId)->addData($shippingAddress->getData());
                } elseif (!empty($customerAddressId)
                    && $billingAddress !== null
                    && $this->getBillingAddress()->getCustomerAddressId() == $customerAddressId
                ) {
                    $billingAddress->setIsDefaultShipping(true);
                } else {
                    $customer->addAddress($shippingAddress);
                }
            }

            if (is_null($customer->getDefaultBilling()) && $billingAddress) {
                $billingAddress->setIsDefaultBilling(true);
            }
            if (is_null($customer->getDefaultShipping())) {
                if ($this->getShippingAddress()->getSameAsBilling() && $billingAddress) {
                    $billingAddress->setIsDefaultShipping(true);
                } elseif ($shippingAddress) {
                    $shippingAddress->setIsDefaultShipping(true);
                }
            }
        } else {
            $customer->addData($this->getBillingAddress()->exportCustomerAddress()->getData())
                ->setPassword($customer->generatePassword())
                ->setStore($store);
            $customer->setEmail($this->_getNewCustomerEmail($customer));
            $this->_setCustomerData($customer);

            $customerBilling = $this->getBillingAddress()->exportCustomerAddress();
            $customerBilling->setIsDefaultBilling(true);
            $customer->addAddress($customerBilling);

            $shipping = $this->getShippingAddress();
            if (!$this->getQuote()->isVirtual() && !$shipping->getSameAsBilling()) {
                $customerShipping = $shipping->exportCustomerAddress();
                $customerShipping->setIsDefaultShipping(true);
                $customer->addAddress($customerShipping);
            } else {
                $customerBilling->setIsDefaultShipping(true);
            }
        }

        // set quote customer data to customer
        $this->_setCustomerData($customer);

        // set customer to quote and convert customer data to quote
        $quote->setCustomer($customer);

        // add user defined attributes to quote
        if (!$this->is14()) {
            $form = $this->_getCustomerForm()->setEntity($customer);
            foreach ($form->getUserAttributes() as $attribute) {
                $quoteCode = sprintf('customer_%s', $attribute->getAttributeCode());
                $quote->setData($quoteCode, $customer->getData($attribute->getAttributeCode()));
            }

            if ($customer->getId()) {
                // we should not change account data for existing customer, so restore it
                $this->_getCustomerForm()
                    ->setEntity($customer)
                    ->resetEntityData();
            } else {
                $quote->setCustomerId(true);
            }
        }


        return $this;
    }

    /**
     * Prepare item otions
     */
    protected function _prepareQuoteItems()
    {
        foreach ($this->getQuote()->getAllItems() as $item) {
            $options = array();
            $productOptions = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
            if ($productOptions) {
                $productOptions['info_buyRequest']['options'] = $this->_prepareOptionsForRequest($item);
                $options = $productOptions;
            }
            $addOptions = $item->getOptionByCode('additional_options');
            if ($addOptions) {
                $options['additional_options'] = unserialize($addOptions->getValue());
            }
            $item->setProductOrderOptions($options);
        }
        return $this;
    }

    protected function getServiceQuoteModel($quote) {
        return Mage::getModel("intersec_orderimportexport/convert_adapter_servicequote", $quote); //new Intersec_Orderimportexport_Model_Convert_Adapter_Servicequote($quote);
    }

    /**
     * Create new order
     *
     * @return Mage_Sales_Model_Order
     */
    public function createOrder()
    {
        $this->_prepareCustomer();
        $this->_validate();
        $quote = $this->getQuote();
        $this->_prepareQuoteItems();

        $service = $this->getServiceQuoteModel($quote); // Mage::getModel('sales/service_quote', $quote);
        if ($this->getSession()->getOrder()->getId()) {
            $oldOrder = $this->getSession()->getOrder();
            $originalId = $oldOrder->getOriginalIncrementId();
            if (!$originalId) {
                $originalId = $oldOrder->getIncrementId();
            }
            $orderData = array(
                'original_increment_id'     => $originalId,
                'relation_parent_id'        => $oldOrder->getId(),
                'relation_parent_real_id'   => $oldOrder->getIncrementId(),
                'edit_increment'            => $oldOrder->getEditIncrement()+1,
                'increment_id'              => $originalId.'-'.($oldOrder->getEditIncrement()+1)
            );
            $quote->setReservedOrderId($orderData['increment_id']);
            $service->setOrderData($orderData);
        }

        $order = $service->submit();
        if ((!$quote->getCustomer()->getId() || !$quote->getCustomer()->isInStore($this->getSession()->getStore()))
            && !$quote->getCustomerIsGuest()
        ) {
            $quote->getCustomer()->setCreatedAt($order->getCreatedAt());
            $quote->getCustomer()
                ->save()
                ->sendNewAccountEmail('registered', '', $quote->getStoreId());;
        }
        if ($this->getSession()->getOrder()->getId()) {
            $oldOrder = $this->getSession()->getOrder();

            $this->getSession()->getOrder()->setRelationChildId($order->getId());
            $this->getSession()->getOrder()->setRelationChildRealId($order->getIncrementId());
            $this->getSession()->getOrder()->cancel()
                ->save();
            $order->save();
        }
        if ($this->getSendConfirmation()) {
            $order->sendNewOrderEmail();
        }

        Mage::dispatchEvent('checkout_submit_all_after', array('order' => $order, 'quote' => $quote));

        return $order;
    }

    /**
     * Validate quote data before order creation
     *
     * @return Mage_Adminhtml_Model_Sales_Order_Create
     */
    protected function _validate()
    {
        $customerId = $this->getSession()->getCustomerId();
        if (is_null($customerId)) {
            Mage::throwException(Mage::helper('adminhtml')->__('Please select a customer.'));
        }

        if (!$this->getSession()->getStore()->getId()) {
            Mage::throwException(Mage::helper('adminhtml')->__('Please select a store.'));
        }
        $items = $this->getQuote()->getAllItems();

        if (count($items) == 0) {
            $this->_errors[] = Mage::helper('adminhtml')->__('You need to specify order items.');
        }

        foreach ($items as $item) {
            $messages = $item->getMessage(false);
            if ($item->getHasError() && is_array($messages) && !empty($messages)) {
                $this->_errors = array_merge($this->_errors, $messages);
            }
        }

        if (!$this->getQuote()->isVirtual()) {
            if (!$this->getQuote()->getShippingAddress()->getShippingMethod()) {
                $this->_errors[] = Mage::helper('adminhtml')->__('Shipping method must be specified.');
            }
        }

        if (!$this->getQuote()->getPayment()->getMethod()) {
            $this->_errors[] = Mage::helper('adminhtml')->__('Payment method must be specified.');
        } else {
            $method = $this->getQuote()->getPayment()->getMethodInstance();
            if (!$method) {
                $this->_errors[] = Mage::helper('adminhtml')->__('Payment method instance is not available.');
            } else {
                try {
                    $method->validate();
                } catch (Mage_Core_Exception $e) {
                    $this->_errors[] = $e->getMessage();
                }
            }
        }

        if (!empty($this->_errors)) {
            foreach ($this->_errors as $error) {
                $this->getSession()->addError($error);
            }
            Mage::throwException('');
        }
        return $this;
    }

    /**
     * Retrieve new customer email
     *
     * @param   Mage_Customer_Model_Customer $customer
     * @return  string
     */
    protected function _getNewCustomerEmail($customer)
    {
        $email = $this->getData('account/email');
        if (empty($email)) {
            $host = $this->getSession()
                ->getStore()
                ->getConfig(Mage_Customer_Model_Customer::XML_PATH_DEFAULT_EMAIL_DOMAIN);
            $account = $customer->getIncrementId() ? $customer->getIncrementId() : time();
            $email = $account.'@'. $host;
            $account = $this->getData('account');
            $account['email'] = $email;
            $this->setData('account', $account);
        }
        return $email;
    }
}
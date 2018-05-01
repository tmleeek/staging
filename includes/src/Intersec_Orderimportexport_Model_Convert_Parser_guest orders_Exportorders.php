<?php
/**
 * ExportOrders.php
 * CommerceThemes @ InterSEC Solutions LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.commercethemes.com/LICENSE-M1.txt
 *
 * @category   Orders
 * @package    Exportorders
 * @copyright  Copyright (c) 2003-2009 CommerceThemes @ InterSEC Solutions LLC. (http://www.commercethemes.com)
 * @license    http://www.commercethemes.com/LICENSE-M1.txt
 */ 

class Intersec_Orderimportexport_Model_Convert_Parser_Exportorders
    extends Mage_Eav_Model_Convert_Parser_Abstract
{
    const MULTI_DELIMITER = ' , ';

    protected $_resource;

    /**
     * Product collections per store
     *
     * @var array
     */
    protected $_collections;

    protected $_customerModel;
    protected $_customerAddressModel;
    protected $_newsletterModel;
    protected $_store;
    protected $_storeId;

    protected $_stores;

    /**
     * Website collection array
     *
     * @var array
     */
    protected $_websites;
    protected $_attributes = array();

    protected $_fields;

    public function getFields()
    {
        if (!$this->_fields) {
            $this->_fields = Mage::getConfig()->getFieldset('customer_dataflow', 'admin');
        }
        return $this->_fields;
    }

    /**
     * Retrieve customer model cache
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomerModel()
    {
        if (is_null($this->_customerModel)) {
            $object = Mage::getModel('customer/customer');
            $this->_customerModel = Mage::objects()->save($object);
        }
        return Mage::objects()->load($this->_customerModel);
    }

    /**
     * Retrieve customer address model cache
     *
     * @return Mage_Customer_Model_Address
     */
    public function getCustomerAddressModel()
    {
        if (is_null($this->_customerAddressModel)) {
            $object = Mage::getModel('customer/address');
            $this->_customerAddressModel = Mage::objects()->save($object);
        }
        return Mage::objects()->load($this->_customerAddressModel);
    }

    /**
     * Retrieve newsletter subscribers model cache
     *
     * @return Mage_Newsletter_Model_Subscriber
     */
    public function getNewsletterModel()
    {
        if (is_null($this->_newsletterModel)) {
            $object = Mage::getModel('newsletter/subscriber');
            $this->_newsletterModel = Mage::objects()->save($object);
        }
        return Mage::objects()->load($this->_newsletterModel);
    }

    /**
     * Retrieve current store model
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if (is_null($this->_store)) {
            try {
                $store = Mage::app()->getStore($this->getVar('store'));
            }
            catch (Exception $e) {
                $this->addException(Mage::helper('catalog')->__('Invalid store specified'), Varien_Convert_Exception::FATAL);
                throw $e;
            }
            $this->_store = $store;
        }
        return $this->_store;
    }

    /**
     * Retrieve store ID
     *
     * @return int
     */
    public function getStoreId()
    {
        if (is_null($this->_storeId)) {
            $this->_storeId = $this->getStore()->getId();
        }
        return $this->_storeId;
    }

    public function getStoreById($storeId)
    {
        if (is_null($this->_stores)) {
            $this->_stores = Mage::app()->getStores(true);
        }
        if (isset($this->_stores[$storeId])) {
            return $this->_stores[$storeId];
        }
        return false;
    }

    /**
     * Retrieve website model by id
     *
     * @param int $websiteId
     * @return Mage_Core_Model_Website
     */
    public function getWebsiteById($websiteId)
    {
        if (is_null($this->_websites)) {
            $this->_websites = Mage::app()->getWebsites(true);
        }
        if (isset($this->_websites[$websiteId])) {
            return $this->_websites[$websiteId];
        }
        return false;
    }

    /**
     * Retrieve eav entity attribute model
     *
     * @param string $code
     * @return Mage_Eav_Model_Entity_Attribute
     */
    public function getAttribute($code)
    {
        if (!isset($this->_attributes[$code])) {
            $this->_attributes[$code] = $this->getCustomerModel()->getResource()->getAttribute($code);
        }
        return $this->_attributes[$code];
    }

    /**
     * @return Mage_Catalog_Model_Mysql4_Convert
     */
    public function getResource()
    {
        if (!$this->_resource) {
            $this->_resource = Mage::getResourceSingleton('catalog_entity/convert');
                #->loadStores()
                #->loadProducts()
                #->loadAttributeSets()
                #->loadAttributeOptions();
        }
        return $this->_resource;
    }

    public function getCollection($storeId)
    {
        if (!isset($this->_collections[$storeId])) {
            $this->_collections[$storeId] = Mage::getResourceModel('customer/customer_collection');
            $this->_collections[$storeId]->getEntity()->setStore($storeId);
        }
        return $this->_collections[$storeId];
    }

    public function unparse()
    {
				#print_r($this->getData());
				$i="";
				//NOTE IN SOME RANDOM CASES ->addAttributeToSelect('increment_id') must be ->addAttributeToSelect('*') or data all blank
				if($this->getVar('date_from') != "" && $this->getVar('date_to') != "" && $this->getVar('filter_by_order_status') != "" ) {
				
					$date_from = $this->getVar('date_from'). " 00:00:00";
					$date_to = $this->getVar('date_to'). " 23:59:59";
					$orders = Mage::getModel('sales/order')->getCollection()
									->addAttributeToSelect('*')
									->addAttributeToFilter ( 'created_at' , array( "from" => $date_from, "to" => $date_to, "datetime" => true ))
									->addFieldToFilter ( 'status' , array( "eq" => $this->getVar('filter_by_order_status') ))
									->load();
				} else if($this->getVar('date_from') != "" && $this->getVar('date_to') != "" && $this->getVar('filter_by_store_id') != "") {
				
					$date_from = $this->getVar('date_from'). " 00:00:00";
					$date_to = $this->getVar('date_to'). " 23:59:59";
					$orders = Mage::getModel('sales/order')->getCollection()
									->addAttributeToSelect('*')
									->addAttributeToFilter ( 'store_id' , $this->getVar('filter_by_store_id'))
									->addAttributeToFilter ( 'created_at' , array( "from" => $date_from, "to" => $date_to, "datetime" => true ))
									->load();
				} else if($this->getVar('date_from') != "" && $this->getVar('date_to') != "" ) {
				
					$date_from = $this->getVar('date_from'). " 00:00:00";
					$date_to = $this->getVar('date_to'). " 23:59:59";
					$orders = Mage::getModel('sales/order')->getCollection()
									->addAttributeToSelect('*')
									->addAttributeToFilter ( 'created_at' , array( "from" => $date_from, "to" => $date_to, "datetime" => true ))
									->load();
				} else if($this->getVar('filter_by_order_status') != "" ) {
					$orders = Mage::getModel('sales/order')->getCollection()
									->addAttributeToSelect('*')
									->addFieldToFilter ( 'status' , array( "eq" => $this->getVar('filter_by_order_status') ))
									->load();
				} else if($this->getVar('filter_by_store_id') != "" ) {
					
					$orders = Mage::getModel('sales/order')->getCollection()
									->addAttributeToSelect('*')
									->addAttributeToFilter ( 'store_id' , $this->getVar('filter_by_store_id'))
									->load();
				} else {
					$orders = Mage::getModel('sales/order')->getCollection()->addAttributeToSelect('*')->load();
					#$entityIds = $this->getData();
				}
				
				$recordlimit = $this->getVar('recordlimit');
        $systemFields = array();
        foreach ($this->getFields() as $code=>$node) {
            if ($node->is('system')) {
                $systemFields[] = $code;
            }
        }

        
				$overallcount = 1;
        foreach ($orders as $entityId) {
				
				if ($overallcount < $recordlimit) {
				$order = Mage::getModel('sales/order')->load($entityId->getId());
				#print_r($order);
				//echo "CUSTOMER ID: " . $order->getData('customer_id');
				$customerId = $order->getData('customer_id');
				$store = $this->getStore();
				$storefromorder = Mage::app()->getStore($order->getStoreId());

        		$row = array();
        		$row['order_id'] = $order->getData('increment_id');
				//echo "isguest: " . $order->getCustomerIsGuest();
				/* THIS HERE GETS ARE INFORMATION IF ITS A GUEST THAT CHECKOUT AND NOT AN ACTUAL CUSTOMER */
				if ($order->getCustomerIsGuest()) {
					#$valueid = $store->getData('website_id');
					$valueid = $storefromorder->getData('website_id');
					$website = $this->getWebsiteById($valueid);
					//print($website);
          			$row['website'] = $website->getCode();
					#$row['website'] = $storefromorder->getCode();
					$row['group_id'] = $store->getGroup()->getName();
					if(method_exists($order, 'getBillingAddress') && method_exists($order->getBillingAddress(), 'getData')) {
						$row['prefix'] = $order->getBillingAddress()->getData('prefix');
						$row['firstname'] = $order->getBillingAddress()->getData('firstname');
						$row['middlename'] = $order->getBillingAddress()->getData('middlename');
						$row['lastname'] = $order->getBillingAddress()->getData('lastname');	
						$row['suffix'] = $order->getBillingAddress()->getData('suffix');	
						$row['password'] = "changeme";	
						$row['billing_prefix'] = $order->getBillingAddress()->getData('prefix');
						$row['billing_firstname'] = $order->getBillingAddress()->getData('firstname');
						$row['billing_middlename'] = $order->getBillingAddress()->getData('middlename');
						$row['billing_lastname'] = $order->getBillingAddress()->getData('lastname');
						$row['billing_suffix'] = $order->getBillingAddress()->getData('suffix');
						#$row['billing_street_full'] = $order->getBillingAddress()->getData('street');
						$row['billing_street_full'] = $order->getBillingAddress()->getStreet(1);
						$row['billing_street_2'] = $order->getBillingAddress()->getStreet(2);
						$row['billing_city'] = $order->getBillingAddress()->getData('city');
						$row['billing_region'] = $order->getBillingAddress()->getData('region');
						$row['billing_country'] = $order->getBillingAddress()->getData('country_id');
						$row['billing_postcode'] = $order->getBillingAddress()->getData('postcode');
						$row['billing_telephone'] = $order->getBillingAddress()->getData('telephone');
						$row['billing_company'] = $order->getBillingAddress()->getData('company');
						$row['billing_fax'] = $order->getBillingAddress()->getData('fax');
					} else {
						$row['prefix'] = $order->getData('prefix');
						$row['firstname'] = $order->getData('firstname');
						$row['middlename'] = $order->getData('middlename');
						$row['lastname'] = $order->getData('lastname');	
						$row['suffix'] = $order->getData('suffix');	
						$row['password'] = "changeme";	
						$row['billing_prefix'] = $order->getData('prefix');
						$row['billing_firstname'] = $order->getData('firstname');
						$row['billing_middlename'] = $order->getData('middlename');
						$row['billing_lastname'] = $order->getData('lastname');
						$row['billing_suffix'] = $order->getData('suffix');
						$row['billing_street_full'] = $order->getData('street');
						$row['billing_street_2'] = "";
						$row['billing_city'] = $order->getData('city');
						$row['billing_region'] = $order->getData('region');
						$row['billing_country'] = $order->getData('country_id');
						$row['billing_postcode'] = $order->getData('postcode');
						$row['billing_telephone'] = $order->getData('telephone');
						$row['billing_company'] = $order->getData('company');
						$row['billing_fax'] = $order->getData('fax');
					}
					//THIS CHECKS TO  MAKE SURE WE ALSO HAVE A SHIPPING ADDDRESS FOR THIS ORDER IN SOMECASE WE MAY NOT.
					if(method_exists($order, 'getShippingAddress') && method_exists($order->getShippingAddress(), 'getData')) {
						$row['shipping_prefix'] = $order->getShippingAddress()->getData('prefix');
						$row['shipping_firstname'] = $order->getShippingAddress()->getData('firstname');
						$row['shipping_middlename'] = $order->getShippingAddress()->getData('middlename');
						$row['shipping_lastname'] = $order->getShippingAddress()->getData('lastname');
						$row['shipping_suffix'] = $order->getShippingAddress()->getData('suffix');
						$row['shipping_street_full'] = $order->getShippingAddress()->getStreet(1);
					    $row['shipping_street_2'] = $order->getShippingAddress()->getStreet(2);
						$row['shipping_city'] = $order->getShippingAddress()->getData('city');
						$row['shipping_region'] = $order->getShippingAddress()->getData('region');
						$row['shipping_country'] = $order->getShippingAddress()->getData('country_id');
						$row['shipping_postcode'] = $order->getShippingAddress()->getData('postcode');
						$row['shipping_telephone'] = $order->getShippingAddress()->getData('telephone');
						$row['shipping_company'] = $order->getShippingAddress()->getData('company');
						$row['shipping_fax'] = $order->getShippingAddress()->getData('fax');
					} else {
						$row['shipping_prefix'] = $order->getData('prefix');
						$row['shipping_firstname'] = $order->getData('firstname');
						$row['shipping_middlename'] = $order->getData('middlename');
						$row['shipping_lastname'] = $order->getData('lastname');
						$row['shipping_suffix'] = $order->getData('suffix');
						$row['shipping_street_full'] = $order->getStreet(1);
						$row['shipping_street_2'] = $order->getStreet(2);
						$row['shipping_city'] = $order->getData('city');
						$row['shipping_region'] = $order->getData('region');
						$row['shipping_country'] = $order->getData('country_id');
						$row['shipping_postcode'] = $order->getData('postcode');
						$row['shipping_telephone'] = $order->getData('telephone');
						$row['shipping_company'] = $order->getData('company');
						$row['shipping_fax'] = $order->getData('fax');
					}
					$row['email'] = $order->getCustomerEmail();
					$row['customers_id'] = "0";	
					
				} else {
            $customer = $this->getCustomerModel()
                ->setData(array())
                ->load($customerId);
            /* @var $customer Mage_Customer_Model_Customer */
			
            $position = Mage::helper('catalog')->__('Line %d, Email: %s', ($i+1), $customer->getEmail());
            $this->setPosition($position);
			
			$customerdataarray = $customer->getData();
			if(!empty($customerdataarray)){
            foreach ($customer->getData() as $field => $value) {
						//echo "FIELDS: " . $field;
                if ($field == 'website_id') {
                    $website = $this->getWebsiteById($value);
                    if ($website === false) {
                        $website = $this->getWebsiteById(0);
                    }
                    $row['website'] = $website->getCode();
					#$row['website'] = $storefromorder->getCode();
                    continue;
                }
                if (in_array($field, $systemFields) || is_object($value)) {
                    continue;
                }

                $attribute = $this->getAttribute($field);
                if (!$attribute) {
                    continue;
                }

                if ($attribute->usesSource()) {

                    $option = $attribute->getSource()->getOptionText($value);
                    if ($value && empty($option)) {
                        $message = Mage::helper('catalog')->__("Invalid option id specified for %s (%s), skipping the record", $field, $value);
                        $this->addException($message, Mage_Dataflow_Model_Convert_Exception::ERROR);
                        continue;
                    }
                    if (is_array($option)) {
                        $value = join(self::MULTI_DELIMITER, $option);
                    } else {
                        $value = $option;
                    }
                    unset($option);
                }
                elseif (is_array($value)) {
                    continue;
                }
                $row[$field] = $value;
				$row['email'] = $order->getCustomerEmail();
            }
			$row['password'] = "changeme";	

            $defaultBillingId  = $customer->getDefaultBilling();
            $defaultShippingId = $customer->getDefaultShipping();

            $customerAddress = $this->getCustomerAddressModel();

            if (!$defaultBillingId) {
				/*
                foreach ($this->getFields() as $code=>$node) {
                    if ($node->is('billing')) {
						#echo "HERE1"  . $order->getBillingAddress()->getData('city'). "<br/>";
                        $row['billing_'.$code] = null;
                    }
                }*/
				if(method_exists($order->getBillingAddress(), 'getData')) {
					$row['prefix'] = $order->getBillingAddress()->getData('prefix');
					$row['firstname'] = $order->getBillingAddress()->getData('firstname');
					$row['middlename'] = $order->getBillingAddress()->getData('middlename');
					$row['lastname'] = $order->getBillingAddress()->getData('lastname');	
					$row['suffix'] = $order->getBillingAddress()->getData('suffix');	
					$row['password'] = "changeme";	
					$row['billing_prefix'] = $order->getBillingAddress()->getData('prefix');
					$row['billing_firstname'] = $order->getBillingAddress()->getData('firstname');
					$row['billing_middlename'] = $order->getBillingAddress()->getData('middlename');
					$row['billing_lastname'] = $order->getBillingAddress()->getData('lastname');
					$row['billing_suffix'] = $order->getBillingAddress()->getData('suffix');
					#$row['billing_street_full'] = $order->getBillingAddress()->getData('street');
					$row['billing_street_full'] = $order->getBillingAddress()->getStreet(1);
					$row['billing_street_2'] = $order->getBillingAddress()->getStreet(2);
					$row['billing_city'] = $order->getBillingAddress()->getData('city');
					$row['billing_region'] = $order->getBillingAddress()->getData('region');
					$row['billing_country'] = $order->getBillingAddress()->getData('country_id');
					$row['billing_postcode'] = $order->getBillingAddress()->getData('postcode');
					$row['billing_telephone'] = $order->getBillingAddress()->getData('telephone');
					$row['billing_company'] = $order->getBillingAddress()->getData('company');
					$row['billing_fax'] = $order->getBillingAddress()->getData('fax');
				} else {
					$row['prefix'] = $order->getData('prefix');
					$row['firstname'] = $order->getData('firstname');
					$row['middlename'] = $order->getData('middlename');
					$row['lastname'] = $order->getData('lastname');	
					$row['suffix'] = $order->getData('suffix');	
					$row['password'] = "changeme";	
					$row['billing_prefix'] = $order->getData('prefix');
					$row['billing_firstname'] = $order->getData('firstname');
					$row['billing_middlename'] = $order->getData('middlename');
					$row['billing_lastname'] = $order->getData('lastname');
					$row['billing_suffix'] = $order->getData('suffix');
					$row['billing_street_full'] = $order->getData('street');
					$row['billing_street_2'] = "";
					$row['billing_city'] = $order->getData('city');
					$row['billing_region'] = $order->getData('region');
					$row['billing_country'] = $order->getData('country_id');
					$row['billing_postcode'] = $order->getData('postcode');
					$row['billing_telephone'] = $order->getData('telephone');
					$row['billing_company'] = $order->getData('company');
					$row['billing_fax'] = $order->getData('fax');
				}
            }
            else {
                $customerAddress->load($defaultBillingId);

                foreach ($this->getFields() as $code=>$node) {
                    if ($node->is('billing')) {
												if($code == "street_full") {
                       	 $row['billing_street_full'] = $customerAddress->getDataUsingMethod("street_1");
                       	 $row['billing_street_2'] = $customerAddress->getDataUsingMethod("street_2");
												} else {
                       	 $row['billing_'.$code] = $customerAddress->getDataUsingMethod($code);
												}
                    }
                }
            }

            if (!$defaultShippingId) {
				/*
                foreach ($this->getFields() as $code=>$node) {
                    if ($node->is('shipping')) {
                        $row['shipping_'.$code] = null;
                    }
                }
				*/
				//THIS CHECKS TO  MAKE SURE WE ALSO HAVE A SHIPPING ADDDRESS FOR THIS ORDER IN SOMECASE WE MAY NOT.
				if(method_exists($order, 'getShippingAddress') && method_exists($order->getShippingAddress(), 'getData')) {
					$row['shipping_prefix'] = $order->getShippingAddress()->getData('prefix');
					$row['shipping_firstname'] = $order->getShippingAddress()->getData('firstname');
					$row['shipping_middlename'] = $order->getShippingAddress()->getData('middlename');
					$row['shipping_lastname'] = $order->getShippingAddress()->getData('lastname');
					$row['shipping_suffix'] = $order->getShippingAddress()->getData('suffix');
					$row['shipping_street_full'] = $order->getShippingAddress()->getStreet(1);
					$row['shipping_street_2'] = $order->getShippingAddress()->getStreet(2);
					$row['shipping_city'] = $order->getShippingAddress()->getData('city');
					$row['shipping_region'] = $order->getShippingAddress()->getData('region');
					$row['shipping_country'] = $order->getShippingAddress()->getData('country_id');
					$row['shipping_postcode'] = $order->getShippingAddress()->getData('postcode');
					$row['shipping_telephone'] = $order->getShippingAddress()->getData('telephone');
					$row['shipping_company'] = $order->getShippingAddress()->getData('company');
					$row['shipping_fax'] = $order->getShippingAddress()->getData('fax');
				} else {
					$row['shipping_prefix'] = $order->getData('prefix');
					$row['shipping_firstname'] = $order->getData('firstname');
					$row['shipping_middlename'] = $order->getData('middlename');
					$row['shipping_lastname'] = $order->getData('lastname');
					$row['shipping_suffix'] = $order->getData('suffix');
					$row['shipping_street_full'] = $order->getStreet(1);
					$row['shipping_street_2'] = $order->getStreet(2);
					$row['shipping_city'] = $order->getData('city');
					$row['shipping_region'] = $order->getData('region');
					$row['shipping_country'] = $order->getData('country_id');
					$row['shipping_postcode'] = $order->getData('postcode');
					$row['shipping_telephone'] = $order->getData('telephone');
					$row['shipping_company'] = $order->getData('company');
					$row['shipping_fax'] = $order->getData('fax');
				}
            }
            else {
                if ($defaultShippingId != $defaultBillingId) {
                    $customerAddress->load($defaultShippingId);
                }
                foreach ($this->getFields() as $code=>$node) {
                    if ($node->is('shipping')) {
												if($code == "street_full") {
                       	 $row['shipping_street_full'] = $customerAddress->getDataUsingMethod("street_1");
                       	 $row['shipping_street_2'] = $customerAddress->getDataUsingMethod("street_2");
												} else {
                         $row['shipping_'.$code] = $customerAddress->getDataUsingMethod($code);
												}
                    }
                }
            }
			
				} else {
					#$valueid = $store->getData('website_id');
					$valueid = $storefromorder->getData('website_id');
					$website = $this->getWebsiteById($valueid);
					//print($website);
					$row['website'] = $website->getCode();
					$row['email'] = $order->getCustomerEmail();
					#$row['website'] = $storefromorder->getCode();
					$row['group_id'] = $store->getGroup()->getName();
					#echo "NAME: " . $order->getBillingAddress()->getData('firstname');
					if(method_exists($order->getBillingAddress(), 'getData')) {
						$row['prefix'] = $order->getBillingAddress()->getData('prefix');
						$row['firstname'] = $order->getBillingAddress()->getData('firstname');
						$row['middlename'] = $order->getBillingAddress()->getData('middlename');
						$row['lastname'] = $order->getBillingAddress()->getData('lastname');	
						$row['suffix'] = $order->getBillingAddress()->getData('suffix');	
						$row['password'] = "changeme";	
						$row['billing_prefix'] = $order->getBillingAddress()->getData('prefix');
						$row['billing_firstname'] = $order->getBillingAddress()->getData('firstname');
						$row['billing_middlename'] = $order->getBillingAddress()->getData('middlename');
						$row['billing_lastname'] = $order->getBillingAddress()->getData('lastname');
						$row['billing_suffix'] = $order->getBillingAddress()->getData('suffix');
						#$row['billing_street_full'] = $order->getBillingAddress()->getData('street');
						$row['billing_street_full'] = $order->getBillingAddress()->getStreet(1);
						$row['billing_street_2'] = $order->getBillingAddress()->getStreet(2);
						$row['billing_city'] = $order->getBillingAddress()->getData('city');
						$row['billing_region'] = $order->getBillingAddress()->getData('region');
						$row['billing_country'] = $order->getBillingAddress()->getData('country_id');
						$row['billing_postcode'] = $order->getBillingAddress()->getData('postcode');
						$row['billing_telephone'] = $order->getBillingAddress()->getData('telephone');
						$row['billing_company'] = $order->getBillingAddress()->getData('company');
						$row['billing_fax'] = $order->getBillingAddress()->getData('fax');
					} else {
						$row['prefix'] = $order->getData('prefix');
						$row['firstname'] = $order->getData('firstname');
						$row['middlename'] = $order->getData('middlename');
						$row['lastname'] = $order->getData('lastname');	
						$row['suffix'] = $order->getData('suffix');	
						$row['password'] = "changeme";	
						$row['billing_prefix'] = $order->getData('prefix');
						$row['billing_firstname'] = $order->getData('firstname');
						$row['billing_middlename'] = $order->getData('middlename');
						$row['billing_lastname'] = $order->getData('lastname');
						$row['billing_suffix'] = $order->getData('suffix');
						$row['billing_street_full'] = $order->getData('street');
						$row['billing_street_2'] = "";
						$row['billing_city'] = $order->getData('city');
						$row['billing_region'] = $order->getData('region');
						$row['billing_country'] = $order->getData('country_id');
						$row['billing_postcode'] = $order->getData('postcode');
						$row['billing_telephone'] = $order->getData('telephone');
						$row['billing_company'] = $order->getData('company');
						$row['billing_fax'] = $order->getData('fax');
					}
					//THIS CHECKS TO  MAKE SURE WE ALSO HAVE A SHIPPING ADDDRESS FOR THIS ORDER IN SOMECASE WE MAY NOT.
					if(method_exists($order->getShippingAddress(), 'getData')) {
						$row['shipping_prefix'] = $order->getShippingAddress()->getData('prefix');
						$row['shipping_firstname'] = $order->getShippingAddress()->getData('firstname');
						$row['shipping_middlename'] = $order->getShippingAddress()->getData('middlename');
						$row['shipping_lastname'] = $order->getShippingAddress()->getData('lastname');
						$row['shipping_suffix'] = $order->getShippingAddress()->getData('suffix');
						$row['shipping_street_full'] = $order->getShippingAddress()->getStreet(1);
						$row['shipping_street_2'] = $order->getShippingAddress()->getStreet(2);
						$row['shipping_city'] = $order->getShippingAddress()->getData('city');
						$row['shipping_region'] = $order->getShippingAddress()->getData('region');
						$row['shipping_country'] = $order->getShippingAddress()->getData('country_id');
						$row['shipping_postcode'] = $order->getShippingAddress()->getData('postcode');
						$row['shipping_telephone'] = $order->getShippingAddress()->getData('telephone');
						$row['shipping_company'] = $order->getShippingAddress()->getData('company');
						$row['shipping_fax'] = $order->getShippingAddress()->getData('fax');
					} else {
						$row['shipping_prefix'] = $order->getData('prefix');
						$row['shipping_firstname'] = $order->getData('firstname');
						$row['shipping_middlename'] = $order->getData('middlename');
						$row['shipping_lastname'] = $order->getData('lastname');
						$row['shipping_suffix'] = $order->getData('suffix');
						$row['shipping_street_full'] = $order->getStreet(1);
						$row['shipping_street_2'] = $order->getStreet(2);
						$row['shipping_city'] = $order->getData('city');
						$row['shipping_region'] = $order->getData('region');
						$row['shipping_country'] = $order->getData('country_id');
						$row['shipping_postcode'] = $order->getData('postcode');
						$row['shipping_telephone'] = $order->getData('telephone');
						$row['shipping_company'] = $order->getData('company');
						$row['shipping_fax'] = $order->getData('fax');
					}
					$row['customers_id'] = "0";	
				}		
			} //if guest end if statement
						
						$customerId = $order->getData('customer_id');
						$customer = $this->getCustomerModel()
                ->setData(array())
                ->load($customerId);
						//print($customer);
            $store = $this->getStoreById($customer->getStoreId());
            if ($store === false) {
                $store = $this->getStoreById(0);
            }
            $row['created_in'] = $store->getCode();

            $newsletter = $this->getNewsletterModel()
                ->loadByCustomer($customer);
            $row['is_subscribed'] = ($newsletter->getId()
                && $newsletter->getSubscriberStatus() == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED)
                ? 1 : 0;
						/* ADDITIONAL ORDER DETAILS TO EXPORT */
						#print_r($order);
			/*
			$payment = $order->getPayment();
			#print_r($payment->getData('po_number'));
			$row['order_po_number'] = $payment->getData('po_number');
			*/
			$row['customers_id'] = $customerId;
            $row['order_id'] = $order->getData('increment_id');
            $row['created_at'] = $order->getData('created_at');
            $row['updated_at'] = $order->getData('updated_at');
            $row['tax_amount'] = $order->getData('tax_amount');
			$row['shipping_method'] = $order->getData('shipping_method');
            $row['shipping_amount'] = $order->getData('shipping_amount');
            $row['discount_amount'] = $order->getData('discount_amount');
            $row['subtotal'] = $order->getData('subtotal');
            $row['grand_total'] = $order->getData('grand_total');
            $row['total_paid'] = $order->getData('total_paid');
            $row['total_refunded'] = $order->getData('total_refunded');
            $row['total_qty_ordered'] = $order->getData('total_qty_ordered');
            $row['total_canceled'] = $order->getData('total_canceled');
            $row['total_invoiced'] = $order->getData('total_invoiced');
            $row['total_online_refunded'] = $order->getData('total_online_refunded');
            $row['total_offline_refunded'] = $order->getData('total_offline_refunded');
            $row['base_tax_amount'] = $order->getData('base_tax_amount');
            $row['base_shipping_amount'] = $order->getData('base_shipping_amount');
            $row['base_discount_amount'] = $order->getData('base_discount_amount');
            $row['base_subtotal'] = $order->getData('base_subtotal');
            $row['base_grand_total'] = $order->getData('base_grand_total');
            $row['base_total_paid'] = $order->getData('base_total_paid');
            $row['base_total_refunded'] = $order->getData('base_total_refunded');
            $row['base_total_qty_ordered'] = $order->getData('base_total_qty_ordered');
            $row['base_total_canceled'] = $order->getData('base_total_canceled');
            $row['base_total_invoiced'] = $order->getData('base_total_invoiced');
            $row['base_total_online_refunded'] = $order->getData('base_total_online_refunded');
            $row['base_total_offline_refunded'] = $order->getData('base_total_offline_refunded');
            $row['subtotal_refunded'] = $order->getData('subtotal_refunded');
            $row['subtotal_canceled'] = $order->getData('subtotal_canceled');
            $row['discount_refunded'] = $order->getData('discount_refunded');
            $row['discount_invoiced'] = $order->getData('discount_invoiced');
            $row['tax_refunded'] = $order->getData('tax_refunded');
            $row['tax_canceled'] = $order->getData('tax_canceled');
            $row['shipping_refunded'] = $order->getData('shipping_refunded');
            $row['shipping_canceled'] = $order->getData('shipping_canceled');
            $row['base_subtotal_refunded'] = $order->getData('base_subtotal_refunded');
            $row['base_subtotal_canceled'] = $order->getData('base_subtotal_canceled');
            $row['base_discount_refunded'] = $order->getData('base_discount_refunded');
            $row['base_discount_canceled'] = $order->getData('base_discount_canceled');
            $row['base_discount_invoiced'] = $order->getData('base_discount_invoiced');
            $row['base_tax_refunded'] = $order->getData('base_tax_refunded');
            $row['base_tax_canceled'] = $order->getData('base_tax_canceled');
            $row['base_shipping_refunded'] = $order->getData('base_shipping_refunded');
            $row['base_shipping_canceled'] = $order->getData('base_shipping_canceled');
            $row['subtotal_invoiced'] = $order->getData('subtotal_invoiced');
            $row['tax_invoiced'] = $order->getData('tax_invoiced');
            $row['shipping_invoiced'] = $order->getData('shipping_invoiced');
            $row['base_subtotal_invoiced'] = $order->getData('base_subtotal_invoiced');
            $row['base_tax_invoiced'] = $order->getData('base_tax_invoiced');
            $row['base_shipping_invoiced'] = $order->getData('base_shipping_invoiced');
            $row['shipping_tax_amount'] = $order->getData('shipping_tax_amount');
            $row['base_shipping_tax_amount'] = $order->getData('base_shipping_tax_amount');
            $row['shipping_tax_refunded'] = $order->getData('shipping_tax_refunded');
            $row['base_shipping_tax_refunded'] = $order->getData('base_shipping_tax_refunded');
			
						if($order->getStoreId() !="") {
						#$row['store_id'] = $this->getStoreId();
						$row['store_id'] = $order->getStoreId();
						} else {
						$row['store_id'] = $this->getVar('store');
						}
						
						#if(method_exists($order->getPayment(), 'getMethod')) {
							$row['payment_method'] = $order->getPayment()->getMethod();
						#} else {
							#$row['payment_method'] = "";
						#}
						$items = $order->getAllItems();
						$itemscount = 1;
						$finalproductsorder = "";
						$finalvaluesfromoptions="";
						$nonconfigurablesku="";
						$theseproductsarebundlesimples="";
						foreach ($items as $itemId => $item)
						{
							$finalvaluesfromoptions=""; //need to reset options values on each successive item
							/*
							 $row['product_name_'.$itemscount.''] = $item->getName();
							 $row['product_price_'.$itemscount.''] = $item->getPrice();
							 $row['product_sku_'.$itemscount.''] = $item->getSku();
							 $row['product_id_'.$itemscount.''] = $item->getProductId();
							 $row['product_qty_'.$itemscount.''] = $item->getQtyToInvoice(); 
							 #$row['product_qty_'.$itemscount.''] = $item->getQtyOrdered(); 
							 //echo "TEST: " . $item->getData('product_options');
							 $itemscount++;
							 */
							 #echo "SKU: " . $item->getSku();
							 #echo "TYPE: " . $item->getProductType();
							
							 $productoptionsfromconfigurables = unserialize($item->getData('product_options'));
							 
							 if(isset($productoptionsfromconfigurables['attributes_info'])) {
								foreach ($productoptionsfromconfigurables['attributes_info'] as $configurablesitemId => $configurablesitem)
								{
							 	 #print_r($configurablesitem);
								 $finalvaluesfromoptions .= $configurablesitem['value'] . ":";
								}
							 }
							 if ($item->getProductType() == "configurable") {
							 	 #print_r($item->getData('product_id'));
								 #$productconfigItem = Mage::getModel('catalog/product')->load($item->getData('product_id')); 
								 #$configskuforexport =  $productconfigItem->getSku();
								 $configskuforexport = $item->getSku();
								 $configskuforexport1 =  $item->getSku(). "config"; //for when oddly simple and config skus match
								 #echo "CONFIG SKU: " . $configskuforexport . "<br/>";
								 $nonconfigurablesku = $item->getProductOptionByCode('simple_sku');
							 }
							 if ($item->getProductType() == "bundle") {
							 	 
								 #print_r($item->getData());
								 $finalbundleoptions = "";
								 $theseproductsarebundlesimples = false;
								 #$productbundleItem = Mage::getModel('catalog/product')->load($item->getData('product_id')); 
								 #$bundleskuforexport =  $productbundleItem->getSku();
								 $bundleskuforexport = $item->getSku();
								 #echo "BUNDLE SKU: " . $bundleskuforexport . "<br/>";
								 #echo "BUNDLE SIMPLE SKU: " . $item->getSku();
								$bundle_simple_skus = explode('-',$item->getSku());
								foreach ($bundle_simple_skus as $bundle_single_sku_data) {
									if($bundleskuforexport != $bundle_single_sku_data && $nonconfigurablesku != $bundle_single_sku_data) {
										#echo "T: " . $bundle_single_sku_data . "~" .$item->getQtyOrdered(). "^";
										$finalbundleoptions .= $bundle_single_sku_data . "~" .$item->getQtyOrdered(). "^";
										$theseproductsarebundlesimples = true;
									}
								}
								$final_bundle_simple_skus = substr_replace($finalbundleoptions,"",-1);
								
								 $productoptionsfrombundles = unserialize($item->getData('product_options'));
							 	 #print_r($productoptionsfrombundles['info_buyRequest']);
								 
								 if(isset($productoptionsfrombundles['info_buyRequest']['bundle_option'])) {
									foreach ($productoptionsfrombundles['info_buyRequest'] as $bundleitemId => $bundleitem)
									{
									 #echo "T: " . $bundleitemId . "<br/>";
									 #echo "T2: " . $bundleitem . "<br/>";
									 #print_r($bundleitem);
									}
								 }
							 }
							 //for when simple and config oddly match
							 if($finalvaluesfromoptions !="" && $item->getProductType() == "configurable" && $nonconfigurablesku != $configskuforexport1) {
							 #if($finalvaluesfromoptions !="" && $item->getProductType() == "configurable" && $nonconfigurablesku != $configskuforexport) {
							 	 $okcleanedfinalvalues = substr_replace($finalvaluesfromoptions,"",-1);
								 
								 if($this->getVar('export_product_pricing') == "true") {
								 	$finalproductsorder .= $configskuforexport . ":" . $item->getQtyOrdered() . ":configurable:" . $okcleanedfinalvalues . "^" . $item->getPrice() . "^" . $item->getName() . "|";
								 } else {
									$finalproductsorder .= $configskuforexport . ":" . $item->getQtyOrdered() . ":configurable:" . $okcleanedfinalvalues . "|";
								 }
								 
							 } else if($item->getProductType() == "bundle") {
							   if($this->getVar('export_product_pricing') == "true") {
								 $finalproductsorder .= $bundleskuforexport . ":" . $item->getQtyOrdered() . ":bundle:" . $final_bundle_simple_skus;
							   } else {
								 $finalproductsorder .= $bundleskuforexport . ":" . $item->getQtyOrdered() . ":bundle:" . $final_bundle_simple_skus;
							   }
							 } else if($nonconfigurablesku != $item->getSku() && $theseproductsarebundlesimples != true) {
								 
								 #$arrayofsimpleproductcustomoptios = $item->getProductOptions();
								 $currentoptionscount=0;
								 #$productsimplecustomItem = Mage::getModel('catalog/product')->load($item->getData('product_id')); 
								 #if($productsimplecustomItem['has_options']) {
								 $itemsOptions = $item->getProductOptions();
								 #print_r($itemsOptions);
								 if(is_array($itemsOptions) && !empty($itemsOptions['options'])) {
								 #if($productsimplecustomItem->getTypeInstance(true)->hasOptions($productsimplecustomItem)) {
									$finalsimpleoptionsexport = "";
									foreach ($item->getProductOptions() as $option) 
									{
										#print_r($option);
										#echo "SKU: " . $item->getSku() . " ID: " . $order->getData('increment_id') .  "<br/>";
										if (isset($option[$currentoptionscount]['label']) && isset($option[$currentoptionscount]['value'])) {
										$itemsoptionscount=1;
											foreach ($option as $optionchoices) {
												if (isset($optionchoices['label'])) {
													//echo $optionchoices['label'];
													$finalsimpleoptionsexport .= $optionchoices['value']  .":";
													$itemsoptionscount++;
												}
											}
										$currentoptionscount++;
										
									 if($this->getVar('export_product_pricing') == "true") {
									 	$finalproductsorder .= $item->getSku() . ":" . $item->getQtyOrdered() . ":simple:" . substr_replace($finalsimpleoptionsexport,"",-1) . "^" . $item->getPrice() . "^" . $item->getName() . "|";
									 } else {
									 	$finalproductsorder .= $item->getSku() . ":" . $item->getQtyOrdered() . ":simple:" . substr_replace($finalsimpleoptionsexport,"",-1) . "|";
									 }
										}
									}
								 } else {
									 
									 if($this->getVar('export_product_pricing') == "true") {
										$finalproductsorder .= $item->getSku() . ":" . $item->getQtyOrdered() . ":" . $item->getPrice() . ":" . $item->getName() . "|";
									 } else {
									 	$finalproductsorder .= $item->getSku() . ":" . $item->getQtyOrdered() . "|";
									 }
								 }
							 }
							 
							 #$finalproductsorder .= $item->getSku() .":" . $item->getQtyOrdered() . "|";
						}
						$row['products_ordered'] = substr_replace($finalproductsorder,"",-1);
						
						//get product options
						/*
						print_r($item);
						foreach ($item->getProductOptions() as $option) 
						{
							//print_r($option);
							if (isset($option[$currentoptionscount]['label']) && isset($option[$currentoptionscount]['value'])) {
							$itemsoptionscount=1;
								foreach ($option as $optionchoices) {
									if (isset($optionchoices['label'])) {
										//echo $optionchoices['label'];
										$row['product_options_'.$itemsoptionscount.''] = $optionchoices['label'];
										$row['product_options_'.$itemsoptionscount.''] .= " (".$optionchoices['value'].")";
										$itemsoptionscount++;
									}
								}
							$currentoptionscount++;
							}
						}
						*/
			$row['order_status'] = $order->getStatus();
														
            $batchExport = $this->getBatchExportModel()
                ->setId(null)
                ->setBatchId($this->getBatchModel()->getId())
                ->setBatchData($row)
                ->setStatus(1)
                ->save();
						$overallcount+=1;
						}	#ends check on count of orders being exported
        }

        return $this;
    }

    public function getExternalAttributes()
    {
        $internal = array(
            'store_id',
            'entity_id',
            'website_id',
            'group_id',
            'created_in',
            'default_billing',
            'default_shipping',
            'country_id'
        );

        $entityTypeId = Mage::getSingleton('eav/config')->getEntityType('customer')->getId();
        $customerAttributes = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter($entityTypeId)
            ->load()->getIterator();

        $entityTypeId = Mage::getSingleton('eav/config')->getEntityType('customer_address')->getId();
        $addressAttributes = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter($entityTypeId)
            ->load()->getIterator();

        $attributes = array(
            'website'       => 'website',
            'email'         => 'email',
            'group'         => 'group',
            'create_in'     => 'create_in',
            'is_subscribed' => 'is_subscribed'
        );

        foreach ($customerAttributes as $attr) {
            $code = $attr->getAttributeCode();
            if (in_array($code, $internal) || $attr->getFrontendInput()=='hidden') {
                continue;
            }
            $attributes[$code] = $code;
        }
        $attributes['password_hash'] = 'password_hash';

        foreach ($addressAttributes as $attr) {
            $code = $attr->getAttributeCode();
            if (in_array($code, $internal) || $attr->getFrontendInput()=='hidden') {
                continue;
            }
            $attributes['billing_'.$code] = 'billing_'.$code;
        }
        $attributes['billing_country'] = 'billing_country';

        foreach ($addressAttributes as $attr) {
            $code = $attr->getAttributeCode();
            if (in_array($code, $internal) || $attr->getFrontendInput()=='hidden') {
                continue;
            }
            $attributes['shipping_'.$code] = 'shipping_'.$code;
        }
        $attributes['shipping_country'] = 'shipping_country';

        return $attributes;
    }

   

    /**
     * @deprecated not used anymore
     */
    public function parse()
    {
        $data = $this->getData();

        $entityTypeId = Mage::getSingleton('eav/config')->getEntityType('customer')->getId();
        $result = array();
        foreach ($data as $i=>$row) {
            $this->setPosition('Line: '.($i+1));
            try {

                // validate SKU
                if (empty($row['email'])) {
                    $this->addException(Mage::helper('customer')->__('Missing email, skipping the record'), Varien_Convert_Exception::ERROR);
                    continue;
                }
                $this->setPosition('Line: '.($i+1).', email: '.$row['email']);

                // try to get entity_id by sku if not set
                /*
                if (empty($row['entity_id'])) {
                    $row['entity_id'] = $this->getResource()->getProductIdBySku($row['email']);
                }
                */

                // if attribute_set not set use default
                if (empty($row['attribute_set'])) {
                    $row['attribute_set'] = 'Default';
                }

                // get attribute_set_id, if not throw error
                $row['attribute_set_id'] = $this->getAttributeSetId($entityTypeId, $row['attribute_set']);
                if (!$row['attribute_set_id']) {
                    $this->addException(Mage::helper('customer')->__("Invalid attribute set specified, skipping the record"), Varien_Convert_Exception::ERROR);
                    continue;
                }

                if (empty($row['group'])) {
                    $row['group'] = 'General';
                }

                if (empty($row['firstname'])) {
                    $this->addException(Mage::helper('customer')->__('Missing firstname, skipping the record'), Varien_Convert_Exception::ERROR);
                    continue;
                }
                //$this->setPosition('Line: '.($i+1).', Firstname: '.$row['firstname']);

                if (empty($row['lastname'])) {
                    $this->addException(Mage::helper('customer')->__('Missing lastname, skipping the record'), Varien_Convert_Exception::ERROR);
                    continue;
                }
                //$this->setPosition('Line: '.($i+1).', Lastname: '.$row['lastname']);

                /*
                // get product type_id, if not throw error
                $row['type_id'] = $this->getProductTypeId($row['type']);
                if (!$row['type_id']) {
                    $this->addException(Mage::helper('catalog')->__("Invalid product type specified, skipping the record"), Varien_Convert_Exception::ERROR);
                    continue;
                }
                */

                // get store ids
                $storeIds = $this->getStoreIds(isset($row['store']) ? $row['store'] : $this->getVar('store'));
                if (!$storeIds) {
                    $this->addException(Mage::helper('customer')->__("Invalid store specified, skipping the record"), Varien_Convert_Exception::ERROR);
                    continue;
                }

                // import data
                $rowError = false;
                foreach ($storeIds as $storeId) {
                    $collection = $this->getCollection($storeId);
                    //print_r($collection);
                    $entity = $collection->getEntity();

                    $model = Mage::getModel('customer/customer');
                    $model->setStoreId($storeId);
                    if (!empty($row['entity_id'])) {
                        $model->load($row['entity_id']);
                    }
                    foreach ($row as $field=>$value) {
                        $attribute = $entity->getAttribute($field);
                        if (!$attribute) {
                            continue;
                            #$this->addException(Mage::helper('catalog')->__("Unknown attribute: %s", $field), Varien_Convert_Exception::ERROR);

                        }

                        if ($attribute->usesSource()) {
                            $source = $attribute->getSource();
                            $optionId = $this->getSourceOptionId($source, $value);
                            if (is_null($optionId)) {
                                $rowError = true;
                                $this->addException(Mage::helper('customer')->__("Invalid attribute option specified for attribute %s (%s), skipping the record", $field, $value), Varien_Convert_Exception::ERROR);
                                continue;
                            }
                            $value = $optionId;
                        }
                        $model->setData($field, $value);

                    }//foreach ($row as $field=>$value)


                    $billingAddress = $model->getPrimaryBillingAddress();
                    $customer = Mage::getModel('customer/customer')->load($model->getId());


                    if (!$billingAddress  instanceof Mage_Customer_Model_Address) {
                        $billingAddress = new Mage_Customer_Model_Address();
                        if ($customer->getId() && $customer->getDefaultBilling()) {
                            $billingAddress->setId($customer->getDefaultBilling());
                        }
                    }

                    $regions = Mage::getResourceModel('directory/region_collection')->addRegionNameFilter($row['billing_region'])->load();
                    if ($regions) foreach($regions as $region) {
                       $regionId = $region->getId();
                    }

                    $billingAddress->setFirstname($row['firstname']);
                    $billingAddress->setLastname($row['lastname']);
                    $billingAddress->setCity($row['billing_city']);
                    $billingAddress->setRegion($row['billing_region']);
                    $billingAddress->setRegionId($regionId);
                    $billingAddress->setCountryId($row['billing_country']);
                    $billingAddress->setPostcode($row['billing_postcode']);
                    $billingAddress->setStreet(array($row['billing_street1'],$row['billing_street2']));
                    if (!empty($row['billing_telephone'])) {
                        $billingAddress->setTelephone($row['billing_telephone']);
                    }

                    if (!$model->getDefaultBilling()) {
                        $billingAddress->setCustomerId($model->getId());
                        $billingAddress->setIsDefaultBilling(true);
                        $billingAddress->save();
                        $model->setDefaultBilling($billingAddress->getId());
                        $model->addAddress($billingAddress);
                        if ($customer->getDefaultBilling()) {
                            $model->setDefaultBilling($customer->getDefaultBilling());
                        } else {
                            $shippingAddress->save();
                            $model->setDefaultShipping($billingAddress->getId());
                            $model->addAddress($billingAddress);

                        }
                    }

                    $shippingAddress = $model->getPrimaryShippingAddress();
                    if (!$shippingAddress instanceof Mage_Customer_Model_Address) {
                        $shippingAddress = new Mage_Customer_Model_Address();
                        if ($customer->getId() && $customer->getDefaultShipping()) {
                            $shippingAddress->setId($customer->getDefaultShipping());
                        }
                    }

                    $regions = Mage::getResourceModel('directory/region_collection')->addRegionNameFilter($row['shipping_region'])->load();
                    if ($regions) foreach($regions as $region) {
                       $regionId = $region->getId();
                    }

                    $shippingAddress->setFirstname($row['firstname']);
                    $shippingAddress->setLastname($row['lastname']);
                    $shippingAddress->setCity($row['shipping_city']);
                    $shippingAddress->setRegion($row['shipping_region']);
                    $shippingAddress->setRegionId($regionId);
                    $shippingAddress->setCountryId($row['shipping_country']);
                    $shippingAddress->setPostcode($row['shipping_postcode']);
                    $shippingAddress->setStreet(array($row['shipping_street1'], $row['shipping_street2']));
                    $shippingAddress->setCustomerId($model->getId());
                    if (!empty($row['shipping_telephone'])) {
                        $shippingAddress->setTelephone($row['shipping_telephone']);
                    }

                    if (!$model->getDefaultShipping()) {
                        if ($customer->getDefaultShipping()) {
                            $model->setDefaultShipping($customer->getDefaultShipping());
                        } else {
                            $shippingAddress->save();
                            $model->setDefaultShipping($shippingAddress->getId());
                            $model->addAddress($shippingAddress);

                        }
                        $shippingAddress->setIsDefaultShipping(true);
                    }

                    if (!$rowError) {
                        $collection->addItem($model);
                    }

                } //foreach ($storeIds as $storeId)

            } catch (Exception $e) {
                if (!$e instanceof Mage_Dataflow_Model_Convert_Exception) {
                    $this->addException(Mage::helper('customer')->__("Error during retrieval of option value: %s", $e->getMessage()), Mage_Dataflow_Model_Convert_Exception::FATAL);
                }
            }
        }
        $this->setData($this->_collections);
        return $this;
    }
}
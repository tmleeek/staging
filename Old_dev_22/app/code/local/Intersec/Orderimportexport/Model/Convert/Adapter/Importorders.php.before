<?php

error_reporting(E_ALL);

ini_set('display_errors', '1');

/**

 * ImportOrders.php

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

 * @package    Importorders

 * @copyright  Copyright (c) 2003-2009 CommerceThemes @ InterSEC Solutions LLC. (http://www.commercethemes.com)

 * @license    http://www.commercethemes.com/LICENSE-M1.txt

 */ 

 

class Intersec_Orderimportexport_Model_Convert_Adapter_Importorders

    extends Mage_Catalog_Model_Convert_Adapter_Product

{

	/**

     * Retrieve order create model

     *

     * @return  Mage_Adminhtml_Model_Sales_Order_Create

     */



    protected $_orderCreateModel;



    protected function _getOrderCreateModel()

    {

        if(!isset($this->_orderCreateModel)){

            $this->_orderCreateModel = Mage::getModel("intersec_orderimportexport/convert_adapter_ordercreate");

        }

        return $this->_orderCreateModel;

    }



    /**

     * Retrieve session object

     *

     * @return  Mage_Adminhtml_Model_Session_Quote

     */

    protected function _getSession()

    {

        return Mage::getSingleton('adminhtml/session_quote');

    }



    /**

     * Initialize order creation session data

     *

     * @param   array $data

     * @return  Mage_Adminhtml_Sales_Order_CreateController

     */

    protected function _initSession($data)

    {

        /**

         * Identify customer

         */

        if (!empty($data['customer_id'])) {

            $this->_getSession()->setCustomerId((int) $data['customer_id']);

        }



        /**

         * Identify store

         */

        if (!empty($data['store_id'])) {

            $this->_getSession()->setStoreId((int) $data['store_id']);

        }

        return $this;

    }



    /**

     * Processing quote data

     *

     * @param   array $data

     * @return  Yournamespace_Yourmodule_IndexController

     */

    protected function _processQuote($data = array())

    {

        /**

         * Saving order data

         */

        if (!empty($data['order'])) {

            $this->_getOrderCreateModel()->importPostData($data['order']);

        }

        /**

         * init first billing address, need for virtual products

         */

        $this->_getOrderCreateModel()->getBillingAddress();

        $this->_getOrderCreateModel()->setShippingAsBilling(true);

        /**

         * Adding products to quote from special grid and

         */

        if (!empty($data['add_products'])) {

            $this->_getOrderCreateModel()->addProducts($data['add_products']);

        }







        /**

         * Collecting shipping rates

         */



        $this->_getOrderCreateModel()->collectShippingRates();

        /**

         * Adding payment data

         */

//        if (!empty($data['payment'])) {

//            $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($data['payment']);

//        }



        $this->_getOrderCreateModel()->initRuleData()->saveQuote();



        return $this;

    }



	protected function _CreateCustomer($password,

                                       $company="",

                                       $city,

                                       $telephone,

                                       $fax="",

                                       $email,

                                       $prefix="",

                                       $firstname,

                                       $middlename="",

                                       $lastname,

                                       $suffix="",

                                       $taxvat="",

                                       $street1,

                                       $street2="",

                                       $postcode,

                                       $billing_region,

                                       $country_id,

                                       $storeId,

                                       $shipping_prefix="",

                                       $shipping_firstname,

                                       $shipping_middlename="",

                                       $shipping_lastname,

                                       $shipping_suffix="",

                                       $shipping_taxvat="",

                                       $shipping_street1,

                                       $shipping_street2="",

                                       $shipping_postcode,

                                       $shipping_region,

                                       $shipping_country_id,

                                       $shipping_city,

                                       $shipping_telephone,

                                       $shipping_fax,

                                       $shipping_company="",
									   
									   $website_id="") {

		

        #require_once '../app/Mage.php';

        #$app=Mage::app();

        #Mage::register('isSecureArea', true);





        $customer = Mage::getModel('customer/customer');



        ///make sure dob is mm/dd/yy

		

		$dob="5/6/88";

		$region = $billing_region;

		$region1 = $shipping_region;

		#$region=34;//getRegionByZip($postcode);

		$regions = Mage::getResourceModel('directory/region_collection')->addRegionNameFilter($billing_region)->load();

        if ($regions) {

             foreach($regions as $region) {

               $region = $region->getId();

             }

        } else {

                 $region = $billing_region;

        }

		$shipping_regions = Mage::getResourceModel('directory/region_collection')->addRegionNameFilter($shipping_region)->load();

        if ($shipping_regions) {

             foreach($shipping_regions as $regions) {

               $region1 = $regions->getId();

             }

        } else {

                $region1 = $shipping_region;

        }

		
		$street_r=array("0"=>$street1,"1"=>$street2);
		$shipping_street_r=array("0"=>$shipping_street1,"1"=>$shipping_street2);
		
		/*
		$customer_discount_group = Mage::getModel('customer/group')
											->getCollection()
											->addFieldToFilter('customer_group_code', array('eq' => $customer_group))
											->getFirstItem();
											
		#$group_id=1; ///double-check this 1 = general group
		$group_id = $customer_discount_group->getId();
		*/
			
		$group_id=1; ///double-check this 1 = general group

		if($website_id =="") {
			//$website_id=Mage::getModel('core/store')->load($storeId)->getWebsiteId();
			$website_id=1;
		}
				

		$default_billing="_item1";
		$default_shipping="_item2";
		$index="_item1";
		$index2="_item2";
		///end hard-coding//*/

		$salt="XD";
		//$hash=md5($salt . $password).":$salt";
		$hash="";

		if($password !="") {

			$customerData=array(

									"prefix"=>$prefix,

									"firstname"=>$firstname,

									"middlename"=>$middlename,

									"lastname"=>$lastname,

									"suffix"=>$suffix,

									"email"=>$email,

									"group_id"=>$group_id,

									"password_hash"=>$hash,

									"taxvat"=>$taxvat,

									"website_id"=>$website_id,

									"password"=>$password,

									"default_billing"=>$default_billing,

									"default_shipping"=>$default_shipping

							);

		} else {

			$customerData=array(

									"prefix"=>$prefix,

									"firstname"=>$firstname,

									"middlename"=>$middlename,

									"lastname"=>$lastname,

									"suffix"=>$suffix,

									"email"=>$email,

									"group_id"=>$group_id,

									"taxvat"=>$taxvat,

									"website_id"=>$website_id,

									"default_billing"=>$default_billing,

									"default_shipping"=>$default_shipping

							);

		}		

		$customer->addData($customerData); ///make sure this is enclosed in arrays correctly



		$addressData=array(

            "prefix"=>$prefix,

            "firstname"=>$firstname,

            "middlename"=>$middlename,

            "lastname"=>$lastname,

            "suffix"=>$suffix,

            "company"=>$company,

            "street"=>$street_r,

            "city"=>$city,

            "region"=>$region,

            "country_id"=>$country_id,

            "postcode"=>$postcode,

            "telephone"=>$telephone,

            "fax"=>$fax

        );



        //added shipping address

        $addressData2=array(

                "prefix"=>$shipping_prefix,

                "firstname"=>$shipping_firstname,

                "middlename"=>$shipping_middlename,

                "lastname"=>$shipping_lastname,

                "suffix"=>$shipping_suffix,

                "company"=>$shipping_company,

                "street"=>$shipping_street_r,

                "city"=>$shipping_city,

                "region"=>$region1,

                "country_id"=>$shipping_country_id,

                "postcode"=>$shipping_postcode,

                "telephone"=>$shipping_telephone,

                "fax"=>$shipping_fax

        );



		#sample comment

		$address = Mage::getModel('customer/address');

        $address->setData($addressData);

        /// We need set post_index for detect default addresses

        ///pretty sure index is a 0 or 1

        $address->setPostIndex($index);

        //added shipping address

        $shippingaddress = Mage::getModel('customer/address');

        //$customAddress = new Mage_Customer_Model_Address();

        $shippingaddress->setData($addressData2);



        $shippingaddress->setPostIndex($index2);

        $shippingaddress->setIsDefaultShipping(1);

        $shippingaddress->setSaveInAddressBook(1);

        $customer->addAddress($address);

        //added shipping address

        $customer->addAddress($shippingaddress);

        $customer->setIsSubscribed(false);



        ///make sure password is encrypted

        #$customer->setPassword($password);

        #$customer->setForceConfirmed(true);



        ///adminhtml_customer_prepare_save

        if($password !="") {

        //make sure password is encrypted

            $customer->setPassword($password);

            $customer->setForceConfirmed(true);

        } else {

            $customer->setPassword($customer->generatePassword(8));

        }

        ///adminhtml_customer_prepare_save

        $customer->save();

        $customer->sendNewAccountEmail();



        ///adminhtml_customer_save_after

        $customerId=$customer->getId();



        #Mage::log("customerId:$customerId");



        return $customerId;

    }

		

	public function _getStoreById($storeId)

    {

        if (is_null($this->_stores)) {

            $this->_stores = Mage::app()->getStores(true);

        }

        if (isset($this->_stores[$storeId])) {

            return $this->_stores[$storeId];

        }

        return false;

    }

	public function _array_replace(){ 

		$array=array();    

		$n=func_num_args(); 

		while ($n-- >0) { 

			$array+=func_get_arg($n); 

		} 

    	return $array; 

	}

    /**

     * Import Orders model

     *

     * @var Mage_Sales_Model_Convert_Adapter

     */

		 

    public function saveRow( array $importData )

	{
		#print_r($importData);
        $orderTypeId = Mage::getModel('eav/entity')->setType('order')->getTypeId();
        $resource = Mage::getSingleton('core/resource');
        $prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix');
        $write = $resource->getConnection('core_write');
        $read = $resource->getConnection('core_read');

        $orders = Mage::getModel('sales/order')->getCollection();
        $orderId = intval($importData['order_id']);
        $orders->addFilter("increment_id", $orderId);

        if ($orderId) {
            $ord = $orders->getFirstItem();
            if ($ord && $ord->getId()) {
			    if (strcasecmp(trim($this->getBatchParams('update_orders')), 'true') == 0) {
					$updateAdapter = Mage::getModel('intersec_orderimportexport/convert_adapter_updateorders');
					$updateAdapter->saveRow($importData);
					return;

				} else {
					return; //added this little check here because we would get duplicate key sql error because orderID already exists and was trying to reimport again

				}
            }
        }

        //update increment ID for store entity
        if(isset($importData['store_id']) && isset($importData['order_id']) && $orderTypeId != "" && $importData['order_id'] !="" && is_numeric($importData['order_id'])) {
            $finalorderID = $importData['order_id'] - 1;
            $write->query("UPDATE `".$prefix."eav_entity_store` SET increment_last_id = '".$finalorderID."' WHERE store_id = '". $importData['store_id'] ."' AND entity_type_id = '". $orderTypeId ."'");

        }

        $add_products_array = array();

        //customer
        $this->handleCustomer($importData, $customer, $customerId);

        //products
		$productcounter=1;
        $products_ordered = explode('|',$importData['products_ordered']);
        foreach ($products_ordered as $data) {

            $parts = explode(':',$data);
            $product_id = $parts[0];
            $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $product_id);

            if (!$product || !$product->getId()) {

                #$product = $this->createProduct($product_id, $importData['store_id'], "I-oe_productstandin");
				if($productcounter == 1) {
					 $product = $this->createProduct($product_id, $importData['store_id'], "I-oe_productstandin");
				} else if($productcounter > 1) {
					$keyforinsert = "I-oe_productstandin" . $productcounter;
					$product = $this->createProduct($product_id, $importData['store_id'], $keyforinsert);
				}
            }



	    try {
                $this->handleProduct($data, $add_products_array, $importData, $product, $productcounter);
            } catch (Exception $e) {

                echo "Order #".$importData['order_id']." ERROR PRODUCT DOESN'T EXIST: [sku]" . $product_id . " " . $e->getMessage();
                Mage::log(sprintf('Order #'.$importData['order_id'].' saving error: %s', $e->getMessage()), null,'ce_order_import_errors.log');

                //load dumby sku
                if($this->getBatchParams('dumby_sku') != "") {
                    $product = Mage::getModel('catalog/product')->loadByAttribute('sku',$this->getBatchParams('dumby_sku'));
                }

	    }

			#echo "OTHER ID: ". $product->getId();
            $add_products_array[$product->getId()]['qty'] = $parts[1];
			$productcounter++;

	}

        //shipping
        $final_shipping = $this->createShipping($customer);

        //assemble data structure
        $orderData = $this->assembleOrderData($customerId,
            $importData,
            isset($importData['order_id']) ? (string)$importData['order_id'] : "",
            $add_products_array,
            $customer,
            $final_shipping);

        //process order
        if (!empty($orderData)) {
            $this->_initSession($orderData['session']);
            $this->processOrder($orderData, $importData, $write, $prefix, $products_ordered, $read);
        }

    }



    protected $_importedSkus = array();



    public function createProduct($product_id, $store_id, $sku_key) {



        $productTypes = $this->_getProductTypes();



        #$key = 'I-oe_productstandin';

		$key = $sku_key;



        $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $key);

        if(!$product) {

            $product = Mage::getModel('catalog/product')

                ->setSku($key)//'sku', $product_id);

                ->setStoreId($store_id)

                ->setName("Imported product")

                ->setTypeId($productTypes['simple']) //use to be virtual needs to be simple or shipments are ignored

                ->setAttributeSetId(4)

                ->setTaxClassId(2)//makes products taxable as they all are

                ->setPrice(0);

            Mage::getModel('catalog/product_visibility');

            $product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);

            try {

                $product->save();

            } catch (Exception $e) {
	            Mage::throwException("Can't save standin product: " . $e->getMessage());

            }

        }



        #$this->_importedSkus[] = $product_id;



        return $product;

    }

	

	

    protected function _getProductTypes()

    {

        $productTypes = array();

        $options = Mage::getModel('catalog/product_type')

            ->getOptionArray();

        foreach ($options as $k => $v) {

            $productTypes[$k] = $k;

        }



        return $productTypes;

    }



    public function processOrder(&$orderData, $importData, $write, $prefix, $products_ordered, $read)
    {

        try {

            //payment
            $this->_processQuote($orderData);

            if (!empty($orderData['payment'])) {
                $this->createPayment($orderData);
            }

			try {
           		 $order1 = $this->createOrder($orderData, $importData);
			} catch (Exception $e) {
                echo "Order #".$importData['order_id']." ERROR SAVING ORDER: " . $e->getMessage();
			}

            #this is needed to not have orders repeat themselves. this is when you have items from previous order as part of new order
            Mage::getSingleton('adminhtml/session_quote')->clear();

            //Adding invoice/shipment creation
            #$this->updateOrderCreationTime($importData, $write, $prefix, $order1);
			if(method_exists($order1, 'getId')) {
           		$this->updateOrderCreationTime($importData, $write, $prefix, $order1);
			} else {
                echo "Order #".$importData['order_id']." ERROR SAVING ORDER SKIPPING ROW";
                Mage::log(sprintf('Order #' . $importData['order_id'] . ' ERROR SAVING ORDER SKIPPING ROW'), null, 'ce_order_import_errors.log');
				exit;
			}

            $itemQty = array();
            $select_qry = null;
            $newrowItemId = null;
            $item_id = null;

            //invoice
            if ($this->getBatchParams('create_invoice') == "true") {
                $this->createInvoice($order1, $itemQty, $e);
            }

            //shipment
            if ($this->getBatchParams('create_shipment') == "true") {
            	if ($importData['order_status'] == "complete" || $importData['order_status'] == "Complete") {
               	 $this->createShipment($order1, $itemQty, $importData);
				}
            }

            //Adding invoice/shipment creation
            $itemQty = array();
            foreach ($products_ordered as $data) {
                $importData = $this->processProductOrdered($data, $importData, $read, $prefix, $order1, $write, $itemQty);
            }

            //update creation time
            if (isset($importData['created_at'])) {
                $this->updateOrderCreationTime($importData, $write, $prefix, $order1);
            }

            //set as complete
            if ($importData['order_status'] == "complete" || $importData['order_status'] == "Complete") {
                $this->setOrderAsComplete($write, $prefix, $order1, $importData);
            } else {
                $this->setAllOtherOrderStatus($write, $prefix, $order1, $importData);
			}

            //invoice dates
            if (isset($importData['created_at']) && $this->getBatchParams('create_invoice') == "true") {
                $this->updateInvoiceDates($importData, $write, $prefix, $order1);
            }

            //shipping dates
            if (isset($importData['created_at']) && $this->getBatchParams('create_shipment') == "true") {
                $this->updateShippingDates($importData, $write, $prefix, $order1);
            }

            $key = 'I-oe_productstandin';
			$qouteproductcounter = 1;
            $skusForOrder = array();

            foreach($order1->getQuote()->getAllItems() as $quoteItem) {
                unset($additionalOptions);
				if($qouteproductcounter == 1) {
					$final_key = $key;
				} else {
					$final_key = $key . $qouteproductcounter;
				}
                if($quoteItem->getProduct()->getSku() == $final_key) {

                    $origSku = array_shift($this->_importedSkus);
					
					#echo "CSV SKU: " . $origSku;
                    $skusForOrder[] = $origSku;
                    $additionalOptions[] = array(
                        array(
                            'label' => "Original Sku",
                            'value' => $origSku
                        )
                    );

                    $quoteItem->addOption(
                        new Varien_Object(
                            array(
                                'product' => $quoteItem->getProduct(),
                                'code' => 'additional_options',
                                'value' => serialize($additionalOptions)
                            )
                        )
                    );

                    $option = $quoteItem->getOptionByCode('additional_options');
                    $option->save();
                    //$quoteItem->setAdditionalData(serialize($additionalOptions) );
                    $quoteItem->save();

                }
				$qouteproductcounter++;
            }


			$qouteproductcounter = 1;
            foreach($order1->getAllItems() as $orderItem) {
			    unset($options);
				if($qouteproductcounter == 1) {
					$final_key = $key;
				} else {
					$final_key = $key . $qouteproductcounter;
				}
				
                if($orderItem->getProduct()->getSku() == $final_key) {

                    $origSku = array_shift($skusForOrder);
					#echo "CSV SKU2: " . $origSku;
                    $options = $orderItem->getProductOptions();

                    $options['additional_options'] = array(array(
                        'label' => "Original Sku",
                        'value' => $origSku
                    ));

                    $orderItem->setProductOptions($options);
					
                }
                $orderItem->save();

				//$option = $quoteItem->getOptionByCode('additional_options');
				//$option->save();
				$qouteproductcounter++;
            }

			$productcounters=1;

            //sync product names/prices, and update shipping

            foreach ($products_ordered as $data) {

                $parts = explode(':', $data);

                $this->updateProductPrice($data, $read, $prefix, $order1, $importData, $write, $select_qry, $newrowItemId, $item_id, $e, $productcounters);

                $this->updateProductItemName($data, $importData, $read, $prefix, $order1, $write, $e, $select_qry, $productcounters);

                $this->updateShippingTotal($importData, $write, $prefix, $order1, $read, $parts, $itemQty, $e, $select_qry, $newrowItemId, $item_id);

				$productcounters++;

            }

            $this->_getSession()->clear();
            Mage::unregister('rule_data');
            Mage::log('Order Successfull', null, 'ce_order_import_errors.log');

        }

        catch (Exception $e) {
            #Mage::log(sprintf('Order saving error: %s', $e->getMessage()), Zend_Log::ERR);
            Mage::log(sprintf('Order #' . $importData['order_id'] . ' saving error: %s', $e->getMessage()), null, 'ce_order_import_errors.log');

        }

    }



    public function handleCustomer(&$importData, &$customer, &$customerId)

    {

        $valueid = Mage::getModel('core/store')->load($importData['store_id'])->getWebsiteId();
        #$website = $this->getWebsiteById($valueid);
        //DUPLICATE CUSTOMERS are appearing after import this value above is likely not found.. so we have a little check here

        if ($valueid < 1) {
            $valueid = 1;
        }
        // look up customer

		if (isset($importData['customer_id']) && $importData['customer_id'] != "") {
			$customerId = $importData['customer_id'];
			$customer = Mage::getModel('customer/customer')->setData(array())->load($customerId);
		} else {
        	$customer = Mage::getModel('customer/customer')
						->setWebsiteId($valueid)
						->loadByEmail($importData['email']);
			$customerId = $customer->getId();
		}
        #echo "VALUEID: " . $valueid;
        #echo "CUSTYID: " . $customer->getId();
        #print_r($importData);

        if ($customer->getId()) {

			if ($this->getBatchParams('update_customer_address') == "true") {
			
				$street_b = array("0"=>$importData['billing_street_full'], "1"=>isset($importData['billing_street_2']) ? $importData['billing_street_2'] : "");
				
				$billing_region = $importData['billing_region'];
				$shipping_region = $importData['shipping_region'];
				
				$regions = Mage::getResourceModel('directory/region_collection')->addRegionNameFilter($billing_region)->load();
				if ($regions) {
					 foreach($regions as $region) {
					   $region = $region->getId();
					 }
				} else {
					 $region = $billing_region;
				}
		
				$shipping_regions = Mage::getResourceModel('directory/region_collection')->addRegionNameFilter($shipping_region)->load();
				if ($shipping_regions) {
					 foreach($shipping_regions as $regions) {
					   $region1 = $regions->getId();
					 }
				} else {
					 $region1 = $shipping_region;
				}
				
				//custom code lets do address update - this works.. can expand for all fields
				$dataBilling = array(
					'company'  => $importData['billing_company'],
					'firstname'  => $importData['billing_firstname'],
					'middlename'  => $importData['billing_middlename'],
					'lastname'  => $importData['billing_lastname'],
					'suffix'  => $importData['billing_suffix'],
					'street'  => $street_b,
					'city'  => $importData['billing_city'],
					'region'  => $region,
					'country_id'  => $importData['billing_country'],
					'postcode'  => $importData['billing_postcode'],
					'telephone'  => $importData['billing_telephone'],
					'company'  => $importData['billing_company'],
					'fax'  => $importData['billing_fax'],
				);
				
				$dataShipping = array(
					'prefix'  => $importData['shipping_prefix'],
					'firstname'  => $importData['shipping_firstname'],
					'middlename'  => $importData['shipping_middlename'],
					'lastname'  => $importData['shipping_lastname'],
					'suffix'  => $importData['shipping_suffix'],
					'street_full'  => $importData['shipping_street_full'],
					'street_2'  => $importData['shipping_street_2'],
					'city'  => $importData['shipping_city'],
					'region'  => $region1,
					'country_id'  => $importData['shipping_country'],
					'postcode'  => $importData['shipping_postcode'],
					'telephone'  => $importData['shipping_telephone'],
					'company'  => $importData['shipping_company'],
					'fax'  => $importData['shipping_fax'],
				);
				
				$customerAddress = Mage::getModel('customer/address');
				
				//update billing address
				if ($defaultBillingId = $customer->getDefaultBilling()){
					#echo "HERE:";
					 $customerAddress->load($defaultBillingId); 
				} else {   
					#echo "HERE1:";
					 $customerAddress->setCustomerId($customer->getId())
									 ->setIsDefaultBilling('1')
									 ->setSaveInAddressBook('1');   
					 $customer->addAddress($customerAddress);
				}             
				
				try {
					$customerAddress->addData($dataBilling)->save();           
				} catch(Exception $e){
					Mage::log(sprintf('ERROR Billing Address Not Updated: '. $e->getMessage(), ''), null, 'ce_order_import_errors.log');
				}  
				
				
				//update shipping address
				if ($defaultShippingId = $customer->getDefaultShipping()){
					 $customerAddress->load($defaultShippingId); 
				} else {   
					 $customerAddress->setCustomerId($customer->getId())
									 ->setIsDefaultShipping('1')
									 ->setSaveInAddressBook('1');   
					 $customer->addAddress($customerAddress);
				}            
				
				try {
					$customerAddress->addData($dataShipping)->save();           
				} catch(Exception $e){
					Mage::log(sprintf('ERROR Shipping Address Not Updated: '. $e->getMessage(), ''), null, 'ce_order_import_errors.log');
				}
				
				$customer = Mage::getModel('customer/customer')->setData(array())->load($customerId);
			}
			
        } else {

            $customerId = $this->_CreateCustomer($importData['password'], $importData['billing_company'], $importData['billing_city'], $importData['billing_telephone'], $importData['billing_fax'], $importData['email'], $importData['billing_prefix'], $importData['billing_firstname'], $importData['billing_middlename'], $importData['billing_lastname'], $importData['billing_suffix'], $taxvat = "", $importData['billing_street_full'], isset($importData['billing_street_2']) ? $importData['billing_street_2'] : "", $importData['billing_postcode'], $importData['billing_region'], $importData['billing_country'], $importData['store_id'], $importData['shipping_prefix'], $importData['shipping_firstname'], $importData['shipping_middlename'], $importData['shipping_lastname'], $importData['shipping_suffix'], $taxvat = "", $importData['shipping_street_full'], isset($importData['shipping_street_2']) ? $importData['shipping_street_2'] : "", $importData['shipping_postcode'], $importData['shipping_region'], $importData['shipping_country'], $importData['shipping_city'], $importData['shipping_telephone'], $importData['shipping_fax'], $importData['shipping_company'], $valueid);

            $customer = Mage::getModel('customer/customer')->setData(array())->load($customerId);



        }

    }



    public function handleProduct(&$partsdata, &$add_products_array, $importData, &$product, $productcounter)

    {
		$parts = explode(':', $partsdata);
		
        if (method_exists($product, 'getTypeId')) {

            $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());
            $productstockItem = Mage::getModel('catalog/product')->load($product->getId());

            #echo "STOCK: " . $stockItem->getQty() . "<br/>";
            #echo "T: " . $productstockItem->getStockItem()->getManageStock();

            $super_attribute_order_values = array();
            $attributes = $values = $bundle_option_order_values = $bundle_option_qty_values = array();

            if(isset($parts[2])) {
            	$part_type = $parts[2];
			} else {
				$part_type =  "";
			}

			if(isset($parts[3])) {
            	$bundle_opt = $parts[3];
			} else {
            	$bundle_opt = "";
			}


            if ($productstockItem->getStockItem()->getIsInStock() && $productstockItem->getStockItem()->getManageStock() == 1 && $stockItem->getQty() != "0.0000" || $productstockItem->getStockItem()->getManageStock() == 0) {

			//if ($productstockItem->getStockItem()->getIsInStock() && $productstockItem->getStockItem()->getManageStock() == 1 && $stockItem->getQty() != "0.0000" || $productstockItem->getStockItem()->getManageStock() == 0 || ($part_type == "bundle" && $productstockItem->getStockItem()->getIsInStock()) || ($part_type == "configurable" && $productstockItem->getStockItem()->getIsInStock())) {

                if ($product->getTypeId() == "simple" && $part_type == "simple") {

                    $options = Mage::getModel("catalog/product_option")->getProductOptionCollection($product)->getItems();
                    $simple_custom_option_values = array();
                    $i = 1;
					
					
					$partsforsimple = explode('^', $partsdata);
					if(isset($partsforsimple[1]) && $partsforsimple[1] !="") {
						$simple_parts = explode(':', $partsforsimple[0]);
					} else {
						$simple_parts = $parts;
					}
					
                    foreach($options as $option) {
						
						#echo "TYPE: " . $option->getType();
						$itemtrue = false; //this is here because sometimes there are simple w/ custom options and 4 choices but only 3 in the export
						if($option->getType() == "drop_down" || $option->getType() == "radio" || $option->getType() == "checkbox" || $option->getType() == "multiple") {
							$values = $option->getValues();
							foreach ($values as $value) {
									#echo "TITLECSV: " . $simple_parts[$i + 2] . " - " . $value->getTitle() . "<br/>";
								if ($value->getTitle() == $simple_parts[$i + 2]) {
									#echo "TITLE: " . $value->getTitle();
									$simple_custom_option_values[$option->getId()] = $value->getId();
									$itemtrue = true;
								}
	
							}
						} else if($option->getType() == "field" || $option->getType() == "area" || $option->getType() == "date" || $option->getType() == "time" || $option->getType() == "date_time") {
								#echo "TITLE2: " . $simple_parts[$i + 2];
								$simple_custom_option_values[$option->getId()] = $simple_parts[$i + 2];
								$itemtrue = true;
						} else if($option->getType() == "file") {
								$simple_custom_option_values[$option->getId()] = "";
								#$itemtrue = true;
								#echo "FILE";
						}
						if($itemtrue == true) {
                       		$i = $i + 1;
						}

                    }

                    $add_products_array[$product->getId()]['options'] = $simple_custom_option_values;

                }





                if ($product->getTypeId() == "configurable" && $part_type == "configurable") {

					$partsforconfigurable = explode('^', $partsdata);
		
					if(isset($partsforconfigurable[2]) && $partsforconfigurable[2] !="") {
						$configurable_parts = explode(':', $partsforconfigurable[0]);
					} else {
						$configurable_parts = $parts;
					}

                    $config = $product->getTypeInstance(true);

					#$configattributearraydata = $this->copyConfigurableAttributes($config, $product, $parts);

                    #print_r($config->getConfigurableAttributesAsArray($product));

                    #$super_attribute_order_values = array_merge($configattributearraydata, $super_attribute_order_values);

                    $super_attribute_order_values = $this->_array_replace($super_attribute_order_values, $this->copyConfigurableAttributes($config, $product, $configurable_parts));

					

					

                    $add_products_array[$product->getId()]['super_attribute'] = $super_attribute_order_values;

                } else {
					
                    if ($stockItem->getQty() > 0 || $product->getTypeId() == "bundle") {



                        if ($product->getTypeId() == "bundle" && $part_type == "bundle") {

                            $this->handleBundle($product, $bundle_opt, $bundle_option_order_values, $bundle_option_qty_values, $add_products_array);

                        }
						
                		if ($part_type == "configurable") {
							
							#echo "HERE LETS DO IT";
							if($productcounter == 1) {
			
								$key = 'I-oe_productstandin';
			
								$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $key);
								#$product = $this->createProduct($parts[0], $importData['store_id'], "I-oe_productstandin");
			
								$add_products_array[$product->getId()]['qty'] = $parts[1];
			
								$this->_importedSkus[] = $parts[0];
			
								
			
							} else if($productcounter > 1) {
								$keyforinsert = "I-oe_productstandin" . $productcounter;
								$product = $this->createProduct($parts[0], $importData['store_id'], $keyforinsert);
								$add_products_array[$product->getId()]['qty'] = $parts[1];			
								$this->_importedSkus[] = $parts[0];
						
							}
						}	

                    } else {

                        #echo "Order #" . $importData['order_id'] . " WARNING PRODUCT OUT OF STOCK BUT STILL IMPORTING ORDER: [sku]" . $parts[0];
                        #Mage::log(sprintf('Order #' . $importData['order_id'] . ' PRODUCT OUT OF STOCK'), null, 'ce_order_import_errors.log');


                    }

                }

            } else {

				/*

                if ($product->getTypeId() == "configurable" && $part_type == "configurable") {



                    $config = $product->getTypeInstance(true);

					#$configattributearraydata = $this->copyConfigurableAttributes($config, $product, $parts);

                    #print_r($config->getConfigurableAttributesAsArray($product));

                    #$super_attribute_order_values = array_merge($configattributearraydata, $super_attribute_order_values);

                    $super_attribute_order_values = $this->_array_replace($super_attribute_order_values, $this->copyConfigurableAttributes($config, $product, $parts));

					

					

                    $add_products_array[$product->getId()]['super_attribute'] = $super_attribute_order_values;

                }

                if ($product->getTypeId() == "simple" && $part_type == "simple") {

                    $options = $product->getOptions();

                    //$add_products_array[$product->getId()]['super_attribute'] = $super_attribute_order_values;

                }

				*/
				
				if($productcounter == 1) {

					$key = 'I-oe_productstandin';

					$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $key);
					if (method_exists($product, 'getTypeId')) {
						$add_products_array[$product->getId()]['qty'] = $parts[1];
					} else {
						$product = $this->createProduct($parts[0], $importData['store_id'], $key);
						$add_products_array[$product->getId()]['qty'] = $parts[1];
					}

					$this->_importedSkus[] = $parts[0];

					

				} else if($productcounter > 1) {
					$keyforinsert = "I-oe_productstandin" . $productcounter;
                	$product = $this->createProduct($parts[0], $importData['store_id'], $keyforinsert);
					$add_products_array[$product->getId()]['qty'] = $parts[1];
					$this->_importedSkus[] = $parts[0];

				} 



            }



        } else {

            echo "Order #" . $importData['order_id'] . " ERROR PRODUCT DOESN'T EXIST: [sku]" . $parts[0];

            Mage::log(sprintf('ERROR PRODUCT NOT EXIST: [sku]' . $parts[0], ''), null, 'ce_order_import_errors.log');

            //load dumby sku

            if ($this->getBatchParams('dumby_sku') != "") {

                $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $this->getBatchParams('dumby_sku'));

            }

        }

    }



    public function handleBundle($product, $bundle_opt, $bundle_option_order_values, $bundle_option_qty_values, &$add_products_array)

    {

        $optionModel = Mage::getModel('bundle/option')->getResourceCollection()->setProductIdFilter($product->getId());

        foreach ($optionModel as $eachOption)

        {



            $selectionModel = Mage::getModel('bundle/selection')->setOptionId($eachOption->getData('option_id'))->getResourceCollection();

            #print_r($selectionModel->getData());

            foreach ($selectionModel as $eachselectionOption)

            {

                if ($eachselectionOption->getData('option_id') == $eachOption->getData('option_id')) {



                    #echo "SKU: " . $eachselectionOption->getData('sku');

                    #echo "ID: " . $eachselectionOption->getData('option_id') . " --- " .  $eachOption->getData('option_id');

                    /*

                    if($eachselectionOption->getData('sku') == "396115904") {

                        $bundle_option_order_values[$eachselectionOption->getData('option_id')] = $eachselectionOption->getData('selection_id');

                    }

                    */

                    //LIABG:1:bundle:amdphenom~1^500gb5400~1

                    $products_ordered_bundle = explode('^', $bundle_opt);



                    foreach ($products_ordered_bundle as $bundle_data) {



                        $bundle_parts = explode('~', $bundle_data);

                        if ($eachselectionOption->getData('sku') == $bundle_parts[0]) {

                            $bundle_option_order_values[$eachselectionOption->getData('option_id')] = $eachselectionOption->getData('selection_id');

                            $bundle_option_qty_values[$eachselectionOption->getData('option_id')] = $bundle_parts[1];

                        }

                    }

                }

            }

        }

        $add_products_array[$product->getId()]['bundle_option'] = $bundle_option_order_values;

        $add_products_array[$product->getId()]['bundle_option_qty'] = $bundle_option_qty_values;

    }





    public function copyConfigurableAttributes($config, $product, $parts)

    {

        $super_attribute_order_values = array();



        foreach ($config->getConfigurableAttributesAsArray($product) as $attributes)

        {

            foreach ($attributes["values"] as $values)

            {

                for($i = 3; $i <= 5; $i++) {

                    if (isset($parts[$i])) {

                        if ($parts[$i] == $values["label"]) {

                            $super_attribute_order_values[$attributes["attribute_id"]] = $values["value_index"];

                        }

                    }

                }

            }

        }



        return $super_attribute_order_values;

    }







    public function copyCustomOptions($config, $product, $parts)

    {

        $super_attribute_order_values = array();



        foreach ($config->getConfigurableAttributesAsArray($product) as $attributes)

        {

            foreach ($attributes["values"] as $values)

            {

                for($i = 3; $i <= 5; $i++) {

                    if (isset($parts[$i])) {

                        if ($parts[$i] == $values["label"]) {

                            $super_attribute_order_values[$attributes["attribute_id"]] = $values["value_index"];

                        }

                    }

                }

            }

        }



        return $super_attribute_order_values;

    }



    public function assembleOrderData($customerId, $importData, $orderDataId, $add_products_array, $customer, $final_shipping)

    {
		if (isset($importData['email_confirmation'])) {
			if (strtolower($importData['email_confirmation']) == "yes") {
				$csv_send_email = 1;
			} else {
				$csv_send_email = 0;
			}
		} else {
			$csv_send_email = 0;
		}
		
		if (isset($importData['order_comments'])) {
			if ($importData['order_comments'] != "") {
				$order_comments = $importData['order_comments'];
			} else {
				$order_comments = "API ORDER";
			}
		} else {
			$order_comments = "API ORDER";
		}
		
		if (isset($importData['order_po_number'])) {
			$orderDataId = $importData['order_po_number'];
		}
		
        $orderData = array(

            'session' => array(

                'customer_id' => $customerId,

                #'store_id'      => $customer->getStoreId(),

                'store_id' => $importData['store_id'],

            ),

            'payment' => array(

                'method' => $importData['payment_method'],

                'po_number' => $orderDataId,

            ),

            // 123456 denotes the product's ID value

            'add_products' => $add_products_array,

            'order' => array(

                'currency' => 'USD',

                'account' => array(

                    'group_id' => $customer->getGroupId(),

                    'email' => (string)$customer->getEmail(),

                ),

                'comment' => array('customer_note' => $order_comments),

                'send_confirmation' => $csv_send_email,

                'shipping_method' => $importData['shipping_method'],

                'billing_address' => array(

                    'customer_address_id' => $customer->getDefaultBillingAddress()->getEntityId(),

                    'prefix' => $customer->getDefaultBillingAddress()->getPrefix(),

                    'firstname' => $customer->getDefaultBillingAddress()->getFirstname(),

                    'middlename' => $customer->getDefaultBillingAddress()->getMiddlename(),

                    'lastname' => $customer->getDefaultBillingAddress()->getLastname(),

                    'suffix' => $customer->getDefaultBillingAddress()->getSuffix(),

                    'company' => $customer->getDefaultBillingAddress()->getCompany(),

                    'street' => $customer->getDefaultBillingAddress()->getStreet(),

                    'city' => $customer->getDefaultBillingAddress()->getCity(),

                    'country_id' => $customer->getDefaultBillingAddress()->getCountryId(),

                    'region' => $customer->getDefaultBillingAddress()->getRegion(),

                    'region_id' => $customer->getDefaultBillingAddress()->getRegionId(),

                    'postcode' => $customer->getDefaultBillingAddress()->getPostcode(),

                    'telephone' => $customer->getDefaultBillingAddress()->getTelephone(),

                    'fax' => $customer->getDefaultBillingAddress()->getFax(),

                ),



                'shipping_address' => array(

                    'customer_address_id' => $final_shipping['entity_id'],

                    'prefix' => $final_shipping['prefix'],

                    'firstname' => $final_shipping['firstname'],

                    'middlename' => $final_shipping['middlename'],

                    'lastname' => $final_shipping['lastname'],

                    'suffix' => $final_shipping['suffix'],

                    'company' => $final_shipping['company'],

                    'street' => $final_shipping['street'],

                    'city' => $final_shipping['city'],

                    'country_id' => $final_shipping['countryid'],

                    'region' => $final_shipping['region'],

                    'region_id' => $final_shipping['regionid'],

                    'postcode' => $final_shipping['postcode'],

                    'telephone' => $final_shipping['telephone'],

                    'fax' => $final_shipping['fax']

                ),

            ),

        );

        return $orderData;

    }



    public function createShipping($customer)

    {

        if (method_exists($customer, 'getDefaultShippingAddress')) {

            $final_shipping = array(

                'entity_id' => $customer->getDefaultShippingAddress()->getEntityId(),

                'prefix' => $customer->getDefaultShippingAddress()->getPrefix(),

                'firstname' => $customer->getDefaultShippingAddress()->getFirstname(),

                'middlename' => $customer->getDefaultShippingAddress()->getMiddlename(),

                'lastname' => $customer->getDefaultShippingAddress()->getLastname(),

                'suffix' => $customer->getDefaultShippingAddress()->getSuffix(),

                'company' => $customer->getDefaultShippingAddress()->getCompany(),

                'street' => $customer->getDefaultShippingAddress()->getStreet(),

                'city' => $customer->getDefaultShippingAddress()->getCity(),

                'countryid' => $customer->getDefaultShippingAddress()->getCountryId(),

                'region' => $customer->getDefaultShippingAddress()->getRegion(),

                'regionid' => $customer->getDefaultShippingAddress()->getRegionId(),

                'postcode' => $customer->getDefaultShippingAddress()->getPostcode(),

                'telephone' => $customer->getDefaultShippingAddress()->getTelephone(),

                'fax' => $customer->getDefaultShippingAddress()->getFax()

            );

            return $final_shipping;

        } else {

            #echo "BILLING";

            $final_shipping = array(

                'entity_id' => $customer->getDefaultBillingAddress()->getEntityId(),

                'prefix' => $customer->getDefaultBillingAddress()->getPrefix(),

                'firstname' => $customer->getDefaultBillingAddress()->getFirstname(),

                'middlename' => $customer->getDefaultBillingAddress()->getMiddlename(),

                'lastname' => $customer->getDefaultBillingAddress()->getLastname(),

                'suffix' => $customer->getDefaultBillingAddress()->getSuffix(),

                'company' => $customer->getDefaultBillingAddress()->getCompany(),

                'street' => $customer->getDefaultBillingAddress()->getStreet(),

                'city' => $customer->getDefaultBillingAddress()->getCity(),

                'countryid' => $customer->getDefaultBillingAddress()->getCountryId(),

                'region' => $customer->getDefaultBillingAddress()->getRegion(),

                'regionid' => $customer->getDefaultBillingAddress()->getRegionId(),

                'postcode' => $customer->getDefaultBillingAddress()->getPostcode(),

                'telephone' => $customer->getDefaultBillingAddress()->getTelephone(),

                'fax' => $customer->getDefaultBillingAddress()->getFax()

            );

            return $final_shipping;

        }

    }



    public function createOrder($orderData, $importData)

    {

        try {

            $order1 = $this->_getOrderCreateModel()

                ->importPostData($orderData['order'])

                ->createOrder();

            return $order1;

        } catch (Exception $e) {

            echo "ERROR Saving Order: " . $e->getMessage();

            #Mage::throwException(Mage::helper('catalog')->__('Order saving error: %s', $e->getMessage()));

            #Mage::log(sprintf('Order saving error: %s', $e->getMessage()), Zend_Log::ERR);

            Mage::log(sprintf('Order #' . $importData['order_id'] . ' saving error: %s', $e->getMessage()), null, 'ce_order_import_errors.log');

        }

    }



    public function createPayment($orderData)

    {

        try {

            $payment = $orderData['payment'];

            $this->_getOrderCreateModel()->setPaymentData($payment);

        } catch (Exception $e) {

            echo "ERROR With Payment: " . $e->getMessage();

            Mage::log(sprintf('ERROR With Payment: %s', $e->getMessage()), null, 'csv_order_import_errors.log');

        }

    }



    public function createInvoice($order1, $itemQty, &$e)

    { //Create a new Invoice for the order

        $invoice = $order1->prepareInvoice($itemQty);

        $invoice->register();

        try {

            $transactionSave = Mage::getModel('core/resource_transaction')

                ->addObject($invoice)

                ->addObject($invoice->getOrder())

                ->save();





        } catch (Mage_Core_Exception $e) {

            Mage::log("failed to create an invoice");

            Mage::log($e->getMessage());

            Mage::log($e->getTraceAsString());

            Mage::log(sprintf('failed to create an invoice: %s', $e->getMessage()), null, 'ce_order_import_errors.log');

        }

    }







    public function updateShippingTotal(&$importData, $write, $prefix, $order1, $read, $parts, &$itemQty, &$e, &$select_qry, &$newrowItemId, &$item_id)

    {

		#$parts = explode(':', $partsdata);

        if (isset($importData['subtotal']) && isset($importData['shipping_amount'])) {

            #$customordergrandtotalamt = $importData['subtotal'] + $importData['shipping_amount'];

			if(isset($importData['discount_amount']) && $importData['discount_amount'] != "0.0000" && $importData['discount_amount'] != "0.00") {

				$final_discount_amount = str_replace('-', '', $importData['discount_amount']);

            	$customordergrandtotalamtbeforediscount = $importData['subtotal'] + $importData['shipping_amount'] + $importData['tax_amount'];

				$customordergrandtotalamt = $customordergrandtotalamtbeforediscount - $final_discount_amount;

			} else {

				$final_discount_amount = "0.00";

            	$customordergrandtotalamt = $importData['subtotal'] + $importData['shipping_amount'] + $importData['tax_amount'];

			}

            #$custom_order_base_price_w_o_tax = $importData['subtotal'] - $importData['tax_amount'];

            $custom_order_base_price_w_o_tax = $importData['subtotal'];



            $base_shipping_tax_amount = $importData['shipping_amount'] - $importData['base_shipping_amount'];

            $subtotal_w_o_tax = $custom_order_base_price_w_o_tax + $importData['base_shipping_amount'];

            #$base_tax_amount = $subtotal_w_o_tax * .10;

            #$base_tax_amount = $subtotal_w_o_tax * $tax_percent;

            $base_tax_amount = $importData['tax_amount'];



            #if (Mage::getVersion() > '1.4.0.1') {

			$verChecksplit = explode(".",Mage::getVersion());

			if ($verChecksplit[1] > 4) {
			//fix for 1.4.1.1
			#if ($verChecksplit[1] >= 4 && $verChecksplit[2] > 0) {

                try {

                    if ($this->getBatchParams('create_invoice') == "true") {

                        $write->query("UPDATE `" . $prefix . "sales_flat_order` SET base_subtotal = '" . $custom_order_base_price_w_o_tax . "', base_tax_amount = '" . $base_tax_amount . "', tax_amount = '" . $base_tax_amount . "', tax_invoiced = '" . $base_tax_amount . "', base_grand_total = '" . $customordergrandtotalamt . "', base_total_paid = '" . $customordergrandtotalamt . "', total_paid = '" . $customordergrandtotalamt . "', base_total_invoiced = '" . $customordergrandtotalamt . "', total_invoiced = '" . $customordergrandtotalamt . "', subtotal = '" . $custom_order_base_price_w_o_tax . "', base_subtotal_incl_tax = '" . $importData['subtotal'] . "', subtotal_incl_tax = '" . $importData['subtotal'] . "', grand_total = '" . $customordergrandtotalamt . "', base_shipping_amount = '" . $importData['base_shipping_amount'] . "', base_shipping_tax_amount = '" . $base_shipping_tax_amount . "', shipping_tax_amount = '" . $base_shipping_tax_amount . "', shipping_amount = '" . $importData['base_shipping_amount'] . "', shipping_invoiced = '" . $importData['shipping_amount'] . "', base_shipping_invoiced = '" . $importData['base_shipping_amount'] . "', shipping_incl_tax = '" . $importData['shipping_amount'] . "', base_shipping_incl_tax = '" . $importData['shipping_amount'] . "', discount_amount = '" . $final_discount_amount . "', base_discount_amount = '" . $final_discount_amount . "' WHERE entity_id = '" . $order1->getId() . "' AND store_id = '" . $importData['store_id'] . "'");



                        //EXTRA DATA SET FOR PRODUCT/ORDER DATA ON INVOICES

                        $write_qry3 = $write->query("UPDATE `" . $prefix . "sales_flat_invoice` SET base_subtotal = '" . $custom_order_base_price_w_o_tax . "', base_tax_amount = '" . $base_tax_amount . "', tax_amount = '" . $base_tax_amount . "', subtotal = '" . $custom_order_base_price_w_o_tax . "', base_subtotal_incl_tax = '" . $importData['subtotal'] . "', subtotal_incl_tax = '" . $importData['subtotal'] . "', base_grand_total = '" . $customordergrandtotalamt . "', grand_total = '" . $customordergrandtotalamt . "', base_shipping_amount = '" . $importData['base_shipping_amount'] . "', shipping_amount = '" . $importData['base_shipping_amount'] . "', shipping_incl_tax = '" . $importData['shipping_amount'] . "', base_shipping_incl_tax = '" . $importData['shipping_amount'] . "', discount_amount = '" . $final_discount_amount . "', base_discount_amount = '" . $final_discount_amount . "' WHERE order_id = '" . $order1->getId() . "' AND store_id = '" . $importData['store_id'] . "'");



                        $write_qry4 = $write->query("UPDATE `" . $prefix . "sales_flat_invoice_grid` SET base_grand_total = '" . $customordergrandtotalamt . "', grand_total = '" . $customordergrandtotalamt . "' WHERE order_id = '" . $order1->getId() . "' AND store_id = '" . $importData['store_id'] . "'");



                    } else {



                        $write_qry2 = $write->query("UPDATE `" . $prefix . "sales_flat_order` SET base_subtotal = '" . $custom_order_base_price_w_o_tax . "', base_tax_amount = '" . $base_tax_amount . "', tax_amount = '" . $base_tax_amount . "', base_grand_total = '" . $customordergrandtotalamt . "', subtotal = '" . $custom_order_base_price_w_o_tax . "', base_subtotal_incl_tax = '" . $importData['subtotal'] . "', subtotal_incl_tax = '" . $importData['subtotal'] . "', grand_total = '" . $customordergrandtotalamt . "', base_shipping_amount = '" . $importData['base_shipping_amount'] . "', base_shipping_tax_amount = '" . $base_shipping_tax_amount . "', shipping_tax_amount = '" . $base_shipping_tax_amount . "', shipping_amount = '" . $importData['base_shipping_amount'] . "', shipping_incl_tax = '" . $importData['shipping_amount'] . "', base_shipping_incl_tax = '" . $importData['shipping_amount'] . "', discount_amount = '" . $final_discount_amount . "', base_discount_amount = '" . $final_discount_amount . "' WHERE entity_id = '" . $order1->getId() . "' AND store_id = '" . $importData['store_id'] . "'");

                    }

                } catch (Exception $e) {

                    echo "ERROR: " . $e->getMessage();

                    Mage::log(sprintf('Order #' . $importData['order_id'] . ' saving error: %s', $e->getMessage()), null, 'ce_order_import_errors.log');

                }



                $shippingMethod = $importData['shipping_method'];

                if ($shippingMethod != 'flatrate_flatrate') {



                    $addressId = $order1->getQuote()->getShippingAddress()->getId();

                    #$shippingDescription = "Imported - " . $shippingMethod;
                    $shippingDescription = $shippingMethod;

                    try {

                        $write->query(

                            "UPDATE `" . $prefix . "sales_flat_order` " .

                                " SET shipping_method = 'imported_imported'"

                                . ", shipping_description = '" . $shippingDescription . "'"

                                . " WHERE entity_id = " . $order1->getId());



                        $write->query(

                            "UPDATE `" . $prefix . "sales_flat_quote_address` " .

                                " SET shipping_method = 'imported_imported'"

                                . ", shipping_description = '" . $shippingDescription . "'"

                                . " WHERE address_id = " . $addressId);



                        $write->query(

                            "UPDATE `" . $prefix . "sales_flat_quote_shipping_rate` " .

                                " SET code = 'imported_imported'"

                                . ", carrier = 'imported'"

                                . ", carrier_title = 'Imported'"

                                . ", method = 'imported'"

                                . ", method_description = '" . $shippingMethod . "'"

                                . ", method_title = '" . $shippingMethod . "'"

                                . " WHERE address_id = '" . $addressId . "'");



                    } catch (Exception $e) {

                        echo "ERROR: " . $e->getMessage();

                        Mage::log(sprintf('Order #' . $importData['order_id'] . ' saving error: %s', $e->getMessage()), null, 'ce_order_import_errors.log');

                    }

                }





            } else {

                $write_qry2 = $write->query("UPDATE `" . $prefix . "sales_order` SET base_subtotal = '" . $custom_order_base_price_w_o_tax . "', base_grand_total = '" . $customordergrandtotalamt . "', subtotal = '" . $custom_order_base_price_w_o_tax . "', grand_total = '" . $customordergrandtotalamt . "' WHERE entity_id = '" . $order1->getId() . "' AND store_id = '" . $importData['store_id'] . "'");

            }



            $write_qry3 = $write->query("UPDATE `" . $prefix . "sales_order_tax` SET amount = '" . $base_tax_amount . "', base_amount = '" . $base_tax_amount . "', base_real_amount = '" . $base_tax_amount . "'  WHERE order_id = '" . $order1->getId() . "'");



            //UPDATE FOR SALES GRID VIEW -- sales -> orders
			if ($this->getBatchParams('create_invoice') == "true") {
            	$write_qry3 = $write->query("UPDATE `" . $prefix . "sales_flat_order_grid` SET base_total_paid = '" . $customordergrandtotalamt . "', total_paid = '" . $customordergrandtotalamt . "', base_grand_total = '" . $customordergrandtotalamt . "', grand_total = '" . $customordergrandtotalamt . "' WHERE entity_id = '" . $order1->getId() . "' AND store_id = '" . $importData['store_id'] . "'");
				
            	$write_qry3 = $write->query("UPDATE `" . $prefix . "sales_flat_order_payment` SET base_shipping_captured = '" . $importData['base_shipping_amount'] . "', shipping_captured = '" . $importData['base_shipping_amount'] . "', base_shipping_amount = '" . $importData['base_shipping_amount'] . "', shipping_amount = '" . $importData['base_shipping_amount'] . "', base_amount_ordered = '" . $customordergrandtotalamt . "', amount_ordered = '" . $customordergrandtotalamt . "', base_amount_paid = '" . $customordergrandtotalamt . "', amount_paid = '" . $customordergrandtotalamt . "' WHERE entity_id = '" . $order1->getId() . "'");
				
			} else {
            	$write_qry3 = $write->query("UPDATE `" . $prefix . "sales_flat_order_grid` SET base_grand_total = '" . $customordergrandtotalamt . "', grand_total = '" . $customordergrandtotalamt . "' WHERE entity_id = '" . $order1->getId() . "' AND store_id = '" . $importData['store_id'] . "'");
			}


            $select_qry = "SELECT item_id FROM `" . $prefix . "sales_flat_order_item` WHERE order_id = '" . $order1->getId() . "'";

			$rows = $read->fetchAll($select_qry);

			foreach($rows as $newrowItemId)

			 { 

				$item_id = $newrowItemId['item_id'];

				//Track the quantities ordered. Need the item_id as the key.

				$itemQty[$item_id] = $parts[1];

				#echo "ITEM ID: " . $item_id;

			

			}



            $write_qry2 = $write->query("UPDATE `" . $prefix . "sales_flat_quote` SET base_subtotal = '" . $custom_order_base_price_w_o_tax . "', base_grand_total = '" . $customordergrandtotalamt . "', subtotal = '" . $custom_order_base_price_w_o_tax . "', grand_total = '" . $customordergrandtotalamt . "', subtotal_with_discount = '" . $importData['subtotal'] . "', base_subtotal_with_discount = '" . $importData['subtotal'] . "' WHERE entity_id = '" . $item_id . "'");



        }

    }



    public function updateProductPrice(&$partsdata, $read, $prefix, $order1, $importData, $write, &$select_qry, &$newrowItemId, &$item_id, &$e, $productcounters)

    {

        $parts = explode(':', $partsdata);

		

        if (isset($parts[2]) && $parts[2] != "configurable" && $parts[2] != "bundle" && $parts[2] != "simple") {

		

            $custom_row_total = $parts[2] * $parts[1];
			$productexistsletsnotupdateprice = false;

            try {

				

		$product2 = Mage::getModel('catalog/product')->loadByAttribute('sku', $parts[0]);

		 if (method_exists($product2, 'getTypeId')) {

			$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product2->getId());

			$productstockItem = Mage::getModel('catalog/product')->load($product2->getId());

			try {

				if ($productstockItem->getStockItem()->getIsInStock() && $productstockItem->getStockItem()->getManageStock() == 1 && $stockItem->getQty() != "0.0000") {

					if (method_exists($product2, 'getTypeId')) {

						$productskutocheck = $parts[0];
						$productexistsletsnotupdateprice = true;
						
					} else if($productcounters > 1) {

						$productskutocheck = 'I-oe_productstandin' . $productcounters;

					} else {

						$productskutocheck = 'I-oe_productstandin';

					}

				 } else {

					if($productcounters > 1) {

						$productskutocheck = 'I-oe_productstandin' . $productcounters;

					} else {

						$productskutocheck = 'I-oe_productstandin';

					}

				 }

			} catch (Exception $e) {

				echo "Order #" . $importData['order_id'] . " ERROR UPDATING PRODUCT PRICE: " . $e->getMessage();

				Mage::log(sprintf('Order #' . $importData['order_id'] . ' ERROR UPDATING PRODUCT PRICE: %s', $e->getMessage()), null, 'ce_order_import_errors.log');

			}

		} else {

			try {

				if($productcounters > 1) {

					$productskutocheck = 'I-oe_productstandin' . $productcounters;

				} else {

					$productskutocheck = 'I-oe_productstandin';

				}

			} catch (Exception $e) {

				echo "Order #" . $importData['order_id'] . " ERROR UPDATING PRODUCT PRICE: " . $e->getMessage();

				Mage::log(sprintf('Order #' . $importData['order_id'] . ' ERROR UPDATING PRODUCT PRICE: %s', $e->getMessage()), null, 'ce_order_import_errors.log');

			}

		

		}

                $select_qry = $read->query("SELECT quote_item_id,tax_amount,tax_percent FROM `" . $prefix . "sales_flat_order_item` WHERE order_id = '" . $order1->getId() . "' AND sku = '" . $productskutocheck . "'");

                $newrowItemId = $select_qry->fetch();



                $item_id = $newrowItemId['quote_item_id'];

                $tax_amount = $importData['tax_amount'];

                $tax_percent = $newrowItemId['tax_percent'];



                #$tax_percent_for_row_total = $custom_row_total * .09090909090909091;

                $decimalfortaxpercent = $tax_percent / 100;

                $tax_percent_for_row_total = $custom_row_total * $decimalfortaxpercent;

                $order_total_without_tax = $custom_row_total - $tax_percent_for_row_total;

                $per_item_tax_amount = $tax_amount / $parts[1];

                $final_item_price_before_tax = $parts[2] - $per_item_tax_amount;



                //row_total_before_redemptions row_total_before_redemptions_incl_tax row_total_after_redemptions row_total_after_redemptions_incl_tax

                #echo "CUSTOMER ORDER TOTAL: " . $custom_row_total . "<br/>";

                #echo "ORDER TOTAL w/o TAX: " . $order_total_without_tax . "<br/>";

                #echo "ITEM PRICE BEFORE TAX: " . $final_item_price_before_tax . "<br/>";


				if($productexistsletsnotupdateprice != true) {
					#echo "SKU: " . $productskutocheck;
                $write_qry2 = $write->query("UPDATE `" . $prefix . "sales_flat_order_item` SET price_incl_tax = '" . $parts[2] . "', base_price_incl_tax = '" . $parts[2] . "', base_row_total_incl_tax = '" . $custom_row_total . "', row_total_incl_tax = '" . $custom_row_total . "', row_total = '" . $order_total_without_tax . "', base_row_total = '" . $order_total_without_tax . "', tax_amount = '" . $tax_percent_for_row_total . "', base_tax_amount = '" . $tax_percent_for_row_total . "', original_price = '" . $parts[2] . "', base_original_price = '" . $parts[2] . "', price = '" . $parts[2] . "', base_price = '" . $parts[2] . "' WHERE order_id = '" . $order1->getId() . "' AND sku = '" . $productskutocheck . "'");
				}
				

				if (isset($importData['discount_amount']) && $importData['discount_amount'] != "0.0000" && $importData['discount_amount'] != "0.00") {

					$final_discount_amount = str_replace('-', '', $importData['discount_amount']);

					$final_base_discount_amount = str_replace('-', '', $importData['base_discount_amount']);

					//don't think we need to discount on indvidual items atm

					#$write_qry2 = $write->query("UPDATE `" . $prefix . "sales_flat_order_item` SET discount_amount = '" . $final_discount_amount . "', base_discount_amount = '" . $final_base_discount_amount . "' WHERE order_id = '" . $order1->getId() . "' AND sku = '" . $parts[0] . "'");

				}

				

                if ($this->getBatchParams('create_invoice') == "true") {


					if($productexistsletsnotupdateprice != true) {
					
					$select_qry = $read->query("SELECT entity_id FROM `" . $prefix . "sales_flat_invoice` WHERE order_id = '" . $order1->getId() . "'");
					$newrowItemId = $select_qry->fetch();
					$invoice_entity_id = $newrowItemId['entity_id'];
					
                    $write_qry3 = $write->query("UPDATE `" . $prefix . "sales_flat_invoice_item` SET price_incl_tax = '" . $parts[2] . "', base_price_incl_tax = '" . $parts[2] . "', base_row_total_incl_tax = '" . $custom_row_total . "', row_total_incl_tax = '" . $custom_row_total . "', row_total = '" . $order_total_without_tax . "', base_row_total = '" . $order_total_without_tax . "', tax_amount = '" . $tax_percent_for_row_total . "', base_tax_amount = '" . $tax_percent_for_row_total . "', price = '" . $order_total_without_tax . "', base_price = '" . $order_total_without_tax . "' WHERE parent_id = '" . $invoice_entity_id . "' AND sku = '" . $productskutocheck . "'");
					}

                }



            } catch (Exception $e) {

                echo "Order #" . $importData['order_id'] . " ERROR UPDATING PRODUCT PRICE: " . $e->getMessage();

                Mage::log(sprintf('Order #' . $importData['order_id'] . ' ERROR UPDATING PRODUCT PRICE: %s', $e->getMessage()), null, 'ce_order_import_errors.log');

            }

        } else if(isset($parts[2]) && $parts[2] == "configurable" || $parts[2] == "simple") {

		

			//this is for configurable product setups that are not in stock or are not available in the store at all.

            $product2 = Mage::getModel('catalog/product')->loadByAttribute('sku', $parts[0]);

		 if (method_exists($product2, 'getTypeId')) {

			$configurableskusomatchonlike = false;
			$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product2->getId());

			$productstockItem = Mage::getModel('catalog/product')->load($product2->getId());

			try {

				if ($productstockItem->getStockItem()->getIsInStock() && $productstockItem->getStockItem()->getManageStock() == 1 && $stockItem->getQty() != "0.0000" || $productstockItem->getStockItem()->getManageStock() == 0) {

					if (method_exists($product2, 'getTypeId') && $product2->getTypeId() == "configurable") {

						$productskutocheck = $parts[0];
						$configurableskusomatchonlike = true;

					} else if (method_exists($product2, 'getTypeId') && $product2->getTypeId() == "simple" && $parts[2] != "configurable") {

						$productskutocheck = $parts[0];
						$partsfornamepricing = explode('^', $partsdata);
						if(isset($partsfornamepricing[1])) {
							$configurableskusomatchonlike = true;
						}

					} else if($productcounters > 1) {

						$productskutocheck = 'I-oe_productstandin' . $productcounters;

					} else {

						$productskutocheck = 'I-oe_productstandin';

					}

				 } else {

					if($productcounters > 1) {

						$productskutocheck = 'I-oe_productstandin' . $productcounters;

					} else {

						$productskutocheck = 'I-oe_productstandin';

					}

				 }

			} catch (Exception $e) {

				echo "Order #" . $importData['order_id'] . " ERROR UPDATING PRODUCT PRICE: " . $e->getMessage();

				Mage::log(sprintf('Order #' . $importData['order_id'] . ' ERROR UPDATING PRODUCT PRICE: %s', $e->getMessage()), null, 'ce_order_import_errors.log');

			}

		} else {

			try {

				if($productcounters > 1) {

					$productskutocheck = 'I-oe_productstandin' . $productcounters;

				} else {

					$productskutocheck = 'I-oe_productstandin';

				}

			} catch (Exception $e) {

				echo "Order #" . $importData['order_id'] . " ERROR UPDATING PRODUCT PRICE: " . $e->getMessage();

				Mage::log(sprintf('Order #' . $importData['order_id'] . ' ERROR UPDATING PRODUCT PRICE: %s', $e->getMessage()), null, 'ce_order_import_errors.log');

			}

		

		}

			

			$partsfornamepricing = explode('^', $partsdata);

            $custom_row_total = $partsfornamepricing[1] * $parts[1];

			

            try {



                $select_qry = $read->query("SELECT quote_item_id,tax_amount,tax_percent FROM `" . $prefix . "sales_flat_order_item` WHERE order_id = '" . $order1->getId() . "' AND sku = '" . $productskutocheck . "'");

                $newrowItemId = $select_qry->fetch();


                $item_id = $newrowItemId['quote_item_id'];

                $tax_amount = $importData['tax_amount'];

                $tax_percent = $newrowItemId['tax_percent'];


                #$tax_percent_for_row_total = $custom_row_total * .09090909090909091;

                $decimalfortaxpercent = $tax_percent / 100;

                $tax_percent_for_row_total = $custom_row_total * $decimalfortaxpercent;

                $order_total_without_tax = $custom_row_total;

                $order_total_has_tax = $custom_row_total + $tax_percent_for_row_total;

                $per_item_tax_amount = $tax_amount / $parts[1];

                $final_item_price_before_tax = $partsfornamepricing[1] - $per_item_tax_amount;



                //row_total_before_redemptions row_total_before_redemptions_incl_tax row_total_after_redemptions row_total_after_redemptions_incl_tax
                #echo "CUSTOMER ORDER TOTAL: " . $custom_row_total . "<br/>";
                #echo "ORDER TOTAL w/o TAX: " . $order_total_without_tax . "<br/>";
                #echo "ITEM PRICE BEFORE TAX: " . $final_item_price_before_tax . "<br/>";


				
				if($configurableskusomatchonlike) {
				//this needs to be fixed... because LIKE sku could conflict if order has 2 products orders with like sku.. so a configurable ordered twice in 2 different sizes for example.. need to do by item_id or something but no data available to support this custom configurable pricing atm
				$write_qry2 = $write->query("UPDATE `" . $prefix . "sales_flat_order_item` SET price_incl_tax = '" . $partsfornamepricing[1] . "', base_price_incl_tax = '" . $partsfornamepricing[1] . "', base_row_total_incl_tax = '" . $order_total_has_tax . "', row_total_incl_tax = '" . $order_total_has_tax . "', row_total = '" . $order_total_without_tax . "', base_row_total = '" . $order_total_without_tax . "', tax_amount = '" . $tax_percent_for_row_total . "', base_tax_amount = '" . $tax_percent_for_row_total . "', original_price = '" . $order_total_without_tax . "', base_original_price = '" . $order_total_without_tax . "', price = '" . $order_total_without_tax . "', base_price = '" . $order_total_without_tax . "' WHERE order_id = '" . $order1->getId() . "' AND sku LIKE '%" . $productskutocheck . "%'");
				} else {
                $write_qry2 = $write->query("UPDATE `" . $prefix . "sales_flat_order_item` SET price_incl_tax = '" . $partsfornamepricing[1] . "', base_price_incl_tax = '" . $partsfornamepricing[1] . "', base_row_total_incl_tax = '" . $order_total_has_tax . "', row_total_incl_tax = '" . $order_total_has_tax . "', row_total = '" . $order_total_without_tax . "', base_row_total = '" . $order_total_without_tax . "', tax_amount = '" . $tax_percent_for_row_total . "', base_tax_amount = '" . $tax_percent_for_row_total . "', original_price = '" . $order_total_without_tax . "', base_original_price = '" . $order_total_without_tax . "', price = '" . $order_total_without_tax . "', base_price = '" . $order_total_without_tax . "' WHERE order_id = '" . $order1->getId() . "' AND sku = '" . $productskutocheck . "'");
				
				}


				if (isset($importData['discount_amount']) && $importData['discount_amount'] != "0.0000" && $importData['discount_amount'] != "0.00") {

				$final_discount_amount = str_replace('-', '', $importData['discount_amount']);

				$final_base_discount_amount = str_replace('-', '', $importData['base_discount_amount']);

				

                $write_qry2 = $write->query("UPDATE `" . $prefix . "sales_flat_order_item` SET discount_amount = '" . $final_discount_amount . "', base_discount_amount = '" . $final_base_discount_amount . "' WHERE order_id = '" . $order1->getId() . "' AND sku = '" . $productskutocheck . "'");

				}

				

                if ($this->getBatchParams('create_invoice') == "true") {

					$select_qry = $read->query("SELECT entity_id FROM `". $prefix ."sales_flat_invoice` WHERE order_id = '" . $order1->getId() . "'");

					$newrowInvoiceItemId = $select_qry->fetch();

					$invoice_item_id = $newrowInvoiceItemId['entity_id'];

					

					if($invoice_item_id > 0) {

					

                    $write_qry3 = $write->query("UPDATE `" . $prefix . "sales_flat_invoice_item` SET price_incl_tax = '" . $partsfornamepricing[1] . "', base_price_incl_tax = '" . $partsfornamepricing[1] . "', base_row_total_incl_tax = '" . $order_total_has_tax . "', row_total_incl_tax = '" . $order_total_has_tax . "', row_total = '" . $order_total_without_tax . "', base_row_total = '" . $order_total_without_tax . "', tax_amount = '" . $tax_percent_for_row_total . "', base_tax_amount = '" . $tax_percent_for_row_total . "', price = '" . $order_total_without_tax . "', base_price = '" . $order_total_without_tax . "' WHERE parent_id = '" . $invoice_item_id . "' AND sku = '" . $productskutocheck . "'");



					

						if (isset($importData['discount_amount']) && $importData['discount_amount'] != "") {

						$final_discount_amount = str_replace('-', '', $importData['discount_amount']);

						$final_base_discount_amount = str_replace('-', '', $importData['base_discount_amount']);

						

						$write_qry3 = $write->query("UPDATE `" . $prefix . "sales_flat_invoice_item` SET discount_amount = '" . $final_discount_amount . "', base_discount_amount = '" . $final_base_discount_amount . "' WHERE parent_id = '" . $invoice_item_id . "' AND sku = '" . $productskutocheck . "'");

						

						}

				

					}

                }

				

				

				//credit memo

				if ($this->getBatchParams('create_creditmemo') == "true") {



                }





            } catch (Exception $e) {

                echo "Order #" . $importData['order_id'] . " ERROR UPDATING PRODUCT PRICE: " . $e->getMessage();

                Mage::log(sprintf('Order #' . $importData['order_id'] . ' ERROR UPDATING PRODUCT PRICE: %s', $e->getMessage()), null, 'ce_order_import_errors.log');

            }

        }

    }



    public function updateProductItemName(&$partsdata, $importData, $read, $prefix, $order1, $write, &$e, &$select_qry, $productcounters)

    {

        $parts = explode(':', $partsdata);

        if (isset($parts[3]) && $parts[2] != "configurable" && $parts[2] != "bundle" && $parts[2] != "simple") {

            $product2 = Mage::getModel('catalog/product')->loadByAttribute('sku', $parts[0]);

            try {

                if (method_exists($product2, 'getTypeId')) {
					
					$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product2->getId());
					$productstockItem = Mage::getModel('catalog/product')->load($product2->getId());
				
					if ($productstockItem->getStockItem()->getIsInStock() && $productstockItem->getStockItem()->getManageStock() == 1 && $stockItem->getQty() != "0.0000") {
                    
						$productskutocheck = $parts[0];
					
					} else {

						if($productcounters > 1) {
	
							$productskutocheck = 'I-oe_productstandin' . $productcounters;
	
						} else {
	
							$productskutocheck = 'I-oe_productstandin';
	
						}
					
					}

                } else {

                    #$productskutocheck = 'I-oe_productstandin';

                    #$productskutocheck = $parts[0];

					if($productcounters > 1) {

						$productskutocheck = 'I-oe_productstandin' . $productcounters;

					} else {

						$productskutocheck = 'I-oe_productstandin';

					}

                }

            } catch (Exception $e) {

                echo "Order #" . $importData['order_id'] . " ERROR UPDATING PRODUCT NAME: " . $e->getMessage();

                Mage::log(sprintf('Order #' . $importData['order_id'] . ' ERROR UPDATING PRODUCT NAME: %s', $e->getMessage()), null, 'ce_order_import_errors.log');

            }

            $customproductname = $parts[3];

            $select_qry = $read->query("SELECT item_id FROM `" . $prefix . "sales_flat_order_item` WHERE order_id = '" . $order1->getId() . "' AND sku = \"" . $productskutocheck . "\"");
	
            $newrowItemId2 = $select_qry->fetch();
            $db_item_id = $newrowItemId2['item_id'];


            try {

                $write_qry2 = $write->query("UPDATE `" . $prefix . "sales_flat_order_item` SET name = \"" . addslashes($customproductname) . "\", sku = \"" . $parts[0] . "\" WHERE item_id = '" . $db_item_id . "'");

                $write_qry2 = $write->query("UPDATE `" . $prefix . "sales_flat_quote_item` SET name = \"" . addslashes($customproductname) . "\", sku = \"" . $parts[0] . "\" WHERE quote_id = '" . $db_item_id . "'");

            } catch (Exception $e) {

                echo "Order #" . $importData['order_id'] . " ERROR UPDATING PRODUCT NAME: " . $e->getMessage();

                Mage::log(sprintf('Order #' . $importData['order_id'] . ' ERROR UPDATING PRODUCT NAME: %s', $e->getMessage()), null, 'ce_order_import_errors.log');

            }

        } else if (isset($parts[2]) && $parts[2] == "configurable" || $parts[2] == "simple") {

			

            $product2 = Mage::getModel('catalog/product')->loadByAttribute('sku', $parts[0]);

			if (method_exists($product2, 'getTypeId')) {

				$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product2->getId());

				$productstockItem = Mage::getModel('catalog/product')->load($product2->getId());

				
				if ($product2->getTypeId() == "configurable") {
				try {

					 if ($productstockItem->getStockItem()->getIsInStock() && $productstockItem->getStockItem()->getManageStock() == 1 && $stockItem->getQty() != "0.0000") {

						if (method_exists($product2, 'getTypeId')) {

							$productskutocheck = $parts[0];

						} else if($productcounters > 1) {

							$productskutocheck = 'I-oe_productstandin' . $productcounters;

						} else {

							$productskutocheck = 'I-oe_productstandin';

						}

					 } else {

						if($productcounters > 1) {

							$productskutocheck = 'I-oe_productstandin' . $productcounters;

						} else {

							$productskutocheck = 'I-oe_productstandin';

						}

					 }

				} catch (Exception $e) {

					echo "Order #" . $importData['order_id'] . " ERROR UPDATING PRODUCT NAME: " . $e->getMessage();

					Mage::log(sprintf('Order #' . $importData['order_id'] . ' ERROR UPDATING PRODUCT NAME: %s', $e->getMessage()), null, 'ce_order_import_errors.log');

				}
				
				} else {
					#echo "MISMATCH PRODUCT TYPE";
					if($productcounters > 1) {

						$productskutocheck = 'I-oe_productstandin' . $productcounters;

					} else {

						$productskutocheck = 'I-oe_productstandin';

					}
				
				}

			}  else {

				try {

					if($productcounters > 1) {

						$productskutocheck = 'I-oe_productstandin' . $productcounters;

					} else {

						$productskutocheck = 'I-oe_productstandin';

					}

				} catch (Exception $e) {

					echo "Order #" . $importData['order_id'] . " ERROR UPDATING PRODUCT NAME: " . $e->getMessage();

					Mage::log(sprintf('Order #' . $importData['order_id'] . ' ERROR UPDATING PRODUCT NAME: %s', $e->getMessage()), null, 'ce_order_import_errors.log');

				}

			

			}

			$partsfornamepricing = explode('^', $partsdata);
		
			if(isset($partsfornamepricing[2]) && $partsfornamepricing[2] !="") {
				$customproductname = $partsfornamepricing[2];
			} else {
				$customproductname = $parts[3];
			}

			

            $select_qry = $read->query("SELECT item_id FROM `" . $prefix . "sales_flat_order_item` WHERE order_id = '" . $order1->getId() . "' AND sku = \"" . $productskutocheck . "\"");

			

            $newrowItemId2 = $select_qry->fetch();

            $db_item_id = $newrowItemId2['item_id'];



            try {

                $write_qry2 = $write->query("UPDATE `" . $prefix . "sales_flat_order_item` SET name = \"" . addslashes($customproductname) . "\", sku = \"" . $parts[0] . "\" WHERE item_id = '" . $db_item_id . "'");

                $write_qry2 = $write->query("UPDATE `" . $prefix . "sales_flat_quote_item` SET name = \"" . addslashes($customproductname) . "\", sku = \"" . $parts[0] . "\" WHERE quote_id = '" . $db_item_id . "'");

            } catch (Exception $e) {

                echo "Order #" . $importData['order_id'] . " ERROR UPDATING PRODUCT NAME: " . $e->getMessage();

                Mage::log(sprintf('Order #' . $importData['order_id'] . ' ERROR UPDATING PRODUCT NAME: %s', $e->getMessage()), null, 'ce_order_import_errors.log');

            }

        }

    }



    public function createShipment($order1, $itemQty, $importData)

    { //Create new Shipment for the order

        $shipment = $order1->prepareShipment($itemQty);
        $shipment->register();

        try {

            $transactionSave = Mage::getModel('core/resource_transaction')
                ->addObject($shipment)
                ->addObject($shipment->getOrder())
                ->save();
				
			if(isset($importData['tracking_date']) && isset($importData['tracking_codes']) && isset($importData['tracking_ship_method'])) {
				if($importData['tracking_codes'] !="") {
					
					$sales_flat_shipment_item_insertID = $shipment->getId();
					// sales_flat_shipment_track
					$tracking_date = $importData['tracking_date'];
					$tracking_codes = $importData['tracking_codes'];
					$ship_service = $importData['tracking_ship_method'];
					$order_entity_id = $order1->getId();
					
					$resource = Mage::getSingleton('core/resource');
					$prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix'); 
					$write = $resource->getConnection('core_write');
					
					$tracking_codes_collection = explode(",", $tracking_codes);
					foreach ($tracking_codes_collection as $tracking_id){
						#echo "INSERT TRACKING: " . $tracking_id;
						$write_qry_shipment_track =$write->query("Insert INTO `".$prefix."sales_flat_shipment_track` (parent_id,weight,qty,order_id,track_number,description,title,carrier_code,created_at,updated_at) VALUES ('$sales_flat_shipment_item_insertID',NULL,NULL,'$order_entity_id','$tracking_id',NULL,'$ship_service','custom','$tracking_date','$tracking_date')");
					}
				}
			}

        } catch (Mage_Core_Exception $e) {

            Mage::log("failed to create a shipment");

            Mage::log($e->getMessage());

            Mage::log($e->getTraceAsString());

            Mage::log(sprintf('failed to create a shipment: %s', $e->getMessage()), null, 'ce_order_import_errors.log');

        }

    }



    public function processProductOrdered($data, $importData, $read, $prefix, $order1, $write, $itemQty)

    {

        $parts = explode(':', $data);



        if (isset($parts[3]) && $parts[2] != "configurable" && $parts[2] != "bundle" && $parts[2] != "simple") {

            $product2 = Mage::getModel('catalog/product')->loadByAttribute('sku', $parts[0]);

            try {

                if (method_exists($product2, 'getTypeId')) {

                    $productskutocheck = $parts[0];

                } else {

                    $productskutocheck = $this->getBatchParams('dumby_sku');

                }

            } catch (Exception $e) {

                $message = " ERROR UPDATING PRODUCT NAME: " . $e->getMessage();

                echo "Order #" . $importData['order_id'] . $message;

                Mage::log(sprintf('Order #' . $importData['order_id'] . $message), null, 'ce_order_import_errors.log');

            }



            $customproductname = $parts[3];

            $select_qry = $read->query("SELECT item_id FROM `" . $prefix . "sales_flat_order_item` WHERE order_id = '" . $order1->getId() . "' AND sku = \"" . $productskutocheck . "\"");

            $newrowItemId2 = $select_qry->fetch();

            $db_item_id = $newrowItemId2['item_id'];



            try {

                $write->query(

                    "UPDATE `" . $prefix . "sales_flat_order_item` "

                        . "SET name = \"" . addslashes($customproductname)

                        . "\", sku = \"" . $parts[0]

                        . "\" WHERE item_id = '" . $db_item_id . "'");



                $write->query(

                    "UPDATE `" . $prefix . "sales_flat_quote_item` "

                        . "SET name = \"" . addslashes($customproductname)

                        . "\", sku = \"" . $parts[0]

                        . "\" WHERE quote_id = '" . $db_item_id . "'");



            } catch (Exception $e) {

                $message = " ERROR UPDATING PRODUCT NAME: " . $e->getMessage();

                echo "Order #" . $importData['order_id'] . $message;

                Mage::log(sprintf('Order #' . $importData['order_id'] . $message), null, 'ce_order_import_errors.log');

            }

        }

        //JUST PRICE UPDATE

        if (isset($parts[2]) && $parts[2] != "configurable" && $parts[2] != "bundle" && $parts[2] != "simple") {

            $custom_row_total = $parts[2] * $parts[1];

            try {

                $select_qry = $read->query("SELECT quote_item_id,tax_amount,tax_percent FROM `" . $prefix . "sales_flat_order_item` WHERE order_id = '" . $order1->getId() . "' AND sku = '" . $parts[0] . "'");

                $newrowItemId = $select_qry->fetch();



                $item_id = $newrowItemId['quote_item_id'];

                $tax_percent = $newrowItemId['tax_percent'];

                #$tax_percent = 8.00;



                $decimalfortaxpercent = $tax_percent / 100;

                $tax_amount_for_row_total = $custom_row_total * $decimalfortaxpercent;

                $order_total_without_tax = $custom_row_total;

                $order_total_with_tax = $tax_amount_for_row_total + $custom_row_total;



                $write->query(

                    "UPDATE `" . $prefix . "sales_flat_order_item` "

                        . "SET price_incl_tax = '" . $parts[2]

                        . "', base_price_incl_tax = '" . $parts[2]

                        . "', base_row_total_incl_tax = '" . $order_total_with_tax

                        . "', row_total_incl_tax = '" . $order_total_with_tax

                        . "', row_total = '" . $order_total_without_tax

                        . "', base_row_total = '" . $order_total_without_tax

                        . "', tax_amount = '" . $tax_amount_for_row_total

                        . "', base_tax_amount = '" . $tax_amount_for_row_total

                        . "', price = '" . $parts[2]

                        . "', base_price = '" . $parts[2]

                        . "' WHERE order_id = '" . $order1->getId()

                        . "' AND sku = '" . $parts[0] . "'");



                $write->query(

                    "UPDATE `" . $prefix . "sales_flat_quote_item` SET "

                        . "row_total_with_discount = '" . $order_total_without_tax

                        . "', base_row_total = '" . $order_total_without_tax

                        . "', row_total = '" . $order_total_without_tax

                        . "', price = '" . $parts[2]

                        . "', base_price = '" . $parts[2]

                        . "', custom_price = '" . $parts[2]

                        . "', original_custom_price = '" . $parts[2]

                        . "', price_incl_tax = '" . $parts[2]

                        . "', base_price_incl_tax = '" . $parts[2]

                        . "', row_total_incl_tax = '" . $order_total_with_tax

                        . "', base_row_total_incl_tax = '" . $order_total_with_tax

                        . "', tax_amount = '" . $tax_amount_for_row_total

                        . "', base_tax_amount = '" . $tax_amount_for_row_total

                        . "' WHERE item_id = '" . $item_id . "'");



                if ($this->getBatchParams('create_invoice') == "true") {

                    $write->query(

                        "UPDATE `" . $prefix . "sales_flat_invoice_item` "

                            . "SET price_incl_tax = '" . $parts[2]

                            . "', base_price_incl_tax = '" . $parts[2]

                            . "', base_row_total_incl_tax = '" . $order_total_with_tax

                            . "', row_total_incl_tax = '" . $order_total_with_tax

                            . "', row_total = '" . $order_total_without_tax

                            . "', base_row_total = '" . $order_total_without_tax

                            . "', tax_amount = '" . $tax_amount_for_row_total

                            . "', base_tax_amount = '" . $tax_amount_for_row_total

                            . "', price = '" . $parts[2]

                            . "', base_price = '" . $parts[2]

                            . "' WHERE parent_id = '" . $order1->getId()

                            . "' AND sku = '" . $parts[0] . "'");

                }



            } catch (Exception $e) {

                $message = " ERROR UPDATING PRODUCT PRICE: " . $e->getMessage();

                echo "Order #" . $importData['order_id'] . $message;

                Mage::log(sprintf('Order #' . $importData['order_id'] . $message), null, 'ce_order_import_errors.log');

            }

        }

        //JUST UPDATE SHIPPING TOTAL

        if (isset($importData['subtotal']) && isset($importData['shipping_amount'])) {



            $customordergrandtotalamt = $importData['subtotal'] + $importData['shipping_amount'] + $importData['tax_amount'];

            $custom_order_base_price_w_o_tax = $importData['subtotal'];



            $base_shipping_tax_amount = $importData['shipping_amount'] - $importData['base_shipping_amount'];

            $base_tax_amount = $importData['tax_amount'];



            #if (Mage::getVersion() > '1.4.0.1') {

			$verChecksplit = explode(".",Mage::getVersion());

			if ($verChecksplit[1] > 4) {

                try {

                    if ($this->getBatchParams('create_invoice') == "true") {

                        $write->query(

                            //update order totals

                            "UPDATE `" . $prefix . "sales_flat_order` "

                                . "SET base_subtotal = '" . $custom_order_base_price_w_o_tax

                                . "', base_tax_amount = '" . $base_tax_amount

                                . "', tax_amount = '" . $base_tax_amount

                                . "', tax_invoiced = '" . $base_tax_amount

                                . "', base_grand_total = '" . $customordergrandtotalamt

                                . "', base_total_paid = '" . $customordergrandtotalamt

                                . "', total_paid = '" . $customordergrandtotalamt

                                . "', base_total_invoiced = '" . $customordergrandtotalamt

                                . "', total_invoiced = '" . $customordergrandtotalamt

                                . "', subtotal = '" . $custom_order_base_price_w_o_tax

                                . "', base_subtotal_incl_tax = '" . $importData['subtotal']

                                . "', subtotal_incl_tax = '" . $importData['subtotal']

                                . "', grand_total = '" . $customordergrandtotalamt

                                . "', base_shipping_amount = '" . $importData['base_shipping_amount']

                                . "', base_shipping_tax_amount = '" . $base_shipping_tax_amount

                                . "', shipping_tax_amount = '" . $base_shipping_tax_amount

                                . "', shipping_amount = '" . $importData['base_shipping_amount']

                                . "', shipping_invoiced = '" . $importData['shipping_amount']

                                . "', base_shipping_invoiced = '" . $importData['base_shipping_amount']

                                . "', shipping_incl_tax = '" . $importData['shipping_amount']

                                . "', base_shipping_incl_tax = '" . $importData['shipping_amount']

                                . "' WHERE entity_id = '" . $order1->getId()

                                . "' AND store_id = '" . $importData['store_id'] . "'");



                        //EXTRA DATA SET FOR PRODUCT/ORDER DATA ON INVOICES

                        $write->query(

                            "UPDATE `" . $prefix . "sales_flat_invoice` "

                                . "SET base_subtotal = '" . $custom_order_base_price_w_o_tax

                                . "', base_tax_amount = '" . $base_tax_amount

                                . "', tax_amount = '" . $base_tax_amount

                                . "', subtotal = '" . $custom_order_base_price_w_o_tax

                                . "', base_subtotal_incl_tax = '" . $importData['subtotal']

                                . "', subtotal_incl_tax = '" . $importData['subtotal']

                                . "', base_grand_total = '" . $customordergrandtotalamt

                                . "', grand_total = '" . $customordergrandtotalamt

                                . "', base_shipping_amount = '" . $importData['base_shipping_amount']

                                . "', shipping_amount = '" . $importData['base_shipping_amount']

                                . "', shipping_incl_tax = '" . $importData['shipping_amount']

                                . "', base_shipping_incl_tax = '" . $importData['shipping_amount']

                                . "' WHERE order_id = '" . $order1->getId()

                                . "' AND store_id = '" . $importData['store_id'] . "'");



                        $write->query(

                            "UPDATE `" . $prefix . "sales_flat_invoice_grid` "

                                . "SET base_grand_total = '" . $customordergrandtotalamt

                                . "', grand_total = '" . $customordergrandtotalamt

                                . "' WHERE order_id = '" . $order1->getId()

                                . "' AND store_id = '" . $importData['store_id'] . "'");

                    } else {

                        $write_qry2 = $write->query("UPDATE `" . $prefix . "sales_flat_order` SET base_subtotal = '" . $custom_order_base_price_w_o_tax . "', base_tax_amount = '" . $base_tax_amount . "', tax_amount = '" . $base_tax_amount . "', base_grand_total = '" . $customordergrandtotalamt . "', subtotal = '" . $custom_order_base_price_w_o_tax . "', base_subtotal_incl_tax = '" . $importData['subtotal'] . "', subtotal_incl_tax = '" . $importData['subtotal'] . "', grand_total = '" . $customordergrandtotalamt . "', base_shipping_amount = '" . $importData['base_shipping_amount'] . "', base_shipping_tax_amount = '" . $base_shipping_tax_amount . "', shipping_tax_amount = '" . $base_shipping_tax_amount . "', shipping_amount = '" . $importData['base_shipping_amount'] . "', shipping_incl_tax = '" . $importData['shipping_amount'] . "', base_shipping_incl_tax = '" . $importData['shipping_amount'] . "' WHERE entity_id = '" . $order1->getId() . "' AND store_id = '" . $importData['store_id'] . "'");

                    }

                } catch (Exception $e) {

                    echo "ERROR: " . $e->getMessage();

                    Mage::log(sprintf('Order #' . $importData['order_id'] . ' saving error: %s', $e->getMessage()), null, 'ce_order_import_errors.log');

                }



            } else {

                //update order totals

                $write->query(

                    "UPDATE `" . $prefix . "sales_order` "

                        . "SET base_subtotal = '" . $custom_order_base_price_w_o_tax

                        . "', base_grand_total = '" . $customordergrandtotalamt

                        . "', subtotal = '" . $custom_order_base_price_w_o_tax

                        . "', grand_total = '" . $customordergrandtotalamt

                        . "' WHERE entity_id = '" . $order1->getId()

                        . "' AND store_id = '" . $importData['store_id'] . "'");

            }



            //Update tax amt

            $write->query(

                "UPDATE `" . $prefix . "sales_order_tax` "

                    . "SET amount = '" . $base_tax_amount

                    . "', base_amount = '" . $base_tax_amount

                    . "', base_real_amount = '" . $base_tax_amount

                    . "'  WHERE order_id = '" . $order1->getId() . "'");



            //UPDATE FOR SALES GRID VIEW -- sales -> orders

            $write->query(

                "UPDATE `" . $prefix . "sales_flat_order_grid` "

                    . "SET base_grand_total = '" . $customordergrandtotalamt

                    . "', grand_total = '" . $customordergrandtotalamt

                    . "' WHERE entity_id = '" . $order1->getId()

                    . "' AND store_id = '" . $importData['store_id'] . "'");



            $select_qry = $read->query("SELECT item_id FROM `" . $prefix . "sales_flat_order_item` WHERE order_id = '" . $order1->getId() . "'");

            $newrowItemId = $select_qry->fetch();



            //Track the quantities ordered. Need the item_id as the key.

            $item_id = $newrowItemId['item_id'];

            $itemQty[$item_id] = $parts[1];

            $write->query(

                "UPDATE `" . $prefix . "sales_flat_quote` "

                    . "SET base_subtotal = '" . $custom_order_base_price_w_o_tax

                    . "', base_grand_total = '" . $customordergrandtotalamt

                    . "', subtotal = '" . $custom_order_base_price_w_o_tax

                    . "', grand_total = '" . $customordergrandtotalamt

                    . "', subtotal_with_discount = '" . $importData['subtotal']

                    . "', base_subtotal_with_discount = '" . $importData['subtotal']

                    . "' WHERE entity_id = '" . $item_id . "'");

        }

        return $importData;

    }



    public function updateOrderCreationTime($importData, $write, $prefix, $order1)

    {

        $dateTime = strtotime($importData['created_at']);



        #if (Mage::getVersion() > '1.4.0.1') {

		$verChecksplit = explode(".",Mage::getVersion());

		if ($verChecksplit[1] > 4) {

            try {

                $write->query(

                    "UPDATE `" . $prefix . "sales_flat_order` "

                        . "SET created_at = '" . date("Y-m-d H:i:s", $dateTime)

                        . "', updated_at = '" . date("Y-m-d H:i:s", $dateTime)

                        . "' WHERE entity_id = '" . $order1->getId()

                        . "' AND store_id = '" . $importData['store_id'] . "'");



                $write->query(

                    "UPDATE `" . $prefix . "sales_flat_order_grid` "

                        . "SET created_at = '" . date("Y-m-d H:i:s", $dateTime)

                        . "', updated_at = '" . date("Y-m-d H:i:s", $dateTime)

                        . "' WHERE entity_id = '" . $order1->getId()

                        . "' AND store_id = '" . $importData['store_id'] . "'");



                $write->query(

                    "UPDATE `" . $prefix . "sales_flat_order_item` "

                        . "SET created_at = '" . date("Y-m-d H:i:s", $dateTime)

                        . "', updated_at = '" . date("Y-m-d H:i:s", $dateTime)

                        . "' WHERE order_id = '" . $order1->getId()

                        . "' AND store_id = '" . $importData['store_id'] . "'");



                $write->query(

                    "UPDATE `" . $prefix . "sales_flat_order_status_history` "

                        . "SET created_at = '" . date("Y-m-d H:i:s", $dateTime)

                        . "' WHERE parent_id = '" . $order1->getId() . "'");

            } catch (Exception $e) {

                $message = "ERROR UPDATING ORDER CREATION TIME: " . $e->getMessage();

                echo $message;

                Mage::log(sprintf('Order #' . $importData['order_id'] . ' saving error: %s', $message), null, 'ce_order_import_errors.log');

            }

        } else {

            try {

                $write->query(

                    "UPDATE `" . $prefix . "sales_order` "

                        . "SET created_at = '" . date("Y-m-d H:i:s", $dateTime)

                        . "' WHERE entity_id = '" . $order1->getId()

                        . "' AND store_id = '" . $importData['store_id'] . "'");

            } catch (Exception $e) {

                $message = "ERROR UPDATING ORDER CREATION TIME: " . $e->getMessage();

                echo $message;

                Mage::log(sprintf('Order #' . $importData['order_id'] . ' saving error: %s', $message), null, 'ce_order_import_errors.log');

                return $e;

            }

        }

    }



    public function setOrderAsComplete($write, $prefix, $order1, $importData)

    {

        #if (Mage::getVersion() > '1.4.0.1') {

		$verChecksplit = explode(".",Mage::getVersion());

		if ($verChecksplit[1] > 4) {

            try {

                $write->query(

                    "UPDATE `" . $prefix . "sales_flat_order` "

                        . "SET state = 'complete', "

                        . "status = 'complete' "

                        . "WHERE entity_id = '" . $order1->getId()

                        . "' AND store_id = '" . $importData['store_id'] . "'");



                $write->query(

                    "UPDATE `" . $prefix . "sales_flat_order_grid` "

                        . "SET status = 'complete' "

                        . "WHERE entity_id = '" . $order1->getId()

                        . "' AND store_id = '" . $importData['store_id'] . "'");



                $write->query(

                    "UPDATE `" . $prefix . "sales_flat_order_status_history` "

                        . "SET status = 'complete' "

                        . "WHERE parent_id = '" . $order1->getId() . "'");



            } catch (Exception $e) {

                $message = "ERROR SETTING ORDER AS COMPLETE: " . $e->getMessage();

                echo $message;

                Mage::log(sprintf('Order #' . $importData['order_id'] . ' saving error: %s', message), null, 'ce_order_import_errors.log');

            }



        } else {

            $order1->setStatus(Mage_Sales_Model_Order::STATE_COMPLETE);

            $order1->setState(Mage_Sales_Model_Order::STATE_COMPLETE, false);

            $order1->addStatusToHistory($order1->getStatus(), '', false);

            $order1->save();

        }

    }



    public function setAllOtherOrderStatus($write, $prefix, $order1, $importData)

    {
		$importDataOrderStatus = $importData['order_status'];
		
		$verChecksplit = explode(".",Mage::getVersion());
		if ($verChecksplit[1] > 4) {
            try {
				//removed set state on first sql due to issue with order history on orders the dropdown being blank and also makes order not show on frontend
                $write->query(
                    "UPDATE `" . $prefix . "sales_flat_order` "
                        . "SET status = '" . $importDataOrderStatus . "' "
                        . "WHERE entity_id = '" . $order1->getId()
                        . "' AND store_id = '" . $importData['store_id'] . "'");

                $write->query(
                    "UPDATE `" . $prefix . "sales_flat_order_grid` "
                        . "SET status = '" . $importDataOrderStatus . "' "
                        . "WHERE entity_id = '" . $order1->getId()
                        . "' AND store_id = '" . $importData['store_id'] . "'");

                $write->query(
                    "UPDATE `" . $prefix . "sales_flat_order_status_history` "
                        . "SET status = '" . $importDataOrderStatus . "' "
                        . "WHERE parent_id = '" . $order1->getId() . "'");

            } catch (Exception $e) {
                $message = "ERROR SETTING ORDER AS STATUS: " . $e->getMessage();
                echo $message;
                Mage::log(sprintf('Order #' . $importData['order_id'] . ' saving error: %s', message), null, 'ce_order_import_errors.log');
            }
        } 
    }
	
    public function updateInvoiceDates($importData, $write, $prefix, $order1)

    {

        $dateTime = strtotime($importData['created_at']);



        #if (Mage::getVersion() > '1.4.0.1') {

		$verChecksplit = explode(".",Mage::getVersion());

		if ($verChecksplit[1] > 4) {

            try {

                $write->query(

                    "UPDATE `" . $prefix . "sales_flat_invoice` "

                        . "SET created_at = '" . date("Y-m-d H:i:s", $dateTime)

                        . "', updated_at = '" . date("Y-m-d H:i:s", $dateTime)

                        . "' WHERE order_id = '" . $order1->getId()

                        . "' AND store_id = '" . $importData['store_id'] . "'");



                $write->query(

                    "UPDATE `" . $prefix . "sales_flat_invoice_grid` "

                        . "SET created_at = '" . date("Y-m-d H:i:s", $dateTime)

                        . "', order_created_at = '" . date("Y-m-d H:i:s", $dateTime)

                        . "' WHERE order_id = '" . $order1->getId()

                        . "' AND store_id = '" . $importData['store_id'] . "'");

            } catch (Exception $e) {

                $message = "ERROR UPDATING INVOICE DATES: " . $e->getMessage();

                echo $message;

                Mage::log(sprintf('Order #' . $importData['order_id'] . ' saving error: %s', $message), null, 'ce_order_import_errors.log');

            }

        }

    }



    public function updateShippingDates($importData, $write, $prefix, $order1)

    {

        $dateTime = strtotime($importData['created_at']);

        #if (Mage::getVersion() > '1.4.0.1') {

		$verChecksplit = explode(".",Mage::getVersion());

		if ($verChecksplit[1] > 4) {

            try {

                $write->query(

                    "UPDATE `" . $prefix . "sales_flat_shipment` "

                        . "SET created_at = '" . date("Y-m-d H:i:s", $dateTime)

                        . "', updated_at = '" . date("Y-m-d H:i:s", $dateTime)

                        . "' WHERE order_id = '" . $order1->getId()

                        . "' AND store_id = '" . $importData['store_id'] . "'");



                $write->query(

                    "UPDATE `" . $prefix . "sales_flat_shipment_grid` "

                        . "SET created_at = '" . date("Y-m-d H:i:s", $dateTime)

                        . "', order_created_at = '" . date("Y-m-d H:i:s", $dateTime)

                        . "' WHERE order_id = '" . $order1->getId()

                        . "' AND store_id = '" . $importData['store_id'] . "'");



            } catch (Exception $e) {

                $message = "ERROR UPDATING SHIPPING DATES: " . $e->getMessage();

                echo $message;

                Mage::log(sprintf('Order #' . $importData['order_id'] . ' saving error: %s', $message), null, 'ce_order_import_errors.log');

            }

        }

    }

}


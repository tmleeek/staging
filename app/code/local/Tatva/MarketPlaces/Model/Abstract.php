<?php
/**
 * created : 30 sept. 2009
 *
 *
 * updated by <user> : <date>
 * Description of the update
 *
 * @category SQLI
 * @package Tatva_MarketPlaces
 * @author alay
 * @copyright SQLI - 2009 - http://www.tatva.com
 */

/**
 *
 * @package Tatva_MarketPlaces
 */
abstract class Tatva_MarketPlaces_Model_Abstract extends Mage_Core_Model_Abstract
{
	protected $_code;
	protected $_errors;
	protected $_orderError;
	protected $_storeId;
	protected $_store;
	protected $_currencyCode;
	protected $_currentOrder;

	protected $_pathXmlShippingMethod;

	/**
	 * Initialisation
	 */
	abstract protected function init();

  	/**
	 * ExÃ©cution
	 */
	abstract protected function _execute();

	/**
	 * Active / Desactive
	 */
	abstract protected function isEnabled();

    public function execute() {
    	try{
    		if($this->isEnabled()){
	    		$this->_errors = array();
	    		$this->_orderError = false;
				$this->init();
				$this->_prepareStore();

				ini_set ( 'memory_limit', '1024M' );
				$this->_execute();

				if(sizeof($this->_errors) > 0){
					$this->sendEmail(null,$this->_errors);
				}
    		}
		}catch(Exception $e){
			$this->sendEmail($e,$this->_errors);
			Mage::logException($e);
		}
    }


    /**
     * PrÃ©paration d'une nouvelle commande
     *
     */
    protected function _prepareNewOrder()
    {
        $this->_currentOrder = Mage::getModel('sales/order');

        $this->_orderError = false;

        $this->_currentOrder->setStoreId($this->_storeId); 
        $this->_currentOrder->setOrderCurrencyCode($this->_currencyCode);
	    $this->_currentOrder->setBaseCurrencyCode($this->_currencyCode);
	    $this->_currentOrder->setStoreCurrencyCode($this->_currencyCode);

	    $this->_currentOrder->setStoreCurrencyCode($this->_currencyCode);
		$this->_currentOrder->setStoreCurrencyCode($this->_currencyCode);

		$this->_currentOrder->setBaseToGlobalRate(1);
		$this->_currentOrder->setStoreToBaseRate(1);
		$this->_currentOrder->setAdjustmentPositive(0);
    }

    /**
     * Ajoute le montant des frais de port
     * @param $shippintAmount
     */
    protected function addShippingAmount($shippingAmount, $taxAmount, $percentTaxShipping){
    	$this->_currentOrder->setShippingAmount($shippingAmount);
    	$this->_currentOrder->setBaseShippingAmount($shippingAmount);

    	$this->_currentOrder->setPercentTaxShipping($percentTaxShipping);
    	$this->_currentOrder->setShippingTaxAmount($taxAmount);
    	$this->_currentOrder->setBaseShippingTaxAmount($taxAmount);
    }


    /**
     * Ajoute les totaux
     * @param $grandTotal
     * @param $subTotal
     * @param $taxAmount
     * @param $taxPercent
     *
     */
    protected function addTotals($grandTotal,$subTotal, $taxAmount, $taxPercent){
    	$this->_currentOrder->setTaxPercent($taxPercent);

    	$this->_currentOrder->setDiscountAmount(0);
		$this->_currentOrder->setBaseDiscountAmount(0);

		$this->_currentOrder->setGrandTotal($grandTotal);
		$this->_currentOrder->setBaseGrandTotal($grandTotal);

		$this->_currentOrder->setBaseSubtotal( $subTotal);
		$this->_currentOrder->setSubtotal( $subTotal );

		$this->_currentOrder->setTaxAmount( $taxAmount);
		$this->_currentOrder->setBaseTaxAmount( $taxAmount);
    }

	/**
     * PrÃ©pare le mode de paiement partenaire et ajoute les valeurs du partenaire
     * @param string $partnerOrder
     * @param string $partnerDate
     */
      protected function addPartnerValues($partnerOrder,$partnerDate){
    	$this->_preparePayment($partnerOrder,$partnerDate);
    }


    /**
     * Sauvegarde la commande
     */
    protected function _saveOrder($code)
    {
    	try{
    		if(!$this->_orderError){
	    		//Calcul les dates estimÃ©es de livraison
	    		//comment by nisha $this->calculationShippingDates();

	    		//Sauvegarde
				$this->_currentOrder->place();
				$this->_currentOrder->setMarketplacesNewOrderSended('N');
				$this->_currentOrder->setMarketplacesOrderSended('N');
				$this->_currentOrder->setMarketplacesPartnerCode($code);
	        	$this->_currentOrder->save();
	        	//comment by nisha Mage::dispatchEvent('tatvaorder_send_new_order_email', array('order' => $this->_currentOrder));
    		}
    	}catch(Exception $e){
    		throw $e;
    	}
    }

    /**
     * PrÃ©pare le mode de paiement
     *
     * @param string $partnerOrder
     * @param string $partnerDate
     */
    protected function _preparePayment($partnerOrder,$partnerDate){
    	$partner_date = "";
    	if($partnerDate != "")
		{
		$partnerDate = explode(" ",$partnerDate);
	    $date = explode("/",date($partnerDate[0]));
		$partner_date = date('Y-m-d h:i:s',strtotime($date[2].'-'.$date[1].'-'.$date[0].' '.$partnerDate[1]));
        }
   
        $items = Mage::getModel('sales/order_payment')->getCollection ()
			->addAttributeToSelect ( '*' )
			->addAttributeToFilter ( 'marketplaces_partner_order', $partnerOrder )
			->addAttributeToFilter ( 'marketplaces_partner_code', $this->getCode() )
			->load ()->getItems ();
    	if($items && sizeof($items) > 0){
//    		throw new Exception ( Mage::helper('tatvamarketplaces')->__("La commande existe déjà " ));
			$this->addError( Mage::helper('tatvamarketplaces')->__("La commande " . $partnerOrder . " existe déjà " ) );
			$this->_orderError = true;
			return;
    	}

        try
        {
		    $method = Mage::getModel('tatvapayment/method_partner');
		    $payment = Mage::getModel('sales/order_payment')
				->importData(array('method'=>$method->getCode()))
				->setData('marketplaces_partner_code',$this->getCode())
				->setData('marketplaces_partner_order',$partnerOrder)
				->setData('marketplaces_partner_date',$partner_date);

		    $this->_currentOrder->setPayment($payment);
		}
        catch(Exception $e)
        {
			//throw $e;
		}

        /*$method = Mage::getModel('tatvapayment/method_partner');
		$payment = Mage::getModel('sales/order_payment')
				->importData(array('method'=>$method->getCode()))
				->setData('marketplaces_partner_code',$this->getCode())
				->setData('marketplaces_partner_order',$partnerOrder)
				->setData('marketplaces_partner_date',$partner_date);

		$this->_currentOrder->setPayment($payment);*/
    }

    /**
     * Initialise la devise des commandes
     */
    protected function _prepareStore(){
    	$this->_storeId = $this->getConfigData('tatvamarketplaces_orders/configuration/storeview');
    	if(!$this->_storeId){
    		throw new Exception ( Mage::helper('tatvamarketplaces')->__("La vue par dÃ©faut n'est pas configurÃ©" ));
    	}
    	$this->_store = Mage::getModel('core/store')->load($this->_storeId); 
		
		
		$this->_currencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
		
    	//$this->_currencyCode = $this->_store->getBaseCurrency();echo $this->_currencyCode;exit;
    }

    /**
     * Ajoute la mÃ©thode de livaison
     * @param string $shippingMethod
     * @param boolean $mapping
     */
    /*protected function addShippingMethod($shippingMethod, $mapping = false){
    	$code = "";

		if(!$mapping){
			$code = $shippingMethod;
		}else{
			$values = $this->getConfigData($this->getPathXmlShippingMethod());
			if(!$values){
				throw new Exception ( Mage::helper('tatvamarketplaces')->__("Le mapping des modes de transport n'est pas configurÃ©" ));
			}

			$values = unserialize($values);
			foreach($values as $mapping){
				if(strcasecmp($shippingMethod, $mapping['shipping_code']) == 0){
					$code = $mapping['shipping_mapping'];
					break;
				}
			}

			if(!$code){
				throw new Exception ( Mage::helper('tatvamarketplaces')->__("Le modes de transport " . $shippingMethod . " n'est pas configurÃ©" ));
			}
		}

		$carrierModel = Mage::getStoreConfig('carriers/'.$code.'/model');
    	$model = Mage::getModel($carrierModel);

    	$this->_currentOrder->setShippingMethod($model->getCode());
       	$this->_currentOrder->setShippingDescription($model->getTitle($this->getStore()));
    }*/
	
	protected function addShippingMethod($shippingMethod){
    	$code = "";
		
		$shipping = array('Colissimo' => 'colissimo','GLS'=>'gls','TNT Express' => 'tnt');

        if(isset($shipping[$shippingMethod]))
		{
		  $code = $shipping[$shippingMethod];
		}
		else
		{
			throw new Exception ( Mage::helper('tatvamarketplaces')->__("Le modes de transport " . $shippingMethod . " n'est pas configurÃ©" ));
		}



		$carrierModel = Mage::getStoreConfig('carriers/'.$code.'/model');
    	$model = Mage::getModel($carrierModel);

    	$this->_currentOrder->setShippingMethod($model->getCode());
       	$this->_currentOrder->setShippingDescription($model->getTitle($this->getStore()));
    }

     /**
     * Ajoute les informations concernant le client
	 * @param string $prefix
	 * @param string $email
	 * @param string $lastname
	 * @param string $firstname
	 * @param string $street
	 * @param string $postcode
	 * @param string $city
	 * @param string $country
	 * @param string $telephone
     */
    protected function addCustomer($prefix, $email, $lastname, $firstname){
    	$this->_currentOrder
    			->setCustomerIsGuest(true)
				->setCustomerId(null)
                ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID)
    			->setCustomerEmail($email)
				->setCustomerPrefix($prefix)
				->setCustomerFirstname($firstname);


    }

    /**
     * Ajoute l'adresse de livraison
	 * @param string $prefix
	 * @param string $email
	 * @param string $lastname
	 * @param string $firstname
	 * @param string $street
	 * @param string $postcode
	 * @param string $city
	 * @param string $country
	 * @param string $telephone
     */
    protected function addShippingAddress($prefix, $email, $lastname, $firstname,$street,$postcode,$city,$country,$telephone,$company){
        $address = $this->_prepareAddress($prefix, $email, $lastname, $firstname,$street,$postcode,$city,$country,$telephone,$company);
    	$address->setType('shipping');
		$this->_currentOrder->setShippingAddress($address);
    	return $address;

    }
	
	//get country name
	public function getCountrycode($country_name)
	{
			$collection = Mage::getModel('directory/country')->getCollection();
			
			foreach ($collection as $country) 
			{
			  $compareName =  Mage::app()->getLocale()->getCountryTranslation($country->getId());
			  
			   if (strcasecmp($compareName, $country_name) == 0) {
			      
				   return $country->getId();
			       
			   }
			}
			return $this;
	}

    /**
     * Prepare l'adresse de livraison
     *
	 * @param string $prefix
	 * @param string $email
	 * @param string $lastname
	 * @param string $firstname
	 * @param string $street
	 * @param string $postcode
	 * @param string $city
	 * @param string $country
	 * @param string $telephone
     */
    protected function addBillingAddress($prefix, $email, $lastname, $firstname,$street,$postcode,$city,$country,$telephone,$company){
    	$address = $this->_prepareAddress($prefix, $email, $lastname, $firstname,$street,$postcode,$city,$country,$telephone,$company);
    	$address->setType('billing');
    	$this->_currentOrder->setBillingAddress($address);
    	return $address;
    }

	/**
	 * Prepare l'adresse
	 *
	 * @param string $prefix
	 * @param string $email
	 * @param string $lastname
	 * @param string $firstname
	 * @param string $street
	 * @param string $postcode
	 * @param string $city
	 * @param string $country
	 * @param string $telephone
	 * @return Mage_Sales_Model_Order_Address
	 */
    protected function _prepareAddress($prefix, $email, $lastname, $firstname,$street,$postcode,$city,$country,$telephone,$company){
    	$address = Mage::getModel('sales/order_address');
    	$address->setPrefix($prefix);
    	$address->setEmail($email);
    	$address->setLastname($lastname);
    	$address->setFirstname($firstname);
    	$address->setStreet($street);
    	$address->setPostcode($postcode);
    	$address->setCity($city);
    	$address->setCountryId($country);
    	$address->setTelephone($telephone);
        $address->setCompany($company);
    	$address->setStoreId($this->_storeId);
    	return $address;
    }

    /**
     * Ajoute une ligne Ã  la commande
     * @param Mage_Catalog_Model_Product $product
     * @param int $qty
     * @param $taxPercent
     * @param $priceHT
     * @param $rowTotalTTC
     */
    protected function addItem($product, $qty, $taxPercent, $priceHT, $rowTotalTTC, $taxAmount, $ref=NULL){
    	$orderItem = Mage::getModel('sales/order_item');

    	$orderItem->setData('product', $product)
	            ->setProductId($product->getId())
	            ->setProductType($product->getTypeId())
	            ->setSku($product->getSku())
	            ->setName($product->getName())
	            ->setWeight($product->getWeight())
	            ->setQty($qty)
	            ->setQtyOrdered($qty)

	            ->setPrice($priceHT)
	            ->setOriginalPrice($priceHT)

	            ->setBasePrice($priceHT)
	            ->setBaseOriginalPrice($priceHT)
	            ->setTaxAmount($taxAmount)
	            ->setBaseTaxAmount($taxAmount)

	            ->setBaseTaxBeforeDiscount($taxAmount)
	            ->setTaxBeforeDiscount($taxAmount)

	            ->setDiscountAmount(0)
	            ->setBaseDiscountAmount(0)
	            ->setRowTotal($rowTotalTTC - $taxAmount)
	            ->setBaseRowTotal($rowTotalTTC - $taxAmount)
	            ->setRowTotalWithDiscount($rowTotalTTC - $taxAmount)
	            ->setRowWeight($qty * $product->getWeight())
	            ->setTaxPercent($taxPercent)
				->setAappliedRuleIds(1)
	            ->setIsVirtual($product->getIsVirtual());
	            if( isset( $ref ) ) {
	             	$orderItem->setItemPixmaniaId( $ref );
	            }

	    $this->_currentOrder->addItem($orderItem);
    }

    /**
     * Calcule le taux de tva
     * @param Mage_Sales_Model_Order_Address $shippingAddress
     * @param Mage_Sales_Model_Order_Address $billingAddress
     * @param Mage_Catalog_Model_Product $product
     * @return int
     */
    protected function getTaxPercent($shippingAddress, $billingAddress, $product){
		$request = Mage::getModel('tax/calculation')->getRateRequest($shippingAddress, $billingAddress, false, $this->getStore());
		$request->setCountryId($shippingAddress->getCountry());
		$request->setProductClassId($product->getTaxClassId());
		return Mage::getSingleton('tax/calculation')->getRate($request);
    }
    /**
     * Ajoute une erreur
     *
     * @param   string $error
     * @return  mixed
     */
    public function addError($error)
    {
    	$this->_errors[] = $error;
    }

    /**
     * Retrieve information from configuration
     *
     * @param   string $path
     * @return  mixed
     */
    public function getConfigData($path)
    {
        if (empty($this->_code)) {
            return false;
        }
        return Mage::getStoreConfig($path);
    }

    /**
     * Envoi un mail lorsqu'une erreur est levÃ©e ou d'autres erreurs
     * @param $exception
     * @param $tbErrors
     * @return unknown_type
     */
	protected function sendEmail(Exception $exception = null, $tbErrors=null) {
		$template = Mage::getStoreConfig ( 'tatvamarketplaces_orders/configuration/email_errors' );

		$error_report = '';

		//Erreurs
		if ($tbErrors && count($tbErrors)>0) {
			$error_report .= "<br/>";
			foreach ($tbErrors as $_err) {
				$error_report .= "<br/>$_err";
			}
		}

		//Exception levÃ©e
		if ($exception) {
			$error_report .= "<br/><br/>";
			$error_report .= $exception->getMessage ();
			$error_report .= "<br/><br/>" . nl2br ( $exception->getTraceAsString () );
		}

		//Destinataire du mail
		$receiver = Mage::getStoreConfig ( 'tatvamarketplaces_orders/configuration/receiver_errors' );
		$receiverName = Mage::getStoreConfig('trans_email/ident_'.$receiver.'/name');
        $receiverEmail = Mage::getStoreConfig('trans_email/ident_'.$receiver.'/email');

		Mage::getModel ( 'core/email_template' )->sendTransactional (
			$template,
			Mage::getStoreConfig ( 'tatvamarketplaces_orders/configuration/sender_errors' ),
			$receiverEmail ,
			$receiverName,
				array (
						'subject_mail' => '[AZ Boutique][Ventes multi-canal]['. $this->getCode() .'] Erreurs',
						'error_report' => $error_report
					  )
		    );
	}

    /**
     * Retourne le code de la place de marchÃ©
     *
     * @return string
     */
    public function getCode()
    {
        return $this->_code;
    }


    /**
     * Retrieve market places code
     *
     * @return string
     */
    public function getPathXmlShippingMethod()
    {
        return $this->_pathXmlShippingMethod;
    }

    /**
     * Retourne la vue magasin par dÃ©faut
     *
     * @return string
     */
    public function getStore()
    {
        return $this->_store;
    }

    /**
     * Calcul les dates de livraison estimÃ©es
     */
    public function calculationShippingDates(){
    	//Mode de livraison
    	$method = $this->_currentOrder->getShippingMethod();

		$code = $this->getCodeShippingMethod($method);
       	$carrierModel = Mage::getStoreConfig('carriers/'.$code.'/model');
		$shipping = Mage::getModel($carrierModel);
		$shipping->setStore($this->getStore()->getId());

		//DÃ©lai de livraison du mode de transport
		$countryId = $this->getCountry();
		if(!$countryId){
			$countryId = Mage::getStoreConfig('tatvadelivery/estimate_delivery/country');
		}
		$deliveryDays = $shipping->getDeliveryDays($countryId);

		//Date d'aujourd'hui
		$today = new Zend_Date();

		//Date la plus Ã©loignÃ©e
        $maxDeliveryDate = $today;

        //Initialisation inventaire
		$inventory = Mage::getModel('tatvainventory/item');
	    $deliveryDate = new Zend_Date();

     	$inStock = $inventory->getStatusInStock();
		$underwayStock = $inventory->getStatusReplenishment();
		$onOrder = $inventory->getStatusOnOrder();
		$bundles = array();
	    foreach($this->_currentOrder->getAllItems() as $item){
	    	if($item->getProduct()->getTypeId() != 'bundle'){
		    	//Stock du produit
		        $inventoryItem = Mage::getModel('tatvainventory/item')->selectStock($item->getProductId(), $item->getQty());
		        if($inventory->getStatusInStock()==$inventoryItem->getStatus()
		        	&& $inventoryItem->getCurrentStock() < $item->getQty()){
		        		$inventoryItem->setStatus($inventory->getStatusReplenishment());
		        	}

		        //REG PC-107 Calcul du dÃ©lai de livraison + Mantis 629
	        	switch ($inventoryItem->getStatus()){
	        		case $inventory->getStatusInStock() :
	        			if($item->getIsVirtual()){
							 $deliveryDate = Mage::helper('tatvashipping/date')->calculDeliveryDate(0, -4,$inventoryItem->getDeliveryTime(),$code);
	        			}else{
	        				$deliveryDate = Mage::helper('tatvashipping/date')->calculDeliveryDate(0, $deliveryDays,$inventoryItem->getDeliveryTime(),$code);
	        			}
						break;
					case ($inventory->getStatusReplenishment() || $inventory->getStatusOnOrder()) :
	        			if($item->getIsVirtual()){
			        		$deliveryDate = Mage::helper('tatvashipping/date')->calculDeliveryDate(0, -4,$inventoryItem->getDeliveryTime(),$code);
			        	}else{
			        		$deliveryDate = Mage::helper('tatvashipping/date')->calculDeliveryDate($inventoryItem->getAvailabilityDays(), $deliveryDays,0,$code);
			        	}
						break;
					default:
						break;
	        	}

	        	switch ($inventoryItem->getStatus()){
	        		//REG PRO-400 En stock
	        		case $inventory->getStatusInStock() :
	        			$item->setStatusStock('In stock');
	        			break;

	        		//REG PRO-401 En cours de rÃ©approvisionnement
	        		case $inventory->getStatusReplenishment() :
						$item->setStatusStock('Underway replenishment');
	        			if($inventoryItem->getCurrentStock() > 0 || $item->getIsVirtual()){
	        				$item->setEstimatedDeliveryDays($inventoryItem->getAvailabilityDays());
	        			}

	        			if($item->getIsVirtual()){
	        				$item->setEstimatedDeliveryDays(1);
	        			}
	        			break;

	        		//REG PRO-402 Sur commande
	        		case $inventory->getStatusOnOrder() :
	        			$item->setStatusStock('On order');

	        			if($item->getIsVirtual()){
	        				$item->setEstimatedDeliveryDays(1);
	        			}else{
	        				$item->setEstimatedDeliveryDays($inventoryItem->getAvailabilityDays());
	        			}
	        			break;

	        		default:

	        			break;
	        	}

	        	$item->setEstimatedDeliveryDate($deliveryDate);
	        	if($maxDeliveryDate->isEarlier($deliveryDate)){
	        		$maxDeliveryDate = $deliveryDate;
	        	}
	        }

	    }

        foreach($this->_currentOrder->getAllItems() as $item){
        	if(!$item->getIsVirtual() ){
         		$item->setEstimatedDeliveryDate($maxDeliveryDate);
        	}

         }
    }

	/**
	 * Retourne le code du mode de transport
	 *
	 * @param $method
	 * @return string
	 */
	private function getCodeShippingMethod($method){
		$data = explode('_', $method );
	    $carrierCode = $data[0];
        return $carrierCode;
	}
}
<?php
/**
 * created : 8 oct. 2009
 *
 * 
 * updated by <user> : <date>
 * Description of the update
 * 
 * @category SQLI
 * @package Tatva_MarketPlaces
 * @author emchaabelasri
 * @copyright SQLI - 2009 - http://www.tatva.com
 * 
 * EXIG : REF-005
 * REG  :
 */

/**
 * 
 * @package Tatva_MarketPlaces
 */
class Tatva_MarketPlaces_Model_Rueducommerce extends Tatva_MarketPlaces_Model_Abstract{
	
	protected $_xmlData ;
	protected $_logFile ; 
	
	protected $_code                   = 'rueducommerce';
	protected $_pathXmlShippingMethod  = 'tatvamarketplaces_rueducommerce/shipping_methods/mapping';
	protected $_pathXmlEnabled 		   = 'tatvamarketplaces_rueducommerce/get_order/active';
	protected $_canUseInternal              = true;
    protected $_canUseCheckout              = false;
    protected $_canUseForMultishipping      = false;
	const GET_ORDER_URL           	   = 'tatvamarketplaces_rueducommerce/get_order/url';
	const GET_ORDER_login     		   = 'tatvamarketplaces_rueducommerce/get_order/login';
	const GET_ORDER_password           = 'tatvamarketplaces_rueducommerce/get_order/password';
	const SET_ORDER_URL           	   = 'tatvamarketplaces_rueducommerce/set_order/url';
	const SET_ORDER_login     		   = 'tatvamarketplaces_rueducommerce/set_order/login';
	const SET_ORDER_password           = 'tatvamarketplaces_rueducommerce/set_order/password';	
	
	const LOG_FILE_NAME                = 'Ventes-Multi-Canal-Rueducommerce';

	const STATUS_CANCELED = "canceled";
	const STATUS_COMPLETE = "complete";
	
	/**
	 * Initialisation
	 */
	protected function init()
    {
		try
        {    
			$this->_prepareReadXml();
		}
        catch(Exception $e)
        {
			throw $e;
		}
	}

	/**
	 * Active / DÃ©sactive
	 */
	protected function isEnabled()
    {
		try
        {
			return Mage::getStoreConfigFlag ( $this->_pathXmlEnabled );

		}
        catch(Exception $e)
        {
			throw $e;
		}
	}
	/**
	 * Check the configuration data 
	 */
	protected function _checkConfig()
    {
		if(!$this->getConfigData( self:: GET_ORDER_URL)||!$this->getConfigData(self:: GET_ORDER_login)||!$this->getConfigData(self:: GET_ORDER_password))
        {
			throw new Exception( "l'url de connexion et/ou le login et/ou le mot de passe, ne sont pas configurÃ©s correctement. Voir: system > configuration > Ventes multi-canal > RueDuCommerce ", Zend_Log::ERR );
			return ; 
		}	
	}	
	
	/**
	 *	
	 */
	protected function _execute()
    {
		$this->_checkConfig();
		$orders = $this->_xmlData;
		if(!$orders)
        {
			throw new Exception ( "Le chargement du fichier XML n'a recupere aucune information" );
		}

		//Lecture des commandes
		Mage::log ( 'Rueducommerce - Lecture des commandes: DEBUT >>> ', Zend_log::INFO, $this->getLogFile() );
        if(!empty($orders))
        {
    		foreach ( $orders as $order )
            {
                if(!empty($order))
                {
                    try
                    {
        				$infoCommande = $order->infocommande ;
        				$errorOrder = false;

        				Mage::log ( 'DEBUT Traitement Commande NÂ° =  '.(int)$infoCommande->refid.' >>> ', Zend_log::INFO, $this->getLogFile() );

        				//- Prepare une nouvelle commande
        				$this->_prepareNewOrder();
        				$this->_currentOrder->setMarketplacesPartnerCode( $this->_code );
        				$this->_currentOrder->setMarketplacesOrderSended( 'N' );

        				$customer = current( $order->xpath('utilisateur[@type="facturation"]') );
        				//- Ajouter le client
        				$this->addCustomer(
        					(string) $customer->nom->attributes()->titre ,
        					(string) $customer->email,
        					(string) $customer->nom,
        					(string) $customer->prenom

        				);

        				//- Adresse de livraison
        				$adress = current( $order->xpath('utilisateur[@type="livraison"]') );
        				//$country = Mage::getModel('directory/country')->loadByName( (string) $adress->pays );
        				$country = $this->getCountrycode((string) $adress->adresse->pays);
        				$shippingAddress = $this->addShippingAddress(
        					(string) $adress->nom->attributes()->titre ,
        					(string) $adress->email ,
        					(string) $adress->nom ,
        					(string) $adress->prenom ,
        					array( $adress->adresse->rue1 ,
        					       $adress->adresse->rue2),
        					$adress->adresse->cpostal ,
        					$adress->adresse->ville ,
        					$country,
        					$adress->telhome,
                            $adress->societe

        				);
        				//- Adresse de facturation
        				$adress = current( $order->xpath('utilisateur[@type="facturation"]') );
        				$billingAddress = $this->addBillingAddress(
        					(string) $adress->nom->attributes()->titre ,
        					(string) $adress->email ,
        					(string) $adress->nom ,
        					(string) $adress->prenom,
        					array( (string) $adress->adresse->rue1 ,
        					       (string) $adress->adresse->rue2),
        					(int) $adress->adresse->cpostal,
        					(string) $adress->adresse->ville,
        					'',
        					(int) $adress->telhome,
                            $adress->societe
        				);



        				//- Methode de livraison
        				$this->addShippingMethod(utf8_encode( (string)$infoCommande->transport->nom ));

        				//Initialisation des totaux
        				$totalTTC = (float)$infoCommande->montant;
        				$shippingAmount = (float)$infoCommande->transport->montant;
        				$totalTaxAmount = 0;
        				$subTotal = 0;

        				//Ajout des produits
        				foreach ( $infoCommande->list->produit as $item  )
                        {

        					Mage::log ( 'DEBUT Traitement Produit =  '. $item->attributes()->merchantProductId , Zend_log::INFO, $this->getLogFile() );

        					$qty                 = 0;
        					$taxPercent          = 0;
        					$priceHT             = 0;
        					$totalLigneTTC       = 0;
        					$totalLigneTaxAmount = 0;
        					$product_id = 0;

        					  $collection = Mage::getModel('catalog/product')->getCollection();
        			          $collection->setStoreId(0);
        			          //$collection->addStoreFilter($store_id);
        			          $collection->addAttributeToSelect('lengow_id');
        			          $collection->addAttributeToSelect('entity_id');
        			          $collection->addFieldToFilter('lengow_id', $item->attributes()->merchantProductId);

        					  foreach($collection as $model_data)
                 			  {
                 			       $product_id = $model_data->getEntityId();
        					  }
        					  if($product_id ==0)
        					  {
        					  	   Mage::log ( 'Le produit n existe pas' , Zend_log::INFO, $this->getLogFile() );
        							$this->addError( Mage::helper('tatvamarketplaces')->__("Commande " . (string)$order->attributes()->morid  . " : le produit " . $item->attributes()->merchantProductId . " n'existe pas " ) );
        							$this->_orderError = true;
        							$errorOrder = true;
        							break;
        					  }

        					  $product = Mage::getModel('catalog/product')->load( $product_id );

        					$qty                 = (float)$item->attributes()->nb;
        					$totalLigneTTC       = (float)$item->attributes()->price * 1;

        					$taxPercent = $this->getTaxPercent($shippingAddress,$billingAddress,$product);
        					if( ! $taxPercent  )
                            {
        						$taxPercent = 20;
        					}
        					$totalLigneTaxAmount = ($totalLigneTTC - ($totalLigneTTC / (1 + ($taxPercent / 100)))) * $qty;
        					$priceHT = $totalLigneTTC / ( 1 + ($taxPercent / 100) );

        					$subTotal = $subTotal + $priceHT * $qty;
        					$totalTaxAmount += $totalLigneTaxAmount;
        					$this->addItem($product, $qty, round ( $taxPercent , 2 ), $priceHT, $totalLigneTTC, round ( $totalLigneTaxAmount, 2) );

        					Mage::log ( 'FIN Traitement Produit' , Zend_log::INFO, $this->getLogFile() );
        				}

        				if(!$errorOrder)
                        {
        					//Frais de port
        					$shippingTaxAmount = round($shippingAmount - ($shippingAmount / (1 + ($taxPercent / 100))),2);
        					$shippingAmount -= 	$shippingTaxAmount;
        					$totalTaxAmount += $shippingTaxAmount;
        					$this->addShippingAmount($shippingAmount,$shippingTaxAmount,$taxPercent);

        					//Totaux
        					$totalTaxAmount = round($totalTaxAmount,2);
        					$subTotal = round($subTotal,2);
        					$this->addTotals($totalTTC,$subTotal, $totalTaxAmount,$taxPercent);

        					//Donnees partenaire
        					$this->addPartnerValues( (string)$order->attributes()->morid , (string)$infoCommande->date);

        					//Sauvegarde la commande en cours
        					$this->_saveOrder('rueducommerce');

        					Mage::log ( '{SUCCES} Traitement Commande NÂ° =  '.(int)$infoCommande->refid.' >>> ', Zend_log::INFO, $this->getLogFile() );
        				}
                        else
                        {
        					Mage::log ( '{ECHEC} Traitement Commande NÂ°  =  '.(int)$infoCommande->refid.' >>> ', Zend_log::INFO, $this->getLogFile() );
        				}
        			}
                    catch(Exception $e)
                    {
        				Mage::log ( '{ECHEC} Traitement Commande NÂ°  =  '.(int)$infoCommande->refid.' >>> ', Zend_log::INFO, $this->getLogFile() );
        				Mage::logException( $e );
        //				throw $e;
        				$this->addError( Mage::helper('tatvamarketplaces')->__("Commande " . (string)$order->attributes()->morid  . " : " . $e->getMessage() ) );
        				$this->_orderError = true;
        			}
                }
    		}
    		$this->sendTreatmentOrder();
        }
		Mage::log ('Rueducommerce - Lecture des commandes: FIN >>> ', Zend_log::INFO, $this->getLogFile());
		
	}
	
	public function sendTreatmentOrder(){  
		Mage::log ( 'Rueducommerce - Mise à jour des statuts "En cours" : DEBUT >>> ', Zend_log::INFO, $this->getLogFile() );
		$orderCollection = $this->_getOrderCollection();
		Mage::log ( 'Order collection ', Zend_log::INFO, $this->getLogFile() );
		if($orderCollection->getSize()){
			Mage::log ( 'Deb generation xml ', Zend_log::INFO, $this->getLogFile() );
			$dom = new DOMDocument ( '1.0', 'utf-8' );
			$dom->formatOutput = true;
			header ( "Content-Type: text/plain" );
			
			// Ajout de la balise mmie
			$mmie = $dom->createElement ( "mmie" );
			$mmie->setAttribute('version','2.0'); 
			$dom->appendChild ( $mmie );
			
			// Ajout de la balise orders
			$orders = $dom->createElement ( "orders" );
			$orders = $this->getOrderToExport( $dom , $orders, $orderCollection );
			$mmie->appendChild( $orders );
			
			/**
			 * Envoi du fichier
			 */
			try{
				Mage::log ( 'Envoi ', Zend_log::INFO, $this->getLogFile() );
				   $config = array(
					    'adapter'      => 'Zend_Http_Client_Adapter_Socket',
					    'ssltransport' => 'tls'
					);
					$client = new Zend_Http_Client(  Mage::getStoreConfig(self::SET_ORDER_URL ) ,  $config); 
				
				
				//$client = new Zend_Http_Client(  Mage::getStoreConfig(self::SET_ORDER_URL ) ,  array('keepalive' => true)  ); 
				
				$xml = $dom->saveXML();
				Mage::log ( "XML : ", Zend_log::INFO, $this->getLogFile() );
				Mage::log ( $xml, Zend_log::INFO, $this->getLogFile() );
				
			   $client->setParameterPost(
					array(
			        	'requests_xml' => $xml,
			    	)
			    );

				$reponse = $client->request( Zend_Http_Client::POST );
				
				
				
				Mage::log ( "REPONSE : ", Zend_log::INFO, $this->getLogFile() );
				Mage::log ( $reponse->getBody(), Zend_log::INFO, $this->getLogFile() );
				
				if($reponse != null){
					if ($reponse->getStatus() !== 200) {
						throw new Exception ( 'Erreur de connexion Ã  l\'url : ' . Mage::getStoreConfig(self::SET_ORDER_URL ) );
					} else {
						foreach($orderCollection as $order){
						  //Passer la valeur de marketplaces_new_order_sended Ã  O
						  $order->setMarketplacesNewOrderSended ( "O" );
						  $order->getResource ()->saveAttribute ( $order, 'marketplaces_new_order_sended' );
						  Mage::log ( 'RueDuCommerce - Mis Ã  jours des status chez RueDuCommerce: CMD NÂ° ='.$order->getId(), Zend_log::INFO, $this->getLogFile() );

							
							
						}
					}
		 		}else{
		 			//throw new Exception ( 'Erreur : aucune reponse de l\'url :' . $this->getConfigData( self::SET_ORDER_URL ) );
		 			$this->addError( Mage::helper('tatvamarketplaces')->__("{ECHEC} Mise à jour des statuts 'En cours' : aucune reponse de l'url :" . $this->getConfigData( self::SET_ORDER_URL ) ) );
		 		}
			}catch(Exception $e){
				Mage::log ( $e->getMessage(), Zend_log::ERR, $this->getLogFile() );
				Mage::logException ( $e );
				$this->addError( Mage::helper('tatvamarketplaces')->__("{ECHEC} Mise à jour des statuts 'En cours' : " . $e->getMessage() ) );
			}
			
			Mage::log ( 'Rueducommerce - Mise à jour des statuts "En cours" : FIN >>> ', Zend_log::INFO, $this->getLogFile() );
		}else{
			Mage::log ( 'Rueducommerce - Mise à jour des statuts "En cours" : FIN >>> AUCUNE COMMANDE ', Zend_log::INFO, $this->getLogFile() );
		}
	}
	
	/**
	 * return order collection for priceminster
	 * @param void
	 * @return order collection
	 */
	protected function _getOrderCollection(){
		$ruducommerceCode = $this->getCode();
		$collectionOrders = Mage::getModel('sales/order')->getCollection()
						->addAttributeToSelect( '*' )
						->addAttributeToFilter( 'status' , array( 'nin'=> array( self::STATUS_CANCELED , self::STATUS_COMPLETE  ) ) )
						->addAttributeToFilter( 'marketplaces_order_sended', array('in'=>'N'))
						->addAttributeToFilter( 'marketplaces_new_order_sended', array('in'=>'N'))
					    ->addAttributeToFilter( 'marketplaces_partner_code', array('in'=>$ruducommerceCode))  	
					    ;
		return $collectionOrders;  	
	}
	
	/**
     * Execution
	 */
	public function getOrderToExport( $dom , $orders, $orderCollection   ){
		
		foreach ( $orderCollection as $order  ){
		  $cmd = $dom->createElement ( "acknowledged" );
		  
		  $cmd->setAttribute('morid', $this->getPartnerOrderRef( $order ) );
		  $cmd->setAttribute('datetime', date('Y-m-d') . 'T' . date('G:i:s'). 'Z' );
		  $orders->appendChild( $cmd );
		} 	
		return $orders;
			
	}
	
	/**
	 * return partner order code
	 * @param Mage_Sales_Model_Order
	 * @return string
	 */
	public function getPartnerOrderRef( $order ) {
		return $order->getPaymentsCollection()->getFirstItem()->getMarketplacesPartnerOrder();	
	}
	
	/**
	 * Charge le fichier
	 */
	protected function _prepareReadXml() {
		$url   = $this->getConfigData( self:: GET_ORDER_URL ); 
		$login = $this->getConfigData( self:: GET_ORDER_login );
		$pass  = $this->getConfigData( self:: GET_ORDER_password );
		try{
			$file = $this->getXmlFileName();
		   
			 //TODO commenter ce traitement et decommenter ce qui vient juste aprÃ¨s
//			if (! $xml = simplexml_load_file ( $file  ) ) {
//				throw new Exception ( "Impossible de charger le fichier $file " );
//			}
//			
//			if( !empty($file) ){		
//				$response = file_get_contents ( $file );
//				if ($response) {
//					$this->_xmlData = simplexml_load_string ( $response );
//				}
//			}			
//			
//			$this->_xmlData = $xml;
             $config = array(
			    'adapter'      => 'Zend_Http_Client_Adapter_Socket',
			    'ssltransport' => 'tls'
			);
			$client = new Zend_Http_Client(  $this->getConfigData( self::GET_ORDER_URL ) ,  $config); 
			$reponse = $client->request( Zend_Http_Client::GET );
			
			if($reponse != null){
				if ($reponse->getStatus() !== 200) {
					throw new Exception ( 'Erreur de connexion Ã  l\'url : ' . $this->getConfigData( self::GET_ORDER_URL ) );
				} else {
//					Mage::log ( $reponse->getBody(), Zend_log::INFO, $this->getLogFile() );	
					
					$this->_xmlData = simplexml_load_string ( $reponse->getBody() );
				}
	 		}else{
	 			throw new Exception ( 'Erreur : aucune reponse de l\'url :' . $this->getConfigData( self::GET_ORDER_URL ) );
	 		}
			
		}catch(Exception $e){echo 'hey1234564657=='.$e->getMessage();exit;
			throw $e;
		}
	}
	
	/**
	 * return log file name
	 * @return string 
	 */
	public function getLogFile (){
		if( ! $this->_logFile ){
			$this->_logFile = date('Ymd').'-'.self::LOG_FILE_NAME.'.log';
		}
		return $this->_logFile;
	}
	
	/**
	 * get market place code 
	 * @see app1/code/local/Tatva/MarketPlaces/Model/Tatva_MarketPlaces_Model_Abstract#getCode()
	 */
	public function getCode(){
		return $this->_code;
	}
	
	/**
	 * return xml file name 
	 * @return string
	 */
	public function getXmlFileName(){
		return "/usr/local/apache2/htdocs/az-boutique/dev.az-boutique.lnet.fr/ALY/var/log/xml_export_commandes.sample.xml";
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
	            ->setRowTotal($priceHT * $qty)
	            ->setBaseRowTotal($priceHT * $qty)
	            ->setRowTotalWithDiscount($priceHT * $qty)
	            ->setRowWeight($qty * $product->getWeight()) 
	            ->setTaxPercent($taxPercent)
				->setAappliedRuleIds(1)
	            ->setIsVirtual($product->getIsVirtual());
	            if( isset( $ref ) ) {
	             $orderItem->setItemPixmaniaId( $ref );
	            }
    	
	    		$this->_currentOrder->addItem($orderItem);
    }
	
}
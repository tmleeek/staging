<?php
/**
 * created : 8 oct. 2009
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
class Tatva_MarketPlaces_Model_Status_Rueducommerce extends Mage_Core_Model_Abstract{

	const STATUS_CANCELED = "canceled";
	const STATUS_COMPLETE = "complete";

	const SET_ORDER_URL           	   = 'tatvamarketplaces_rueducommerce/set_order/url';
	const SET_ORDER_login     		   = 'tatvamarketplaces_rueducommerce/set_order/login';
	const SET_ORDER_password           = 'tatvamarketplaces_rueducommerce/set_order/password';
	
	protected $_rueducommerceModel ;
	protected $_fileName = "xml_exchange_azboutique.xml"; 
	
	protected $_collectionOrders ;
		
	/**
     * get rueducommerce model 
     * @return Tatva_MarketPlaces_Model_Priceminister 
	 */
	protected function _rueducommerceModel(){
		if( ! $this->_rueducommerceModel ){
			$this->_rueducommerceModel = Mage::getModel('tatvamarketplaces/rueducommerce');
		}
		return 	$this->_rueducommerceModel;
	}

	/**
	 * Main function 
	 */
	public function execute() {
		ini_set ( 'memory_limit', '1024M' );
		Mage::log ( '-----------------', Zend_log::INFO, $this->_rueducommerceModel()->getLogFile() );

		//Enregistrer le fichier
		Mage::log ( 'RueDuCommerce - Mis à jours des status chez RueDuCommerce: DEBUT', Zend_log::INFO, $this->_rueducommerceModel()->getLogFile() );
		
		$orderCollection = $this->_getOrderCollection();
		if( ! count ( $orderCollection ) ){
			Mage::log ( 'RueDuCommerce - Mis à jours des status chez RueDuCommerce: AUCUNE CMD A ENVOYER', Zend_log::INFO, $this->_rueducommerceModel()->getLogFile() );
			return;
		}
		
		Mage::log ( 'Creation du fichier', Zend_log::INFO, $this->_rueducommerceModel()->getLogFile() );
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
		 *  Enregistrer le fichier
		 */
		//$dom->save ( $this->getFileName() );
		
		/**
		 * Envoi du fichier
		 */
		try{
			Mage::log ( 'Envoi du fichier', Zend_log::INFO, $this->_rueducommerceModel()->getLogFile() );
			Mage::log ( Mage::getStoreConfig(self::SET_ORDER_URL ), Zend_log::INFO, $this->_rueducommerceModel()->getLogFile() );
			$xml = $dom->saveXML();
			
			Mage::log ( $xml, Zend_log::INFO, $this->_rueducommerceModel()->getLogFile() );
			
			$config = array(
					    'adapter'      => 'Zend_Http_Client_Adapter_Socket',
					    'ssltransport' => 'tls'
					);
			$client = new Zend_Http_Client(  Mage::getStoreConfig(self::SET_ORDER_URL ) ,  $config); 
			
			//$client = new Zend_Http_Client(  Mage::getStoreConfig(self::SET_ORDER_URL ) ,  array('keepalive' => true)  ); 
			
			$client->setParameterPost(
					array(
			        	'requests_xml' => $xml,
			    	)
			    );
			   
			$reponse = $client->request( Zend_Http_Client::POST );
			Mage::log ( $reponse->getBody(), Zend_log::INFO, $this->_rueducommerceModel()->getLogFile() );
			
			if($reponse != null){
				if ($reponse->getStatus() !== 200) {
					throw new Exception ( 'Erreur de connexion à l\'url : ' . $this->getConfigData( self::SET_ORDER_URL ) );
				} else {
					foreach($this->_collectionOrders as $order){
					  //Passer la valeur de marketplaces_order_sended à O
					  $order->setMarketplacesOrderSended ( "O" );
					  $order->getResource ()->saveAttribute ( $order, 'marketplaces_order_sended' );
					  Mage::log ( 'RueDuCommerce - Mis à jours des status chez RueDuCommerce: CMD N° ='.$order->getId(), Zend_log::INFO, $this->_rueducommerceModel()->getLogFile() );
						
					}
				}
	 		}else{
	 			throw new Exception ( 'Erreur : aucune reponse de l\'url :' . $this->getConfigData( self::SET_ORDER_URL ) );
	 		}
		}catch(Exception $e){
			Mage::log ( $e->getMessage(), Zend_log::ERR, $this->_rueducommerceModel()->getLogFile() );
			Mage::logException ( $e );
		}
				
		Mage::log ( 'RueDuCommerce - Mis à jours des status chez RueDuCommerce: FIN', Zend_log::INFO, $this->_rueducommerceModel()->getLogFile() );
	}
	
	/**
     * Execution
	 */
	public function getOrderToExport( $dom , $orders, $orderCollection   ){
		
		foreach ( $orderCollection as $order  ){
		  if( $order->getStatus() == self::STATUS_CANCELED ){
		  	$cmd = $dom->createElement ( "cancelled" );
		  }elseif( $order->getStatus() == self::STATUS_COMPLETE ){
		  	$cmd = $dom->createElement ( "sent" );
		  	$order = $order->load($order->getId());
		  	Mage::log ( 'CMD N° ='.$order->getIncrementId(), Zend_log::INFO, $this->_rueducommerceModel()->getLogFile() );
		  	foreach($order->getShipmentsCollection() as $_shipment){
		  		$tracks = $_shipment->getTracksCollection();
		  		if ($tracks->count()){
		  			foreach($tracks as $track){
		  				if($track->getNumber()){
		  					$tracking = $dom->createElement ( "tracking", $track->getNumber() );
		  					$cmd->appendChild( $tracking );
		  					Mage::log ( 'CMD N° ='.$order->getIncrementId() . ' / tracking : ' . $track->getNumber(), Zend_log::INFO, $this->_rueducommerceModel()->getLogFile() );
		  				}
		  			}
		  		}
		  	}
		  }//else{
		  	//throw new Exception ( " La commande id= ".$order->getId()." n'a pas le status CANCELED ou COMPLETE" );
		  //}
		  $cmd->setAttribute('morid', $this->getPartnerOrderRef( $order ) );
		  $cmd->setAttribute('datetime', date('Y-m-d') . 'T' . date('G:i:s'). 'Z' );
		  $orders->appendChild( $cmd );

		} 	
		return $orders;
			
	}
	
	/**
	 * return order collection for priceminster
	 * @param void
	 * @return order collection
	 */
	protected function _getOrderCollection(){
		if(!$this->_collectionOrders){
			$ruducommerceCode = $this->_rueducommerceModel->getCode();
			$this->_collectionOrders = Mage::getModel('sales/order')->getCollection()
							->addAttributeToSelect( '*' )
							->addAttributeToFilter( 'status' , array( 'in'=> array( self::STATUS_CANCELED , self::STATUS_COMPLETE  ) ) )
							->addAttributeToFilter( 'marketplaces_order_sended', array('in'=>'N'))
						    ->addAttributeToFilter( 'marketplaces_partner_code', array('in'=>$ruducommerceCode))  	
						    ;
		}
		return $this->_collectionOrders;				  	
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
     * return xml file name
	 */
	protected function getFileName(){
		return $this->_fileName;
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
	
	
}

<?php
/**
 * created : 13 oct. 2009
 *
 *
 * @category SQLI
 * @package Tatva_MarketPlaces
 * @author emchaabelasri
 * @copyright SQLI - 2009 - http://www.tatva.com
 * 
 * EXIG : REF-005
 * 
 * Remarques: 
 * - Selon la documentation technique seules les commandes completes doivent remonter au PIXplace
 *  ( cf: "Comment traiter vos commandes par fichier.csv et web service"  ).
 * - Il y a un choix d'utiliser ";" ou "double tab" comme délimiteur, les doubles tab sont choisi dans ce cas. 
 * 
 * 
 */

/**
 * 
 * @package Tatva_MarketPlaces
 */
class Tatva_MarketPlaces_Model_Status_Pixmania extends Mage_Core_Model_Abstract{

	const STATUS_CANCELED = "canceled";
	const STATUS_COMPLETE = "complete";
	const STATUS_CLOSED   = "closed";
	const DELIMITEUR      = ";";
	
	protected $_pathXmlEnabled 		= 'tatvamarketplaces_pixmania/orders/active';
	
	protected $_pixmaniaModel;
	protected $_closedOrders;
	protected $_fileNameCompletedOrders;
	protected $_fileNameClosedOrders;
	protected $_completedOrders;	
	
	
	
	/**
     * Execution
	 */
	public function execute(){
		
		if($this->isEnabled()){
			$this->getOrderToSend();
			
			// Envoyer les commandes
			$this->sendToPixplace();
		}
		
	}
	
	/**
	 * Active / Désactive
	 */
	protected function isEnabled(){
		try{
			return Mage::getStoreConfigFlag ( $this->_pathXmlEnabled );

		}catch(Exception $e){
			throw $e;
		}
	}
	
	protected function _pixmaniaModel(){
		if( ! $this->_pixmaniaModel ){
		 $this->_pixmaniaModel = Mage::getModel('tatvamarketplaces/pixmania');
		}
		return $this->_pixmaniaModel; 
	}
	
	/**
	 *  retourne les lignes des toutes les commandes envoyées
	 *  @return array
	 */
	public function getOrderToSend(){
		$orderCollection = $this->_getOrderCollection();
		Mage::log ( $orderCollection->getSize(), Zend_log::INFO, $this->_pixmaniaModel()->getLogFile() );	
		if( ! $orderCollection->getSize() ){
			Mage::log ( 'PIXmania - Mis à jours des status chez Pixmania: AUCUNE CMD A ENVOYER', Zend_log::INFO, $this->_pixmaniaModel()->getLogFile() );
			return;
			
		}
		$this->_fileNameCompletedOrders = 'importDelivredOrders-'.date('Ymd').'-'.date('Gis').'.csv';		
		
		//Open file
		$file = fopen(Mage::getBaseDir('media').DS.'pixmania' . DS . $this->_fileNameCompletedOrders, 'w');
				
		//- Parcours les commandes expédiées ou annullées
		foreach ( $orderCollection as $order  ){
			
			// Identifient de la commande chez PIXplace
			$pixplaceCmdId = $order->getPaymentsCollection()->getFirstItem()->getMarketplacesPartnerOrder() ;
			// Ajout d'une ligne par Item
			
			foreach (   $order->getItemsCollection() as $item  ) {
				$line = array( $pixplaceCmdId , $item->getItemPixmaniaId(),"\n"   );
				fputs($file,implode(';',$line));
			}					

			//Passe la valeur de marketplaces_order_sended à O
			$order->setMarketplacesOrderSended ( "O" );
			$order->getResource ()->saveAttribute ( $order, 'marketplaces_order_sended' );			
		}
				
		//Close file
		fclose($file);
	}
	
	/**
	 * return order collection for pixmania
	 * @param void
	 * @return order collection
	 */
	protected function _getOrderCollection(){
		
		$pixmaniaCode = $this->_pixmaniaModel()->getCode();
		$collection = Mage::getModel('sales/order')->getCollection()
						->addAttributeToSelect( '*' )
						->addAttributeToFilter( 'marketplaces_order_sended', array('in'=>'N'))
						->addAttributeToFilter( 'status' , array( 'in'=> array( self::STATUS_COMPLETE , self::STATUS_CLOSED ) ) )
					    ->addAttributeToFilter( 'marketplaces_partner_code', array('in'=>$pixmaniaCode))  	;
		Mage::log ( $collection->getSelect()->__toString(), Zend_log::INFO, $this->_pixmaniaModel()->getLogFile() );			    	    
		return $collection;				  	
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
	 * Envoyer les fichiers au PIXplace
	 * 
	 */
	public function sendToPixplace(){
		$url = $this->_getConfigData( Tatva_MarketPlaces_Model_Pixmania::URL) . '?d=webServices_Server&c=ServerRest';
	
		$client = new Zend_Http_Client( $url  ,  array('keepalive' => true)  ); 
		$client->setAuth( $this->_getConfigData( Tatva_MarketPlaces_Model_Pixmania::LOGIN ), $this->_getConfigData( Tatva_MarketPlaces_Model_Pixmania::PASSWORD )  );
		
//		Mage::log (  "URL : " . $url , Zend_log::INFO, $this->_pixmaniaModel()->getLogFile() );
//		Mage::log (  "LOGIN : " . $this->_getConfigData( Tatva_MarketPlaces_Model_Pixmania::LOGIN ) , Zend_log::INFO, $this->_pixmaniaModel()->getLogFile() );
//		Mage::log (  "MDP : " . $this->_getConfigData( Tatva_MarketPlaces_Model_Pixmania::PASSWORD)  , Zend_log::INFO, $this->_pixmaniaModel()->getLogFile() );
		Mage::log (  $this->_fileNameCompletedOrders , Zend_log::INFO, $this->_pixmaniaModel()->getLogFile() );
	
//		$params = array( 
//				   'rm'=>'importFileContr',
//				   'rf'=> 'importDeliveredOrders' ,
//				   'sl'=> $this->_getConfigData (  Tatva_MarketPlaces_Model_Pixmania::KEY ),
//				   'FILENAME'=>	'@/media/pixmania/'. $this->_fileNameCompletedOrders				
//		) ;

		$params = array( 
				   'rm'=>'importFileContr',
				   'rf'=> 'importDeliveredOrders' ,
				   'sl'=> $this->_getConfigData (  Tatva_MarketPlaces_Model_Pixmania::KEY )				
		) ;		

//		Mage::log ( $params , Zend_log::INFO, $this->_pixmaniaModel()->getLogFile() );		
		                                
		$client->setParameterPost($params);
		$client->setFileUpload(Mage::getBaseDir('media').DS.'pixmania' . DS . $this->_fileNameCompletedOrders, 'FILENAME');
		$reponse = $client->request( Zend_Http_Client::POST );    
				
		Mage::log ( "REPONSE :" , Zend_log::INFO, $this->_pixmaniaModel()->getLogFile() );		
		Mage::log ( $reponse->getBody() , Zend_log::INFO, $this->_pixmaniaModel()->getLogFile() );	
	}
	
	/**
	 * retourne les paramètres de configuration
	 * @param  string : path 
	 * @return string | int   
	 */
	protected function _getConfigData ( $path  ){
		$value =  Mage::getStoreConfig($path);
		if( ! $value ){
			throw new Exception ( Mage::helper('tatvamarketplaces')->__(" Mauvaise configuration en BO, vérifier le chemin $path ") );		
		}
		return 	$value ;
	}

	public function buildCsv( $lines ){
		$string = "";
		foreach (  $lines as $line   ){
			$string .= implode ( self::DELIMITEUR , $line  )."\n"; 
		}
		return $string ;
	}	
}

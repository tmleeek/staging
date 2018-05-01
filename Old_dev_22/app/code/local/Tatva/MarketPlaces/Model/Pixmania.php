<?php
/**
 * created : 6 oct. 2009
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
class Tatva_MarketPlaces_Model_Pixmania extends Tatva_MarketPlaces_Model_Abstract{
	
	protected $_fileCsv ;
	protected $_logFile ; 
	protected $_code                = 'pixmania';
	protected $_pathXmlEnabled 		= 'tatvamarketplaces_pixmania/orders/active';
	protected $_canUseInternal              = true;
    protected $_canUseCheckout              = false;
    protected $_canUseForMultishipping      = false;
	const LOG_FILE_NAME            	= 'Ventes-Multi-Canal-Pixmania';
	
	const URL                = 'tatvamarketplaces_pixmania/orders/url';
	const KEY                = 'tatvamarketplaces_pixmania/orders/key'; 	 
	const LOGIN              = 'tatvamarketplaces_pixmania/orders/login'; 
	const PASSWORD           = 'tatvamarketplaces_pixmania/orders/password'; 
	const DEFAULT_TRANSPORT  = 'tatvamarketplaces_pixmania/orders/default_transport';

	//const DELIMITEUR         = ';';
	const DELIMITEUR         = "\t\t";
	
	const NUM_CMD       = 0;
	const NUM_LIGNE_CMD = 1;
	const DATE_CMD      = 2; 		 
	const FUPID         = 3;
	const QTY           = 4;
	const P_HTT         = 5;
	const TVA           = 6;
	const DEVISE        = 7;
	const EMAIL         = 8;
	const NOM_PRENOM    = 9;
	const ADRESS_1      = 10;
	const ADRESS_2      = 11;
	const C_POSTAL      = 12;
	const VILLE         = 13;
	const REGION        = 14;
	const PAYS          = 15;
	const TEL           = 16;
	const OPTIONS       = 17;
	
	 
	/**
	 * Initialisation
	 */
	protected function init(){
		try{
			$this->_prepareReadCsv();
		}catch(Exception $e){
			throw $e;
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
	
	/**
	 *	Execution
	 */
	protected function _execute(){
		$orderLines = $this->_fileCsv;
		
		if( ! count ( $orderLines )  ){
			return;
		}
		
		//- Classer les items par commande
		$linesParCommande = array();
		foreach (  $orderLines as $key => $line ){
			if(!array_key_exists($line[self::NUM_CMD], $linesParCommande) ){
				$linesParCommande[ $line[self::NUM_CMD] ] = array();
			}
			$linesParCommande[ $line[self::NUM_CMD] ][]= $key ;
		}		

		
		//Lecture des commandes
		Mage::log ( 'PIXmania - Lecture des commandes: DEBUT >>> ', Zend_log::INFO, $this->getLogFile() );
			
		foreach ( $linesParCommande as $cmdId => $itemCommandeIds ){
			
			try{
				//- Prépare une nouvelle commande
				$this->_prepareNewOrder();
				$this->_currentOrder->setMarketplacesPartnerCode( $this->_code );
				$this->_currentOrder->setMarketplacesOrderSended( 'N' );				
				
				$csvItem = $orderLines [ $itemCommandeIds[0] ] ;
				
				//- Ajouter le client									
				$this->addCustomer(
					'',
					'',
					utf8_encode($csvItem[self::NOM_PRENOM] ),
					''
				);

				//- Adresse de livraison
				$shippingAddress = $this->addShippingAddress(
					'',
					'',
					$csvItem[self::NOM_PRENOM],
					'',
					array( $csvItem[self::ADRESS_1], $csvItem[self::ADRESS_2]),
					$csvItem[self::C_POSTAL],
					$csvItem[self::VILLE],
					$csvItem[self::PAYS],
					$csvItem[self::TEL]				
				);

				//- Adresse de facturation
				$billingAddress = $this->addBillingAddress(
					'',
					'',
					$csvItem[self::NOM_PRENOM],
					'',
					array( $csvItem[self::ADRESS_1], $csvItem[self::ADRESS_2]),
					$csvItem[self::C_POSTAL],
					$csvItem[self::VILLE],
					$csvItem[self::PAYS],
					$csvItem[self::TEL]										
				);

				//- Méthode de livraison
				$this->addShippingMethod(  $this->_getConfigData (self::DEFAULT_TRANSPORT)  );			

				//Initialisation des totaux
				$totalTTC = 0;
				$shippingAmount = 0;
				$totalTaxAmount = 0;
				$taxPercent     = 0;
				$subTotal = 0;
				
				//Ajout des produits
				foreach ( $itemCommandeIds as $itemCommandeId  ){
					
					$csvProductItem      = $orderLines [ $itemCommandeId ] ;
					if($csvProductItem[ self::FUPID ] == "Livraison"){
						$taxPercent = 19.6;
						$shippingAmount = round($csvProductItem [ self::P_HTT],2);
						
						$totalTaxAmount += round($csvProductItem [ self::TVA],2);
						//Frais de port						
						$this->addShippingAmount($shippingAmount, round($csvProductItem [ self::TVA],2),$taxPercent);
					}else{
					
						// Prix hors tax
						$priceHT             =  round($csvProductItem [ self::P_HTT],2);
						// Quantité
						$qty                 =  round($csvProductItem [ self::QTY  ],2);
						
						// Tax 
						$tax                 = round($csvProductItem [ self::TVA],2);
						
						$totalLigneTaxAmount =  $tax*$qty; 
						$totalLigneTTC       = $priceHT*$qty + $totalLigneTaxAmount ;						
						
						
						// Load produit
						$product = $this->getProduct( $csvProductItem [ self::OPTIONS ] , $csvProductItem[ self::FUPID ] );
	
						// calcul du pourcentage de la tax
						$taxPercent = $this->getTaxPercent($shippingAddress,$billingAddress,$product);
						if( ! $taxPercent  ){
							$taxPercent = round (( $totalLigneTaxAmount * 100 ) / ( $totalLigneTTC - $totalLigneTaxAmount ), 1);
						}					
						
						$this->addItem($product, $qty, round ( $taxPercent , 2 ) , $priceHT, $totalLigneTTC, $totalLigneTaxAmount, $csvProductItem [ self::NUM_LIGNE_CMD] );
						$totalTaxAmount +=  $totalLigneTaxAmount;					
						$subTotal 		+= $priceHT*$qty;				
					}
				}
				
				
				//Totaux
				$totalTTC = $subTotal + $totalTaxAmount + $shippingAmount;
				
				//$subTotal = $totalTTC - $totalTaxAmount;
				$this->addTotals($totalTTC,$subTotal, $totalTaxAmount,$taxPercent);
				
				//Données partenaire
				$this->addPartnerValues( $cmdId , $csvItem[self::DATE_CMD] );
				
				//Sauvegarde la commande en cours
				$this->_saveOrder();
				Mage::log ( 'PriceMinister - Lecture des commandes > {'.$this->_priceministerFile.'}: {SUCCES} Traitement Commande N° =  '.$cmdId.' >>> ', Zend_log::INFO, $this->getLogFile() );	
			}catch(Exception $e){
				Mage::log ( 'PriceMinister - Lecture des commandes > {'.$this->_priceministerFile.'}: {ECHEC} Traitement Commande N°  =  '.$cmdId.' >>> ', Zend_log::INFO, $this->getLogFile() );	
				Mage::logException($e);
				
				throw $e;
			}
		}
		Mage::log ( 'PIXmania - Lecture des commandes: FIN >>> ', Zend_log::INFO, $this->getLogFile() );		

	}
	/**
	 * Charge le fichier
	 *
	 */
	protected function _prepareReadCsv() {
		$reponse = $this->_getHttpReponse();
		
		if($reponse != null){
			if ($reponse->getStatus() !== 200) {
				throw new Exception ( 'Erreur de connexion à l\'url : ' . $this->_getConfigData( self::URL ) );
			} else {
				$lines = array();
				$datas = explode( "\n" , $reponse->getBody()  );
				foreach ( $datas as $data ){
					$line = explode( self::DELIMITEUR , $data  );
					if(  sizeof( $line )> 1 ){
						$lines[] = $line;
					}
				}
				$this->_fileCsv = $lines; 
			}
 		}else{
 			throw new Exception ( 'Erreur : aucune reponse de l\'url :' . $this->getUrl() );
 		}
		
//		$file = "/usr/local/apache2/htdocs/az-boutique/dev.az-boutique.lnet.fr/ALY/get_order_pix.csv";
//		$handler = fopen($file, "r");
//		$this->_fileCsv = array();
//		while ( ($line = fgetcsv ( $handler, 10000, self::DELIMITEUR )) !== FALSE ) {
//			//Mage::log($line[0]);
//			$values = explode(';',$line[0]);
//			Mage::log($values);
//			$this->_fileCsv[] = $values;
//		}
		
	}
	
    protected function _getHttpReponse(){
		$client = new Zend_Http_Client(  $this->_getConfigData( self::URL ) ,  array('keepalive' => true)  ); 
		$client->setAuth( $this->_getConfigData( self::LOGIN ), $this->_getConfigData( self::PASSWORD )  );
		
		$client->setParameterGet( 
			array( 
				'd' => 'webServices_Server',
				'c' => 'ServerRest',
				'rm'=>'exportFile',
				'rf'=>'exportOrdersToDeliver',
				'site_id'=> 1 ,
				'sl'=> $this->_getConfigData (  self::KEY )						 
		   ) 
		);
	
		return $client->request( Zend_Http_Client::GET );
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
	
	public function getProduct( $options, $fuid ){
	
		if($options){
			$valuesOptions = explode('||',$options);
			$id = null;
			foreach($valuesOptions as $valueOption){
				$values = explode('=',$valueOption);
				if(sizeof($values) >= 2){
					if($values[0] == 'SKU'){
						$datasProduct = explode('_',$values[1]);
						if(sizeof($datasProduct) >= 2){
							$id = $datasProduct[1];
						}
						break;
					}
				}
			}
			
			if($id){
				$product = Mage::getModel('catalog/product')->load($id);
				if($product->getId()){
					return $product;
				}
			}
		}
		throw new Exception ( Mage::helper('tatvamarketplaces')->__("Le produit dont le FUID est $fuid n'existe pas" ));	
	}

	

	
}
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
class Tatva_MarketPlaces_Model_Priceminister extends Tatva_MarketPlaces_Model_Abstract{
	
	protected $_fileCsv ;
	protected $_logFile ; 
	protected $_websiteModel           = null;
	protected $_ftpConnec              = null;
	protected $_tempFile               = 'var/priceminister-temp.csv';
	protected $_priceministerFile      = 'purshase/purshase_intem.csv';	
	
	protected $_code                   = 'priceminister';
	protected $_pathXmlShippingMethod  = 'tatvamarketplaces_priceminister/shipping_methods/mapping';
	protected $_pathXmlEnabled 		   = 'tatvamarketplaces_priceminister/orders/active';
	
	const CONFIG_FTP_ADRESS            = 'tatvamarketplaces_priceminister/configuration/ftp_adress';
	const CONFIG_CUSTOMER_ACCOUNT      = 'tatvamarketplaces_priceminister/configuration/customer_account';
	const CONFIG_PASSWORD              = 'tatvamarketplaces_priceminister/configuration/password';
	const CONFIG_VENDOR_LOGIN          = 'tatvamarketplaces_priceminister/configuration/vendor_login';
	
	const DELIMITER                    = '|';
	const LOG_FILE_NAME                = 'Ventes-Multi-Canal-PriceMinister';
	
	 const C_SELLER_ACCOUNT_ID         =0; 
	 const C_PM_PURSHASE_ID            =1;
	 const C_AUTHORISATION_DATE        =4;
	 const PM_ITEM_ID                  =2;
//	 const SELLER_ADVERT_REF           =3;
//	 const C_AUTHORISATION_DATE        =4;
//	 const PRD_TITLE                   =5;
	 const ITEM_COSTE_PRICE            =6;
//	 const PRD_REFERENCE               =7;
	 const SHIP_COST_PRICE             =8;
	 const SHIPPING_TYPE               =9;
	 const USR_TITLE                   =10;
	 const USA_FIRST_NAME              =11;
	 const USA_LAST_NAME               =12;
	 const USA_ADRESSE1                =13;
	 const USA_ADRESSE2                =14;
	 const USA_ZIP                     =15;
	 const USA_CITY                    =16;
	 const USA_COUNTRY_NAME            =17;
	 const USA_COUNTRY_CODE            =18;
	 const EMAIL_ADRESS                =19;
	 
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
	 * Check the configuration data 
	 */
	protected function _checkConfig(){
		if( ! $this->getConfigData( self:: CONFIG_FTP_ADRESS ) || 
		    ! $this->getConfigData( self:: CONFIG_CUSTOMER_ACCOUNT ) ||
		    ! $this->getConfigData( self:: CONFIG_PASSWORD ) )  {
			Mage::log( "L'adresse du serveur FTP et/ou le compte client et/ou le mot de passe ne sont pas configurés correctement. Voir: system > configuration > Ventes multi-canal > Priceminister > Configuration", Zend_Log::ERR );
			return ; 
		}	
	}	
	
	/**
	 *	
	 */
	protected function _execute(){
		$orderLines = $this->_fileCsv;

		unset( $orderLines[0] );
		if( ! count ( $orderLines )  ){
			return;
		}
		$linesParCommande = array();
		foreach (  $orderLines as $key => $line ){
			$linesParCommande[ $line[self::C_PM_PURSHASE_ID] ][]= $key ;
		}
		
		//Lecture des commandes
		Mage::log ( 'PriceMinister - Lecture des commandes: DEBUT >>> ', Zend_log::INFO, $this->getLogFile() );
		foreach ( $linesParCommande as $cmdId => $itemCommandeIds ){
			
			try{
				//- Prépare une nouvelle commande
				$this->_prepareNewOrder();
				$this->_currentOrder->setMarketplacesPartnerCode( $this->_code );
				$this->_currentOrder->setMarketplacesOrderSended( 'N' );				
				
				$csvItem = $orderLines [ $itemCommandeIds[0] ] ;
				//- Ajouter le client									
				$this->addCustomer(
					utf8_encode($csvItem[self::USR_TITLE]),
					utf8_encode($csvItem[self::EMAIL_ADRESS]),
					utf8_encode($csvItem[self::USA_LAST_NAME]),
					utf8_encode($csvItem[self::USA_FIRST_NAME])
				);

				//- Adresse de livraison
				$shippingAddress = $this->addShippingAddress(
					utf8_encode($csvItem[self::USR_TITLE]),
					utf8_encode($csvItem[self::EMAIL_ADRESS]),
					utf8_encode($csvItem[self::USA_LAST_NAME]),
					utf8_encode($csvItem[self::USA_FIRST_NAME]),
					array( utf8_encode($csvItem[self::USA_ADRESSE1]), utf8_encode($csvItem[self::USA_ADRESSE2])),
					utf8_encode($csvItem[self::USA_ZIP]),
					utf8_encode($csvItem[self::USA_CITY]),
					utf8_encode($csvItem[self::USA_COUNTRY_CODE]),
					''					
				);

				//- Adresse de facturation
				$billingAddress = $this->addBillingAddress(
					utf8_encode($csvItem[self::USR_TITLE]),
					utf8_encode($csvItem[self::EMAIL_ADRESS]),
					utf8_encode($csvItem[self::USA_LAST_NAME]),
					utf8_encode($csvItem[self::USA_FIRST_NAME]),
					array( utf8_encode($csvItem[self::USA_ADRESSE1]), utf8_encode($csvItem[self::USA_ADRESSE2])),
					utf8_encode($csvItem[self::USA_ZIP]),
					utf8_encode($csvItem[self::USA_CITY]),
					utf8_encode($csvItem[self::USA_COUNTRY_CODE]),
					''										
				);
					
				//- Méthode de livraison
				$this->addShippingMethod(utf8_encode($csvItem[self::SHIPPING_TYPE]), true);								

				//Initialisation des totaux
				$totalTTC = 0;
				$shippingAmount = 0;
				$totalTaxAmount = 0;
				
				//Ajout des produits
				foreach ( $itemCommandeIds as $itemCommandeId  ){
					 $csvProductItem = $orderLines [ $itemCommandeId ] ;
					 $shippingAmount += $csvProductItem [ self::SHIP_COST_PRICE ];
					 $totalTTC       += $csvProductItem [ self::ITEM_COSTE_PRICE];
					 $totalLigneTTC   = $csvProductItem [ self::ITEM_COSTE_PRICE];
					 
					$product = Mage::getModel('catalog/product')->load( $csvProductItem [ self::PM_ITEM_ID ] );
					$taxPercent = $this->getTaxPercent($shippingAddress,$billingAddress,$product);
					$totalLigneTaxAmount = $totalLigneTTC - ($totalLigneTTC / (1 + ($taxPercent / 100)));
					$totalTaxAmount += $totalLigneTaxAmount;
					$priceHT = $totalLigneTTC / (1 + ($taxPercent / 100));
					$qty = 1;
					$this->addItem($product, $qty, $taxPercent, $priceHT, $totalLigneTTC, $totalLigneTaxAmount);
				}
				//Frais de port		
				$shippingTaxAmount = $shippingAmount - ($shippingAmount / (1 + ($taxPercent / 100)));									
				$this->addShippingAmount($shippingAmount,$shippingTaxAmount,$taxPercent);
				
				//Totaux
				$totalTTC += $shippingAmount;
				$subTotal = $totalTTC - $totalTaxAmount;
				$this->addTotals($totalTTC,$subTotal, $totalTaxAmount,$taxPercent);
				
				//Données partenaire
				$this->addPartnerValues( $cmdId , $csvItem[self::C_AUTHORISATION_DATE] );
				
				//Sauvegarde la commande en cours
				$this->_saveOrder();
				Mage::log ( 'PriceMinister - Lecture des commandes > {'.$this->_priceministerFile.'}: {SUCCES} Traitement Commande N° =  '.$csvItem[self::C_PM_PURSHASE_ID].' >>> ', Zend_log::INFO, $this->getLogFile() );	
			}catch(Exception $e){
				Mage::log ( 'PriceMinister - Lecture des commandes > {'.$this->_priceministerFile.'}: {ECHEC} Traitement Commande N°  =  '.$csvItem[self::C_PM_PURSHASE_ID].' >>> ', Zend_log::INFO, $this->getLogFile() );	
				Mage::logExeception( $e );
				throw $e;
			}
		}
		Mage::log ( 'PriceMinister - Lecture des commandes: FIN >>> ', Zend_log::INFO, $this->getLogFile() );
		
		// Renommer le fihicer
		$this->_renameFileInPriceminister();

	}
	/**
	 * Charge le fichier
	 *
	 */
	protected function _prepareReadCsv() {
		$ftp = $this->getFtpConnextion();
        $tempFile                   = $this->_tempFile; 
        $priceMinisterFile          = $this->_priceministerFile;		
		try{
			if( $fp = $ftp->read( $priceMinisterFile ,$tempFile ) ){
				if (! file_exists ( $tempFile )) {
					throw new Exception ( "Le fichier $tempFile n'existe pas" );
				}
	            $csvObject  = new Varien_File_Csv();
	            $csvObject->setDelimiter(  self::DELIMITER );
	            $this->_fileCsv = $csvObject->getData( $tempFile );
			}else{
				throw new Exception ( Mage::helper('tatvamarketplaces')->__(" Erreur lors du transfert du fichier $tempFile ") );
			}
		}catch(Exception $e){
			throw $e;
		}
		//- Suppresion du fichier temporaire
		unlink( $tempFile );
	}
	
	public function getFtpConnextion(){
		if( $this->_ftpConnec ){
			return $this->_ftpConnec;
		}
		//- Vérification des paramètres de connextion
		$this->_checkConfig();
		
		//- Paramètres de connexion FTP
	    $paramConnecFtp             = array();
        $paramConnecFtp['host']     = $this->getConfigData( self::CONFIG_FTP_ADRESS       );
        $paramConnecFtp['user']     = $this->getConfigData( self::CONFIG_CUSTOMER_ACCOUNT );
        $paramConnecFtp['password'] = $this->getConfigData( self::CONFIG_PASSWORD         );
       
		//- Etablir la connexion
        $ftp = new Varien_Io_Ftp(); 
		if( ! $ftp->open( $paramConnecFtp ) ){
			throw new Exception ( Mage::helper('tatvamarketplaces')->__(" Echec de connexion FTP ") );
			return;
		}
		$this->_ftpConnec = $ftp;
		return 	$this->_ftpConnec;
	}
	
	protected function _renameFileInPriceminister() {
		$ftp = $this->getFtpConnextion();
		
		//- Récupération du fichier
        $ftp = new Varien_Io_Ftp(); 
		if( ! $ftp->open( $paramConnecFtp ) ){
			throw new Exception ( Mage::helper('tatvamarketplaces')->__(" Echec de connexion FTP ") );
			return;
		}		
		if( $ftp->mv( $this->_priceministerFile, $this->_priceministerFile.'done' ) ){
			Mage::log ( 'PriceMinister - Lecture des commandes : le fichier '.$this->_priceministerFile.' a bien ete renome', Zend_log::INFO, $this->getLogFile() );			
		}else{
			Mage::log ( 'PriceMinister - Lecture des commandes : Erreur lors de renommage du fichier '.$this->_priceministerFile , Zend_log::INFO, $this->getLogFile() );			
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

	public function getCode(){
		return $this->_code;
	}
	
}
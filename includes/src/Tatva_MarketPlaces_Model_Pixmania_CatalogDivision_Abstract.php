<?php
/**
 * created : 14 oct. 2009
 * 
 * @category SQLI
 * @package Tatva_MarketPlaces
 * @author emchaabelasri
 * @copyright SQLI - 2009 - http://www.tatva.com
 *  
 * EXIG : REF-005
 * REG  : MARK-32106, MARK-32106 
 */

/**
 * Description of the class
 * @package Tatva_MarketPlaces
 */
abstract class Tatva_MarketPlaces_Model_Pixmania_CatalogDivision_Abstract  extends  Mage_Core_Model_Abstract {

	protected $_logFile ;
	protected $_ftpConnec    = null;
	protected $_fileCsv      = null;
	protected $_catalog;
	
	protected $_fupidPid     = array(); 
	
	const LOCAL_DIRECTORY    = "var/iziflux-pixmania/";
	
	const LOG_FILE_NAME      = "Iziflux-Pixmania-Division-Catalog" ;
	const DELIMITER          = "|";
	
	const TRANSFERT_TYPE     = 'tatvamarketplaces_pixmania/catalog/type'; 
	const FILENAME           = 'tatvamarketplaces_pixmania/catalog/filename'; 
	const PATH               = 'tatvamarketplaces_pixmania/catalog/path'; 
	const HOT                = 'tatvamarketplaces_pixmania/catalog/hot'; 
	const USERNAME           = 'tatvamarketplaces_pixmania/catalog/username'; 
	const PASSWORD           = 'tatvamarketplaces_pixmania/catalog/password'; 
	const PASSIVEMODE        = 'tatvamarketplaces_pixmania/catalog/passivemode';
	
	const REF_INTERNE = 0;
	const VIDE = 1;
	const LANGUE = 2;
	const MPN = 3;
	const EAN = 4;
	const ID_CATéGORIE = 5;
	const ID_SEGMENT = 6;
	const ID_MARQUE = 7;
	const LIBELLE_PRINCIPAL = 8;
	const LIBELLE_SECONDAIRE = 9;
	const DESCRIPTION = 10;
	const FUPID = 11;
	const URL_MEDIA = 12;
	const PRINCIPAL = 13;
	const TYPE = 14;
	const FUPID_BIS1 = 15;
	const PAYS = 16;
	const DISPONIBILITE = 17;
	const PRIX_HT = 18;
	const ECO_TAXE_HT = 19;
	const CODE_ETAT = 20;
	const FRAIS_LIVRAISON_SIMPLE_HT = 21;
	const FRAIS_LIVRAISON_MULTIPLE_HT = 22;
	const SITE_DE_VENTE = 23;
	const DESCRIPTION_LIVRAISON = 24;
	const PRIX_DE_VENTE_DISCOUNTE_HT = 25;
	const PRIX_HT_BUNDLE = 26;
	const STOCK = 27;
	const FUPID_BIS2 = 28;
	const CODE_HD_FICHE_TECHNIQUE = 29;
	const LANGUE_BIS = 30;
	const DESCRIPTION_TECHNIQUE = 31;

	
	/**
	 * Initialisation
	 */
	abstract protected function init();
	   
  	/**
	 * Exécution
	 */
	abstract protected function _run();
	
    public function runDivision() {
    	try{
			$this->init();
			ini_set ( 'memory_limit', '1024M' );
			$this->_run();
			return $this->_catalog;
		}catch(Exception $e){
			Mage::logException( $e );
		}
    }
    
    /**
     * Enregistrer le journal d'erreur
     * @param string message à loger 
     */
    protected function _logMessage( $message ){
	    Mage::log ( $message , Zend_log::INFO, $this->getLogFile() );    
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
     * Préparer la connexion FTP 
     * @param void 
     * @return Varien_Io_Ftp
	 */
	public function getFtpConnextion(){
		if( $this->_ftpConnec ){
			return $this->_ftpConnec;
		}
		//- Paramètres de connexion FTP
	    $paramConnecFtp             = array();
        $paramConnecFtp['host']     = $this->_getConfigData( self::HOT      );
        $paramConnecFtp['user']     = $this->_getConfigData( self::USERNAME );
        $paramConnecFtp['password'] = $this->_getConfigData( self::PASSWORD );
       
		//- Etablir la connexion
        $ftp = new Varien_Io_Ftp(); 
		if( ! $ftp->open( $paramConnecFtp ) ){
			throw new Exception ( Mage::helper('tatvamarketplaces')->__(" Echec de connexion FTP") );
		}
		$this->_ftpConnec = $ftp;
		return 	$this->_ftpConnec;
	}
	
	/**
	 * Charger le catalog (fichier CSV) depuis Iziflux
	 * @return array
	 */
	protected function _readCsv() {
		$izifluxFile = $this->_getConfigData( self::PATH ).$this->_getConfigData( self::FILENAME );
		
		switch ( $this->_getConfigData( self:: TRANSFERT_TYPE )  ){
			case Tatva_Cibleweb_Model_System_Config_Source_Type::LOCAL :
				
				//- Recupérer le fichier depuis le serveur local
	            $this->_fileCsv =  $this->getCsv( $izifluxFile , self::DELIMITER ); 
				break;

			case Tatva_Cibleweb_Model_System_Config_Source_Type::FTP :
				
				//- Recupérer le fichier depuis le serveur Cibleweb
				$ftp = $this->getFtpConnextion();
				$this->createDir( self::LOCAL_DIRECTORY );
				$tempFile = self::LOCAL_DIRECTORY.$this->_getConfigData( self::FILENAME );
				if( $file = $ftp->read( $this->_getConfigData( self::FILENAME ) , $tempFile ) ){
		            $this->_fileCsv =  $this->getCsv( $tempFile , self::DELIMITER ); 
				}else{
					throw new Exception ( Mage::helper('tatvamarketplaces')->__(" Erreur lors du transfert du fichier $tempFile ") );
				}				
				break;	
		}
	}

	/**
	 * Charger le catalog selon le type de transfert
	 * Si le fichier a été récupérer depuis iziflux alors on récupère le fichier temporaire
	 * sinon dés le debut on a un fichier en local
	 * 
	 * @return array
	 */
	protected function _readLocalCsv() {
		switch ( $this->_getConfigData( self:: TRANSFERT_TYPE )  ){
			case Tatva_Cibleweb_Model_System_Config_Source_Type::LOCAL :
				$izifluxFile = $this->_getConfigData( self::PATH ).$this->_getConfigData( self::FILENAME );
				break;
			case Tatva_Cibleweb_Model_System_Config_Source_Type::FTP :
				$izifluxFile = self::LOCAL_DIRECTORY.$this->_getConfigData( self::FILENAME );
				break;	
		}
        $this->_fileCsv =  $this->getCsv( $izifluxFile , self::DELIMITER );
	}

	
	protected function getCsv( $file , $delimiteur  ){
		$csvObject  = new Varien_File_Csv();
        $csvObject->setDelimiter(  $delimiteur );
        return $csvObject->getData( $file );	
	}
	
	protected function createDir( $dir ) {
		if (! is_dir ( $dir )) {
			if (! mkdir ( $dir, 0755, true )) {
				Mage::log ( "Erreur lors de la création du répertoire de stockage \"$dir\"", Zend_Log::ERR );
				return false;
			}
		}
		return true;
	}

	protected function getFupidProductId(){
		$file = self::LOCAL_DIRECTORY.Tatva_MarketPlaces_Model_Pixmania_Reponse_Product::FILE_PRODUCT_REF_PUFID;
		$datas = $this->getCsv( $file , self::DELIMITER  );
		foreach( $datas as $data ) {
			$this->_fupidPid[ $data[0] ] = $data[1]; 
		}
	}


	
	
	
	
}

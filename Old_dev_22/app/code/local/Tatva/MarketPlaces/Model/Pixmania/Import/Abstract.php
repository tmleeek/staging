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
abstract class Tatva_MarketPlaces_Model_Pixmania_Import_Abstract  extends  Mage_Core_Model_Abstract {

	protected $_method;
	protected $_postParams;
	protected $_urlParam = NULL;
	protected $_codeStep;
	
	const EXPEDITEUR         = 'tatvamarketplaces_pixmania/import/sender';
	const NOM_DEST           = 'tatvamarketplaces_pixmania/import/dest_name';			
	const EMAIL_DEST         = 'tatvamarketplaces_pixmania/import/dest_email'; 
	const URL                = 'tatvamarketplaces_pixmania/import/url';
	const KEY                = 'tatvamarketplaces_pixmania/import/key';			
	const USERNAME           = 'tatvamarketplaces_pixmania/import/login'; 
	const PASSWORD           = 'tatvamarketplaces_pixmania/import/password'; 
	
	/**
	 * Initialisation
	 */
	abstract protected function init();
	

	/**
     * Import des fichiers et recevoir la réponse
	 */
    public function runImport() {
    	try{
			ini_set ( 'memory_limit', '1024M' );    		
			$this->init();
			$client = new Varien_Http_Client( $this->getUrl() ,  array('keepalive' => true)  ); 
			$client->setAuth( $this->_getConfigData( self::USERNAME ), $this->_getConfigData( self::PASSWORD )  );		
		    if( $this->_postParams ){
				$client->setParameterPost( $this->_postParams  );		    
		    }
			$client->request( $this->_method );
		}catch(Exception $e){
			Mage::logException( $e );
			$this->sendEmail( $e );
		}
    }
    
    public function getUrl(){
    	return $this->_getConfigData( self::URL ).$this->_urlParam;
    }
    
	/**
	 * retourne les paramètre de configuration
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
	 * Type de la methode 
	 * @param POST|GET : utiliser Zend_Http_Client::POST OU Zend_Http_Client::GET
	 * @return void
	 */
    public function setMethod( $method  ){
    	$this->_method = $method ; 
    }
    
    /**
     * Ajouter les paremeters POST
     * @param array
     * @return void
     */    
    public function setPostParameters(  $params ){
    	$this->_postParams = $params ; 
    }
    
    /**
     * Ajouter les parametres d'url
     * @param $key
     * @param $value
     * @return void
     */
    public function setUrlParam( $key , $value ){
    	if( $this->_urlParam ){
    		$this->_urlParam .="&$key=$value";
    	}else{
    		$this->_urlParam = "";
    		$this->_urlParam .="?$key=$value";
    	}
    }
    
    public function getCodeStep(){
    	return $this->_codeStep;
    }
    
    /**
     * Envoi un mail lorsqu'une erreur est levée ou d'autres erreurs
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
		
		//Exception levée
		if ($exception) {
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
						'subject_mail' => '[AZ Boutique][Ventes multi-canal][PIXmania][Import Fichiers:'.$this->getCodeStep().'] Erreurs', 
						'error_report' => $error_report 
					  ) 
		    );
	}    
    
	    
    
    
	
}

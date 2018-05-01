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
abstract class Tatva_MarketPlaces_Model_Pixmania_Reponse_Abstract  extends  Mage_Core_Model_Abstract {

	protected $_method;
	protected $_getParam;
	protected $_urlParam = NULL;
	protected $_reponse;
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
	 * Traitement de la réponse
	 */
	abstract protected function sendReport();
	   
    public function getReponse() {
    	try{
			ini_set ( 'memory_limit', '1024M' );    		
			$this->init();
			$this->_getHttpReponse();
			$this->sendReport();
		}catch(Exception $e){
			$this->sendEmail( $e );
			Mage::logException( $e );
		}
    }
    
    protected function _getHttpReponse(){
		$client = new Zend_Http_Client( $this->getUrl() ,  array('keepalive' => true)  ); 
		$client->setAuth( $this->_getConfigData( self::USERNAME ), $this->_getConfigData( self::PASSWORD )  );
		if( $this->_getParam ){
			$client->setParameterGet( $this->_getParam );
		}		
		$this->_reponse = $client->request( $this->_method );    
    }
    
    public function setParameterGet( $params  ){
    	$this->_getParam = $params;
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
     * Lire le fichier csv récuperer depuis PIXmania
     * On suppose pour l'instant que la réponse est récupérée sous
     * forme d'une chaîne de caractére
     * 
     * @param string $fileReponse
     * @return array  $lines
	 */
	public function readCsv( $fileReponse  ){
		// On suppose que la réponse est une chaîne de caractère
		$lines = array();
		$datas = explode( "\n" , $fileReponse  );
		foreach ( $datas as $data ){
			$line = explode( ";" , $data  );
			if(  sizeof( $line )> 1 ){
				$lines[] = $line;
			}
			 
		}
		return $lines;
	}    
    
    
    /**
     * Envoi un mail lorsqu'une erreur est levée ou d'autres erreurs
     * @param $exception
     * @param $tbErrors
     * @return unknown_type
     */
	protected function sendEmail(Exception $exception = null, $tbErrors=null, $reponse=FALSE) {
		
		$template = Mage::getStoreConfig ( 'tatvamarketplaces_orders/configuration/email_errors' );
		if( $reponse ){
		    $template = Mage::getStoreConfig ( 'tatvamarketplaces_pixmania/configuration/email_reponse' );
		}
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

        if ($tbErrors && count($tbErrors)>0) {
			$receiverName = $this->_getConfigData ( self::NOM_DEST );
	        $receiverEmail = $this->_getConfigData ( self::EMAIL_DEST );
        }        
		Mage::getModel ( 'core/email_template' )->sendTransactional ( 
			$template, 
			Mage::getStoreConfig ( 'tatvamarketplaces_orders/configuration/sender_errors' ), 
			$receiverEmail , 
			$receiverName,
				array (
						'subject_mail' => '[AZ Boutique][Ventes multi-canal][PIXmania][Rapport Import Fichiers:'.$this->getCodeStep().']', 
						'error_report' => $error_report 
					  ) 
		    );
	}        
    
	    
    
    
	
}

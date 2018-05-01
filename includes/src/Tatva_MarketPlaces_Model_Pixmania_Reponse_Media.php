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
class Tatva_MarketPlaces_Model_Pixmania_Reponse_Media  extends  Tatva_MarketPlaces_Model_Pixmania_Reponse_Abstract {

	protected $_rapportError  = array();
	protected $_codeStep      = "MEDIA";
	
	const PRODUCT_IMPORTED      = "CRE";
	const PRODUCT__NON_IMPORTED = "FAI";

	const PUFID           = 0;
	const STATUS          = 4;
	
	protected function init(){
		$this->setMethod( Zend_Http_Client::GET );
		$this->setUrlParam( 'd' , 'webServices_Server' );
		$this->setUrlParam( 'c' , 'ServerRest' );		
		$this->setParameterGet( array( 
									   'rm'=>'exportFile',
									   'rf'=>'getReportFile',
									   'sl'=>$this->_getConfigData( Tatva_MarketPlaces_Model_Pixmania_Import_Abstract::KEY ),
									   'PARENT_FILE_ID'=>'',
									   'FILE_TYPE_CODE'=>'MED',
									   'FILE_CONTENT_TYPE_CODE'=>'RPT'						 
		                                ) 
		                         );
	}
	
	protected function sendReport(){
		$reponse = $this->_reponse;
		if($reponse != null){
			if ($reponse->getStatus() !== 200) {
				throw new Exception ( 'Erreur de connexion à l\'url : ' . $this->getUrl() );
			} else {
				// Add FUPID to product
				$this->postTreatment( $reponse->getBody() );
				
				// Send repport error
				if( $this->_rapportError  ){
				    $this->sendEmail( NULL , $this->_rapportError , TRUE );
				}
				
			}
 		}else{
 			throw new Exception ( 'Erreur : aucune reponse de l\'url :' . $this->getUrl() );
 		}		
	}
	
	
	protected function postTreatment( $fileReponse ){
		$mediaLines = $this->readCsv( $fileReponse );
		foreach( $mediaLines as $mediaLine ){
			//- Si L'image n'est pas importée
			if(  $mediaLine[ self::STATUS] == self::PRODUCT__NON_IMPORTED ){
				$this->_rapportError[] = "Image non importée chez Pixmania, FUPID: ".$mediaLine[ self::PUFID];
			}
		}
	}
	
	
}

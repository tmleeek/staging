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
class Tatva_MarketPlaces_Model_Pixmania_Reponse_PriceInventory  extends  Tatva_MarketPlaces_Model_Pixmania_Reponse_Abstract {

	protected $_rapportWillbe    =array( 'Les produits suivant seront en ligne à la prochaine synchronisation :' ); 
	protected $_rapportCanBe     =array( 'Les produits suivant pourront passer en ligne à la prochaine synchronisation:' ); 
	protected $_rapportWillBeNot =array( 'Les produits suivant ne peuvent pas passer en ligne à la prochaine synchronisation:' ); 

	protected $_codeStep      = "MEDIA";
	
	const  WILL_BE_ON_LINE     = "ACT";
	const  CAN_BE_ON_LINE      = "INA";
	const  WILL_BE_NOT_ON_LINE = "BLK";
 
	const PUFID           = 0;   // 1er   colonne
	const STATUS          = 13;  // 14ème colonne
	
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
				
				// Send repport 
				$rapport = array();
				$rapport = array_merge ( $this->_rapportWillbe     , $rapport  );
				$rapport = array_merge ( $this->_rapportCanBe      , $rapport  );
				$rapport = array_merge ( $this->_rapportWillBeNot  , $rapport  );
				
				$this->sendEmail( NULL , $rapport );
				
			}
 		}else{
 			throw new Exception ( 'Erreur : aucune reponse de l\'url :' . $this->getUrl() );
 		}		
	}
	
	
	protected function postTreatment( $fileReponse ){
		$stockInventoryLines = $this->readCsv( $fileReponse );
		foreach( $stockInventoryLines as $stockInventoryLine ){
			
			if(  $stockInventoryLine[ self::STATUS] == self::WILL_BE_ON_LINE ){
				$this->_rapportWillbe[] = "FUPID: ".$stockInventoryLine[ self::PUFID];
			}
			if(  $stockInventoryLine[ self::STATUS] == self::CAN_BE_ON_LINE ){
				$this->_rapportCanBe[] = "FUPID: ".$stockInventoryLine[ self::PUFID];
			}
			if(  $stockInventoryLine[ self::STATUS] == self::WILL_BE_NOT_ON_LINE ){
				$this->_rapportWillBeNot[] = "FUPID: ".$stockInventoryLine[ self::PUFID];
			}
									
		}
	}
	
}

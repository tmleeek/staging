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
class Tatva_MarketPlaces_Model_Pixmania_Reponse_Product  extends  Tatva_MarketPlaces_Model_Pixmania_Reponse_Abstract {

	
	protected $_rapportError  = array();
	protected $_rapportSucces = array();
	protected $_codeStep      = "PRODUIT";
	
	
	const FILE_PRODUCT_REF_PUFID = "produit-importes-pufid.csv";
	
	const PRODUCT_IMPORTED      = "CRE";
	const PRODUCT__NON_IMPORTED = "FAI";
	
	const NON                   = "N";
	const YES                   = "Y";	

	const REF_INTERNE     = 0 ;
	const STATUS          = 11;
	const REF_PIXPLACE    = 12;
	const SPECIFIC_STATUS = 13;
	
	protected function init(){
		$this->setMethod( Zend_Http_Client::GET );
		$this->setUrlParam( 'd' , 'webServices_Server' );
		$this->setUrlParam( 'c' , 'ServerRest' );		
		$this->setParameterGet( array( 
									   'rm'=>'exportFile',
									   'rf'=>'getReportFile',
									   'sl'=>$this->_getConfigData( Tatva_MarketPlaces_Model_Pixmania_Import_Abstract::KEY ),
									   'PARENT_FILE_ID'=>'',
									   'FILE_TYPE_CODE'=>'PRD',
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
				if( $this->_rapportError ){
				    $this->sendEmail( NULL , $this->_rapportError, TRUE );
				}
				
				// Enregistrer dans un fichier temporaire les produits qui sont bien importés et qu'on doit envoyer leur autres fichier
				$this->saveRefProductInLocalServer();
			}
 		}else{
 			throw new Exception ( 'Erreur : aucune reponse de l\'url :' . $this->getUrl() );
 		}		
	}
	
	
	protected function postTreatment( $fileReponse ){
		$productsLines = $this->readCsv( $fileReponse );
		foreach( $productsLines as $productLine ){
			//- Si le produit est importé
			if(  $productLine[ self::STATUS] == self::PRODUCT_IMPORTED ){

				//- L'envoi du MEDIA et TECH_INFO est necessaire 
				if( $productLine[ self::SPECIFIC_STATUS] == self::NON ){
					$this->_rapportSucces[]= array( $productLine[ self::REF_INTERNE]  , $productLine[ self::REF_PIXPLACE]   );
				}
				
				//- Enregistrer le FUPID
				$product = Mage::getModel('catalog/product')->load( $productLine[ self::REF_INTERNE] );
				$product->setFupid( $productLine[ self::REF_PIXPLACE] );
				$product->getResource ()->saveAttribute ( $product, 'fupid' );
				unset( $product );
				
			//- Si le produit n'est pas importé
			}elseif( $productLine[ self::STATUS] == self::PRODUCT__NON_IMPORTED ){
				$this->_rapportError[] = "Produits non importé chez Pixmania, ref interne: ".$productLine[ self::REF_INTERNE];
			//- Si aucun retour d'une information
			}else{
				throw new Exception ( "Erreur aucun status récupéré pour le produit id = ".$productLine[ self::REF_INTERNE] );
			}
		}
	}
	
	public function saveRefProductInLocalServer (){
		$directory  = Tatva_MarketPlaces_Model_Pixmania_CatalogDivision_Abstract::LOCAL_DIRECTORY;
		$file       = $directory.self::FILE_PRODUCT_REF_PUFID;
		$csvObject  = new Varien_File_Csv();
        $csvObject->setDelimiter(  Tatva_MarketPlaces_Model_Pixmania_CatalogDivision_Abstract::DELIMITER );
        $this->_fileCsv = $csvObject->saveData( $file ,  $this->_rapportSucces );		
	}
	
	
	
}

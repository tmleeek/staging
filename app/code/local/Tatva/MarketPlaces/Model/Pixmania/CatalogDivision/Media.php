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
class Tatva_MarketPlaces_Model_Pixmania_CatalogDivision_Media  extends  Tatva_MarketPlaces_Model_Pixmania_CatalogDivision_Abstract {

	
	protected function init(){
		try{
			$this->_readLocalCsv();
			$this->getFupidProductId();
		}catch(Exception $e){
			throw $e;
		}
	}

	/**
     * Execution 
	 */
	protected function _run(){
		
		//$produitsPixmania = array();
		$mediaPixmania = "";
		foreach ( $this->_fileCsv  as $produitIzi  ){

			if(  array_key_exists ( $produitIzi[Tatva_MarketPlaces_Model_Pixmania_CatalogDivision_Abstract::REF_INTERNE ] , $this->_fupidPid  )  ){
				$line   = array();
				if( sizeof( $produitIzi )  ){
					$line[] = $this->_fupidPid [  $produitIzi[Tatva_MarketPlaces_Model_Pixmania_CatalogDivision_Abstract::REF_INTERNE ]  ];
					$line[] = $produitIzi[Tatva_MarketPlaces_Model_Pixmania_CatalogDivision_Abstract::URL_MEDIA ];
					$line[] = $produitIzi[Tatva_MarketPlaces_Model_Pixmania_CatalogDivision_Abstract::PRINCIPAL ];
					$line[] = $produitIzi[Tatva_MarketPlaces_Model_Pixmania_CatalogDivision_Abstract::TYPE ];
					$mediaPixmania .= implode(';', $line )."\n";
				}
			}
			
		}
		$this->_catalog = $mediaPixmania;
	}
	
	
	
}

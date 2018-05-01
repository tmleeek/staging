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
class Tatva_MarketPlaces_Model_Pixmania_CatalogDivision_Product  extends  Tatva_MarketPlaces_Model_Pixmania_CatalogDivision_Abstract {

	protected function init(){
		try{
			$this->_readCsv();
		}catch(Exception $e){
			throw $e;
		}
	}

	/**
     * Execution 
	 */
	protected function _run(){
		$produitsIziflux = $this->_fileCsv;
		//$produitsPixmania = array();
		$produitsPixmania = "";
		foreach ( $produitsIziflux as $produitIzi  ){
			$line   = array();
			if( sizeof( $produitIzi ) > 1 ){
				$line[] = $produitIzi[Tatva_MarketPlaces_Model_Pixmania_CatalogDivision_Abstract::REF_INTERNE ];
				$line[] = $produitIzi[Tatva_MarketPlaces_Model_Pixmania_CatalogDivision_Abstract::VIDE ];
				$line[] = $produitIzi[Tatva_MarketPlaces_Model_Pixmania_CatalogDivision_Abstract::LANGUE ];
				$line[] = $produitIzi[Tatva_MarketPlaces_Model_Pixmania_CatalogDivision_Abstract::MPN ];
				$line[] = $produitIzi[Tatva_MarketPlaces_Model_Pixmania_CatalogDivision_Abstract::EAN ];
				$line[] = $produitIzi[Tatva_MarketPlaces_Model_Pixmania_CatalogDivision_Abstract::ID_CATéGORIE ];
				$line[] = $produitIzi[Tatva_MarketPlaces_Model_Pixmania_CatalogDivision_Abstract::ID_SEGMENT ];
				$line[] = $produitIzi[Tatva_MarketPlaces_Model_Pixmania_CatalogDivision_Abstract::ID_MARQUE ];
				$line[] = $produitIzi[Tatva_MarketPlaces_Model_Pixmania_CatalogDivision_Abstract::LIBELLE_PRINCIPAL ];
				$line[] = $produitIzi[Tatva_MarketPlaces_Model_Pixmania_CatalogDivision_Abstract::LIBELLE_SECONDAIRE ];
				$line[] = $produitIzi[Tatva_MarketPlaces_Model_Pixmania_CatalogDivision_Abstract::DESCRIPTION ];
				$produitsPixmania .= implode(';', $line )."\n";
			}
		}
		$this->_catalog = $produitsPixmania;
	}
}

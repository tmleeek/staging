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
class Tatva_MarketPlaces_Model_Pixmania_CatalogDivision_PriceInventory  extends  Tatva_MarketPlaces_Model_Pixmania_CatalogDivision_Abstract {

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
		$priceInventoryPixmania = "";
		foreach ( $this->_fileCsv  as $produitIzi  ){
			if(  array_key_exists ( $produitIzi[Tatva_MarketPlaces_Model_Pixmania_CatalogDivision_Abstract::REF_INTERNE ] , $this->_fupidPid  )  ){
				$line   = array();
				if( sizeof( $produitIzi )  ){
					$line[] = $this->_fupidPid [  $produitIzi[Tatva_MarketPlaces_Model_Pixmania_CatalogDivision_Abstract::REF_INTERNE ]  ];
					
					$begin = 16;
					$end   = 27;
					// récupération des colonnes 16->27 
					for (  $i=$begin ; $i <= $end ; $i++   ){
						$line[] = $produitIzi[ $i ];
					}
					$priceInventoryPixmania .= implode(';', $line )."\n";
					
				}
			}
			
		}
		$this->_catalog = $priceInventoryPixmania;
	}
		
	
	
}

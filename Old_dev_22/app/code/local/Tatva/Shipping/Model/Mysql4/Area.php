<?php

/**
 *
 * @package Tatva_Shipping
 */
class Tatva_Shipping_Model_Mysql4_Area extends Mage_Core_Model_Mysql4_Abstract
{
	
	/**
	 * Constructor
	 */
    protected function _construct()
    {
        $this->_init('tatvashipping/shipping_area', 'area_id');
    }
    
    
	/**
	 * delete the link between areas and countries
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return Tatva_Shipping_Model_Mysql4_Rule
	 */
	protected function deleteCountries(Mage_Core_Model_Abstract $object) {
		$condition = $this->_getWriteAdapter ()->quoteInto ( 'area_id = ?', $object->getId () );
		$this->_getWriteAdapter ()->delete ( $this->getTable ( 'tatvashipping/shipping_area_country' ), $condition );
		return $this;
	}
	
 	/** 
 	 * After save : update the link between areas and countries
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return Tatva_Shipping_Model_Mysql4_Area
	 */
	protected function _afterSave(Mage_Core_Model_Abstract $object) {
		$this->deleteCountries ( $object );
		foreach ( ( array ) $object->getData ( 'countries_ids' ) as $countryId ) {
			$countriesArray = array ();
			$countriesArray ['area_id'] = $object->getId ();
			$countriesArray ['country_id'] = $countryId;
			$this->_getWriteAdapter ()->insert ( $this->getTable ( 'tatvashipping/shipping_area_country'), $countriesArray );
		}
		return parent::_afterSave ( $object );
	}
	
	/**
	 * After load : load the link between areas and countries
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return Tatva_Sponsoring_Model_Mysql4_Area
	 */
	protected function _afterLoad(Mage_Core_Model_Abstract $object) {
	
		$select = $this->_getReadAdapter ()
			->select ()
			->from ( $this->getTable ( 'tatvashipping/shipping_area_country') )
			->where ( 'area_id = ?', $object->getId () );
		if ($data = $this->_getReadAdapter ()->fetchAll ( $select )) {
			$countriesArray = array ();
			foreach ( $data as $row ) {
				$countriesArray [$row ['country_id']] = $row ['country_id'];
			}
			$object->setCountriesIds ( $countriesArray );
		}
		
		return parent::_afterLoad ( $object );
	}
	
	protected function _beforeDelete(Mage_Core_Model_Abstract $object) {
		$this->deleteCountries ( $object );
		return parent::_beforeDelete($object);
	}
	
	/**
     * Vérifie que la zone n'existe pas 
     * 
     * 
     * @param int $areaId
     * @param string $areaCode
     * @return boolean
     */
    public function exists($areaId, $areaCode){
    	$select = $this->_getReadAdapter ()
    			->select ()
    			->from(array('area'=>$this->getMainTable()))
    	  		->where('area_code = ?',$areaCode);
     	if($areaId){
     		$select->where('area.area_id != ?', $areaId);
     	}
		
     	
    	$data = $this->_getReadAdapter()->fetchAll($select);
    	$count = 0;
    	if($data && is_array($data)){
    		$count = sizeof($data);
    	}
    	return  $count > 0 ? true : false;
     }
}

?>
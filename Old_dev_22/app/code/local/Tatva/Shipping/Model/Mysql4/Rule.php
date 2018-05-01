<?php


/**
 * 
 * @package Tatva_Shipping
 */
class Tatva_Shipping_Model_Mysql4_Rule extends Mage_Core_Model_Mysql4_Abstract
{
	/**
	 * Constructor
	 */
    protected function _construct()
    {
        $this->_init('tatvashipping/shipping_rules', 'shipping_rule_id');
    }
    
    /**
     * Checks if the rule exists
     * 
     * EXIG TRA-001
     * REG BO-713
     * 
     * @param int $ruleId
     * @param string $shippingCode
     * @param double $weightMin
     * @param double $weightMax
     * @param array $areasIds
     * @return boolean
     */
    public function exists($ruleId, $shippingCode,$weightMin, $weightMax, $areasIds){

    	try{
	    	$selectMin = $this->_getReadAdapter ()
	    			->select ()
	    			->from(array('rule'=>$this->getMainTable()),'shipping_rule_id')
	    	  		->where('shipping_code = ?',$shippingCode)
	    	  		->where($weightMin . ' between weight_min and weight_max')
	    	  		->where('weight_max != '. $weightMin);
	
	    	$selectMax = $this->_getReadAdapter ()
	    			->select ()
	    			->from(array('rule'=>$this->getMainTable()),'shipping_rule_id')
	    	  		->where('shipping_code = ?',$shippingCode)
	    	  		->where($weightMax . ' between weight_min and weight_max')
	    	  		->where('weight_min != '. $weightMax);
	     	
	    	if($ruleId){
	     		$selectMin->where('rule.shipping_rule_id != ?', $ruleId);
	     		$selectMax->where('rule.shipping_rule_id != ?', $ruleId);
	     	}
	
	     	if($areasIds && sizeof($areasIds)>0){
	
	     		$selectMin->joinLeft(
	     			array('rule_area' =>$this->getTable ( 'tatvashipping/shipping_rules_area' )),
	     			'rule_area.shipping_rule_id = rule.shipping_rule_id'
	     		);
	     		$selectMin->where('area_id IN (?)', $areasIds);
	
	     		$selectMax->joinLeft(
	     			array('rule_area' =>$this->getTable ( 'tatvashipping/shipping_rules_area' )),
	     			'rule_area.shipping_rule_id = rule.shipping_rule_id'
	     		);
	     		$selectMax->where('area_id IN (?)', $areasIds);
	     	}
	
	
	    	$dataMin = $this->_getReadAdapter()->fetchCol($selectMin);
			$dataMax = $this->_getReadAdapter()->fetchCol($selectMax);
    	}catch(Exception $e){
    		Mage::log($e->getMessage());
    	}
    	$count = 0;
    	if($dataMin && is_array($dataMin)){
    		$count += sizeof($dataMin);
    	}
    	if($dataMax && is_array($dataMax)){
    		$count += sizeof($dataMax);
    	}
    	return  $count > 0 ? true : false;
     }
    
	/**
	 * delete the link between rules and areas
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return Tatva_Shipping_Model_Mysql4_Rule
	 */
	protected function deleteAreas(Mage_Core_Model_Abstract $object) {
		$condition = $this->_getWriteAdapter ()->quoteInto ( 'shipping_rule_id = ?', $object->getId () );
		$this->_getWriteAdapter ()->delete ( $this->getTable ( 'tatvashipping/shipping_rules_area' ), $condition );
		return $this;
	}
	
 	/** 
 	 * After save : update the link between rules and areas
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return Tatva_Shipping_Model_Mysql4_Rule
	 */
	protected function _afterSave(Mage_Core_Model_Abstract $object) {
		$this->deleteAreas ( $object );
		foreach ( ( array ) $object->getData ( 'areas_ids' ) as $areaId ) {
			$areasArray = array ();
			$areasArray ['shipping_rule_id'] = $object->getId ();
			$areasArray ['area_id'] = $areaId;
			$this->_getWriteAdapter ()->insert ( $this->getTable ( 'tatvashipping/shipping_rules_area'), $areasArray );
		}
		return parent::_afterSave ( $object );
	}
	
	/**
	 * After load : load the link between rules and areas
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return Tatva_Shipping_Model_Mysql4_Rule
	 */
	protected function _afterLoad(Mage_Core_Model_Abstract $object) {
	
		$select = $this->_getReadAdapter ()
			->select ()
			->from ( $this->getTable ( 'tatvashipping/shipping_rules_area') )
			->where ( 'shipping_rule_id = ?', $object->getId () );
		if ($data = $this->_getReadAdapter ()->fetchAll ( $select )) {
			$areasArray = array ();
			foreach ( $data as $row ) {
				$areasArray [$row ['area_id']] = $row ['area_id'];
			}
			$object->setAreasIds ( $areasArray );
		}
		
		return parent::_afterLoad ( $object );
	}
	
	protected function _beforeDelete(Mage_Core_Model_Abstract $object) {
		$this->deleteAreas ( $object );
		return parent::_beforeDelete($object);
	}
}

?>
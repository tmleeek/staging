<?php

/**
 * 
 * @package Tatva_Shipping
 */
class Tatva_Shipping_Model_Mysql4_Rule_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('tatvashipping/rule');
    }
    
    public function addShippingFilter($code)
    {
        $this	->getSelect()
        		->where('shipping_code = ?', $code);
        return $this;
    } 
    
    public function addWeightFilter($weight)
    {
        $this	->getSelect()
        		->where($weight . ' between weight_min and weight_max'); 
        return $this;
    } 
    
	public function addAreaFilter($code)
    {
        $this	->getSelect()
        		->join(array('rule_area'=>$this->getTable('tatvashipping/shipping_rules_area')),
        			'rule_area.shipping_rule_id=main_table.shipping_rule_id')
        		->join(array('area'=>$this->getTable('tatvashipping/shipping_area')),
        			'rule_area.area_id=area.area_id and area_code= "'. $code .'"' )  ; 
        return $this;
    } 

	public function addCountryFilter($code)
    {
        $this	->getSelect()
        		->join(array('rule_area'=>$this->getTable('tatvashipping/shipping_rules_area')),
        			'rule_area.shipping_rule_id=main_table.shipping_rule_id')
        		->join(array('area'=>$this->getTable('tatvashipping/shipping_area')),
        			'rule_area.area_id=area.area_id' )
        		->join(array('country'=>$this->getTable('tatvashipping/shipping_area_country')),
        			'area.area_id=country.area_id and country.country_id = "' . $code . '"'  )  ; 
        return $this;
    } 
	public function addAreaOrder()
    {
        $this	->getSelect()->order('weight_min') ;   
        return $this;
    } 
	public function addAreasToResult()
    {
    	$areas = array();
        foreach ($this as $rule) {
            $areas[$rule->getId()] = array();
        }

        if (!empty($areas)) {
            $select = $this->getConnection()->select()
                ->from(array('rule_country'=>$this->getTable('tatvashipping/shipping_rules_area')))
                ->where($this->getConnection()->quoteInto(
                    'rule_country.shipping_rule_id IN (?)',
                    array_keys($areas))
                );
                
            $data = $this->getConnection()->fetchAll($select);
            
            foreach ($data as $row) {
                $areas[$row['shipping_rule_id']][] = $row['area_id'];
            }
        }

        foreach ($this as $rule) {
            if (isset($areas[$rule->getId()])) {
				$rule->setData('areas', $areas[$rule->getId()]);
            }
        }
        return $this;
    }
    
    public function joinAreas($values){
    	
    	$this->getSelect()
    		->join(array('rule_country'=>$this->getTable('tatvashipping/shipping_rules_area')),
    					'rule_country.shipping_rule_id=main_table.shipping_rule_id and area_id IN('.implode ( ',', $values ) .')');   
    	return $this;	    
    
    }
}

<?php

/**
 * 
 * @package Tatva_Shipping
 */
class Tatva_Shipping_Model_Mysql4_Area_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('tatvashipping/area');
    }
        
	public function addCountriesToResult()
    {
    	$countries = array();
        foreach ($this as $rule) {
            $countries[$rule->getId()] = array();
        }
        $countAllCountries = Mage::getModel('directory/country')->getCollection()->count();
        
        if (!empty($countries)) {
            $select = $this->getConnection()->select()
                ->from(array('rule_country'=>$this->getTable('tatvashipping/shipping_area_country')))
                ->where($this->getConnection()->quoteInto(
                    'rule_country.area_id IN (?)',
                    array_keys($countries))
                );
                
            $data = $this->getConnection()->fetchAll($select);
            
            foreach ($data as $row) {
                $countries[$row['area_id']][] = $row['country_id'];
                
            }
        }

        foreach ($this as $rule) {
            if (isset($countries[$rule->getId()])) {
             	$countriesRule = array();
            	$i=0;
            	foreach($countries[$rule->getId()] as $key=>$value ){
            		$countriesRule[$key] = "'".$value."'";
            		$i++;
            		if($i==4){
            			
            			$countriesRule['.'] = '.';
            			break;
            		}
            	}
            	
            	$rule->setData('countries', $countriesRule);
            }
        }
        return $this;
    }
    
    public function joinCountries($values){

    	$this->getSelect()
    		->join(array('rule_country'=>$this->getTable('tatvashipping/shipping_area_country')),
    					'rule_country.area_id=main_table.area_id and country_id IN('.implode ( ',', $values ) .')');
    	return $this;
    
    }

  public function toOptionArray($emptyLabel = '&nbsp;')
    {
        $options = $this->_toOptionArray('country_id', 'name', array('title'=>'iso2_code'));

        $sort = array();
        foreach ($options as $index=>$data) {
            $name = Mage::app()->getLocale()->getCountryTranslation($data['value']);
            if (!empty($name)) {
                $sort[$name] = $data['value'];
            }
        }

        ksort($sort);
        $options = array();
        foreach ($sort as $label=>$value) {
            $options[] = array(
               'value' => $value,
               'label' => $label
            );
        }

        if (count($options)>0 && $emptyLabel !== false) {
            array_unshift($options, array('value'=>'', 'label'=>$emptyLabel));
        }
        return $options;
    }
}

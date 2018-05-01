<?php
class MondialRelay_Pointsrelais_Model_System_Config_Source_Shipping_Pointsrelaiscd
{
    public function toOptionArray()
    {
        $tableRate = Mage::getSingleton('pointsrelais/carrier_pointsrelaiscd');
        $arr = array();
        
        foreach ($tableRate->getCode('condition_name') as $k=>$v) 
        {
        	$arr[] = array('value'=>$k, 'label'=>$v);
        }
//        if(!count($arr)){
//        	$arr[] = array('value'=>'groups[flatrate][fields][specificcountry][value]', 'label'=>' Livrer aux pays spécifiques');
//        }
        return $arr;
    }
}
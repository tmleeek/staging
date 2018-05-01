<?php
class MondialRelay_Pointsrelais_Model_System_Config_Source_Shipping_Pointsrelaisld1
{
    public function toOptionArray()
    {
        $tableRate = Mage::getSingleton('pointsrelais/carrier_pointsrelaisld1');
        $arr = array();
        
        foreach ($tableRate->getCode('condition_name') as $k=>$v) 
        {
        	$arr[] = array('value'=>$k, 'label'=>$v);
        }
        return $arr;
    }
}
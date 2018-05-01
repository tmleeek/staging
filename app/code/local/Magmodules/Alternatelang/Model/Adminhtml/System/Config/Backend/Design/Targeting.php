<?php
/**
 * Magmodules.eu - http://www.magmodules.eu - info@magmodules.eu
 * =============================================================
 * NOTICE OF LICENSE [Single domain license]
 * This source file is subject to the EULA that is
 * available through the world-wide-web at:
 * http://www.magmodules.eu/license-agreement/
 * =============================================================
 * @category    Magmodules
 * @package     Magmodules_Alternatelang
 * @author      Magmodules <info@magmodules.eu>
 * @copyright   Copyright (c) 2016 (http://www.magmodules.eu)
 * @license     http://www.magmodules.eu/license-agreement/  
 * =============================================================
 */

class Magmodules_ALternatelang_Model_Adminhtml_System_Config_Backend_Design_Targeting extends Mage_Adminhtml_Model_System_Config_Backend_Serialized_Array {    

 	protected function _beforeSave() 
 	{
        $value = $this->getValue();
        if(is_array($value)) {
            unset($value['__empty']);
            if(count($value)){            	            	
            	$value = $this->orderData($value, 'group');
            	$keys = array();
            	for($i=0; $i < count($value); $i++){
            		$keys[] = 'targeting_' . uniqid();
            	}   
				foreach($value as $key => $field){													
					$value[$key]['store_id'] = $field['store_id'];
					$value[$key]['language_code'] = $field['language_code'];
					$value[$key]['group'] = $field['group'];			
				}
				$value = array_combine($keys, array_values($value));            	
            }
        }
        $this->setValue($value);
        parent::_beforeSave();
    }

	function orderData($data, $sort)
	{ 
		$code = "return strnatcmp(\$a['$sort'], \$b['$sort']);"; 
		usort($data, create_function('$a,$b', $code)); 
		return $data; 
	} 
	    
}

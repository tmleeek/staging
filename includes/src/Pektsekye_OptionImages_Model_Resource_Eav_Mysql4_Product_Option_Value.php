<?php

class Pektsekye_OptionImages_Model_Resource_Eav_Mysql4_Product_Option_Value extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Option_Value
{
    /**
     * Duplicate product options value
     *
     * @param Mage_Catalog_Model_Product_Option_Value $object
     * @param int $oldOptionId
     * @param int $newOptionId
     * @return Mage_Catalog_Model_Product_Option_Value
     */
    public function duplicate(Mage_Catalog_Model_Product_Option_Value $object, $oldOptionId, $newOptionId)
    {
	
	
         $result = parent::duplicate($object, $oldOptionId, $newOptionId);			
		
		
			$read   = $this->_getReadAdapter();			
         $write  = $this->_getWriteAdapter();
			$maintable = $this->getTable('catalog/product_option_type_value');
			$imagetable = $this->getTable('optionimages/product_option_type_image');		
				  
			$select = $read->select()
				->from($maintable, array('option_type_id'))
				->where('option_id=?', $oldOptionId);
			$oldTypeIds = $read->fetchCol($select);

			$select = $read->select()
				->from($maintable, array('option_type_id'))
				->where('option_id=?', $newOptionId);
			$newTypeIds = $read->fetchCol($select);

			foreach ($oldTypeIds as $ind => $oldTypeId) {
            $sql = 'REPLACE INTO `' . $imagetable . '` '
                . 'SELECT NULL, ' . $newTypeIds[$ind] . ', `store_id`, `image`'
                . 'FROM `' . $imagetable . '` WHERE `option_type_id`=' . $oldTypeId;
				$write->query($sql);			
			}

        return $result;
    }
	
}
<?php
/**
 * created : 09 oct. 2009
 * 
 * @category Tatva
 * @package Tatva_FlashSales
 * @author Elmiloud Chaabelasri
 * @copyright Tatva - 2009 - http://www.Tatva.com
 */

/**
 * @package Tatva_cibleweb
 */
class Tatva_Cibleweb_Model_Attribute_Product extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product {
	
	protected $attributeTypes = array();
	
	/**
	 * récupère la valeur d'un attribut d'un produit pour un magasin donné.
     * @param int $entityId
     * @param string $attributeCode
     * @param int $storeId
     * @return mixed
	 */      
    public function selectValue($entityId, $attributeCode, $storeId) {
    	
    	$attribute = null;
    	
    	if (key_exists($attributeCode, $this->attributeTypes)) {
    		$attribute = $this->attributeTypes[$attributeCode];
    	} else {
    	
	    	if (empty($attributeCode)) {
	    		return null;
	    	}
	    	
	    	// Récupération du backend type de l'attribut
	    	$select = $this	->_getReadAdapter()->select()
	    					->from($this->getTable('eav/attribute'), array('attribute_id', 'backend_type'))
	    					->where('entity_type_id=?', Mage::getModel ( 'eav/entity' )->setType ( 'catalog_product' )->getTypeId ())
	    					->where('attribute_code=?', $attributeCode)
	    	;
	    					
	    	if (!($attribute = $this->_getReadAdapter()->fetchRow($select))) {
	    		return null;
	    	}
	    	
	    	$this->attributeTypes[$attributeCode] = $attribute;
	    	
    	}
    	
    	// Récupération de la valeur
    	$select = $this	->_getReadAdapter()->select()
    					->from( array('product' => $this->getTable('catalog/product')), 
    												array('myval'=>new Zend_Db_Expr("IF(ISNULL(val2.value),val1.value,val2.value)"))
    												)
    					->joinLeft(array('val1' => $this->getTable('catalog/product') . "_{$attribute['backend_type']}")
    									, "product.entity_id=val1.entity_id AND val1.attribute_id={$attribute['attribute_id']} AND val1.store_id = 0", 
    									array())
    					->joinLeft(array('val2' => $this->getTable('catalog/product') . "_{$attribute['backend_type']}")
    									, "product.entity_id=val2.entity_id AND val2.attribute_id={$attribute['attribute_id']} AND val2.store_id = $storeId", 
    									array())
    					->where("product.entity_id=?",$entityId)
    					
    	;
    	
        return $this->_getReadAdapter()->fetchOne($select);
    }
	
}
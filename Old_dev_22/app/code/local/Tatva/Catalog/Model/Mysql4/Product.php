<?php
/**
 * created : 9 févr 2010
 * Catalog product resource model
 * 
 * 
 * updated by <user> : <date>
 * Description of the update
 * 
 * @category SQLI
 * @package Sqli_Catalog
 * @author alay
 * @copyright SQLI - 2009 - http://www.sqli.com
 */

/**
 * Catalog product model
 * 
 * @package Sqli_Catalog
 */
class Tatva_Catalog_Model_Mysql4_Product extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product
{
	protected $attributeTypes = array();
	
    /**
	 * recupere la valeur d'un attribut d'un produit pour un magasin donne.
	 * La valeur par défaut (store=0) sera ramenee si la valeur n'est pa definie pour le magasin donne
	 * 
	 * ex : Mage::getResourceSingleton('sqlicatalog/product')->selectValue(22327, 'name', 3);
	 *
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
	    	
	    	// Rï¿½cupï¿½ration du backend type de l'attribut
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
    	
    	// Rï¿½cupï¿½ration de la valeur
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

    /**
	 * recupere la valeur d'un attribut d'un produit pour un magasin donne.
	 * La valeur par défaut (store=0) sera ramenee si la valeur n'est pa definie pour le magasin donne
	 * 
	 * ex : Mage::getResourceSingleton('sqlicatalog/product')->selectValue(22327, 'name', 3);
	 *
     * @param int $entityId
     * @param string $attributeCode
     * @param int $storeId
     * @return mixed
	 */      
    public function selectValueNotDefault($entityId, $attributeCode, $storeId) {
    	
    	$attribute = null;
    	
    	if (key_exists($attributeCode, $this->attributeTypes)) {
    		$attribute = $this->attributeTypes[$attributeCode];
    	} else {
    	
	    	if (empty($attributeCode)) {
	    		return null;
	    	}
	    	
	    	// Rï¿½cupï¿½ration du backend type de l'attribut
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
    	
    	// Rï¿½cupï¿½ration de la valeur
    	$select = $this	->_getReadAdapter()->select()
    					->from( array('product' => $this->getTable('catalog/product')), 
    												array('myval'=>new Zend_Db_Expr("IF(ISNULL(val2.value),0, val2.value)"))
    												)
    					->joinLeft(array('val2' => $this->getTable('catalog/product') . "_{$attribute['backend_type']}")
    									, "product.entity_id=val2.entity_id AND val2.attribute_id={$attribute['attribute_id']} AND val2.store_id = $storeId", 
    									array())
    					->where("product.entity_id=?",$entityId)
    					
    	;
    	
        return $this->_getReadAdapter()->fetchOne($select);
    } 
}

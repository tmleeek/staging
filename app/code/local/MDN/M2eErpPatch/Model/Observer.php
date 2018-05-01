<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2013 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_M2eErpPatch_Model_Observer {

    /**
     * Update product attribute if available_qty change
     *
     * @param Varien_Event_Observer $observer
     */
    public function salesorderplanning_productavailabilitystatus_aftersave(Varien_Event_Observer $observer) {   
    	//Mage::helper('ewpagecache/api')->setIgnoreFlushes(true);
        $productAvailabilityStatus = $observer->getEvent()->getproductavailabilitystatus();
        if ($this->fieldHasChanged($productAvailabilityStatus, 'pa_available_qty') || (mage::getStoreConfig('m2eerppatch/general/init_mode') == 1)) {
            $productId = $productAvailabilityStatus->getpa_product_id();
            if(!is_null($productId) && $productId>0){
                $product = mage::getModel('catalog/product')->load($productId);
                if($product->getId()>0){
                    $attributeCode = mage::getStoreConfig('m2eerppatch/general/qty_attribute');
                    if($attributeCode){
                        $availableQty = $productAvailabilityStatus->getpa_available_qty();
                        if (!is_null($availableQty)) {
                            if (Mage::getStoreConfig('advancedstock/general/avoid_magento_auto_reindex')) {
                              Mage::getSingleton('catalog/Resource_Product_Action')->updateAttributes(array($productId), array($attributeCode => $availableQty), 0);
                            }else{
                              Mage::getSingleton('catalog/product_action')->updateAttributes(array($productId), array($attributeCode => $availableQty), 0);
                            }
                        }
                    }
                }
            }
        }
    }

    private function fieldHasChanged($object, $fieldname) {
        if ($object->getData($fieldname) != $object->getOrigData($fieldname))
            return true;
        else
            return false;
    }
}

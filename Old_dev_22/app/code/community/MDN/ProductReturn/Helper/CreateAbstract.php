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
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author     : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_ProductReturn_Helper_CreateAbstract extends Mage_Core_Helper_Abstract
{
    public function manageProductDestination($product, $websiteId, $description, $rmaId = null)
    {
        $productId   = $product['product_id'];
        $qty         = $product['qty'];
        $destination = $product['destination'];


        mage::helper('ProductReturn/Stock')->productBackInStock($productId, $qty, $destination, $websiteId, $description, $rmaId);
    }
}

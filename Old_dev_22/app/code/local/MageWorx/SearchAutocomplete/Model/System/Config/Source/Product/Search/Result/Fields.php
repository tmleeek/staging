<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_SearchAutocomplete
 * @copyright  Copyright (c) 2011 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Search Autocomplete extension
 *
 * @category   MageWorx
 * @package    MageWorx_SearchAutocomplete
 * @author     MageWorx Dev Team
 */

class MageWorx_SearchAutocomplete_Model_System_Config_Source_Product_Search_Result_Fields {

    protected $_options;

    public function toOptionArray() {
        if (!$this->_options) {
            $this->_options = array(
                array('value' => 'product_name', 'label' => Mage::helper('searchautocomplete')->__('Product Name')),
                array('value' => 'sku', 'label' => Mage::helper('searchautocomplete')->__('SKU')),
                array('value' => 'product_image', 'label' => Mage::helper('searchautocomplete')->__('Product Image')),
                array('value' => 'reviews_rating', 'label' => Mage::helper('searchautocomplete')->__('Reviews Rating')),
                array('value' => 'short_description', 'label' => Mage::helper('searchautocomplete')->__('Short Description')),
                array('value' => 'description', 'label' => Mage::helper('searchautocomplete')->__('Description')),
                array('value' => 'price', 'label' => Mage::helper('searchautocomplete')->__('Price')),
                array('value' => 'add_to_cart_button', 'label' => Mage::helper('searchautocomplete')->__('Add to Cart Button')),
            );
        }
        return $this->_options;
    }

}
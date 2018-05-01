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
 * @copyright  Copyright (c) 2012 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Search Autocomplete extension
 *
 * @category   MageWorx
 * @package    MageWorx_SearchAutocomplete
 * @author     MageWorx Dev Team
 */

class MageWorx_SearchAutocomplete_Model_System_Config_Source_Product_Search_Result_Sort_Order {

    protected $_options;

    public function toOptionArray() {
        if (!$this->_options) {
            $this->_options = array(
                array('value' => 'price_asc', 'label' => Mage::helper('searchautocomplete')->__('Price Asc')),
                array('value' => 'price_desc', 'label' => Mage::helper('searchautocomplete')->__('Price Desc')),
                array('value' => 'relevance', 'label' => Mage::helper('searchautocomplete')->__('Relevance')),
                array('value' => 'name_asc', 'label' => Mage::helper('searchautocomplete')->__('Name A-Z')),
                array('value' => 'name_desc', 'label' => Mage::helper('searchautocomplete')->__('Name Z-A')),
            );
        }
        return $this->_options;
    }

}
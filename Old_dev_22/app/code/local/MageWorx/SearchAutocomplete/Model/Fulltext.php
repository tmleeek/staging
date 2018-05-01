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

class MageWorx_SearchAutocomplete_Model_Fulltext extends Mage_Core_Model_Abstract {

    protected function _construct() {
        $this->_init('searchautocomplete/fulltext');
    }

    public function regenerateIndex($storeId = null, $pageId = null) {        
        $this->getResource()->regenerateIndex($storeId, $pageId);
        return $this;
    }

    public function cleanIndex($storeId = null, $pageId = null) {
        $this->getResource()->cleanIndex($storeId, $pageId);
        return $this;
    }

    public function resetSearchResults() {
        $this->getResource()->resetSearchResults();
        return $this;
    }

    public function prepareResult($query = null) {
        if (!$query instanceof Mage_CatalogSearch_Model_Query) {
            $query = Mage::helper('catalogSearch')->getQuery();
        }
        $queryText = Mage::helper('catalogSearch')->getQueryText();
        if ($query->getSynonimFor()) {
            $queryText = $query->getSynonimFor();
        }
        $this->getResource()->prepareResult($this, $queryText, $query);
        return $this;
    }

    public function getSearchType($storeId = null) {
        return Mage::helper('searchautocomplete')->getSearchType($storeId);
    }

}
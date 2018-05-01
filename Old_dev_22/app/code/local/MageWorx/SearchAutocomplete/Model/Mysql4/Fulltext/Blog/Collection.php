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

class MageWorx_SearchAutocomplete_Model_Mysql4_Fulltext_Blog_Collection extends AW_Blog_Model_Mysql4_Post_Collection {   
    
    public function setOrder($attribute, $dir = 'DESC') {
        if ('relevance' == $attribute) {
            $this->getSelect()->order("relevance $dir");
        } else {
            parent::setOrder($attribute, $dir);
        }

        return $this;
    }

    public function addSearchFilter($query) {
        Mage::getSingleton('searchautocomplete/fulltext')->prepareResult();

        $this->getSelect()->joinInner(
                array('search_result' => $this->getTable('searchautocomplete/blog_result')),
                $this->getConnection()->quoteInto(
                        'search_result.`post_id`=main_table.`post_id` AND search_result.`query_id` = ?',
                        $this->_getQuery()->getId()
                ),
                array('relevance' => 'relevance')
        );

        return $this;
    }
    
    protected function _getQuery() {
        return Mage::helper('catalogSearch')->getQuery();
    }

}
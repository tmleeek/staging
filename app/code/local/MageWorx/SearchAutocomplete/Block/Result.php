<?php

class MageWorx_SearchAutocomplete_Block_Result extends MageWorx_SearchAutocomplete_Block_Result_Abstract {

    protected $_pageCollection;
    protected $_blogCollection;
    protected $_newsCollection;

    public function __construct() {
        Mage::helper('searchautocomplete')->setLastQueryText(Mage::helper('catalogSearch')->getEscapedQueryText());
        parent::__construct();
    }
    public function getPageResultCount() {
        if (!$this->getData('page_result_count')) {
            $size = $this->_getPageCollection()->getSize();
            $this->setPageResultCount($size);
        }
        return $this->getData('page_result_count');
    }

    protected function _getPageCollection() {
        if (is_null($this->_pageCollection)) {
            $this->_pageCollection = Mage::getResourceModel('searchautocomplete/fulltext_collection');
            $this->_pageCollection->addSearchFilter(Mage::helper('catalogSearch')->getEscapedQueryText())
                    ->addStoreFilter(Mage::app()->getStore());
        }
        
        return $this->_pageCollection;
    }

    protected function _getBlogCollection() {
        if (is_null($this->_blogCollection)) {
            $this->_blogCollection = Mage::getResourceModel('searchautocomplete/fulltext_blog_collection');
            $this->_blogCollection->addSearchFilter(Mage::helper('catalogSearch')->getEscapedQueryText())
                    ->addStoreFilter(Mage::app()->getStore());
        }
        return $this->_blogCollection;
    }

    protected function _sanitizeContent($page) {
        return Mage::helper('searchautocomplete')->sanitizeContent($page);
    }

    public function _getNewsCollection() {
        if ((string) Mage::getConfig()->getModuleConfig('MW_Cmspro')->active == 'true' && class_exists('MW_Cmspro_Model_Mysql4_News_Collection')) {
            if (is_null($this->_newsCollection)) {
                $this->_newsCollection = Mage::getResourceModel('searchautocomplete/fulltext_cmspro_collection')
                        ->addSearchFilter(Mage::helper('catalogSearch')->getEscapedQueryText());
            }
        }
        
        return $this->_newsCollection;
    }
}
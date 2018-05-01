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
class MageWorx_SearchAutocomplete_Model_Mysql4_Fulltext extends Mage_Core_Model_Mysql4_Abstract {
    const XML_PATH_FILTER_CMS_PAGES = 'mageworx_tweaks/searchautocomplete/filter_cms_pages';

    public function _construct() {
        $this->_init('searchautocomplete/fulltext', 'page_id');
    }

    protected function _regenerateStoreIndex($storeId, $pageIds = null) {    
        
        try {
            $this->cleanIndex($storeId, $pageIds);

            $contentFilter = Mage::getModel('searchautocomplete/cms_content_filter');
            $designSettings = $contentFilter->getDesignSettings();            
            $designSettings->setArea('frontend');
            $designSettings->setStore($storeId);

            $lastPageId = 0;

            while (true) {
                $pages = $this->_getSearchablePages($storeId, $pageIds, $lastPageId);
                if (!$pages) {
                    break;
                }
                // index cms
                $pageIndexes = array();
                foreach ($pages as $pageData) {
                    //load widgets
                    $pageData['content'] = preg_replace_callback('@{{.*?}}@si','MageWorx_SearchAutocomplete_Helper_Data::loadWidget', $pageData['content']);

                    $lastPageId = $pageData['page_id'];
                    if (!isset($pageData['page_id'])) {
                        continue;
                    }

                    $index = array();
                    if (isset($pageData['title'])) {
                        $index[] = $pageData['title'];
                    }

                    if (isset($pageData['content'])) {
                        $html = "";
                        try {
                            $html = $contentFilter->process($pageData['content']);
                        } catch (Exception $e) {
                            Mage::log($pageData);
                            Mage::log($e);
                            continue;
                        }

                        $searchString = array('@&lt;script.*?&gt;.*?&lt;/script&gt;@si', '@&lt;style.*?&gt;.*?&lt;/style&gt;@si');
                        $replaceString = array('', '');
                        $html = trim(preg_replace($searchString, $replaceString, $html));
                        $html = preg_replace("#\s+#si", " ", trim(strip_tags($html)));
                        $index[] = html_entity_decode($html, ENT_QUOTES, "UTF-8");
                    }
                    $pageIndexes[$pageData['page_id']] = join(' ', $index);
                }
                $this->_savePageIndexes($storeId, $pageIndexes);
            }
            // index blogs
            $postIndexes = array();
            $posts = $this->_getSearchableBlogPages();
            foreach ($posts as $postData) {
                if (!isset($postData['post_id'])) {
                    continue;
                }

                $index = array();
                if (isset($postData['title'])) {
                    $index[] = $postData['title'];
                }

                if (isset($postData['post_content'])) {
                    $html = "";
                    try {
                        $html = $contentFilter->process($postData['post_content']);
                    } catch (Exception $e) {
                        Mage::log($postData);
                        Mage::log($e);
                        continue;
                    }
					
                    $searchString = array('@&lt;script.*?&gt;.*?&lt;/script&gt;@si', '@&lt;style.*?&gt;.*?&lt;/style&gt;@si');
                    $replaceString = array('', '');
                    $html = trim(preg_replace($searchString, $replaceString, $html));
                    $html = preg_replace("#\s+#si", " ", trim(strip_tags($html)));
                    $index[] = html_entity_decode($html, ENT_QUOTES, "UTF-8");
                }
                $postIndexes[$postData['post_id']] = join(' ', $index);
            }
            $this->_savePostIndexes($storeId, $postIndexes);
            
            // index news           
            $news = $this->_getSearchableCmsproPages();
            $newsIndexes = array();
            foreach ($news as $newsData) {
                if (!isset($newsData['news_id'])) {
                    continue;
                }
                $index = array();
                if (isset($newsData['title'])) {
                    $index[] = $newsData['title'];
                }
                if (isset($newsData['summary'])) {
                    $html = "";
                    $newsData['summary'] = preg_replace_callback('@{{.*?}}@si','MageWorx_SearchAutocomplete_Helper_Data::loadWidget', $newsData['summary']);
                    try {
                        $html = $contentFilter->process($newsData['summary']);
                    } catch (Exception $e) {
                        Mage::log($newsData);
                        Mage::log($e);
                        continue;
                    }
					
                    $searchString = array('@&lt;script.*?&gt;.*?&lt;/script&gt;@si', '@&lt;style.*?&gt;.*?&lt;/style&gt;@si');
                    $replaceString = array('', '');
                    $html = trim(preg_replace($searchString, $replaceString, $html));
                    $html = preg_replace("#\s+#si", " ", trim(strip_tags($html)));
                    $index[] = html_entity_decode($html, ENT_QUOTES, "UTF-8");
                }
                if (isset($newsData['content'])) {
                    $html = "";
                    $newsData['content'] = preg_replace_callback('@{{.*?}}@si','MageWorx_SearchAutocomplete_Helper_Data::loadWidget', $newsData['content']);
                    try {
                        $html = $contentFilter->process($newsData['content']);
                    } catch (Exception $e) {
                        Mage::log($newsData);
                        Mage::log($e);
                        continue;
                    }
					
                    $searchString = array('@&lt;script.*?&gt;.*?&lt;/script&gt;@si', '@&lt;style.*?&gt;.*?&lt;/style&gt;@si');
                    $replaceString = array('', '');
                    $html = trim(preg_replace($searchString, $replaceString, $html));
                    $html = preg_replace("#\s+#si", " ", trim(strip_tags($html)));
                    $index[] = html_entity_decode($html, ENT_QUOTES, "UTF-8");
                }
                $newsIndexes[$newsData['news_id']] = join(' ', $index);
            }
            $this->_saveNewsIndexes($storeId, $newsIndexes);
            
            $this->resetSearchResults();
        } catch (Exception $e) {
            Mage::log($e);
            throw $e;
        }

        return $this;
    }

    public function regenerateIndex($storeId = null, $pageIds = null) { 
        if ($storeId == null) {
            $storeCollection = Mage::getModel('core/store')->getCollection();
            foreach ($storeCollection as $store) {
                $this->_regenerateStoreIndex($store->getId(), $pageIds);
            }
        } else {
            $this->_regenerateStoreIndex($storeId, $pageIds);
        }

        return $this;
    }

    protected function _savePageIndexes($storeId, $pageIndexes) {
        $values = array();
        $bind = array();

        foreach ($pageIndexes as $pageId => &$index) {
            $values[] = sprintf('(%s,%s,%s)', $this->_getWriteAdapter()->quoteInto('?', $pageId), $this->_getWriteAdapter()->quoteInto('?', $storeId), '?');
            $bind[] = $index;
        }

        if ($values) {
            $sql = "REPLACE INTO `{$this->getMainTable()}` VALUES"
                    . join(',', $values);
            $this->_getWriteAdapter()->query($sql, $bind);
        }

        return $this;
    }

    protected function _savePostIndexes($storeId, $pageIndexes) {
        $values = array();
        $bind = array();

        foreach ($pageIndexes as $pageId => &$index) {
            $index = str_replace('"', "'", $index);
            $values[] = sprintf('(%s,%s,%s)', $this->_getWriteAdapter()->quoteInto('?', $pageId), $this->_getWriteAdapter()->quoteInto('?', $storeId), '?');
            $bind[] = $index;
        }

        if ($values) {
            $sql = "REPLACE INTO `{$this->getTable('searchautocomplete/blog_fulltext')}` VALUES"
                    . join(',', $values);
            $this->_getWriteAdapter()->query($sql, $bind);
        }

        return $this;
    }

    protected function _getSearchablePages($storeId, $pageIds = null, $lastPageId = 0, $limit = 100) {
        
        $filterPages = Mage::getStoreConfig(self::XML_PATH_FILTER_CMS_PAGES, $storeId);
        if (is_null($filterPages)) $filterPages = 'no-route,enable-cookies';
        $filterPages = explode(',', $filterPages);        
        
        $select = $this->_getReadAdapter()->select()->
                from(array('p' => $this->getTable('cms/page')), array('page_id', 'title', 'identifier', 'content'))->
                joinInner(
                        array('store' => $this->getTable('cms/page_store')),
                        $this->_getReadAdapter()->quoteInto('store.page_id=p.page_id AND (store.store_id=? OR store.store_id=0)', $storeId),
                        array()
                )->where('p.identifier NOT IN(?)', $filterPages);
        

        if ($pageIds != null) {
            $select->where('p.page_id IN(?)', $pageIds);
        }

        $select->where('p.is_active');
        $select->where('p.page_id>?', $lastPageId)->
                limit($limit)->
                order('p.page_id');
        
        return $this->_getReadAdapter()->fetchAll($select);
    }

    protected function _getSearchableBlogPages() {
        if ((string)Mage::getConfig()->getModuleConfig('AW_Blog')->active!='true') return array();        
        $select = $this->_getReadAdapter()->select()
                ->from(array('blog' => Mage::getConfig()->getTablePrefix().'aw_blog', array('post_id', 'title', 'identifier', 'post_content')))
                ->where('blog.status > 0');
        return $this->_getReadAdapter()->fetchAll($select);
    }

    public function resetSearchResults() {
        $this->beginTransaction();

        try {
            $this->_getWriteAdapter()->update($this->getTable('catalogsearch/search_query'), array('is_cmspage_processed' => 0));
            $this->_getWriteAdapter()->query("DELETE FROM {$this->getTable('searchautocomplete/result')}");

            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }

        Mage::dispatchEvent('searchautocomplete_reset_search_result');

        return $this;
    }

    public function cleanIndex($storeId = null, $pageId = null) {
        $where = array();

        if ($storeId != null) {
            $where[] = $this->_getWriteAdapter()->quoteInto('store_id= ?', $storeId);
        }
        if ($pageId != null) {
            $where[] = $this->_getWriteAdapter()->quoteInto('page_id IN(?)', $pageId);
        }

        $this->_getWriteAdapter()->delete($this->getMainTable(), $where);

        return $this;
    }

    public function prepareResult($object, $queryText, $query) {
        if (!$query->getIsCmspageProcessed()) {
            $searchType = $object->getSearchType($query->getStoreId());

            $bind = array(
                ':query' => $queryText
            );
            $like = array();
            $unLike = array();

            $fulltextCond = '';
            $likeCond = '';
            $separateCond = '';

            if ($searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_LIKE ||
                    $searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_COMBINE) {
                $words = Mage::helper('core/string')->splitWords($queryText, true, $query->getMaxQueryWords());
                $i = 0;
                foreach ($words as $word) {
                    $like[] = '`data_index` LIKE :likew' . $i;
                    $bind[':likew' . $i] = '%' . $word . '%';
                    $i++;
                }
                if ($like) {
                    $likeCond = '(' . join(' AND ', $like) . ')';
                }
            }
            if ($searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_FULLTEXT ||
                    $searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_COMBINE) {
                $fulltextCond = 'MATCH (`data_index`) AGAINST (:query IN BOOLEAN MODE)';
            }
            if ($searchType == Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_COMBINE && $likeCond) {
                $separateCond = ' OR ';
            }

            $sql = sprintf("REPLACE INTO `{$this->getTable('searchautocomplete/result')}` " .
                    "(SELECT '%d', `page_id`, MATCH (`data_index`) AGAINST (:query IN BOOLEAN MODE) " .
                    "FROM `{$this->getMainTable()}` WHERE (%s%s%s) AND `store_id`='%d')", $query->getId(), $fulltextCond, $separateCond, $likeCond, $query->getStoreId()
            );

            $sqlBlog = sprintf("REPLACE INTO `{$this->getTable('searchautocomplete/blog_result')}` " .
                    "(SELECT '%d', `post_id`, MATCH (`data_index`) AGAINST (:query IN BOOLEAN MODE) " .
                    "FROM `{$this->getTable('searchautocomplete/blog_fulltext')}` WHERE (%s%s%s) AND `store_id`='%d')", $query->getId(), $fulltextCond, $separateCond, $likeCond, $query->getStoreId()
            );

            $sqlCmspro = sprintf("REPLACE INTO `{$this->getTable('searchautocomplete/cmspro_result')}` " .
                    "(SELECT '%d', `news_id`, MATCH (`data_index`) AGAINST (:query IN BOOLEAN MODE) " .
                    "FROM `{$this->getTable('searchautocomplete/cmspro_fulltext')}` WHERE (%s%s%s) AND `store_id`='%d')", $query->getId(), $fulltextCond, $separateCond, $likeCond, $query->getStoreId()
            );
            $this->_getWriteAdapter()->query($sql, $bind);

            $this->_getWriteAdapter()->query($sqlBlog, $bind);
            
            $this->_getWriteAdapter()->query($sqlCmspro, $bind);

            $query->setIsCmspageProcessed(1);
        }

        return $this;
    }
    
    protected function _getSearchableCmsproPages() {
        if ((string)Mage::getConfig()->getModuleConfig('MW_Cmspro')->active!='true') return array();        
        $select = $this->_getReadAdapter()->select()
                ->from(array('cmspro' => Mage::getConfig()->getTablePrefix().'cmspro_news', array('news_id', 'title', 'summary', 'content')))
                ->where('cmspro.status > 0');
        return $this->_getReadAdapter()->fetchAll($select);
    }
    
    protected function _saveNewsIndexes($storeId, $pageIndexes) {
        $values = array();
        $bind = array();

        foreach ($pageIndexes as $pageId => &$index) {
            $index = str_replace('"', "'", $index);
            $values[] = sprintf('(%s,%s,%s)', $this->_getWriteAdapter()->quoteInto('?', $pageId), $this->_getWriteAdapter()->quoteInto('?', $storeId), '?');
            $bind[] = $index;
        }

        if ($values) {
            $sql = "REPLACE INTO `{$this->getTable('searchautocomplete/cmspro_fulltext')}` VALUES"
                    . join(',', $values);
            $this->_getWriteAdapter()->query($sql, $bind);
        }

        return $this;
    }
}
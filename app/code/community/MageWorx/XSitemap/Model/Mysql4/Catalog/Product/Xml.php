<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_XSitemap_Model_Mysql4_Catalog_Product_Xml extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * For sites where all products belong to one website and
     * distribution of products in shops will be organized
     * by purpose of a product in category belonging to certain shops.
     * In this case in sitemap excess products of other shops.
     * Set FILTER_PRODUCT = 1 to prevent it.
     *
     */
    const FILTER_PRODUCT = 0;

    /**
     * Collection Zend Db select
     *
     * @var Zend_Db_Select
     */
    protected $_select;

    /**
     * Attribute cache
     *
     * @var array
     */
    protected $_attributesCache = array();

    /**
     * Init resource model (catalog/category)
     */
    protected function _construct()
    {
        $this->_init('catalog/product', 'entity_id');
    }

    /**
     * Add attribute to filter
     *
     * @param int $storeId
     * @param string $attributeCode
     * @param mixed $value
     * @param string $type
     *
     * @return Zend_Db_Select
     */
    protected function _addFilter($storeId, $attributeCode, $value, $type = '=')
    {
        if (!isset($this->_attributesCache[$attributeCode])) {
            $attribute = Mage::getSingleton('catalog/product')->getResource()->getAttribute($attributeCode);

            $this->_attributesCache[$attributeCode] = array(
                'entity_type_id' => $attribute->getEntityTypeId(),
                'attribute_id'   => $attribute->getId(),
                'table'          => $attribute->getBackend()->getTable(),
                'is_global'      => $attribute->getIsGlobal() == Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                'backend_type'   => $attribute->getBackendType()
            );
        }

        $attribute = $this->_attributesCache[$attributeCode];

        if (!$this->_select instanceof Zend_Db_Select) {
            return false;
        }

        switch ($type) {
            case '=':
                $conditionRule = '=?';
                break;
            case 'in':
                $conditionRule = ' IN(?)';
                break;
            default:
                return false;
                break;
        }

        if ($attribute['backend_type'] == 'static') {
            $this->_select->where('e.' . $attributeCode . $conditionRule, $value);
        }
        else {
            $this->_select->join(
                    array('t1_' . $attributeCode => $attribute['table']),
                    'e.entity_id=t1_' . $attributeCode . '.entity_id AND t1_' . $attributeCode . '.store_id=0', array()
                )
                ->where('t1_' . $attributeCode . '.attribute_id=?', $attribute['attribute_id']);

            if ($attribute['is_global']) {
                $this->_select->where('t1_' . $attributeCode . '.value' . $conditionRule, $value);
            }
            else {
                $this->_select->joinLeft(
                        array('t2_' . $attributeCode => $attribute['table']),
                        $this->_getWriteAdapter()->quoteInto('t1_' . $attributeCode . '.entity_id = t2_' . $attributeCode . '.entity_id AND t1_' . $attributeCode . '.attribute_id = t2_' . $attributeCode . '.attribute_id AND t2_' . $attributeCode . '.store_id=?',
                            $storeId), array()
                    )
                    ->where('IFNULL(t2_' . $attributeCode . '.value, t1_' . $attributeCode . '.value)' . $conditionRule,
                        $value);
            }
        }

        return $this->_select;
    }

    /**
     * Get category collection array
     *
     * @return array
     */
    public function getCollection($storeId, $onlyCount = false, $limit = 4000000000, $from = 0)
    {
        $products = array();

        $store = Mage::app()->getStore($storeId);
        /* @var $store Mage_Core_Model_Store */

        if (!$store) {
            return false;
        }

        if (self::FILTER_PRODUCT == 1) {
            $fpstring = " AND product_id IN (" . implode(',', $this->_getStoreProductIds($storeId)) . ")";
        }
        else {
            $fpstring = '';
        }

        $read = $this->_getReadAdapter();

        $this->_select = $read->select()
            ->distinct()
            ->from(array('e' => $this->getMainTable()), array(($onlyCount ? 'COUNT(*)' : $this->getIdFieldName())))
            ->join(
                array('w' => $this->getTable('catalog/product_website')), "e.entity_id=w.product_id $fpstring", array()
            )
            ->where('w.website_id=?', $store->getWebsiteId())
            ->limit($limit, $from);

        $excludeAttr = Mage::getModel('catalog/product')->getResource()->getAttribute('exclude_from_sitemap');

        if ($excludeAttr) {
            $this->_select->joinLeft(
                    array('exclude_tbl' => $excludeAttr->getBackend()->getTable()),
                    'exclude_tbl.entity_id = e.entity_id AND exclude_tbl.attribute_id = ' . $excludeAttr->getAttributeId() . new Zend_Db_Expr(" AND store_id =
                    IF(
						((SELECT `exclude`.`value` FROM `{$excludeAttr->getBackend()->getTable()}` AS `exclude` WHERE `exclude`.`entity_id` = `e`.`entity_id` AND `attribute_id` = {$excludeAttr->getAttributeId()} AND `store_id` = $storeId) IS NOT NULL) ,
						(SELECT $storeId),
						(SELECT 0)
					)"),
                    array()
                )
                ->where('exclude_tbl.value=0 OR exclude_tbl.value IS NULL');
        }

        if(Mage::helper('xsitemap')->isExcludeFromXMLOutOfStockProduct($storeId)){
            $cond = 'e.entity_id = csi.product_id';

            if (Mage::getStoreConfig('cataloginventory/item_options/manage_stock', $storeId)) {
                $cond .= ' AND IF (csi.use_config_manage_stock = 1, csi.is_in_stock = 1, (csi.manage_stock = 0 OR (csi.manage_stock = 1 AND csi.is_in_stock = 1)))';
            } else {
                $cond .= ' AND IF (csi.use_config_manage_stock = 1, TRUE, (csi.manage_stock = 0 OR (csi.manage_stock = 1 AND csi.is_in_stock = 1)))';
            }


            $this->_select->join(
                array('csi' => $this->getTable('cataloginventory/stock_item')),
                $cond,
                array('is_in_stock', 'manage_stock', 'use_config_manage_stock'));
        }

        $this->_addFilter($storeId, 'visibility',
            Mage::getSingleton('catalog/product_visibility')->getVisibleInSiteIds(), 'in');

        $this->_addFilter($storeId, 'status', Mage::getSingleton('catalog/product_status')->getVisibleStatusIds(), 'in');

        if ($onlyCount) {
            return $read->fetchOne($this->_select);
        }

        $sort = '';

        if (Mage::helper('xsitemap/adapter_seobase')->isSeoBaseAvailable() &&
            Mage::helper('xsitemap/adapter_seobase')->isSeoBaseCanonicalUrlEnabled($storeId) &&
            Mage::helper('xsitemap/adapter_seobase')->getSeoBaseProductCanonicalType($storeId))
        {
            $productCanonicalType = Mage::helper('xsitemap/adapter_seobase')->getSeoBaseProductCanonicalType($storeId);

            if(Mage::helper('xsitemap')->isEnterpriseSince113())
            {
                if($productCanonicalType == 1){
                    // 1: Canonical Longest by Path Length
                    $sort = 'DESC';
                    $eeSuffix = "LENGTH(`url`)";
                }elseif($productCanonicalType == 4){
                    // 4: Canonical Longest by Category Counter
                    $sort = 'DESC';
                    $eeSuffix = "char_length(`url`) - char_length(replace(`url`,'/',''))";
                }elseif($productCanonicalType == 2){
                    // 2: Canonical Shortest by Path Length
                    $sort = 'ASC';
                    $eeSuffix = "LENGTH(`url`)";
                }elseif($productCanonicalType == 5){
                    // 5: Canonical Longest by Category Counter
                    $sort = 'ASC';
                    $eeSuffix = "char_length(`url`) - char_length(replace(`url`,'/',''))";
                }
            }else{
                if($productCanonicalType == 3){
                    //$suffix  = "AND canonical_url_rewrite.category_id IS NULL";
                    $suffix = '';
                    $suffix2 = "AND category_id IS NULL";
                }else{
                    //$suffix  = "AND canonical_url_rewrite.category_id IS NOT NULL";
                    $suffix = '';
                    $suffix2 = "AND category_id IS NOT NULL";
                }

                if ($productCanonicalType == 1 || $productCanonicalType == 4) {
                    $sort = 'DESC';
                }
                else if ($productCanonicalType == 2 || $productCanonicalType == 5) {
                    $sort = 'ASC';
                }
                else {

                }
            }
        }
        else {
            $length = Mage::helper('xsitemap')->getXmlSitemapUrlLength();
            if ($length == 'short') {
                $sort = 'ASC';
            }
            elseif ($length == 'long') {
                $sort = 'DESC';
            }
        }

        if(Mage::getStoreConfigFlag('catalog/seo/product_use_categories', $storeId)){
            $suffix3 = '';
        }else{
            $suffix3 = 'AND `category_id` IS NULL';
        }

        $canonicalAttr = Mage::getModel('catalog/product')->getResource()->getAttribute('canonical_url');
        $urlPathAttr   = Mage::getModel('catalog/product')->getResource()->getAttribute('url_path');


        if(Mage::helper('xsitemap')->isEnterpriseSince113()){

            $urlSuffix = Mage::helper('catalog/product')->getProductUrlSuffix($storeId);
            if($urlSuffix){
                $urlSuffix = '.' . $urlSuffix;
            }else{
                $urlSuffix = '';
            }

            $productCanonicalType = empty($productCanonicalType) ? 3 : $productCanonicalType;

            if($productCanonicalType == 3){
                $this->_select
                    ->joinLeft(
                        array('ecp' => $this->getTable('enterprise_catalog/product')),
                        'ecp.product_id = e.entity_id ' . 'AND ecp.store_id = ' . $storeId,
                        array()
                    )
                    ->joinLeft(array('euur' => $this->getTable('enterprise_urlrewrite/url_rewrite')),
                        'ecp.url_rewrite_id = euur.url_rewrite_id AND euur.is_system = 1',
                        array()
                    )
                    ->joinLeft(array('ecp2' => $this->getTable('enterprise_catalog/product')),
                        'ecp2.product_id = e.entity_id AND ecp2.store_id = 0',
                        array()
                    )
                    ->joinLeft(array('euur2' => $this->getTable('enterprise_urlrewrite/url_rewrite')),
                        'ecp2.url_rewrite_id = euur2.url_rewrite_id',
                        array('url' => 'concat( ' . $this->_getWriteAdapter()->getIfNullSql('euur.request_path', 'euur2.request_path') . ',"' . $urlSuffix . '")')
                    );
            }else{

                $this->_select->columns(array('url' => new Zend_Db_Expr("
                        IFNULL(
                            (
                            SELECT CONCAT(rwc.request_path, '/' , rwp.identifier, '{$urlSuffix}') as `url` FROM `{$this->getMainTable()}` AS `e2`
                            LEFT JOIN `{$this->getTable('enterprise_catalog/product')}` AS `ecp` ON ecp.product_id = `e2`.`entity_id` AND `store_id` =
                                IF(
                                    (SELECT `ecp2`.`store_id` FROM `{$this->getTable('enterprise_catalog/product')}` AS `ecp2` WHERE `ecp2`.`product_id` = `e2`.`entity_id` AND `store_id` = {$storeId}),
                                    (SELECT {$storeId}),
                                    (SELECT 0)
                                )
                            JOIN `{$this->getTable('enterprise_urlrewrite/url_rewrite')}` AS `rwp` ON `ecp`.`url_rewrite_id` = `rwp`.`url_rewrite_id`
                            JOIN `{$this->getTable('catalog/category_product')}` AS `ccp` ON `ccp`.`product_id` = `e2`.`entity_id`
                            JOIN `{$this->getTable('enterprise_catalog/category')}` AS `ecc` ON `ecc`.`category_id` = `ccp`.`category_id`
                            JOIN `{$this->getTable('enterprise_urlrewrite/url_rewrite')}` AS `rwc` ON `ecc`.`url_rewrite_id` = `rwc`.`url_rewrite_id`  AND (`rwp`.`entity_type` = 3) AND (`rwc`.`store_id` = {$storeId})
                            WHERE `e2`.`entity_id` = `e`.`entity_id`
                            ORDER BY $eeSuffix $sort LIMIT 1
                            ),

                            (
                            SELECT CONCAT(rwp.identifier, '{$urlSuffix}') AS `url` FROM `{$this->getMainTable()}` AS `e2`
                                LEFT JOIN `{$this->getTable('enterprise_catalog/product')}` AS `ecp` ON ecp.product_id = `e2`.`entity_id` AND `store_id` =
                                IF(
                                    (SELECT `ecp2`.`store_id` FROM `{$this->getTable('enterprise_catalog/product')}` AS `ecp2` WHERE `ecp2`.`product_id` = `e2`.`entity_id` AND `store_id` = {$storeId}),
                                    (SELECT {$storeId}),
                                    (SELECT 0)
                                )
                                JOIN `{$this->getTable('enterprise_urlrewrite/url_rewrite')}` AS `rwp` ON `ecp`.`url_rewrite_id` = `rwp`.`url_rewrite_id`
                                WHERE `e2`.`entity_id` = `e`.`entity_id` LIMIT 1
                            )
                        )
                    "
                )));

//                echo $this->_select->assemble(); exit;
            }
        }

        ////~~~~////

        elseif (!empty($productCanonicalType) && $canonicalAttr) {

            if($productCanonicalType == '3'){
                $this->_select->columns(array('url' => new Zend_Db_Expr("
                    IFNULL(
                        (IFNULL(
                            (SELECT canonical_url_rewrite.`request_path`
                                FROM `" . $canonicalAttr->getBackend()->getTable() . "` AS canonical_path
                                LEFT JOIN `" . $this->getTable('core/url_rewrite') . "` AS canonical_url_rewrite ON canonical_url_rewrite.`id_path` = canonical_path.`value`
                                WHERE canonical_path.`entity_id` = e.`entity_id` AND canonical_path.`attribute_id` = " . $canonicalAttr->getAttributeId() . " AND canonical_url_rewrite.`store_id` IN (0," . $storeId . ") $suffix" .
                                ($sort ? " ORDER BY LENGTH(canonical_url_rewrite.`request_path`) " . $sort : "") .
                                " LIMIT 1),
                            (SELECT `request_path`
                            FROM `" . $this->getTable('core/url_rewrite') . "`
                            WHERE `product_id`=e.`entity_id` AND `store_id` IN (0," . $storeId . ") AND `is_system`=1 AND `request_path` IS NOT NULL $suffix2" .
                            ($sort ? " ORDER BY LENGTH(`request_path`) " . $sort : "") .
                            " LIMIT 1)
                            )),
                        (SELECT p.`value` FROM `" . $urlPathAttr->getBackend()->getTable() . "` AS p
                         WHERE p.`entity_id` = e.`entity_id` AND p.`attribute_id` = " . $urlPathAttr->getAttributeId() . " AND p.`store_id` IN (0," . $storeId . ") ORDER BY p.`store_id` DESC LIMIT 1
                        )
                    )"
                )));
            }
            else{
                $this->_select->columns(array('url' => new Zend_Db_Expr("
                    IFNULL(
                        (IFNULL(
                            (SELECT canonical_url_rewrite.`request_path`
                            FROM `" . $canonicalAttr->getBackend()->getTable() . "` AS canonical_path
                            LEFT JOIN `" . $this->getTable('core/url_rewrite') . "` AS canonical_url_rewrite ON canonical_url_rewrite.`id_path` = canonical_path.`value`
                            WHERE canonical_path.`entity_id` = e.`entity_id` AND canonical_path.`attribute_id` = " . $canonicalAttr->getAttributeId() . " AND canonical_url_rewrite.`store_id` IN (0," . $storeId . ") $suffix" .
                            ($sort ? " ORDER BY LENGTH(canonical_url_rewrite.`request_path`) " . $sort : "") .
                            " LIMIT 1),
                            (SELECT IF(
                                (SELECT LENGTH((SELECT `request_path`
                                FROM `" . $this->getTable('core/url_rewrite') . "`
                                WHERE `product_id`=e.`entity_id` AND `store_id` IN (0," . $storeId . ") AND `is_system`=1 AND `request_path` IS NOT NULL $suffix2" .
                                ($sort ? " ORDER BY LENGTH(`request_path`) " . $sort : "") .
                                " LIMIT 1)) > 0),
                                (SELECT CONCAT(`request_path`, '@', `category_id`)
                                FROM `" . $this->getTable('core/url_rewrite') . "`
                                WHERE `product_id`=e.`entity_id` AND `store_id` IN (0," . $storeId . ") AND `is_system`=1 AND `request_path` IS NOT NULL $suffix2" .
                                ($sort ? " ORDER BY LENGTH(`request_path`) " . $sort : "") .
                                " LIMIT 1),
                                (SELECT NULL)
                                )
                            )
                        )),
                        (SELECT p.`value` FROM `" . $urlPathAttr->getBackend()->getTable() . "` AS p
                         WHERE p.`entity_id` = e.`entity_id` AND p.`attribute_id` = " . $urlPathAttr->getAttributeId() . " AND p.`store_id` IN (0," . $storeId . ") ORDER BY p.`store_id` DESC LIMIT 1
                        )
                    )")
                ));
            }
        }
        else {
            $this->_select->columns(array('url' => new Zend_Db_Expr("(
                SELECT `request_path`
                FROM `" . $this->getTable('core/url_rewrite') . "`
                WHERE `product_id`=e.`entity_id` AND `store_id`=" . intval($storeId) . " AND `is_system`=1 AND `request_path` IS NOT NULL $suffix3" .
                    ($sort ? " ORDER BY LENGTH(`request_path`) " . $sort : "") .
                    " LIMIT 1)")));
        }

        $crossDomainAttr = Mage::getModel('catalog/product')->getResource()->getAttribute('canonical_cross_domain');

        if ($crossDomainAttr && !empty($productCanonicalType)) {
            $this->_select->joinLeft(
                array('cross_domain_tbl' => $crossDomainAttr->getBackend()->getTable()),
                'cross_domain_tbl.entity_id = e.entity_id AND cross_domain_tbl.attribute_id = ' . $crossDomainAttr->getAttributeId(),
                array('canonical_cross_domain' => 'cross_domain_tbl.value')
            );
        }

        $updatedAt = Mage::getModel('catalog/product')->getResource()->getAttribute('updated_at');
        if ($updatedAt) {
            $this->_select->joinLeft(
                array('updatedat_tbl' => $updatedAt->getBackend()->getTable()), 'updatedat_tbl.entity_id = e.entity_id',
                array('updated_at' => 'updatedat_tbl.updated_at')
            );
        }

//        echo $this->_select->assemble(); exit;

        $query = $read->query($this->_select);

        while ($row = $query->fetch()) {
            $product = $this->_prepareProduct($row);

            /**
            if (isset($productCanonicalType) && $productCanonicalType == 3) { // use root
                $urlArr = explode('/', $product->getUrl());
                $product->setUrl(end($urlArr));
            }
            **/

            $products[$product->getId()] = $product;
        }

        return $products;
    }

    /**
     * Prepare product
     *
     * @param array $productRow
     * @return Varien_Object
     */
    protected function _prepareProduct(array $productRow)
    {
        $product    = new Varien_Object();
        $product->setId($productRow[$this->getIdFieldName()]);

        if(!empty($productRow['url'])){
            if(strpos($productRow['url'], '@') !== false){
                list($productUrl, $categoryId) = explode('@', $productRow['url']);
            }else{
                $productUrl = $productRow['url'];
            }
        }

        $categoryPart = (!empty($categoryId)) ? '/category/' . $categoryId : "";

        $productUrl = !empty($productUrl) ? $productUrl : 'catalog/product/view/id/' . $product->getId();
        $product->setUrl($productUrl);
        $product->setUpdatedAt($productRow['updated_at']);

        $productTargetPath = !empty($productRow['target_path']) ? $productRow['target_path'] : 'catalog/product/view/id/' . $product->getId() . $categoryPart;
        $product->setTargetPath($productTargetPath);

        if (isset($productRow['canonical_cross_domain'])){
            $product->setCanonicalCrossDomain($productRow['canonical_cross_domain']);
        }

        if(Mage::helper('xsitemap')->isProductImages()){
            $attribute  = Mage::getSingleton('catalog/product')->getResource()->getAttribute('media_gallery');
            $media      = Mage::getResourceSingleton('catalog/product_attribute_backend_media');
            $gallery = $media->loadGallery($product, new Varien_Object(array('attribute' => $attribute)));
            if (count($gallery)) {
                $product->setGallery($gallery);
            }
        }

        return $product;
    }

    /**
     * See description for FILTER_PRODUCT
     * @param int $storeId
     * @return array
     */
    protected function _getStoreProductIds($storeId)
    {
        $categories = Mage::getResourceModel('xsitemap/catalog_category')->getCollection($storeId);
        $catIds     = array_keys($categories);

        $collection = Mage::getModel('catalog/product')
            ->getCollection()
            ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('category_id', array('in' => $catIds));

        return $collection->getAllIds();
    }

}
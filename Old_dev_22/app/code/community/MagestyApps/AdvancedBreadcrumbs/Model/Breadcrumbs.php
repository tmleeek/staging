<?php
/**
 * MagestyApps
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to https://www.magestyapps.com for more information or
 * send an email to office@magestyapps.com .
 *
 * @category    MagestyApps
 * @package     MagestyApps_AdvancedBreadcrumbs
 * @copyright   Copyright (c) 2016 MagestyApps (https://www.magestyapps.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class MagestyApps_AdvancedBreadcrumbs_Model_Breadcrumbs extends Mage_Core_Model_Abstract
{
    private $_catModels = array();

    /**
     * Get product breadcrumbs
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getProductBreadcrumbs(Mage_Catalog_Model_Product $product)
    {
        $allPaths = array();
        $priorPaths = array();
        $defaultCategory = Mage::helper('crumbs')->getDefaultCategory();

        foreach ($product->getCategoryIds() as $catId) {
            $category = $this->getCategoryModel($catId);

            if (!Mage::helper('catalog/category')->canShow($category)) {
                continue;
            }

            if (Mage::helper('crumbs')->showOnlyOnePath()
                && $product->getDefaultBreadcrumbs()
                && $catId !=$product->getDefaultBreadcrumbs()
            ) {
                continue;
            }

            $key = $category->getParentId() . '_' . $category->getPosition();

            if ($defaultCategory && in_array($defaultCategory, $category->getPathIds())) {
                $priorPaths[$key] = $category->getPathInStore();
            } else {
                $allPaths[$key] = $category->getPathInStore();
            }
        }

        if (count($priorPaths)) {
            $allPaths = $priorPaths;
        }

        ksort($allPaths, SORT_NATURAL);

        $allPaths = $this->_removeDuplicates($allPaths);

        return $this->preparePaths($allPaths);
    }

    /**
     * Get breadcrumbs if user came from a search result page
     *
     * @return array
     */
    public function getSearchCrumbs()
    {
        $searchQuery = Mage::helper('crumbs')->getSearchQuery();
        $searchUrl = Mage::helper('crumbs')->getSearchUrl();

        $crumbs = array();
        $crumbs[] = array(
            array(
                'category_id' => 0,
                'title' =>Mage::helper('catalog')->__('Home'),
                'link' =>Mage::getBaseUrl(),
                'last' => false
            ),
            array(
                'category_id' => -1,
                'title' => Mage::helper('catalogsearch')->__("Search results for: '%s'", $searchQuery),
                'link' => $searchUrl,
                'last' => false
            ),

        );

        return $crumbs;
    }

    /**
     * Get breadcrumbs for a category or product opened through category
     *
     * @return array
     */
    public function getDirectBreadcrumbs($isCategory = false)
    {
        $allPaths = array();
        $category = Mage::registry('current_category');
        if (!$category || !$category->getId()) {
            return $allPaths;
        }

        $path = $category->getPathInStore();
        if ($isCategory) {
            $path = explode(',', $path);
            unset ($path[0]);
            $path = implode(',', $path);
        }

        $allPaths[] = $path;

        return $this->preparePaths($allPaths);
    }

    /**
     * Format breadcrumb paths
     *
     * @param array $pathArr
     * @return array
     */
    public function preparePaths(array $pathArr)
    {
        $result = array();

        foreach ($pathArr as $path) {
            $catIdsArr = explode(',', $path);
            krsort($catIdsArr);

            $newPath = array();

            $newPath[] = array(
                'category_id' => 0,
                'title' =>Mage::helper('catalog')->__('Home'),
                'link' =>Mage::getBaseUrl(),
                'last' => false
            );

            foreach ($catIdsArr as $catId) {
                $category = $this->getCategoryModel($catId);

                if (!Mage::helper('catalog/category')->canShow($category)) {
                    continue;
                }

                $newPath[] = array(
                    'category_id' => $category->getId(),
                    'title' => $category->getName(),
                    'link' => $category->getUrl(),
                    'last' => false
                );
            }
            $result[] = $newPath;
        }

        if (!count($result)) {
            $result[] = array(array(
                'category_id' => 0,
                'title' =>Mage::helper('catalog')->__('Home'),
                'link' =>Mage::getBaseUrl(),
                'last' => false
            ));
        }

        return $result;
    }

    /**
     * Remove dublicated paths
     *
     * @param array $pathArr
     * @return mixed
     */
    protected function _removeDuplicates(array $pathArr)
    {
        foreach ($pathArr as $k => $path) {
            foreach ($pathArr as $pathCompare) {
                if (strpos($pathCompare, $path)) {
                    unset ($pathArr[$k]);
                }
            }
        }
        return $pathArr;
    }

    /**
     * Get category model
     *
     * @param $categoryId
     * @return mixed
     */
    public function getCategoryModel($categoryId, $storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }

        if (!isset($this->_catModels[$categoryId.'_'.$storeId])) {
            $category = Mage::getModel('catalog/category')
                ->setStoreId($storeId)
                ->load($categoryId);

            $this->_catModels[$categoryId] = $category;
        }

        return $this->_catModels[$categoryId];
    }
}
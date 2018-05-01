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

class MagestyApps_AdvancedBreadcrumbs_Model_Observer
{
    /**
     * Add breadcrumbs to additional pages
     *
     * @param $observer
     * @return $this
     */
    public function addAdditionalPagesBreadcrumbs($observer)
    {
        $block = $observer->getBlock();
        $crumbs = Mage::helper('crumbs/additional')->getCrumbs($block->getNameInLayout());
        if (!$crumbs) {
            return $this;
        }

        $breadcrumbsBlock = $block->getLayout()->getBlock('breadcrumbs');
        if (!$breadcrumbsBlock) {
            return $this;
        }

        //Add homepage breadcrumb
        $breadcrumbsBlock->addCrumb('home', array(
                'label' => Mage::helper('crumbs')->__('Home'),
                'title' => Mage::helper('crumbs')->__('Go to Home Page'),
                'link'  => Mage::getBaseUrl()
            ));

        //Add custom breadcrumbs
        foreach ($crumbs as $_crumb) {
            $crumbObj = new Varien_Object($_crumb);
            $breadcrumbsBlock->addCrumb($crumbObj->getCode(), array(
                'label' => $crumbObj->getTitle(),
                'title' => $crumbObj->getTitle(),
                'link'  => $crumbObj->getUrl(),
            ));
        }

        return $this;
    }

    /**
     * Render attribute "Default Breadcrumbs" on product edit page
     *
     * @param $observer
     * @return $this
     */
    public function renderAttrDefaultBreadcrumbs($observer)
    {
        $block = $observer->getBlock();

        if (Mage::app()->getRequest()->getControllerName() != 'catalog_product'
            || !($block instanceof Mage_Adminhtml_Block_Catalog_Form_Renderer_Fieldset_Element)
            || !$block->getAttribute()
            || $block->getAttributeCode() != 'default_breadcrumbs'
        ) {
            return $this;
        }

        $availablePaths = array(
            '' => 'Detect Automatically'
        );

        $crumbsModel = Mage::getSingleton('crumbs/breadcrumbs');
        $categoryIds = $block->getDataObject()->getCategoryIds();
        foreach ($categoryIds as $categoryId) {

            $pathStr = array('Home');

            $category = $crumbsModel->getCategoryModel($categoryId);
            $path = explode(',', $category->getPathInStore());
            krsort($path);

            foreach ($path as $pathCatId) {
                $pathCat = $crumbsModel->getCategoryModel($pathCatId);
                if ($pathCat->getLevel() < 2) {
                    continue;
                }

                $pathStr[] = $pathCat->getName();
            }

            $availablePaths[$categoryId] = implode(' / ', $pathStr);

        }

        $block->getElement()->setValues($availablePaths);
        $block->getElement()->setStyle('width: auto;');

        return $this;
    }
}
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

class MagestyApps_AdvancedBreadcrumbs_Model_System_Config_Source_Category
{
    public function toOptionArray()
    {
        $collection = Mage::getResourceModel('catalog/category_collection');
        $collection->addAttributeToSelect('name');
        $collection->getSelect()->order('path ASC');

        $options = array();

        if ($store = Mage::app()->getRequest()->getParam('store', false)) {
            $rootCatId = Mage::app()->getStore($store)->getRootCategoryId();
            $collection->getSelect()->where('path LIKE "1/'.$rootCatId.'/%"');
        }

        $options[] = array(
            'label' => Mage::helper('crumbs')->__('--- no category ---'),
            'value' => ''
        );

        foreach ($collection as $category) {
            if (!$category->getName()) {
                continue;
            }
            $options[] = array(
                'label' => $this->_getLabelPadding($category->getLevel()) . ' ' . $category->getName(),
                'value' => $category->getId()
            );
        }

        return $options;
    }

    protected function _getLabelPadding($level)
    {
        $padding = '';
        for ($i = 0; $i <= ($level - 2); $i++) {
            $padding .= '...';
        }

        return $padding;
    }
}
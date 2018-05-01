<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Mysql4_Amazon_Listing_Product
    extends Ess_M2ePro_Model_Mysql4_Component_Child_Abstract
{
    protected $_isPkAutoIncrement = false;

    //########################################

    public function _construct()
    {
        $this->_init('M2ePro/Amazon_Listing_Product', 'listing_product_id');
        $this->_isPkAutoIncrement = false;
    }

    //########################################

    public function getChangedItems(array $attributes,
                                    $withStoreFilter = false)
    {
        return Mage::getResourceModel('M2ePro/Listing_Product')->getChangedItems(
            $attributes,
            Ess_M2ePro_Helper_Component_Amazon::NICK,
            $withStoreFilter
        );
    }

    public function getChangedItemsByListingProduct(array $attributes,
                                                    $withStoreFilter = false)
    {
        return Mage::getResourceModel('M2ePro/Listing_Product')->getChangedItemsByListingProduct(
            $attributes,
            Ess_M2ePro_Helper_Component_Amazon::NICK,
            $withStoreFilter
        );
    }

    public function getChangedItemsByVariationOption(array $attributes,
                                                     $withStoreFilter = false)
    {
        return Mage::getResourceModel('M2ePro/Listing_Product')->getChangedItemsByVariationOption(
            $attributes,
            Ess_M2ePro_Helper_Component_Amazon::NICK,
            $withStoreFilter
        );
    }

    //########################################

    public function setSynchStatusNeedByDescriptionTemplate($newData, $oldData, $listingProduct)
    {
        $newTemplateData = array();
        if ($newData['template_description_id']) {

            $template = Mage::helper('M2ePro/Component_Amazon')->getCachedObject(
                'Template_Description', $newData['template_description_id'], NULL, array('template')
            );
            $template && $newTemplateData = $template->getDataSnapshot();
        }

        $oldTemplateData = array();
        if ($oldData['template_description_id']) {

            $template = Mage::helper('M2ePro/Component_Amazon')->getCachedObject(
                'Template_Description', $oldData['template_description_id'], NULL, array('template')
            );
            $template && $oldTemplateData = $template->getDataSnapshot();
        }

        Mage::getResourceModel('M2ePro/Amazon_Template_Description')->setSynchStatusNeed(
            $newTemplateData,
            $oldTemplateData,
            array($listingProduct)
        );
    }

    public function setSynchStatusNeedByShippingTemplate($newData, $oldData, $listingProduct, $modelName, $fieldName)
    {
        $newTemplateData = array();
        if (!empty($newData[$fieldName])) {

            $template = Mage::helper('M2ePro')->getCachedObject(
                $modelName, $newData[$fieldName], NULL, array('template')
            );
            $template && $newTemplateData = $template->getDataSnapshot();
        }

        $oldTemplateData = array();
        if (!empty($oldData[$fieldName])) {

            $template = Mage::helper('M2ePro')->getCachedObject(
                $modelName, $oldData[$fieldName], NULL, array('template')
            );
            $template && $oldTemplateData = $template->getDataSnapshot();
        }

        Mage::getResourceModel("M2ePro/{$modelName}")->setSynchStatusNeed(
            $newTemplateData,
            $oldTemplateData,
            array($listingProduct)
        );
    }

    public function setSynchStatusNeedByProductTaxCodeTemplate($newData, $oldData, $listingProduct)
    {
        $newTemplateData = array();
        if ($newData['template_product_tax_code_id']) {

            $template = Mage::helper('M2ePro')->getCachedObject(
                'Amazon_Template_ProductTaxCode', $newData['template_product_tax_code_id'], NULL, array('template')
            );
            $template && $newTemplateData = $template->getDataSnapshot();
        }

        $oldTemplateData = array();
        if ($oldData['template_product_tax_code_id']) {

            $template = Mage::helper('M2ePro')->getCachedObject(
                'Amazon_Template_ProductTaxCode', $oldData['template_product_tax_code_id'], NULL, array('template')
            );
            $template && $oldTemplateData = $template->getDataSnapshot();
        }

        Mage::getResourceModel('M2ePro/Amazon_Template_ProductTaxCode')->setSynchStatusNeed(
            $newTemplateData,
            $oldTemplateData,
            array($listingProduct)
        );
    }

    //########################################

    public function getProductsDataBySkus(array $skus = array(),
                                          array $filters = array(),
                                          array $columns = array())
    {
        /** @var Ess_M2ePro_Model_Mysql4_Listing_Product_Collection $listingProductCollection */
        $listingProductCollection = Mage::helper('M2ePro/Component_Amazon')->getCollection('Listing_Product');
        $listingProductCollection->getSelect()->joinLeft(
            array('l' => Mage::getResourceModel('M2ePro/Listing')->getMainTable()),
            'l.id = main_table.listing_id',
            array()
        );

        if (!empty($skus)) {
            $skus = array_map(function($el){ return (string)$el; }, $skus);
            $listingProductCollection->addFieldToFilter('sku', array('in' => array_unique($skus)));
        }

        if (!empty($filters)) {
            foreach ($filters as $columnName => $columnValue) {
                $listingProductCollection->addFieldToFilter($columnName, $columnValue);
            }
        }

        if (!empty($columns)) {
            $listingProductCollection->getSelect()->reset(Zend_Db_Select::COLUMNS);
            $listingProductCollection->getSelect()->columns($columns);
        }

        return $listingProductCollection->getData();
    }

    //########################################
}
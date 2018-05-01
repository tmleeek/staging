<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2017 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

/**
 * @method Ess_M2ePro_Model_Mysql4_Amazon_Template_ProductTaxCode getResource()
 */
class Ess_M2ePro_Model_Amazon_Template_ProductTaxCode extends Ess_M2ePro_Model_Component_Abstract
{
    const PRODUCT_TAX_CODE_MODE_VALUE     = 1;
    const PRODUCT_TAX_CODE_MODE_ATTRIBUTE = 2;

    /**
     * @var Ess_M2ePro_Model_Amazon_Template_ProductTaxCode_Source[]
     */
    private $productTaxCodeSourceModels = array();

    //########################################

    public function _construct()
    {
        parent::_construct();
        $this->_init('M2ePro/Amazon_Template_ProductTaxCode');
    }

    //########################################

    /**
     * @return bool
     * @throws Ess_M2ePro_Model_Exception_Logic
     */
    public function isLocked()
    {
        if (parent::isLocked()) {
            return true;
        }

        return (bool)Mage::getModel('M2ePro/Amazon_Listing_Product')
            ->getCollection()
            ->addFieldToFilter('template_product_tax_code_id', $this->getId())
            ->getSize();
    }

    public function deleteInstance()
    {
        if ($this->isLocked()) {
            return false;
        }

        $this->delete();
        return true;
    }

    //########################################

    /**
     * @param Ess_M2ePro_Model_Magento_Product $magentoProduct
     * @return Ess_M2ePro_Model_Amazon_Template_ProductTaxCode_Source
     */
    public function getSource(Ess_M2ePro_Model_Magento_Product $magentoProduct)
    {
        $id = $magentoProduct->getProductId();

        if (!empty($this->productTaxCodeSourceModels[$id])) {
            return $this->productTaxCodeSourceModels[$id];
        }

        $this->productTaxCodeSourceModels[$id] =
            Mage::getModel('M2ePro/Amazon_Template_ProductTaxCode_Source');

        $this->productTaxCodeSourceModels[$id]->setMagentoProduct($magentoProduct);
        $this->productTaxCodeSourceModels[$id]->setProductTaxCodeTemplate($this);

        return $this->productTaxCodeSourceModels[$id];
    }

    //########################################

    public function getTitle()
    {
        return $this->getData('title');
    }

    // ---------------------------------------

    public function getProductTaxCodeMode()
    {
        return (int)$this->getData('product_tax_code_mode');
    }

    public function isProductTaxCodeModeValue()
    {
        return $this->getProductTaxCodeMode() == self::PRODUCT_TAX_CODE_MODE_VALUE;
    }

    public function isProductTaxCodeModeAttribute()
    {
        return $this->getProductTaxCodeMode() == self::PRODUCT_TAX_CODE_MODE_ATTRIBUTE;
    }

    // ---------------------------------------

    public function getProductTaxCodeValue()
    {
        return $this->getData('product_tax_code_value');
    }

    public function getProductTaxCodeAttribute()
    {
        return $this->getData('product_tax_code_attribute');
    }

    // ---------------------------------------

    public function getCreateDate()
    {
        return $this->getData('create_date');
    }

    public function getUpdateDate()
    {
        return $this->getData('update_date');
    }

    //########################################

    public function getProductTaxCodeAttributes()
    {
        $attributes = array();

        if ($this->isProductTaxCodeModeAttribute()) {
            $attributes[] = $this->getProductTaxCodeAttribute();
        }

        return $attributes;
    }

    //########################################

    /**
     * @return array
     */
    public function getTrackingAttributes()
    {
        return $this->getUsedAttributes();
    }

    /**
     * @return array
     */
    public function getUsedAttributes()
    {
        return array_unique(
            $this->getProductTaxCodeAttributes()
        );
    }

    //########################################

    /**
     * @param bool $asArrays
     * @param string|array $columns
     * @param bool $onlyPhysicalUnits
     * @return array
     */
    public function getAffectedListingsProducts($asArrays = true, $columns = '*', $onlyPhysicalUnits = false)
    {
        /** @var Ess_M2ePro_Model_Mysql4_Listing_Product_Collection $listingProductCollection */
        $listingProductCollection = Mage::helper('M2ePro/Component_Amazon')->getCollection('Listing_Product');
        $listingProductCollection->addFieldToFilter('template_product_tax_code_id', $this->getId());

        if ($onlyPhysicalUnits) {
            $listingProductCollection->addFieldToFilter('is_variation_parent', 0);
        }

        if (is_array($columns) && !empty($columns)) {
            $listingProductCollection->getSelect()->reset(Zend_Db_Select::COLUMNS);
            $listingProductCollection->getSelect()->columns($columns);
        }

        return $asArrays ? (array)$listingProductCollection->getData() : (array)$listingProductCollection->getItems();
    }

    public function setSynchStatusNeed($newData, $oldData)
    {
        $listingsProducts = $this->getAffectedListingsProducts(true, array('id'), true);
        if (empty($listingsProducts)) {
            return;
        }

        $this->getResource()->setSynchStatusNeed($newData,$oldData,$listingsProducts);
    }

    //########################################

    public function save()
    {
        Mage::helper('M2ePro/Data_Cache_Permanent')->removeTagValues('amazon_template_productaxcode');
        return parent::save();
    }

    public function delete()
    {
        Mage::helper('M2ePro/Data_Cache_Permanent')->removeTagValues('amazon_template_productaxcode');
        return parent::delete();
    }

    //########################################
}
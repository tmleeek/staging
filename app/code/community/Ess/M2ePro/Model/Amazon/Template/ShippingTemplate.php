<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

/**
 * @method Ess_M2ePro_Model_Mysql4_Amazon_Template_ShippingTemplate getResource()
 */
class Ess_M2ePro_Model_Amazon_Template_ShippingTemplate extends Ess_M2ePro_Model_Component_Abstract
{
    const TEMPLATE_NAME_VALUE     = 1;
    const TEMPLATE_NAME_ATTRIBUTE = 2;

    /**
     * @var Ess_M2ePro_Model_Amazon_Template_ShippingTemplate_Source[]
     */
    private $shippingTemplateSourceModels = array();

    //########################################

    public function _construct()
    {
        parent::_construct();
        $this->_init('M2ePro/Amazon_Template_ShippingTemplate');
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
            ->addFieldToFilter('template_shipping_template_id', $this->getId())
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
     * @return Ess_M2ePro_Model_Amazon_Template_ShippingTemplate_Source
     */
    public function getSource(Ess_M2ePro_Model_Magento_Product $magentoProduct)
    {
        $id = $magentoProduct->getProductId();

        if (!empty($this->shippingTemplateSourceModels[$id])) {
            return $this->shippingTemplateSourceModels[$id];
        }

        $this->shippingTemplateSourceModels[$id] =
            Mage::getModel('M2ePro/Amazon_Template_ShippingTemplate_Source');

        $this->shippingTemplateSourceModels[$id]->setMagentoProduct($magentoProduct);
        $this->shippingTemplateSourceModels[$id]->setShippingTemplate($this);

        return $this->shippingTemplateSourceModels[$id];
    }

    //########################################

    public function getTitle()
    {
        return $this->getData('title');
    }

    // ---------------------------------------

    public function getTemplateNameMode()
    {
        return (int)$this->getData('template_name_mode');
    }

    public function isTemplateNameModeValue()
    {
        return $this->getTemplateNameMode() == self::TEMPLATE_NAME_VALUE;
    }

    public function isTemplateNameModeAttribute()
    {
        return $this->getTemplateNameMode() == self::TEMPLATE_NAME_ATTRIBUTE;
    }

    // ---------------------------------------

    public function getTemplateNameValue()
    {
        return $this->getData('template_name_value');
    }

    public function getTemplateNameAttribute()
    {
        return $this->getData('template_name_attribute');
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

    public function getTemplateNameAttributes()
    {
        $attributes = array();

        if ($this->isTemplateNameModeAttribute()) {
            $attributes[] = $this->getTemplateNameAttribute();
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
            $this->getTemplateNameAttributes()
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
        $listingProductCollection->addFieldToFilter('template_shipping_template_id', $this->getId());

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
        Mage::helper('M2ePro/Data_Cache_Permanent')->removeTagValues('amazon_template_shippingtemplate');
        return parent::save();
    }

    public function delete()
    {
        Mage::helper('M2ePro/Data_Cache_Permanent')->removeTagValues('amazon_template_shippingtemplate');
        return parent::delete();
    }

    //########################################
}
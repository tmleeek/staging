<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Ebay_Account_PickupStore extends Ess_M2ePro_Model_Component_Abstract
{
    const QTY_MODE_PRODUCT = 1;
    const QTY_MODE_SINGLE = 2;
    const QTY_MODE_NUMBER = 3;
    const QTY_MODE_ATTRIBUTE = 4;
    const QTY_MODE_PRODUCT_FIXED = 5;
    const QTY_MODE_SELLING_FORMAT_TEMPLATE = 6;

    //########################################

    public function _construct()
    {
        parent::_construct();
        $this->_init('M2ePro/Ebay_Account_PickupStore');
    }

    //########################################

    public function deleteInstance()
    {
        if ($this->isLocked()) {
            return false;
        }

        if (is_null($this->getId())) {
            throw new Ess_M2ePro_Model_Exception_Logic('Method require loaded instance first');
        }

        /** @var $connWrite Varien_Db_Adapter_Pdo_Mysql */
        $connWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        $connWrite->delete(
            Mage::getResourceModel('M2ePro/Ebay_Listing_Product_PickupStore')->getMainTable(),
            array('account_pickup_store_id = ?' => $this->getId())
        );
        $connWrite->delete(
            Mage::getResourceModel('M2ePro/Ebay_Account_PickupStore_State')->getMainTable(),
            array('account_pickup_store_id = ?' => $this->getId())
        );

        $this->delete();
        return true;
    }

    //########################################

    public function isAccountExists($accountId)
    {
        return (bool)Mage::getModel('M2ePro/Ebay_Account_PickupStore')->getCollection()
                           ->addFieldToFilter('account_id', $accountId)->getSize();
    }

    //########################################

    public function getName()
    {
        return $this->getData('name');
    }

    public function getLocationId()
    {
        return $this->getData('location_id');
    }

    public function getAccountId()
    {
        return (int)$this->getData('account_id');
    }

    public function getMarketplaceId()
    {
        return (int)$this->getData('marketplace_id');
    }

    public function getPhone()
    {
        return $this->getData('phone');
    }

    public function getUrl()
    {
        return $this->getData('url');
    }

    public function getPickupInstruction()
    {
        return $this->getData('pickup_instruction');
    }

    // ---------------------------------------

    public function getCountry()
    {
        return $this->getData('country');
    }

    public function getRegion()
    {
        return $this->getData('region');
    }

    public function getCity()
    {
        return $this->getData('city');
    }

    public function getPostalCode()
    {
        return $this->getData('postal_code');
    }

    public function getMainAddress()
    {
        return $this->getData('address_1');
    }

    public function getSecondAddress()
    {
        return $this->getData('address_2');
    }

    public function getLatitude()
    {
        return $this->getData('latitude');
    }

    public function getLongitude()
    {
        return $this->getData('longitude');
    }

    public function getUtcOffset()
    {
        return $this->getData('utc_offset');
    }

    // ---------------------------------------

    public function getBusinessHours()
    {
        return $this->getData('business_hours');
    }

    public function getSpecialHours()
    {
        return $this->getData('special_hours');
    }

    // ---------------------------------------

    public function getQtyMode()
    {
        return (int)$this->getData('qty_mode');
    }

    public function isQtyModeProduct()
    {
        return $this->getQtyMode() == self::QTY_MODE_PRODUCT;
    }

    public function isQtyModeSingle()
    {
        return $this->getQtyMode() == self::QTY_MODE_SINGLE;
    }

    public function isQtyModeNumber()
    {
        return $this->getQtyMode() == self::QTY_MODE_NUMBER;
    }

    public function isQtyModeAttribute()
    {
        return $this->getQtyMode() == self::QTY_MODE_ATTRIBUTE;
    }

    public function isQtyModeProductFixed()
    {
        return $this->getQtyMode() == self::QTY_MODE_PRODUCT_FIXED;
    }

    public function isQtyModeSellingFormatTemplate()
    {
        return $this->getQtyMode() == self::QTY_MODE_SELLING_FORMAT_TEMPLATE;
    }

    // ---------------------------------------

    public function getQtyNumber()
    {
        return (int)$this->getData('qty_custom_value');
    }

    public function getQtySource()
    {
        return array(
            'mode'      => $this->getQtyMode(),
            'value'     => $this->getQtyNumber(),
            'attribute' => $this->getData('qty_custom_attribute'),
            'qty_modification_mode'     => $this->getQtyModificationMode(),
            'qty_min_posted_value'      => $this->getQtyMinPostedValue(),
            'qty_max_posted_value'      => $this->getQtyMaxPostedValue(),
            'qty_percentage'            => $this->getQtyPercentage()
        );
    }

    public function getQtyAttributes()
    {
        $attributes = array();
        $src = $this->getQtySource();

        if ($src['mode'] == self::QTY_MODE_ATTRIBUTE) {
            $attributes[] = $src['attribute'];
        }

        return $attributes;
    }

    //----------------------------------------

    public function getQtyPercentage()
    {
        return (int)$this->getData('qty_percentage');
    }

    //----------------------------------------

    public function getQtyModificationMode()
    {
        return (int)$this->getData('qty_modification_mode');
    }

    public function isQtyModificationModeOn()
    {
        return (int)$this->getQtyModificationMode();
    }

    public function isQtyModificationModeOff()
    {
        return !((int)$this->getQtyModificationMode());
    }

    public function getQtyMinPostedValue()
    {
        return (int)$this->getData('qty_min_posted_value');
    }

    public function getQtyMaxPostedValue()
    {
        return (int)$this->getData('qty_max_posted_value');
    }

    //########################################

    public function getTrackingAttributes()
    {
        return $this->getQtyAttributes();
    }

    //########################################

    public function save()
    {
        Mage::helper('M2ePro/Data_Cache_Permanent')->removeTagValues('ebay_account_pickup_store');
        return parent::save();
    }

    public function delete()
    {
        Mage::helper('M2ePro/Data_Cache_Permanent')->removeTagValues('ebay_account_pickup_store');
        return parent::delete();
    }

    //########################################
}
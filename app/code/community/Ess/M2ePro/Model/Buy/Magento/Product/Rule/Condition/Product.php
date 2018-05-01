<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Buy_Magento_Product_Rule_Condition_Product
    extends Ess_M2ePro_Model_Magento_Product_Rule_Condition_Product
{
    //########################################

    protected function getCustomFilters()
    {
        $buyFilters = array(
            'buy_general_id'        => 'BuyGeneralId',
            'buy_sku'               => 'BuySku',
            'buy_online_qty'        => 'BuyOnlineQty',
            'buy_online_price'      => 'BuyOnlinePrice',
            'buy_status'            => 'BuyStatus'
        );

        return array_merge_recursive(
            parent::getCustomFilters(),
            $buyFilters
        );
    }

    /**
     * @param $filterId
     * @param $isReadyToCache
     * @return Ess_M2ePro_Model_Magento_Product_Rule_Custom_Abstract
     */
    protected function getCustomFilterInstance($filterId, $isReadyToCache = true)
    {
        $parentFilters = parent::getCustomFilters();
        if (isset($parentFilters[$filterId])) {
            return parent::getCustomFilterInstance($filterId, $isReadyToCache);
        }

        $customFilters = $this->getCustomFilters();
        if (!isset($customFilters[$filterId])) {
            return null;
        }

        if (isset($this->_customFiltersCache[$filterId])) {
            return $this->_customFiltersCache[$filterId];
        }

        /** @var Ess_M2ePro_Model_Magento_Product_Rule_Custom_Abstract $model */
        $model = Mage::getModel('M2ePro/Buy_Magento_Product_Rule_Custom_'.$customFilters[$filterId]);
        $model->setFilterOperator($this->getData('operator'))
              ->setFilterCondition($this->getData('value'));

        $isReadyToCache && $this->_customFiltersCache[$filterId] = $model;
        return $model;
    }

    //########################################
}
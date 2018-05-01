<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Buy_Connector_Product_List_ProcessingRunner
    extends Ess_M2ePro_Model_Buy_Connector_Product_ProcessingRunner
{
    // ########################################

    protected function eventBefore()
    {
        parent::eventBefore();

        $params = $this->getParams();

        $skus = array();

        foreach ($params['request_data']['items'] as $productData) {
            $skus[] = $productData['sku'];
        }

        /** @var Ess_M2ePro_Model_Lock_Item_Manager $lockItem */
        $lockItem = Mage::getModel('M2ePro/Lock_Item_Manager');
        $lockItem->setNick('buy_list_skus_queue_' . $params['account_id']);

        if ($lockItem->isExist()) {
            $existSkus = $lockItem->getContentData();
        } else {
            $existSkus = array();
            $lockItem->create();
        }

        $skus = array_map('strval', $skus);
        $skus = array_merge($existSkus, $skus);

        $lockItem->setContentData($skus);
    }

    protected function eventAfter()
    {
        parent::eventAfter();

        $params = $this->getParams();

        /** @var Ess_M2ePro_Model_Lock_Item_Manager $lockItem */
        $lockItem = Mage::getModel('M2ePro/Lock_Item_Manager');
        $lockItem->setNick('buy_list_skus_queue_' . $params['account_id']);

        if (!$lockItem->isExist()) {
            return;
        }

        $skusToRemove = array();

        foreach ($params['request_data']['items'] as $productData) {
            $skusToRemove[] = (string)$productData['sku'];
        }

        $resultSkus = array_diff($lockItem->getContentData(), $skusToRemove);

        if (empty($resultSkus)) {
            $lockItem->remove();
            return;
        }

        $lockItem->setContentData($resultSkus);
    }

    // ########################################
}
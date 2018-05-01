<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Amazon_Connector_Product_List_ProcessingRunner
    extends Ess_M2ePro_Model_Amazon_Connector_Product_ProcessingRunner
{
    // ########################################

    protected function eventBefore()
    {
        $this->addRequestSkuToQueue();
        parent::eventBefore();
    }

    protected function eventAfter()
    {
        $this->removeRequestSkuFromQueue();
        parent::eventAfter();
    }

    // ########################################

    private function addRequestSkuToQueue()
    {
        $params = $this->getParams();

        /** @var Ess_M2ePro_Model_Lock_Item_Manager $lockItem */
        $lockItem = Mage::getModel('M2ePro/Lock_Item_Manager');
        $lockItem->setNick('amazon_list_skus_queue_' . $params['account_id']);

        if ($lockItem->isExist()) {
            $existSkus = $lockItem->getContentData();
        } else {
            $existSkus = array();
            $lockItem->create();
        }

        $existSkus[] = (string)$params['request_data']['sku'];
        $existSkus = array_unique($existSkus);

        $lockItem->setContentData($existSkus);
    }

    private function removeRequestSkuFromQueue()
    {
        $params = $this->getParams();

        /** @var Ess_M2ePro_Model_Lock_Item_Manager $lockItem */
        $lockItem = Mage::getModel('M2ePro/Lock_Item_Manager');
        $lockItem->setNick('amazon_list_skus_queue_' . $params['account_id']);

        if (!$lockItem->isExist()) {
            return;
        }

        $existSkus = $lockItem->getContentData();

        $skuToRemoveIndex = array_search((string)$params['request_data']['sku'], $existSkus);
        if ($skuToRemoveIndex === false) {
            return;
        }

        unset($existSkus[$skuToRemoveIndex]);

        if (empty($existSkus)) {
            $lockItem->remove();
            return;
        }

        $lockItem->setContentData($existSkus);
    }

    // ########################################
}
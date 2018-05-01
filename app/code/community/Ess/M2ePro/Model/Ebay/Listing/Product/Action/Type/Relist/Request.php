<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Ebay_Listing_Product_Action_Type_Relist_Request
    extends Ess_M2ePro_Model_Ebay_Listing_Product_Action_Type_Request
{
    //########################################

    protected function beforeBuildDataEvent()
    {
        parent::beforeBuildDataEvent();

        $additionalData = $this->getListingProduct()->getAdditionalData();

        unset($additionalData['add_to_schedule']);
        unset($additionalData['item_duplicate_action_required']);

        $this->getListingProduct()->setSettings('additional_data', $additionalData);
        $this->getListingProduct()->setData('is_duplicate', 0);

        $this->getListingProduct()->save();
    }

    protected function afterBuildDataEvent()
    {
        $this->getConfigurator()->setPriority(
            Ess_M2ePro_Model_Ebay_Listing_Product_Action_Configurator::PRIORITY_RELIST
        );
    }

    //########################################

    /**
     * @return array
     */
    public function getActionData()
    {
        if (!$uuid = $this->getEbayListingProduct()->getItemUUID()) {

            $uuid = $this->getEbayListingProduct()->generateItemUUID();
            $this->getEbayListingProduct()->setData('item_uuid', $uuid)->save();
        }

        $data = array_merge(
            array(
                'item_id'   => $this->getEbayListingProduct()->getEbayItemIdReal(),
                'item_uuid' => $uuid
            ),
            $this->getRequestVariations()->getData()
        );

        if ($this->getConfigurator()->isGeneralAllowed()) {

            $data['sku'] = $this->getEbayListingProduct()->getSku();

            $data = array_merge(

                $data,

                $this->getRequestCategories()->getData(),

                $this->getRequestPayment()->getData(),
                $this->getRequestReturn()->getData(),
                $this->getRequestShipping()->getData()
            );
        }

        return array_merge(
            $data,
            $this->getRequestSelling()->getData(),
            $this->getRequestDescription()->getData()
        );
    }

    protected function prepareFinalData(array $data)
    {
        $data = $this->addConditionIfItIsNecessary($data);
        $data = $this->removeImagesIfThereAreNoChanges($data);
        return parent::prepareFinalData($data);
    }

    //########################################

    private function addConditionIfItIsNecessary(array $data)
    {
        $additionalData = $this->getListingProduct()->getAdditionalData();

        if (!isset($additionalData['is_need_relist_condition']) ||
            !$additionalData['is_need_relist_condition'] ||
            isset($data['item_condition'])) {
            return $data;
        }

        $data = array_merge($data, $this->getRequestDescription()->getConditionData());

        return $data;
    }

    //########################################
}
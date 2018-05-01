<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

final class Ess_M2ePro_Model_Ebay_Synchronization_Templates_Synchronization_Revise
    extends Ess_M2ePro_Model_Ebay_Synchronization_Templates_Synchronization_Abstract
{
    //########################################

    /**
     * @return string
     */
    protected function getNick()
    {
        return '/synchronization/revise/';
    }

    /**
     * @return string
     */
    protected function getTitle()
    {
        return 'Revise';
    }

    // ---------------------------------------

    /**
     * @return int
     */
    protected function getPercentsStart()
    {
        return 35;
    }

    /**
     * @return int
     */
    protected function getPercentsEnd()
    {
        return 55;
    }

    //########################################

    protected function performActions()
    {
        $this->executeQtyChanged();
        $this->executePriceChanged();

        $this->executeTitleChanged();
        $this->executeSubTitleChanged();
        $this->executeDescriptionChanged();
        $this->executeImagesChanged();

        if (Mage::helper('M2ePro/Component_Ebay_PickupStore')->isFeatureEnabled()) {
            $this->executePickupStoreQtyChanged();
        }

        $this->executeNeedSynchronize();
        $this->executeTotal();
    }

    //########################################

    private function executeQtyChanged()
    {
        $this->getActualOperationHistory()->addTimePoint(__METHOD__,'Update Quantity');

        $changedListingsProducts = $this->getProductChangesManager()->getInstances(
            array(Ess_M2ePro_Model_ProductChange::UPDATE_ATTRIBUTE_CODE)
        );

        foreach ($changedListingsProducts as $listingProduct) {

            try {

                /** @var $configurator Ess_M2ePro_Model_Ebay_Listing_Product_Action_Configurator */
                $configurator = Mage::getModel('M2ePro/Ebay_Listing_Product_Action_Configurator');
                $configurator->reset();
                $configurator->allowQty()->allowVariations();

                $isExistInRunner = $this->getRunner()->isExistProduct(
                    $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_REVISE, $configurator
                );

                if ($isExistInRunner) {
                    continue;
                }

                if (!$this->getInspector()->isMeetReviseQtyRequirements($listingProduct)) {
                    continue;
                }

                $this->getRunner()->addProduct(
                    $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_REVISE, $configurator
                );

            } catch (Exception $exception) {

                $this->logError($listingProduct, $exception);
                continue;
            }
        }

        $this->getActualOperationHistory()->saveTimePoint(__METHOD__);
    }

    private function executePriceChanged()
    {
        $this->getActualOperationHistory()->addTimePoint(__METHOD__,'Update Price');

        $changedListingsProducts = $this->getProductChangesManager()->getInstances(
            array(Ess_M2ePro_Model_ProductChange::UPDATE_ATTRIBUTE_CODE)
        );

        foreach ($changedListingsProducts as $listingProduct) {

            try {

                /** @var $configurator Ess_M2ePro_Model_Ebay_Listing_Product_Action_Configurator */
                $configurator = Mage::getModel('M2ePro/Ebay_Listing_Product_Action_Configurator');
                $configurator->reset();
                $configurator->allowPrice()->allowVariations();

                $isExistInRunner = $this->getRunner()->isExistProduct(
                    $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_REVISE, $configurator
                );

                if ($isExistInRunner) {
                    continue;
                }

                if (!$this->getInspector()->isMeetRevisePriceRequirements($listingProduct)) {
                    continue;
                }

                $this->getRunner()->addProduct(
                    $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_REVISE, $configurator
                );

            } catch (Exception $exception) {

                $this->logError($listingProduct, $exception);
                continue;
            }
        }

        $this->getActualOperationHistory()->saveTimePoint(__METHOD__);
    }

    //########################################

    private function executeTitleChanged()
    {
        $this->getActualOperationHistory()->addTimePoint(__METHOD__,'Update Title');

        $attributesForProductChange = array();

        /** @var Ess_M2ePro_Model_Ebay_Template_Description $template */
        foreach (Mage::getModel('M2ePro/Ebay_Template_Description')->getCollection()->getItems() as $template) {
            $attributesForProductChange = array_merge($attributesForProductChange,$template->getTitleAttributes());
        }

        /** @var Ess_M2ePro_Model_Listing_Product[] $changedListingsProducts */
        $changedListingsProducts = $this->getProductChangesManager()->getInstancesByListingProduct(
            array_unique($attributesForProductChange), true
        );

        foreach ($changedListingsProducts as $listingProduct) {

            try {

                /** @var Ess_M2ePro_Model_Ebay_Listing_Product $ebayListingProduct */
                $ebayListingProduct = $listingProduct->getChildObject();

                $titleAttributes = $ebayListingProduct->getEbayDescriptionTemplate()->getTitleAttributes();

                if (!in_array($listingProduct->getData('changed_attribute'), $titleAttributes)) {
                    continue;
                }

                /** @var $configurator Ess_M2ePro_Model_Ebay_Listing_Product_Action_Configurator */
                $configurator = Mage::getModel('M2ePro/Ebay_Listing_Product_Action_Configurator');
                $configurator->reset();
                $configurator->allowTitle();

                $isExistInRunner = $this->getRunner()->isExistProduct(
                    $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_REVISE, $configurator
                );

                if ($isExistInRunner) {
                    continue;
                }

                if (!$this->getInspector()->isMeetReviseTitleRequirements($listingProduct, false)) {
                    continue;
                }

                $this->getRunner()->addProduct(
                    $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_REVISE, $configurator
                );

            } catch (Exception $exception) {

                $this->logError($listingProduct, $exception);
                continue;
            }
        }

        $this->getActualOperationHistory()->saveTimePoint(__METHOD__);
    }

    private function executeSubTitleChanged()
    {
        $this->getActualOperationHistory()->addTimePoint(__METHOD__,'Update Subtitle');

        $attributesForProductChange = array();

        /** @var Ess_M2ePro_Model_Ebay_Template_Description $template */
        foreach (Mage::getModel('M2ePro/Ebay_Template_Description')->getCollection()->getItems() as $template) {
            $attributesForProductChange = array_merge($attributesForProductChange, $template->getSubTitleAttributes());
        }

        $changedListingsProducts = $this->getProductChangesManager()->getInstancesByListingProduct(
            array_unique($attributesForProductChange), true
        );

        foreach ($changedListingsProducts as $listingProduct) {

            try {

                /** @var Ess_M2ePro_Model_Ebay_Listing_Product $ebayListingProduct */
                $ebayListingProduct = $listingProduct->getChildObject();

                $subTitleAttributes = $ebayListingProduct->getEbayDescriptionTemplate()->getSubTitleAttributes();

                if (!in_array($listingProduct->getData('changed_attribute'), $subTitleAttributes)) {
                    continue;
                }

                /** @var $configurator Ess_M2ePro_Model_Ebay_Listing_Product_Action_Configurator */
                $configurator = Mage::getModel('M2ePro/Ebay_Listing_Product_Action_Configurator');
                $configurator->reset();
                $configurator->allowSubtitle();

                $isExistInRunner = $this->getRunner()->isExistProduct(
                    $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_REVISE, $configurator
                );

                if ($isExistInRunner) {
                    continue;
                }

                if (!$this->getInspector()->isMeetReviseSubTitleRequirements($listingProduct, false)) {
                    continue;
                }

                $this->getRunner()->addProduct(
                    $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_REVISE, $configurator
                );

            } catch (Exception $exception) {

                $this->logError($listingProduct, $exception);
                continue;
            }
        }

        $this->getActualOperationHistory()->saveTimePoint(__METHOD__);
    }

    private function executeDescriptionChanged()
    {
        $this->getActualOperationHistory()->addTimePoint(__METHOD__,'Update Description');

        $attributesForProductChange = array();

        /** @var Ess_M2ePro_Model_Ebay_Template_Description $template */
        foreach (Mage::getModel('M2ePro/Ebay_Template_Description')->getCollection()->getItems() as $template) {
            $attributesForProductChange = array_merge(
                $attributesForProductChange,
                $template->getDescriptionAttributes()
            );
        }

        $changedListingsProducts = $this->getProductChangesManager()->getInstancesByListingProduct(
            array_unique($attributesForProductChange), true
        );

        foreach ($changedListingsProducts as $listingProduct) {

            try {

                /** @var Ess_M2ePro_Model_Ebay_Listing_Product $ebayListingProduct */
                $ebayListingProduct = $listingProduct->getChildObject();

                $descriptionAttributes = $ebayListingProduct->getEbayDescriptionTemplate()->getDescriptionAttributes();

                if (!in_array($listingProduct->getData('changed_attribute'), $descriptionAttributes)) {
                    continue;
                }

                /** @var $configurator Ess_M2ePro_Model_Ebay_Listing_Product_Action_Configurator */
                $configurator = Mage::getModel('M2ePro/Ebay_Listing_Product_Action_Configurator');
                $configurator->reset();
                $configurator->allowDescription();

                $isExistInRunner = $this->getRunner()->isExistProduct(
                    $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_REVISE, $configurator
                );

                if ($isExistInRunner) {
                    continue;
                }

                if (!$this->getInspector()->isMeetReviseDescriptionRequirements($listingProduct, false)) {
                    continue;
                }

                $this->getRunner()->addProduct(
                    $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_REVISE, $configurator
                );

            } catch (Exception $exception) {

                $this->logError($listingProduct, $exception);
                continue;
            }
        }

        $this->getActualOperationHistory()->saveTimePoint(__METHOD__);
    }

    private function executeImagesChanged()
    {
        $this->getActualOperationHistory()->addTimePoint(__METHOD__,'Update Images');

        $ebayTemplateDescriptionItems = Mage::getModel('M2ePro/Ebay_Template_Description')->getCollection()->getItems();
        $attributesForProductChange = array();

        /** @var Ess_M2ePro_Model_Ebay_Template_Description $template */
        foreach ($ebayTemplateDescriptionItems as $template) {
            $attributesForProductChange = array_merge(
                $attributesForProductChange,
                $template->getImageMainAttributes(),
                $template->getGalleryImagesAttributes()
            );
        }

        $attributesForProductChange = array_unique($attributesForProductChange);

        $changedListingsProducts = $this->getProductChangesManager()->getInstancesByListingProduct(
            $attributesForProductChange, true
        );

        foreach ($changedListingsProducts as $listingProduct) {

            try {

                /** @var Ess_M2ePro_Model_Ebay_Listing_Product $ebayListingProduct */
                $ebayListingProduct = $listingProduct->getChildObject();

                $imagesAttributes = array_merge(
                    $ebayListingProduct->getEbayDescriptionTemplate()->getImageMainAttributes(),
                    $ebayListingProduct->getEbayDescriptionTemplate()->getGalleryImagesAttributes()
                );

                if (!in_array($listingProduct->getData('changed_attribute'), $imagesAttributes)) {
                    continue;
                }

                /** @var $configurator Ess_M2ePro_Model_Ebay_Listing_Product_Action_Configurator */
                $configurator = Mage::getModel('M2ePro/Ebay_Listing_Product_Action_Configurator');
                $configurator->reset();
                $configurator->allowImages();

                $isExistInRunner = $this->getRunner()->isExistProduct(
                    $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_REVISE, $configurator
                );

                if ($isExistInRunner) {
                    continue;
                }

                if (!$this->getInspector()->isMeetReviseImagesRequirements($listingProduct, false)) {
                    continue;
                }

                $this->getRunner()->addProduct(
                    $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_REVISE, $configurator
                );

            } catch (Exception $exception) {

                $this->logError($listingProduct, $exception);
                continue;
            }
        }

        /** @var Ess_M2ePro_Model_Ebay_Template_Description $template */
        foreach ($ebayTemplateDescriptionItems as $template) {
            $attributesForProductChange = array_merge(
                $attributesForProductChange,
                $template->getVariationImagesAttributes()
            );
        }

        $changedListingsProductsByVariationOption = $this->getProductChangesManager()->getInstancesByVariationOption(
            array_unique($attributesForProductChange), true
        );

        foreach ($changedListingsProductsByVariationOption as $listingProduct) {

            /** @var Ess_M2ePro_Model_Ebay_Listing_Product $ebayListingProduct */
            $ebayListingProduct = $listingProduct->getChildObject();

            $imagesAttributes = $ebayListingProduct->getEbayDescriptionTemplate()->getVariationImagesAttributes();

            if (!in_array($listingProduct->getData('changed_attribute'), $imagesAttributes)) {
                continue;
            }

            /** @var $configurator Ess_M2ePro_Model_Ebay_Listing_Product_Action_Configurator */
            $configurator = Mage::getModel('M2ePro/Ebay_Listing_Product_Action_Configurator');
            $configurator->reset();
            $configurator->allowVariations();

            $isExistInRunner = $this->getRunner()->isExistProduct(
                $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_REVISE, $configurator
            );

            if ($isExistInRunner) {
                continue;
            }

            if (!$this->getInspector()->isMeetReviseImagesRequirements($listingProduct)) {
                continue;
            }

            $this->getRunner()->addProduct(
                $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_REVISE, $configurator
            );
        }

        $this->getActualOperationHistory()->saveTimePoint(__METHOD__);
    }

    //########################################

    private function executePickupStoreQtyChanged()
    {
        $this->getActualOperationHistory()->addTimePoint(__METHOD__,'Update Pickup Store Quantity');

        $changedListingsProducts = $this->getProductChangesManager()->getInstances(
            array(Ess_M2ePro_Model_ProductChange::UPDATE_ATTRIBUTE_CODE)
        );

        foreach ($changedListingsProducts as $listingProduct) {

            try {

                /** @var Ess_M2ePro_Model_Ebay_Listing_Product $ebayListingProduct */
                $ebayListingProduct = $listingProduct->getChildObject();
                if (!$ebayListingProduct->getEbayAccount()->isPickupStoreEnabled()) {
                    continue;
                }

                $ebaySynchronizationTemplate = $ebayListingProduct->getEbaySynchronizationTemplate();
                if (!$ebaySynchronizationTemplate->isReviseWhenChangeQty()) {
                    continue;
                }

                $pickupStoreStateUpdater = Mage::getModel('M2ePro/Ebay_Listing_Product_PickupStore_State_Updater');
                $pickupStoreStateUpdater->setListingProduct($listingProduct);

                if ($ebaySynchronizationTemplate->isReviseUpdateQtyMaxAppliedValueModeOn()) {
                    $pickupStoreStateUpdater->setMaxAppliedQtyValue(
                        $ebaySynchronizationTemplate->getReviseUpdateQtyMaxAppliedValue()
                    );
                }

                $pickupStoreStateUpdater->process();

            } catch (Exception $exception) {

                $this->logError($listingProduct, $exception);
                continue;
            }
        }

        $this->getActualOperationHistory()->saveTimePoint(__METHOD__);
    }

    //########################################

    private function executeNeedSynchronize()
    {
        $this->getActualOperationHistory()->addTimePoint(__METHOD__,'Execute is need synchronize');

        $listingProductCollection = Mage::helper('M2ePro/Component_Ebay')->getCollection('Listing_Product');
        $listingProductCollection->addFieldToFilter('status', Ess_M2ePro_Model_Listing_Product::STATUS_LISTED);
        $listingProductCollection->addFieldToFilter('synch_status',Ess_M2ePro_Model_Listing_Product::SYNCH_STATUS_NEED);

        $tag = 'in_action';
        $modelName = Mage::getModel('M2ePro/Listing_Product')->getResourceName();

        $listingProductCollection->getSelect()->joinLeft(
            array('mpc' => Mage::getResourceModel('M2ePro/Processing_Lock')->getMainTable()),
            "mpc.object_id = main_table.id AND mpc.tag='{$tag}' AND mpc.model_name = '{$modelName}'",
            array()
        );
        $listingProductCollection->addFieldToFilter('mpc.id', array('null' => true));

        $listingProductCollection->getSelect()->limit(100);

        foreach ($listingProductCollection->getItems() as $listingProduct) {

            try {

                /** @var $listingProduct Ess_M2ePro_Model_Listing_Product */
                $listingProduct->setData('synch_status',Ess_M2ePro_Model_Listing_Product::SYNCH_STATUS_SKIP)->save();

                /** @var $configurator Ess_M2ePro_Model_Ebay_Listing_Product_Action_Configurator */
                $configurator = Mage::getModel('M2ePro/Ebay_Listing_Product_Action_Configurator');

                $isExistInRunner = $this->getRunner()->isExistProduct(
                    $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_REVISE, $configurator
                );

                if ($isExistInRunner) {
                    continue;
                }

                if (!$this->getInspector()->isMeetReviseSynchReasonsRequirements($listingProduct, false)) {
                    continue;
                }

                $this->getRunner()->addProduct(
                    $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_REVISE, $configurator
                );

            } catch (Exception $exception) {

                $this->logError($listingProduct, $exception);
                continue;
            }
        }

        $this->getActualOperationHistory()->saveTimePoint(__METHOD__);
    }

    private function executeTotal()
    {
        $this->getActualOperationHistory()->addTimePoint(__METHOD__,'Execute Revise all');

        $lastListingProductProcessed = $this->getConfigValue(
            $this->getFullSettingsPath().'total/','last_listing_product_id'
        );

        if (is_null($lastListingProductProcessed)) {
            return;
        }

        $itemsPerCycle = 100;

        /* @var $collection Varien_Data_Collection_Db */
        $collection = Mage::helper('M2ePro/Component_Ebay')
            ->getCollection('Listing_Product')
            ->addFieldToFilter('id',array('gt' => $lastListingProductProcessed))
            ->addFieldToFilter('status', Ess_M2ePro_Model_Listing_Product::STATUS_LISTED);

        $collection->getSelect()->limit($itemsPerCycle);
        $collection->getSelect()->order('id ASC');

        /* @var $listingProduct Ess_M2ePro_Model_Listing_Product */
        foreach ($collection->getItems() as $listingProduct) {

            try {

                /** @var $configurator Ess_M2ePro_Model_Ebay_Listing_Product_Action_Configurator */
                $configurator = Mage::getModel('M2ePro/Ebay_Listing_Product_Action_Configurator');

                $isExistInRunner = $this->getRunner()->isExistProduct(
                    $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_REVISE, $configurator
                );

                if ($isExistInRunner) {
                    continue;
                }

                if (!$this->getInspector()->isMeetReviseGeneralRequirements($listingProduct, false)) {
                    continue;
                }

                $this->getRunner()->addProduct(
                    $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_REVISE, $configurator
                );

            } catch (Exception $exception) {

                $this->logError($listingProduct, $exception);
                continue;
            }
        }

        $lastListingProduct = $collection->getLastItem()->getId();

        if ($collection->count() < $itemsPerCycle) {

            $this->setConfigValue(
                $this->getFullSettingsPath().'total/', 'end_date',
                Mage::helper('M2ePro')->getCurrentGmtDate()
            );

            $lastListingProduct = NULL;
        }

        $this->setConfigValue(
            $this->getFullSettingsPath().'total/', 'last_listing_product_id',
            $lastListingProduct
        );

        $this->getActualOperationHistory()->saveTimePoint(__METHOD__);
    }

    //########################################
}
<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

final class Ess_M2ePro_Model_Amazon_Synchronization_Templates_Synchronization_Revise
    extends Ess_M2ePro_Model_Amazon_Synchronization_Templates_Synchronization_Abstract
{
    //########################################

    protected function getNick()
    {
        return '/synchronization/revise/';
    }

    protected function getTitle()
    {
        return 'Revise';
    }

    // ---------------------------------------

    protected function getPercentsStart()
    {
        return 15;
    }

    protected function getPercentsEnd()
    {
        return 40;
    }

    //########################################

    protected function performActions()
    {
        $this->executeQtyChanged();

        $this->executeRegularPriceChanged();
        $this->executeBusinessPriceChanged();

        $this->executeDetailsChanged();
        $this->executeImagesChanged();

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

                /** @var $configurator Ess_M2ePro_Model_Amazon_Listing_Product_Action_Configurator */
                $configurator = Mage::getModel('M2ePro/Amazon_Listing_Product_Action_Configurator');
                $configurator->reset();
                $configurator->allowQty();

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

    private function executeRegularPriceChanged()
    {
        $this->getActualOperationHistory()->addTimePoint(__METHOD__,'Update Price');

        /** @var Ess_M2ePro_Model_Listing_Product[] $changedListingsProducts */
        $changedListingsProducts = $this->getProductChangesManager()->getInstances(
            array(Ess_M2ePro_Model_ProductChange::UPDATE_ATTRIBUTE_CODE)
        );

        foreach ($changedListingsProducts as $listingProduct) {

            try {

                /** @var $configurator Ess_M2ePro_Model_Amazon_Listing_Product_Action_Configurator */
                $configurator = Mage::getModel('M2ePro/Amazon_Listing_Product_Action_Configurator');
                $configurator->reset();
                $configurator->allowRegularPrice();

                $isExistInRunner = $this->getRunner()->isExistProduct(
                    $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_REVISE, $configurator
                );

                if ($isExistInRunner) {
                    continue;
                }

                if (!$this->getInspector()->isMeetReviseRegularPriceRequirements($listingProduct)) {
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

    private function executeBusinessPriceChanged()
    {
        $this->getActualOperationHistory()->addTimePoint(__METHOD__,'Update Price');

        /** @var Ess_M2ePro_Model_Listing_Product[] $changedListingsProducts */
        $changedListingsProducts = $this->getProductChangesManager()->getInstances(
            array(Ess_M2ePro_Model_ProductChange::UPDATE_ATTRIBUTE_CODE)
        );

        foreach ($changedListingsProducts as $listingProduct) {

            try {

                /** @var $configurator Ess_M2ePro_Model_Amazon_Listing_Product_Action_Configurator */
                $configurator = Mage::getModel('M2ePro/Amazon_Listing_Product_Action_Configurator');
                $configurator->reset();
                $configurator->allowBusinessPrice();

                $isExistInRunner = $this->getRunner()->isExistProduct(
                    $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_REVISE, $configurator
                );

                if ($isExistInRunner) {
                    continue;
                }

                if (!$this->getInspector()->isMeetReviseBusinessPriceRequirements($listingProduct)) {
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

    private function executeDetailsChanged()
    {
        $this->getActualOperationHistory()->addTimePoint(__METHOD__,'Update details');

        $attributesForProductChange = array();
        foreach (Mage::getModel('M2ePro/Amazon_Template_Description')->getCollection() as $template) {

            /** @var Ess_M2ePro_Model_Amazon_Template_Description $template */

            $attributes = $template->getDefinitionTemplate()->getUsedDetailsAttributes();

            $specifics = $template->getSpecifics(true);
            foreach ($specifics as $specific) {
                $attributes = array_merge($attributes,$specific->getUsedAttributes());
            }

            $attributesForProductChange = array_merge($attributesForProductChange,$attributes);
        }

        foreach (Mage::getModel('M2ePro/Amazon_Template_ProductTaxCode')->getCollection() as $template) {

            /** @var Ess_M2ePro_Model_Amazon_Template_ProductTaxCode $template */

            $attributesForProductChange = array_merge($attributesForProductChange, $template->getUsedAttributes());
        }

        foreach (Mage::getModel('M2ePro/Amazon_Template_ShippingTemplate')->getCollection() as $template) {

            /** @var Ess_M2ePro_Model_Amazon_Template_ShippingTemplate $template */

            $attributesForProductChange = array_merge($attributesForProductChange, $template->getUsedAttributes());
        }

        foreach (Mage::getModel('M2ePro/Amazon_Listing')->getCollection() as $listing) {

            /** @var Ess_M2ePro_Model_Amazon_Listing $listing */

            $attributesForProductChange = array_merge(
                $attributesForProductChange,
                $listing->getConditionNoteAttributes(),
                $listing->getGiftWrapAttributes(),
                $listing->getGiftMessageAttributes()
            );
        }

        foreach ($this->getChangedListingsProducts($attributesForProductChange) as $listingProduct) {

            try {

                /** @var Ess_M2ePro_Model_Amazon_Listing_Product $amazonListingProduct */
                $amazonListingProduct = $listingProduct->getChildObject();

                $detailsAttributes = array_merge(
                    $amazonListingProduct->getAmazonListing()->getConditionNoteAttributes(),
                    $amazonListingProduct->getAmazonListing()->getGiftWrapAttributes(),
                    $amazonListingProduct->getAmazonListing()->getGiftMessageAttributes()
                );

                if ($amazonListingProduct->isExistDescriptionTemplate()) {
                    $descriptionTemplateDetailsAttributes = $amazonListingProduct->getAmazonDescriptionTemplate()
                                                                                 ->getDefinitionTemplate()
                                                                                 ->getUsedDetailsAttributes();

                    $specifics = $amazonListingProduct->getAmazonDescriptionTemplate()->getSpecifics(true);
                    foreach ($specifics as $specific) {
                        $descriptionTemplateDetailsAttributes = array_merge(
                            $descriptionTemplateDetailsAttributes, $specific->getUsedAttributes()
                        );
                    }

                    $detailsAttributes = array_merge(
                        $detailsAttributes,
                        $descriptionTemplateDetailsAttributes
                    );
                }

                if ($amazonListingProduct->isExistProductTaxCodeTemplate()) {
                    $detailsAttributes = array_merge(
                        $detailsAttributes,
                        $amazonListingProduct->getProductTaxCodeTemplate()->getUsedAttributes()
                    );
                }

                if ($amazonListingProduct->isExistShippingTemplateTemplate()) {
                    $detailsAttributes = array_merge(
                        $detailsAttributes,
                        $amazonListingProduct->getShippingTemplateTemplate()->getUsedAttributes()
                    );
                }

                if (!in_array($listingProduct->getData('changed_attribute'), $detailsAttributes)) {
                    continue;
                }

                /** @var $configurator Ess_M2ePro_Model_Amazon_Listing_Product_Action_Configurator */
                $configurator = Mage::getModel('M2ePro/Amazon_Listing_Product_Action_Configurator');
                $configurator->reset();
                $configurator->allowDetails();

                $isExistInRunner = $this->getRunner()->isExistProduct(
                    $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_REVISE, $configurator
                );

                if ($isExistInRunner) {
                    continue;
                }

                if (!$this->getInspector()->isMeetReviseDetailsRequirements($listingProduct, false)) {
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
        $this->getActualOperationHistory()->addTimePoint(__METHOD__,'Update images');

        $attributesForProductChange = array();
        foreach (Mage::getModel('M2ePro/Amazon_Template_Description')->getCollection() as $template) {

            /** @var Ess_M2ePro_Model_Amazon_Template_Description $template */

            $attributesForProductChange = array_merge(
                $attributesForProductChange,
                $template->getDefinitionTemplate()->getUsedImagesAttributes()
            );
        }

        foreach (Mage::getModel('M2ePro/Amazon_Listing')->getCollection() as $listing) {

            /** @var Ess_M2ePro_Model_Amazon_Listing $listing */

            $attributesForProductChange = array_merge(
                $attributesForProductChange,
                $listing->getImageMainAttributes(),
                $listing->getGalleryImagesAttributes()
            );
        }

        foreach ($this->getChangedListingsProducts($attributesForProductChange) as $listingProduct) {

            try {

                /** @var Ess_M2ePro_Model_Amazon_Listing_Product $amazonListingProduct */
                $amazonListingProduct = $listingProduct->getChildObject();

                $amazonListing = $amazonListingProduct->getAmazonListing();

                $imagesAttributes = array_merge(
                    $amazonListing->getImageMainAttributes(),
                    $amazonListing->getGalleryImagesAttributes()
                );

                if ($amazonListingProduct->isExistDescriptionTemplate()) {
                    $amazonDescriptionTemplate = $amazonListingProduct->getAmazonDescriptionTemplate();
                    $imagesAttributes = array_merge(
                        $imagesAttributes,
                        $amazonDescriptionTemplate->getDefinitionTemplate()->getUsedImagesAttributes()
                    );
                }

                if (!in_array($listingProduct->getData('changed_attribute'), $imagesAttributes)) {
                    continue;
                }

                /** @var $configurator Ess_M2ePro_Model_Amazon_Listing_Product_Action_Configurator */
                $configurator = Mage::getModel('M2ePro/Amazon_Listing_Product_Action_Configurator');
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

        $this->getActualOperationHistory()->saveTimePoint(__METHOD__);
    }

    //########################################

    private function executeNeedSynchronize()
    {
        $this->getActualOperationHistory()->addTimePoint(__METHOD__,'Execute is need synchronize');

        $listingProductCollection = Mage::helper('M2ePro/Component_Amazon')->getCollection('Listing_Product');
        $listingProductCollection->addFieldToFilter(
            'status',
            array('in' => array(
                Ess_M2ePro_Model_Listing_Product::STATUS_LISTED,
                Ess_M2ePro_Model_Listing_Product::STATUS_UNKNOWN,
            ))
        );
        $listingProductCollection->addFieldToFilter('synch_status',Ess_M2ePro_Model_Listing_Product::SYNCH_STATUS_NEED);
        $listingProductCollection->addFieldToFilter('is_variation_parent', 0);

        $tag = 'in_action';
        $modelName = Mage::getModel('M2ePro/Listing_Product')->getResourceName();

        $listingProductCollection->getSelect()->joinLeft(
            array('mpc' => Mage::getResourceModel('M2ePro/Processing_Lock')->getMainTable()),
            "mpc.object_id = main_table.id AND mpc.tag='{$tag}' AND mpc.model_name = '{$modelName}'",
            array()
        );
        $listingProductCollection->addFieldToFilter('mpc.id', array('null' => true));

        $listingProductCollection->getSelect()->limit(100);

        /** @var $listingProduct Ess_M2ePro_Model_Listing_Product */
        foreach ($listingProductCollection->getItems() as $listingProduct) {

            try {

                $listingProduct->setData('synch_status',Ess_M2ePro_Model_Listing_Product::SYNCH_STATUS_SKIP)->save();

                /** @var $configurator Ess_M2ePro_Model_Amazon_Listing_Product_Action_Configurator */
                $configurator = Mage::getModel('M2ePro/Amazon_Listing_Product_Action_Configurator');

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
        $this->getActualOperationHistory()->addTimePoint(__METHOD__,'Execute revise all');

        $lastListingProductProcessed = $this->getConfigValue(
            $this->getFullSettingsPath().'total/','last_listing_product_id'
        );

        if (is_null($lastListingProductProcessed)) {
            return;
        }

        $itemsPerCycle = 100;

        /* @var $collection Varien_Data_Collection_Db */
        $collection = Mage::helper('M2ePro/Component_Amazon')
            ->getCollection('Listing_Product')
            ->addFieldToFilter('id',array('gt' => $lastListingProductProcessed))
            ->addFieldToFilter('status', Ess_M2ePro_Model_Listing_Product::STATUS_LISTED)
            ->addFieldToFilter('is_variation_parent', 0);

        $collection->getSelect()->limit($itemsPerCycle);
        $collection->getSelect()->order('id ASC');

        /* @var $listingProduct Ess_M2ePro_Model_Listing_Product */
        foreach ($collection->getItems() as $listingProduct) {

            try {

                /** @var $configurator Ess_M2ePro_Model_Amazon_Listing_Product_Action_Configurator */
                $configurator = Mage::getModel('M2ePro/Amazon_Listing_Product_Action_Configurator');

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

    /**
     * @param array $trackingAttributes
     * @return Ess_M2ePro_Model_Listing_Product[]
     */
    private function getChangedListingsProducts(array $trackingAttributes)
    {
        $filteredChangedListingsProducts = array();

        $changedListingsProducts = $this->getProductChangesManager()->getInstancesByListingProduct(
            array_unique($trackingAttributes), true
        );

        foreach ($changedListingsProducts as $changedListingProduct) {
            /** @var Ess_M2ePro_Model_Amazon_Listing_Product $amazonListingProduct */
            $amazonListingProduct = $changedListingProduct->getChildObject();

            if ($amazonListingProduct->getVariationManager()->isRelationParentType()) {
                continue;
            }

            $magentoProduct = $changedListingProduct->getMagentoProduct();

            if ($magentoProduct->isConfigurableType() || $magentoProduct->isGroupedType()) {
                continue;
            }

            $filteredChangedListingsProducts[$changedListingProduct->getId()] = $changedListingProduct;
        }

        $changedListingsProducts = $this->getProductChangesManager()->getInstancesByVariationOption(
            array_unique($trackingAttributes), true
        );

        foreach ($changedListingsProducts as $changedListingProduct) {
            $magentoProduct = $changedListingProduct->getMagentoProduct();

            if ($magentoProduct->isSimpleTypeWithCustomOptions() || $magentoProduct->isBundleType()) {
                continue;
            }

            $filteredChangedListingsProducts[$changedListingProduct->getId()] = $changedListingProduct;
        }

        return $filteredChangedListingsProducts;
    }

    //########################################
}
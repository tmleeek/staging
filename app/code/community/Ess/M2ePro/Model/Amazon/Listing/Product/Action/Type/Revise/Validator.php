<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Amazon_Listing_Product_Action_Type_Revise_Validator
    extends Ess_M2ePro_Model_Amazon_Listing_Product_Action_Type_Validator
{
    //########################################

    /**
     * @return bool
     */
    public function validate()
    {
        if (!$this->validateBlocked()) {
            return false;
        }

        if ($this->getVariationManager()->isRelationParentType() && !$this->validateParentListingProductFlags()) {
            return false;
        }

        if (!$this->validatePhysicalUnitAndSimple()) {
            return false;
        }

        $params = $this->getParams();

        if (!empty($params['switch_to']) && !$this->getConfigurator()->isQtyAllowed()) {
            // M2ePro_TRANSLATIONS
            // Fulfillment mode can not be switched if QTY feed is not allowed.
            $this->addMessage('Fulfillment mode can not be switched if QTY feed is not allowed.');
            return false;
        }

        if ($this->getConfigurator()->isQtyAllowed()) {

            if ($this->getAmazonListingProduct()->isAfnChannel()) {

                if (empty($params['switch_to'])) {

                    $this->getConfigurator()->disallowQty();

                    // M2ePro_TRANSLATIONS
                    // This Product is an FBA Item, so it’s Quantity updating will change it to MFN. Thus QTY feed, Production Time and Restock Date Values will not be updated. Inventory management for FBA Items is currently unavailable in M2E Pro. However, you can do that directly in your Amazon Seller Central.
                    $this->addMessage(
                        'This Product is an FBA Item, so it’s Quantity updating will change it to MFN. Thus QTY feed,
                        Production Time and Restock Date Values will not be updated. Inventory management for FBA
                        Items is currently unavailable in M2E Pro. However, you can do that directly in your Amazon
                        Seller Central.',
                        Ess_M2ePro_Model_Connector_Connection_Response_Message::TYPE_WARNING
                    );

                } else {

                    $afn = Ess_M2ePro_Model_Amazon_Listing_Product_Action_Request_Qty::FULFILLMENT_MODE_AFN;

                    if ($params['switch_to'] === $afn) {
                        // M2ePro_TRANSLATIONS
                        // You cannot switch Fulfillment because it is applied now.
                        $this->addMessage('You cannot switch Fulfillment because it is applied now.');
                        return false;
                    }
                }

            } else {

                $mfn = Ess_M2ePro_Model_Amazon_Listing_Product_Action_Request_Qty::FULFILLMENT_MODE_MFN;

                if (!empty($params['switch_to']) && $params['switch_to'] === $mfn) {
                    // M2ePro_TRANSLATIONS
                    // You cannot switch Fulfillment because it is applied now.
                    $this->addMessage('You cannot switch Fulfillment because it is applied now.');
                    return false;
                }
            }
        }

        if ($this->getAmazonListingProduct()->isAfnChannel()) {

            if ($this->getConfigurator()->isShippingOverrideAllowed() &&
                $this->getAmazonAccount()->isShippingModeOverride() &&
                $this->getAmazonListingProduct()->isExistShippingOverrideTemplate()) {

                $this->getConfigurator()->disallowShippingOverride();

                // M2ePro_TRANSLATIONS
                // This Product is an FBA Item, so it’s Shipping Override Settings updating will not be sent.
                $this->addMessage(
                    'This Product is an FBA Item, so it’s Shipping Override Settings will not be sent.',
                    Ess_M2ePro_Model_Connector_Connection_Response_Message::TYPE_WARNING
                );
            } elseif ($this->getConfigurator()->isShippingTemplateAllowed() &&
                $this->getAmazonAccount()->isShippingModeTemplate() &&
                $this->getAmazonListingProduct()->isExistShippingTemplateTemplate()) {

                $this->getConfigurator()->disallowShippingTemplate();

                // M2ePro_TRANSLATIONS
                // This Product is an FBA Item, so it’s Shipping Template Settings updating will not be sent.
                $this->addMessage(
                    'This Product is an FBA Item, so it’s Shipping Template Settings will not be sent.',
                    Ess_M2ePro_Model_Connector_Connection_Response_Message::TYPE_WARNING
                );
            }
        }

        if ($this->getVariationManager()->isPhysicalUnit() && !$this->validatePhysicalUnitMatching()) {
            return false;
        }

        if (!$this->validateSku()) {
            return false;
        }

        if (!$this->getAmazonListingProduct()->isAfnChannel() &&
            (!$this->getListingProduct()->isListed() || !$this->getListingProduct()->isRevisable())
        ) {

            // M2ePro_TRANSLATIONS
            // Item is not Listed or not available
            $this->addMessage('Item is not Listed or not available');

            return false;
        }

        if (!$this->validateQty()) {
            return false;
        }

        if (!$this->validateRegularPrice() || !$this->validateBusinessPrice()) {
            return false;
        }

        return true;
    }

    //########################################
}
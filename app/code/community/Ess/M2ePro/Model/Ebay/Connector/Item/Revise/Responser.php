<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Ebay_Connector_Item_Revise_Responser
    extends Ess_M2ePro_Model_Ebay_Connector_Item_Responser
{
    //########################################

    protected function getSuccessfulMessage()
    {
        return $this->getResponseObject()->getSuccessfulMessage();
    }

    //########################################

    protected function processCompleted(array $data = array(), array $params = array())
    {
        if (!empty($data['already_stop'])) {
            $this->getResponseObject()->processAlreadyStopped($data, $params);

            // M2ePro_TRANSLATIONS
            // Item was already Stopped on eBay
            $message = Mage::getModel('M2ePro/Connector_Connection_Response_Message');
            $message->initFromPreparedData(
                'Item was already Stopped on eBay',
                Ess_M2ePro_Model_Connector_Connection_Response_Message::TYPE_ERROR
            );

            $this->getLogger()->logListingProductMessage(
                $this->listingProduct, $message
            );

            return;
        }

        parent::processCompleted($data, $params);
    }

    public function eventAfterExecuting()
    {
        $responseMessages = $this->getResponse()->getMessages()->getEntities();

        if ($this->getStatusChanger() == Ess_M2ePro_Model_Listing_Product::STATUS_CHANGER_SYNCH &&
            (!$this->getConfigurator()->isDefaultMode()) &&
            $this->isNewRequiredSpecificNeeded($responseMessages)) {

            $message = Mage::getModel('M2ePro/Connector_Connection_Response_Message');
            $message->initFromPreparedData(Mage::helper('M2ePro')->__(
                'It has been detected that the Category you are using is going to require the Product Identifiers
                to be specified (UPC, EAN, ISBN, etc.). Full Revise will be automatically performed to send
                the value(s) of the required Identifier(s) based on the settings
                provided in the eBay Catalog Identifiers section of the Description Policy.'),
                Ess_M2ePro_Model_Connector_Connection_Response_Message::TYPE_WARNING
            );

            $this->getLogger()->logListingProductMessage($this->listingProduct, $message);

            $configurator = Mage::getModel('M2ePro/Ebay_Listing_Product_Action_Configurator');

            $this->processAdditionalAction($this->getActionType(), $configurator);
        }

        $additionalData = $this->listingProduct->getAdditionalData();

        if ($this->isVariationErrorAppeared($responseMessages) &&
            $this->getRequestDataObject()->hasVariations() &&
            !isset($additionalData['is_variation_mpn_filled'])
        ) {
            $this->tryToResolveVariationMpnErrors();
        }

        parent::eventAfterExecuting();
    }

    //########################################
}
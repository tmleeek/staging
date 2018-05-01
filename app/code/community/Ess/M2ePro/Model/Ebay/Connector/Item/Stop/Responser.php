<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Ebay_Connector_Item_Stop_Responser
    extends Ess_M2ePro_Model_Ebay_Connector_Item_Responser
{
    //########################################

    protected function getSuccessfulMessage()
    {
        return 'Item was successfully Stopped';
    }

    //########################################

    public function eventAfterExecuting()
    {
        parent::eventAfterExecuting();

        $responseData = $this->getPreparedResponseData();

        if (!empty($this->params['params']['remove']) &&
            (!empty($this->params['is_realtime']) || !empty($responseData['request_time']))
        ) {
            $this->listingProduct->setData('status', Ess_M2ePro_Model_Listing_Product::STATUS_STOPPED);
            $this->listingProduct->deleteInstance();
        }
    }

    protected function inspectProduct()
    {
        if (empty($this->params['params']['remove'])) {
            parent::inspectProduct();
            return;
        }

        $responseData = $this->getPreparedResponseData();
        if (!empty($this->params['is_realtime']) || !empty($responseData['request_time'])) {
            return;
        }

        $configurator = $this->getConfigurator();
        if (!empty($responseData['start_processing_date'])) {
            $configurator->setParams(array('start_processing_date' => $responseData['start_processing_date']));
        }

        $this->processAdditionalAction(
            Ess_M2ePro_Model_Listing_Product::ACTION_STOP,
            $configurator,
            $this->params['params']
        );
    }

    //########################################

    protected function processCompleted(array $data = array(), array $params = array())
    {
        if (!empty($data['already_stop'])) {

            $this->getResponseObject()->processSuccess($data, $params);

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

    //########################################
}
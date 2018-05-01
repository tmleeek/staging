<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Buy_Search_Custom
{
    /** @var Ess_M2ePro_Model_Listing_Product $listingProduct */
    private $listingProduct = null;

    private $query = null;

    //########################################

    /**
     * @param Ess_M2ePro_Model_Listing_Product $listingProduct
     * @return $this
     */
    public function setListingProduct(Ess_M2ePro_Model_Listing_Product $listingProduct)
    {
        $this->listingProduct = $listingProduct;
        return $this;
    }

    /**
     * @param string $query
     * @return $this
     */
    public function setQuery($query)
    {
        $this->query = $query;
        return $this;
    }

    //########################################

    public function process()
    {
        $dispatcherObject = Mage::getModel('M2ePro/Buy_Connector_Dispatcher');
        $connectorObj = $dispatcherObject->getCustomConnector('Buy_Search_Custom_ByQuery_Requester',
                                                        $this->getConnectorParams(),
                                                        $this->listingProduct->getAccount());

        $dispatcherObject->process($connectorObj);

        return $this->prepareResult($connectorObj->getPreparedResponseData());
    }

    //########################################

    private function getConnectorParams()
    {
        return array(
            'query' => $this->query,
        );
    }

    private function prepareResult($searchData)
    {
        return array(
            'type'  => 'string',
            'value' => $this->query,
            'data'  => $searchData,
        );
    }

    //########################################
}
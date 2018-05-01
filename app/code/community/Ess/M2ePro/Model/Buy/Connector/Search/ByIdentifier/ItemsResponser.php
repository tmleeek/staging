<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

abstract class Ess_M2ePro_Model_Buy_Connector_Search_ByIdentifier_ItemsResponser
    extends Ess_M2ePro_Model_Buy_Connector_Command_Pending_Responser
{
    // ########################################

    protected function validateResponse()
    {
        $responseData = $this->getResponse()->getData();
        if (!isset($responseData['items'])) {
            return false;
        }

        return true;
    }

    protected function prepareResponseData()
    {
        $responseData = $this->getResponse()->getData();

        $products = array();

        foreach ($responseData['items'] as $item) {

            if (isset($item['variations'])) {
                $product = array(
                    'title' => $item['title'],
                    'image_url' => $item['image_url'],
                    'variations' => $item['variations']
                );

                $products[] = $product;
                continue;
            }

            $product = array(
                'general_id' => $item['product_id'],
                'title' => $item['title'],
                'image_url' => $item['image_url']
            );

            if (!empty($item['price'])) {
                $product['price'] = $item['price'];
            }

            $products[] = $product;
        }

        $this->preparedResponseData = $products;
    }

    // ########################################
}
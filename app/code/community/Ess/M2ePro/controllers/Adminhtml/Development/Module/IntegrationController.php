<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Adminhtml_Development_Module_IntegrationController
    extends Ess_M2ePro_Controller_Adminhtml_Development_CommandController
{
    //########################################

    /**
     * @title "Revise Total"
     * @description "Full Force Revise"
     */
    public function reviseTotalAction()
    {
        $html = '';
        foreach (Mage::helper('M2ePro/Component')->getActiveComponents() as $component) {

            $reviseAllStartDate = Mage::helper('M2ePro/Module')->getSynchronizationConfig()->getGroupValue(
                "/{$component}/templates/revise/total/", 'start_date'
            );

            $reviseAllEndDate = Mage::helper('M2ePro/Module')->getSynchronizationConfig()->getGroupValue(
                "/{$component}/templates/revise/total/", 'end_date'
            );

            $reviseAllInProcessingState = !is_null(
                Mage::helper('M2ePro/Module')->getSynchronizationConfig()->getGroupValue(
                    "/{$component}/templates/revise/total/", 'last_listing_product_id'
                )
            );

            $urlHelper = Mage::helper('adminhtml');

            $runNowUrl = $urlHelper->getUrl('*/*/processReviseTotal', array('component' => $component));
            $resetUrl = $urlHelper->getUrl('*/*/resetReviseTotal', array('component' => $component));

            $html .= <<<HTML
<div>
    <span style="display:inline-block; width: 100px;">{$component}</span>
    <span style="display:inline-block; width: 150px;">
        <button onclick="window.location='{$runNowUrl}'">turn on</button>
        <button onclick="window.location='{$resetUrl}'">stop</button>
    </span>
    <span id="{$component}_start_date" style="color: indianred; display: none;">
        Started at - {$reviseAllStartDate}
    </span>
    <span id="{$component}_end_date" style="color: green; display: none;">
        Finished at - {$reviseAllEndDate}
    </span>
</div>

HTML;
            $html.= "<script type=\"text/javascript\">";
            if ($reviseAllInProcessingState) {
                $html .= "document.getElementById('{$component}_start_date').style.display = 'inline-block';";
            } else {

                if ($reviseAllEndDate) {
                    $html .= "document.getElementById('{$component}_end_date').style.display = 'inline-block';";
                }
            }
            $html.= "</script>";
        }

        echo $html;
    }

    /**
     * @title "Process Revise Total for Component"
     * @hidden
    */
    public function processReviseTotalAction()
    {
        $component = $this->getRequest()->getParam('component', false);

        if (!$component) {
            $this->_getSession()->addError('Component is not presented.');
            $this->_redirectUrl(Mage::helper('M2ePro/View_Development')->getPageModuleTabUrl());
        }

        Mage::helper('M2ePro/Module')->getSynchronizationConfig()->setGroupValue(
            "/{$component}/templates/revise/total/", 'start_date', Mage::helper('M2ePro')->getCurrentGmtDate()
        );

        Mage::helper('M2ePro/Module')->getSynchronizationConfig()->setGroupValue(
            "/{$component}/templates/revise/total/", 'end_date', null
        );

        Mage::helper('M2ePro/Module')->getSynchronizationConfig()->setGroupValue(
            "/{$component}/templates/revise/total/", 'last_listing_product_id', 0
        );

        $this->_redirect('*/*/reviseTotal');
    }

    /**
     * @title "Reset Revise Total for Component"
     * @hidden
     */
    public function resetReviseTotalAction()
    {
        $component = $this->getRequest()->getParam('component', false);

        if (!$component) {
            $this->_getSession()->addError('Component is not presented.');
            $this->_redirectUrl(Mage::helper('M2ePro/View_Development')->getPageModuleTabUrl());
        }

        Mage::helper('M2ePro/Module')->getSynchronizationConfig()->setGroupValue(
            "/{$component}/templates/revise/total/", 'last_listing_product_id', null
        );

        $this->_redirect('*/*/reviseTotal');
    }

    /**
     * @title "Print Request Data"
     * @description "Print [List/Relist/Revise] Request Data"
     */
    public function getRequestDataAction()
    {
        if ($this->getRequest()->getParam('print')) {

            $listingProductId = $this->getRequest()->getParam('listing_product_id');
            $componentMode    = $this->getRequest()->getParam('component_mode');
            $requestType      = $this->getRequest()->getParam('request_type');

            if ($componentMode == 'ebay') {

                /** @var Ess_M2ePro_Model_Listing_Product $lp */
                $lp = Mage::helper('M2ePro/Component_Ebay')->getObject('Listing_Product', $listingProductId);

                /** @var Ess_M2ePro_Model_Ebay_Listing_Product_Action_Configurator $configurator */
                $configurator = Mage::getModel('M2ePro/Ebay_Listing_Product_Action_Configurator');

                /** @var Ess_M2ePro_Model_Ebay_Listing_Product_Action_Type_Request $request */
                $request = Mage::getModel("M2ePro/Ebay_Listing_Product_Action_Type_{$requestType}_Request");
                $request->setParams(array());
                $request->setListingProduct($lp);
                $request->setConfigurator($configurator);

                echo '<pre>' . print_r($request->getData(), true);
                return;
            }

            if ($componentMode == 'amazon') {

                /** @var Ess_M2ePro_Model_Listing_Product $lp */
                $lp = Mage::helper('M2ePro/Component_Amazon')->getObject('Listing_Product', $listingProductId);

                /** @var Ess_M2ePro_Model_Amazon_Listing_Product_Action_Configurator $configurator */
                $configurator = Mage::getModel('M2ePro/Amazon_Listing_Product_Action_Configurator');

                /** @var Ess_M2ePro_Model_Amazon_Listing_Product_Action_Type_Request $request */
                $request = Mage::getModel("M2ePro/Amazon_Listing_Product_Action_Type_{$requestType}_Request");
                $request->setParams(array());
                $request->setListingProduct($lp);
                $request->setConfigurator($configurator);

                if ($requestType == 'List') {
                    $request->setValidatorsData(array(
                                                    'sku'        => 'placeholder',
                                                    'general_id' => 'placeholder',
                                                    'list_type'  => 'placeholder'
                                                ));
                }

                echo '<pre>' . print_r($request->getData(), true);
                return;
            }

            return;
        }

        $formKey = Mage::getSingleton('core/session')->getFormKey();
        $actionUrl = Mage::helper('adminhtml')->getUrl('*/*/*');

        echo <<<HTML
<form method="get" enctype="multipart/form-data" action="{$actionUrl}">

    <div style="margin: 5px 0; width: 400px;">
        <label style="width: 170px; display: inline-block;">Listing Product ID: </label>
        <input name="listing_product_id" style="width: 200px;" required>
    </div>

    <div style="margin: 5px 0; width: 400px;">
        <label style="width: 170px; display: inline-block;">Component: </label>
        <select name="component_mode" style="width: 200px;" required>
            <option style="display: none;"></option>
            <option value="ebay">eBay</option>
            <option value="amazon">Amazon</option>
        </select>
    </div>

    <div style="margin: 5px 0; width: 400px;">
        <label style="width: 170px; display: inline-block;">Request Type: </label>
        <select name="request_type" style="width: 200px;" required>
            <option style="display: none;"></option>
            <option value="List">List</option>
            <option value="Relist">Relist</option>
            <option value="Revise">Revise</option>
        </select>
    </div>

    <input name="form_key" value="{$formKey}" type="hidden" />
    <input name="print" value="1" type="hidden" />

    <div style="margin: 10px 0; width: 365px; text-align: right;">
        <button type="submit">Show</button>
    </div>

</form>
HTML;
    }

    /**
     * @title "Print Inspector Data"
     * @description "Print Inspector Data"
     * @new_line
     */
    public function getInspectorDataAction()
    {
        if ($this->getRequest()->getParam('print')) {

            /** @var Ess_M2ePro_Model_Listing_Product $lp */
            $listingProductId = $this->getRequest()->getParam('listing_product_id');

            if ($this->getRequest()->getParam('component_mode') == 'ebay') {

                /** @var Ess_M2ePro_Model_Ebay_Listing_Product $elp */
                $lp = Mage::helper('M2ePro/Component_Ebay')->getObject('Listing_Product', $listingProductId);
                $elp = $lp->getChildObject();

                $insp = Mage::getModel('M2ePro/Ebay_Synchronization_Templates_Synchronization_Inspector');

                echo '<pre>isMeetListRequirements: ' .$insp->isMeetListRequirements($lp).'<br>';
                echo '<pre>isMeetRelistRequirements: ' .$insp->isMeetRelistRequirements($lp).'<br>';
                echo '<pre>isMeetStopRequirements: ' .$insp->isMeetStopRequirements($lp).'<br>';
                echo '<pre>isMeetReviseGeneralRequirements: '.$insp->isMeetReviseGeneralRequirements($lp).'<br>';
                echo '<pre>isMeetRevisePriceRequirements: ' .$insp->isMeetRevisePriceRequirements($lp).'<br>';
                echo '<pre>isMeetReviseQtyRequirements: ' .$insp->isMeetReviseQtyRequirements($lp).'<br>';

                echo '<br>';
                echo '<pre>isSetCategoryTemplate: ' .$elp->isSetCategoryTemplate().'<br>';
                echo '<pre>isInAction: ' .$lp->isSetProcessingLock('in_action'). '<br>';

                echo '<pre>isStatusEnabled: ' .($lp->getMagentoProduct()->isStatusEnabled()).'<br>';
                echo '<pre>isStockAvailability: ' .($lp->getMagentoProduct()->isStockAvailability()).'<br>';

                echo '<pre>onlineQty: '.($elp->getOnlineQty() - $elp->getOnlineQtySold()).'<br>';

                $totalQty = 0;

                if (!$elp->isVariationsReady()) {
                    $totalQty = $elp->getQty();
                } else {
                    foreach ($lp->getVariations(true) as $variation) {
                        /** @var Ess_M2ePro_Model_Ebay_Listing_Product_Variation $ebayVariation */
                        $ebayVariation = $variation->getChildObject();
                        $totalQty += $ebayVariation->getQty();
                    }
                }

                echo '<pre>productQty: ' .$totalQty. '<br>';

                return;
            }

            if ($this->getRequest()->getParam('component_mode') == 'amazon') {

                /** @var Ess_M2ePro_Model_Amazon_Listing_Product $alp */
                $lp = Mage::helper('M2ePro/Component_Amazon')->getObject('Listing_Product', $listingProductId);

                $insp = Mage::getModel('M2ePro/Amazon_Synchronization_Templates_Synchronization_Inspector');

                echo '<pre>isMeetListRequirements: '.$insp->isMeetListRequirements($lp).'<br>';
                echo '<pre>isMeetRelistRequirements: '.$insp->isMeetRelistRequirements($lp).'<br>';
                echo '<pre>isMeetStopRequirements: '.$insp->isMeetStopRequirements($lp).'<br>';
                echo '<pre>isMeetReviseGeneralRequirements: '.$insp->isMeetReviseGeneralRequirements($lp).'<br>';
                echo '<pre>isMeetReviseRegularPriceRequirements: '
                    .$insp->isMeetReviseRegularPriceRequirements($lp).'<br>';
                echo '<pre>isMeetReviseBusinessPriceRequirements: '
                    .$insp->isMeetReviseBusinessPriceRequirements($lp).'<br>';
                echo '<pre>isMeetReviseQtyRequirements: '.$insp->isMeetReviseQtyRequirements($lp).'<br>';

                echo '<pre>isStatusEnabled: '.($lp->getMagentoProduct()->isStatusEnabled()).'<br>';
                echo '<pre>isStockAvailability: '.($lp->getMagentoProduct()->isStockAvailability()).'<br>';
            }

            return;
        }

        $formKey = Mage::getSingleton('core/session')->getFormKey();
        $actionUrl = Mage::helper('adminhtml')->getUrl('*/*/*');

        echo <<<HTML
<form method="get" enctype="multipart/form-data" action="{$actionUrl}">

    <div style="margin: 5px 0; width: 400px;">
        <label style="width: 170px; display: inline-block;">Listing Product ID: </label>
        <input name="listing_product_id" style="width: 200px;" required>
    </div>

    <div style="margin: 5px 0; width: 400px;">
        <label style="width: 170px; display: inline-block;">Component: </label>
        <select name="component_mode" style="width: 200px;" required>
            <option style="display: none;"></option>
            <option value="ebay">eBay</option>
            <option value="amazon">Amazon</option>
        </select>
    </div>

    <input name="form_key" value="{$formKey}" type="hidden" />
    <input name="print" value="1" type="hidden" />

    <div style="margin: 10px 0; width: 365px; text-align: right;">
        <button type="submit">Show</button>
    </div>

</form>
HTML;
    }

    //########################################

    /**
     * @title "Build Order Quote"
     * @description "Print Order Quote Data"
     * @new_line
     */
    public function getPrintOrderQuoteDataAction()
    {
        if ($this->getRequest()->getParam('print')) {

            /** @var Ess_M2ePro_Model_Order $order */
            $orderId = $this->getRequest()->getParam('order_id');
            $order =  Mage::helper('M2ePro/Component')->getUnknownObject('Order', $orderId);

            if (!$order->getId()) {

                $this->_getSession()->addError('Unable to load order instance.');
                $this->_redirectUrl(Mage::helper('M2ePro/View_Development')->getPageModuleTabUrl());
                return;
            }

            $proxy = $order->getProxy()->setStore($order->getStore());

            $magentoQuote = Mage::getModel('M2ePro/Magento_Quote', $proxy);
            $magentoQuote->buildQuote();
            $magentoQuote->getQuote()->setIsActive(false)->save();

            $shippingAddressData = $magentoQuote->getQuote()->getShippingAddress()->getData();
            unset(
                $shippingAddressData['cached_items_all'],
                $shippingAddressData['cached_items_nominal'],
                $shippingAddressData['cached_items_nonnominal']
            );
            $billingAddressData  = $magentoQuote->getQuote()->getBillingAddress()->getData();
            unset(
                $billingAddressData['cached_items_all'],
                $billingAddressData['cached_items_nominal'],
                $billingAddressData['cached_items_nonnominal']
            );

            $quote = $magentoQuote->getQuote();

            echo '<pre><b>Grand Total:</b> ' .$quote->getGrandTotal(). '<br>';
            echo '<pre><b>Shipping Amount:</b> ' .$quote->getShippingAddress()->getShippingAmount(). '<br>';

            echo '<pre><b>Quote Data:</b> ' .print_r($quote->getData(), true). '<br>';
            echo '<pre><b>Shipping Address Data:</b> ' .print_r($shippingAddressData, true). '<br>';
            echo '<pre><b>Billing Address Data:</b> ' .print_r($billingAddressData, true). '<br>';

            return;
        }

        $formKey = Mage::getSingleton('core/session')->getFormKey();
        $actionUrl = Mage::helper('adminhtml')->getUrl('*/*/*');

        echo <<<HTML
<form method="get" enctype="multipart/form-data" action="{$actionUrl}">

    <div style="margin: 5px 0; width: 400px;">
        <label style="width: 170px; display: inline-block;">Order ID: </label>
        <input name="order_id" style="width: 200px;" required>
    </div>

    <input name="form_key" value="{$formKey}" type="hidden" />
    <input name="print" value="1" type="hidden" />

    <div style="margin: 10px 0; width: 365px; text-align: right;">
        <button type="submit">Build</button>
    </div>

</form>
HTML;
    }

    //########################################

    /**
     * @title "Reset eBay 3rd Party"
     * @description "Clear all eBay 3rd party items for all Accounts"
     */
    public function resetOtherListingsAction()
    {
        $listingOther = Mage::getModel('M2ePro/Listing_Other');
        $ebayListingOther = Mage::getModel('M2ePro/Ebay_Listing_Other');

        $stmt = Mage::helper('M2ePro/Component_Ebay')->getCollection('Listing_Other')->getSelect()->query();

        foreach ($stmt as $row) {
            $listingOther->setData($row);
            $ebayListingOther->setData($row);

            $listingOther->setChildObject($ebayListingOther);
            $ebayListingOther->setParentObject($listingOther);

            $listingOther->deleteInstance();
        }

        foreach (Mage::helper('M2ePro/Component_Ebay')->getCollection('Account') as $account) {
            $account->setData('other_listings_last_synchronization',NULL)->save();
        }

        $this->_getSession()->addSuccess('Successfully removed.');
        $this->_redirectUrl(Mage::helper('M2ePro/View_Development')->getPageModuleTabUrl());
    }

    /**
     * @title "Stop eBay 3rd Party"
     * @description "[in order to resolve the problem with duplicates]"
     * @new_line
     */
    public function stopEbay3rdPartyAction()
    {
        $collection = Mage::helper('M2ePro/Component_Ebay')->getCollection('Listing_Other');
        $collection->addFieldToFilter('status', array('in' => array(
            Ess_M2ePro_Model_Listing_Product::STATUS_LISTED,
            Ess_M2ePro_Model_Listing_Product::STATUS_HIDDEN
        )));

        $total       = 0;
        $groupedData = array();

        foreach ($collection->getItems() as $item) {
            /** @var Ess_M2ePro_Model_Ebay_Listing_Other $item */

            $key = $item->getAccount()->getId() .'##'. $item->getMarketplace()->getId();
            $groupedData[$key][$item->getId()] = $item->getItemId();
            $total++;
        }

        foreach ($groupedData as $groupKey => $items) {

            list($accountId, $marketplaceId) = explode('##', $groupKey);

            foreach (array_chunk($items, 10, true) as $itemsPart) {

                /** @var $dispatcherObject Ess_M2ePro_Model_Ebay_Connector_Dispatcher */
                $dispatcherObject = Mage::getModel('M2ePro/Ebay_Connector_Dispatcher');
                $connectorObj = $dispatcherObject->getVirtualConnector('item','update','ends',
                    array('items' => $itemsPart), null, $marketplaceId, $accountId
                );

                $dispatcherObject->process($connectorObj);
                $response = $connectorObj->getResponseData();

                foreach ($response['result'] as $itemId => $iResp) {

                    $item = Mage::helper('M2ePro/Component_Ebay')->getObject('Listing_Other', $itemId);
                    if ($item->getId() &&
                        ((isset($iResp['already_stop']) && $iResp['already_stop']) ||
                          isset($iResp['ebay_end_date_raw'])))
                    {
                        $item->setData('status', Ess_M2ePro_Model_Listing_Product::STATUS_STOPPED)->save();
                    }
                }
            }
        }

        echo "Processed {$total} products.";
    }

    /**
     * @title "Reset eBay Images Hashes"
     * @description "Clear eBay images hashes for listing products"
     * @prompt "Please enter Listing Product ID or `all` code for reset all products."
     * @prompt_var "listing_product_id"
     */
    public function resetEbayImagesHashesAction()
    {
        $listingProductId = $this->getRequest()->getParam('listing_product_id');

        $listingProducts = array();
        if (strtolower($listingProductId) == 'all') {

            $listingProducts = Mage::getModel('M2ePro/Listing_Product')->getCollection()
                ->addFieldToFilter('component_mode', 'ebay');
        } else {

            $listingProduct = Mage::getModel('M2ePro/Listing_Product')->load((int)$listingProductId);
            $listingProduct && $listingProducts[] = $listingProduct;
        }

        if (empty($listingProducts)) {
            $this->_getSession()->addError('Failed to load Listing Product.');
            return $this->_redirectUrl(Mage::helper('M2ePro/View_Development')->getPageModuleTabUrl());
        }

        $affected = 0;
        foreach ($listingProducts as $listingProduct) {

            $additionalData = $listingProduct->getAdditionalData();

            if (!isset($additionalData['ebay_product_images_hash']) &&
                !isset($additionalData['ebay_product_variation_images_hash'])) {
                continue;
            }

            unset($additionalData['ebay_product_images_hash'],
                  $additionalData['ebay_product_variation_images_hash']);

            $affected++;
            $listingProduct->setData('additional_data', Mage::helper('M2ePro')->jsonEncode($additionalData))
                           ->save();
        }

        $this->_getSession()->addSuccess("Successfully removed for {$affected} affected Products.");
        return $this->_redirectUrl(Mage::helper('M2ePro/View_Development')->getPageModuleTabUrl());
    }

    /**
     * @title "Set eBay EPS Images Mode"
     * @description "Set EPS Images Mode = true for listing products"
     * @prompt "Please enter Listing Product ID or `all` code for all products."
     * @prompt_var "listing_product_id"
     */
    public function setEpsImagesModeAction()
    {
        $listingProductId = $this->getRequest()->getParam('listing_product_id');

        $listingProducts = array();
        if (strtolower($listingProductId) == 'all') {

            $listingProducts = Mage::getModel('M2ePro/Listing_Product')->getCollection()
                ->addFieldToFilter('component_mode', 'ebay');
        } else {

            $listingProduct = Mage::getModel('M2ePro/Listing_Product')->load((int)$listingProductId);
            $listingProduct && $listingProducts[] = $listingProduct;
        }

        if (empty($listingProducts)) {
            $this->_getSession()->addError('Failed to load Listing Product.');
            return $this->_redirectUrl(Mage::helper('M2ePro/View_Development')->getPageModuleTabUrl());
        }

        $affected = 0;
        foreach ($listingProducts as $listingProduct) {

            $additionalData = $listingProduct->getAdditionalData();

            if (!isset($additionalData['is_eps_ebay_images_mode']) ||
                $additionalData['is_eps_ebay_images_mode'] == true) {
                continue;
            }

            $additionalData['is_eps_ebay_images_mode'] = true;
            $affected++;

            $listingProduct->setData('additional_data', Mage::helper('M2ePro')->jsonEncode($additionalData))
                           ->save();
        }

        $this->_getSession()->addSuccess("Successfully set for {$affected} affected Products.");
        return $this->_redirectUrl(Mage::helper('M2ePro/View_Development')->getPageModuleTabUrl());
    }

    //########################################

    /**
     * @title "Show eBay Nonexistent Templates"
     * @description "Show Nonexistent Templates [eBay]"
     * @new_line
     */
    public function showNonexistentTemplatesAction()
    {
        if ($this->getRequest()->getParam('fix')) {

            $action       = $this->getRequest()->getParam('action');

            $template     = $this->getRequest()->getParam('template_nick');
            $currentMode  = $this->getRequest()->getParam('current_mode');
            $currentValue = $this->getRequest()->getParam('value');

            if ($action == 'set_null') {

                $field = $currentMode == 'template' ? "template_{$template}_id"
                                                    : "template_{$template}_custom_id";

                $collection = Mage::helper('M2ePro/Component_Ebay')->getCollection('Listing_Product');
                $collection->addFieldToFilter($field, $currentValue);

                foreach ($collection->getItems() as $listingProduct) {
                    $listingProduct->setData($field, null)->save();
                }
            }

            if ($action == 'set_parent') {

                $field = $currentMode == 'template' ? "template_{$template}_id"
                                                    : "template_{$template}_custom_id";

                $collection = Mage::helper('M2ePro/Component_Ebay')->getCollection('Listing_Product');
                $collection->addFieldToFilter($field, $currentValue);

                $data = array(
                    "template_{$template}_mode" => Ess_M2ePro_Model_Ebay_Template_Manager::MODE_PARENT,
                    $field                      => null
                );

                foreach ($collection->getItems() as $listingProduct) {
                    $listingProduct->addData($data)->save();
                }
            }

            if ($action == 'set_template' && $this->getRequest()->getParam('template_id')) {

                $field = $currentMode == 'template' ? "template_{$template}_id"
                                                    : "template_{$template}_custom_id";

                $collection = Mage::helper('M2ePro/Component_Ebay')->getCollection('Listing');
                $collection->addFieldToFilter($field, $currentValue);

                $data = array(
                    "template_{$template}_mode" => Ess_M2ePro_Model_Ebay_Template_Manager::MODE_TEMPLATE,
                    $field                      => null,
                );
                $data["template_{$template}_id"] = (int)$this->getRequest()->getParam('template_id');

                foreach ($collection->getItems() as $listing) {
                    $listing->addData($data)->save();
                }
            }

            $this->_redirectUrl(Mage::helper('adminhtml')->getUrl('*/*/*'));
        }

        $nonexistentTemplates = array();

        $simpleTemplates = array('category', 'other_category');
        foreach ($simpleTemplates as $templateName) {

            $tempResult = $this->getNonexistentTemplatesBySimpleLogic($templateName);
            !empty($tempResult) && $nonexistentTemplates[$templateName] = $tempResult;
        }

        $difficultTemplates = array(
            Ess_M2ePro_Model_Ebay_Template_Manager::TEMPLATE_SELLING_FORMAT,
            Ess_M2ePro_Model_Ebay_Template_Manager::TEMPLATE_SYNCHRONIZATION,
            Ess_M2ePro_Model_Ebay_Template_Manager::TEMPLATE_DESCRIPTION,
            Ess_M2ePro_Model_Ebay_Template_Manager::TEMPLATE_SHIPPING,
            Ess_M2ePro_Model_Ebay_Template_Manager::TEMPLATE_PAYMENT,
            Ess_M2ePro_Model_Ebay_Template_Manager::TEMPLATE_RETURN,
        );
        foreach ($difficultTemplates as $templateName) {

            $tempResult = $this->getNonexistentTemplatesByDifficultLogic($templateName);
            !empty($tempResult) && $nonexistentTemplates[$templateName] = $tempResult;
        }

        if (count($nonexistentTemplates) <= 0) {
            echo $this->getEmptyResultsHtml('There are no any nonexistent templates.');
            return;
        }

        $tableContent = <<<HTML
<tr>
    <th>Listing ID</th>
    <th>Listing Product ID</th>
    <th>Policy ID</th>
    <th>My Mode</th>
    <th>Parent Mode</th>
    <th>Actions</th>
</tr>
HTML;

        $alreadyRendered = array();
        foreach ($nonexistentTemplates as $templateName => $items) {

            $tableContent .= <<<HTML
<tr>
    <td colspan="6" align="center"><b>{$templateName}</b></td>
</tr>
HTML;

            foreach ($items as $index => $itemInfo) {

                $myModeWord = '';
                $parentModeWord = '';
                $actionsHtml = '';

                if (!isset($itemInfo['my_mode']) && !isset($itemInfo['parent_mode'])) {

                    $url = Mage::helper('adminhtml')->getUrl('*/*/*', array(
                        'fix'           => '1',
                        'template_nick' => $templateName,
                        'current_mode'  => 'template',
                        'action'        => 'set_null',
                        'value'         => $itemInfo['my_needed_id'],
                    ));

                    $actionsHtml .= <<<HTML
<a href="{$url}">set null</a><br>
HTML;
                }

                if (isset($itemInfo['my_mode']) && $itemInfo['my_mode'] == 0) {
                    $myModeWord = 'parent';
                }

                if (isset($itemInfo['my_mode']) && $itemInfo['my_mode'] == 1) {

                    $myModeWord = 'custom';
                    $url = Mage::helper('adminhtml')->getUrl('*/*/*', array(
                        'fix'           => '1',
                        'template_nick' => $templateName,
                        'current_mode'  => $myModeWord,
                        'action'        => 'set_parent',
                        'value'         => $itemInfo['my_needed_id'],
                    ));

                    $actionsHtml .= <<<HTML
<a href="{$url}">set parent</a><br>
HTML;
                }

                if (isset($itemInfo['my_mode']) && $itemInfo['my_mode'] == 2) {

                    $myModeWord = 'template';
                    $url = Mage::helper('adminhtml')->getUrl('*/*/*', array(
                        'fix'           => '1',
                        'template_nick' => $templateName,
                        'current_mode'  => $myModeWord,
                        'action'        => 'set_parent',
                        'value'         => $itemInfo['my_needed_id'],
                    ));

                    $actionsHtml .= <<<HTML
<a href="{$url}">set parent</a><br>
HTML;
                }

                if (isset($itemInfo['parent_mode']) && $itemInfo['parent_mode'] == 1) {

                    $parentModeWord = 'custom';
                    $url = Mage::helper('adminhtml')->getUrl('*/*/*', array(
                        'fix'           => '1',
                        'action'        => 'set_template',
                        'template_nick' => $templateName,
                        'current_mode'  => $parentModeWord,
                        'value'         => $itemInfo['my_needed_id'],
                    ));
                    $onClick = <<<JS
var result = prompt('Enter Template ID');
if (result) {
    window.location.href = '{$url}' + '?template_id=' + result;
}
return false;
JS;
                    $actionsHtml .= <<<HTML
<a href="javascript:void();" onclick="{$onClick}">set template</a><br>
HTML;
                }

                if (isset($itemInfo['parent_mode']) && $itemInfo['parent_mode'] == 2) {

                    $parentModeWord = 'template';
                    $url = Mage::helper('adminhtml')->getUrl('*/*/*', array(
                        'fix'           => '1',
                        'action'        => 'set_template',
                        'template_nick' => $templateName,
                        'current_mode'  => $parentModeWord,
                        'value'         => $itemInfo['my_needed_id'],
                    ));
                    $onClick = <<<JS
var result = prompt('Enter Template ID');
if (result) {
    window.location.href = '{$url}' + '?template_id=' + result;
}
return false;
JS;
                    $actionsHtml .= <<<HTML
<a href="javascript:void();" onclick="{$onClick}">set template</a><br>
HTML;
                }

                $key = $templateName .'##'. $myModeWord .'##'. $itemInfo['listing_id'];
                if ($myModeWord == 'parent' && in_array($key, $alreadyRendered)) {
                    continue;
                }

                $alreadyRendered[] = $key;
                $tableContent .= <<<HTML
<tr>
    <td>{$itemInfo['listing_id']}</td>
    <td>{$itemInfo['my_id']}</td>
    <td>{$itemInfo['my_needed_id']}</td>
    <td>{$myModeWord}</td>
    <td>{$parentModeWord}</td>
    <td>
        {$actionsHtml}
    </td>
</tr>
HTML;
            }
        }

        $html = $this->getStyleHtml() . <<<HTML
<html>
    <body>
        <h2 style="margin: 20px 0 0 10px">Nonexistent templates
            <span style="color: #808080; font-size: 15px;">(#count# entries)</span>
        </h2>
        <br/>
        <table class="grid" cellpadding="0" cellspacing="0">
            {$tableContent}
        </table>
    </body>
</html>
HTML;

        echo str_replace('#count#', count($alreadyRendered), $html);
    }

    private function getNonexistentTemplatesByDifficultLogic($templateCode)
    {
        /** @var $resource Mage_Core_Model_Resource */
        $resource = Mage::getSingleton('core/resource');
        $connRead = $resource->getConnection('core_write');

        $subSelect = $connRead->select()
            ->from(
                array('melp' => $resource->getTableName('m2epro_ebay_listing_product')),
                array(
                    'my_id'          => 'listing_product_id',
                    'my_mode'        => "template_{$templateCode}_mode",
                    'my_template_id' => "template_{$templateCode}_id",
                    'my_custom_id'   => "template_{$templateCode}_custom_id",

                    'my_needed_id'   => new Zend_Db_Expr(
                    "CASE
                        WHEN melp.template_{$templateCode}_mode = 2 THEN melp.template_{$templateCode}_id
                        WHEN melp.template_{$templateCode}_mode = 1 THEN melp.template_{$templateCode}_custom_id
                        WHEN melp.template_{$templateCode}_mode = 0 THEN IF(mel.template_{$templateCode}_mode = 1,
                                                                            mel.template_{$templateCode}_custom_id,
                                                                            mel.template_{$templateCode}_id)
                    END"
                    ))
            )
            ->joinLeft(
                array('mlp' => $resource->getTableName('m2epro_listing_product')),
                'melp.listing_product_id = mlp.id',
                array('listing_id' => 'listing_id')
            )
            ->joinLeft(
                array('mel' => $resource->getTableName('m2epro_ebay_listing')),
                'mlp.listing_id = mel.listing_id',
                array(
                    'parent_mode'        => "template_{$templateCode}_mode",
                    'parent_template_id' => "template_{$templateCode}_id",
                    'parent_custom_id'   => "template_{$templateCode}_custom_id"
                )
            );

        $templateIdName = 'id';
        $horizontalTemplates = array(
            Ess_M2ePro_Model_Ebay_Template_Manager::TEMPLATE_SELLING_FORMAT,
            Ess_M2ePro_Model_Ebay_Template_Manager::TEMPLATE_SYNCHRONIZATION,
            Ess_M2ePro_Model_Ebay_Template_Manager::TEMPLATE_DESCRIPTION,
        );
        in_array($templateCode, $horizontalTemplates) && $templateIdName = "template_{$templateCode}_id";

        $result = $connRead->select()
           ->from(
               array('subselect' => new Zend_Db_Expr('('.$subSelect->__toString().')')),
               array(
                   'subselect.my_id',
                   'subselect.listing_id',
                   'subselect.my_mode',
                   'subselect.parent_mode',
                   'subselect.my_needed_id',
               )
           )
           ->joinLeft(
               array('template' => $resource->getTableName("m2epro_ebay_template_{$templateCode}")),
               "subselect.my_needed_id = template.{$templateIdName}",
               array()
           )
           ->where("template.{$templateIdName} IS NULL")
           ->query()->fetchAll();

        return $result;
    }

    private function getNonexistentTemplatesBySimpleLogic($templateCode)
    {
        /** @var $resource Mage_Core_Model_Resource */
        $resource = Mage::getSingleton('core/resource');
        $connRead = $resource->getConnection('core_read');

        $select = $connRead->select()
            ->from(
                array('melp' => $resource->getTableName('m2epro_ebay_listing_product')),
                array(
                    'my_id'        => 'listing_product_id',
                    'my_needed_id' => "template_{$templateCode}_id",
                )
            )
            ->joinLeft(
                array('mlp' => $resource->getTableName('m2epro_listing_product')),
                'melp.listing_product_id = mlp.id',
                array('listing_id' => 'listing_id')
            )
            ->joinLeft(
                array('template' => $resource->getTableName("m2epro_ebay_template_{$templateCode}")),
                "melp.template_{$templateCode}_id = template.id",
                array()
            )
            ->where("melp.template_{$templateCode}_id IS NOT NULL")
            ->where("template.id IS NULL");

        return $select->query()->fetchAll();
    }

    //########################################

    /**
     * @title "Show eBay Duplicates [parse logs]"
     * @description "Show eBay Duplicates According with Logs"
     */
    public function showEbayDuplicatesByLogsAction()
    {
        /** @var $resource Mage_Core_Model_Resource */
        $resource = Mage::getSingleton('core/resource');
        $queryObj = $resource->getConnection('core_read')
                             ->select()
                             ->from(array('mll' => $resource->getTableName('m2epro_listing_log')))
                             ->joinLeft(
                                 array('ml' => $resource->getTableName('m2epro_listing')),
                                 'mll.listing_id = ml.id',
                                 array('marketplace_id')
                             )
                            ->joinLeft(
                                array('mm' => $resource->getTableName('m2epro_marketplace')),
                                'ml.marketplace_id = mm.id',
                                array('marketplace_title' => 'title')
                            )
                             ->where("mll.description LIKE '%a duplicate of your item%' OR " . // ENG
                                     "mll.description LIKE '%ette annonce est identique%' OR " . // FR
                                     "mll.description LIKE '%ngebot ist identisch mit dem%' OR " .  // DE
                                     "mll.description LIKE '%un duplicato del tuo oggetto%' OR " . // IT
                                     "mll.description LIKE '%es un duplicado de tu art%'" // ESP
                             )
                             ->where("mll.component_mode = ?", 'ebay')
                             ->order('mll.id DESC')
                             ->group(array('mll.product_id', 'mll.listing_id'))
                             ->query();

        $duplicatesInfo = array();
        while ($row = $queryObj->fetch()) {

            preg_match('/.*\((\d*)\)/', $row['description'], $matches);
            $ebayItemId = !empty($matches[1]) ? $matches[1] : '';

            $duplicatesInfo[] = array(
                'date'               => $row['create_date'],
                'listing_id'         => $row['listing_id'],
                'listing_title'      => $row['listing_title'],
                'product_id'         => $row['product_id'],
                'product_title'      => $row['product_title'],
                'listing_product_id' => $row['listing_product_id'],
                'description'        => $row['description'],
                'ebay_item_id'       => $ebayItemId,
                'marketplace_title'  => $row['marketplace_title']
            );
        }

        if (count($duplicatesInfo) <= 0) {
            echo $this->getEmptyResultsHtml('According to you logs there are no duplicates.');
            return;
        }

        $tableContent = <<<HTML
<tr>
    <th>Listing ID</th>
    <th>Listing Title</th>
    <th>Product ID</th>
    <th>Product Title</th>
    <th>Listing Product ID</th>
    <th>eBay Item ID</th>
    <th>eBay Site</th>
    <th>Date</th>
</tr>
HTML;
        foreach ($duplicatesInfo as $row) {
            $tableContent .= <<<HTML
<tr>
    <td>{$row['listing_id']}</td>
    <td>{$row['listing_title']}</td>
    <td>{$row['product_id']}</td>
    <td>{$row['product_title']}</td>
    <td>{$row['listing_product_id']}</td>
    <td>{$row['ebay_item_id']}</td>
    <td>{$row['marketplace_title']}</td>
    <td>{$row['date']}</td>
</tr>
HTML;
        }

        $html = $this->getStyleHtml() . <<<HTML
<html>
    <body>
        <h2 style="margin: 20px 0 0 10px">eBay Duplicates
            <span style="color: #808080; font-size: 15px;">(#count# entries)</span>
        </h2>
        <br/>
        <table class="grid" cellpadding="0" cellspacing="0">
            {$tableContent}
        </table>
    </body>
</html>
HTML;
        echo str_replace('#count#', count($duplicatesInfo), $html);
    }

    /**
     * @title "Show eBay Duplicates"
     * @description "[can be stopped and removed as option, by using remove=1 query param]"
     */
    public function showEbayDuplicatesAction()
    {
        $removeMode = (bool)$this->getRequest()->getParam('remove', false);

        /* @var $writeConnection Varien_Db_Adapter_Pdo_Mysql */
        $writeConnection = Mage::getSingleton('core/resource')->getConnection('core_write');

        $listingProduct = Mage::getSingleton('core/resource')->getTableName('m2epro_listing_product');
        $ebayListingProduct = Mage::getSingleton('core/resource')->getTableName('m2epro_ebay_listing_product');

        $subQuery = $writeConnection
            ->select()
            ->from(array('melp' => $ebayListingProduct),
                   array())
            ->joinInner(array('mlp' => $listingProduct),
                        'mlp.id = melp.listing_product_id',
                        array('listing_id',
                              'product_id',
                              new Zend_Db_Expr('COUNT(product_id) - 1 AS count_of_duplicates'),
                              new Zend_Db_Expr('MIN(mlp.id) AS save_this_id'),
                        ))
            ->group(array('mlp.product_id', 'mlp.listing_id'))
            ->having(new Zend_Db_Expr('count_of_duplicates > 0'));

        $query = $writeConnection
            ->select()
            ->from(array('melp' => $ebayListingProduct),
                   array('listing_product_id'))
            ->joinInner(array('mlp' => $listingProduct),
                        'mlp.id = melp.listing_product_id',
                        array('status'))
            ->joinInner(array('templ_table' => $subQuery),
                        'mlp.product_id = templ_table.product_id AND
                         mlp.listing_id = templ_table.listing_id')
            ->where('melp.listing_product_id <> templ_table.save_this_id')
            ->query();

        $removed = 0;
        $stopped = 0;
        $duplicated = array();

        while ($row = $query->fetch()) {

            if ($removeMode) {

                $writeConnection->delete($listingProduct,
                                         array('id = ?' => $row['listing_product_id']));

                $writeConnection->delete($ebayListingProduct,
                                         array('listing_product_id = ?' => $row['listing_product_id']));

                if ($row['status'] == Ess_M2ePro_Model_Listing_Product::STATUS_LISTED) {

                    $dispatcherObject = Mage::getModel('M2ePro/Ebay_Connector_Item_Dispatcher');
                    $dispatcherObject->process(Ess_M2ePro_Model_Listing_Product::ACTION_STOP,
                                               array($row['listing_product_id']));

                    $stopped++;
                }

                $removed++;
                continue;
            }

            $duplicated[$row['save_this_id']] = $row;
        }

        if (count($duplicated) <= 0) {

            $message = 'There are no duplicates.';
            $removed > 0 && $message .= ' Removed: ' . $removed;
            $stopped > 0 && $message .= ' Stopped: ' . $stopped;

            echo $this->getEmptyResultsHtml($message);
            return;
        }

        $tableContent = <<<HTML
<tr>
    <th>Listing ID</th>
    <th>Magento Product ID</th>
    <th>Count Of Copies</th>
</tr>
HTML;
        foreach ($duplicated as $row) {
            $tableContent .= <<<HTML
<tr>
    <td>{$row['listing_id']}</td>
    <td>{$row['product_id']}</td>
    <td>{$row['count_of_duplicates']}</td>
</tr>
HTML;
        }

        $url = Mage::helper('adminhtml')->getUrl('*/*/*', array('remove' => '1'));
        $html = $this->getStyleHtml() . <<<HTML
<html>
    <body>
        <h2 style="margin: 20px 0 0 10px">eBay Duplicates
            <span style="color: #808080; font-size: 15px;">(#count# entries)</span>
        </h2>
        <br/>
        <table class="grid" cellpadding="0" cellspacing="0">
            {$tableContent}
        </table>
        <form action="{$url}" method="get" style="margin-top: 1em;">
            <button type="submit">Remove</button>
        </form>
    </body>
</html>
HTML;
        echo str_replace('#count#', count($duplicated), $html);
    }

    /**
     * @title "Show Amazon Duplicates"
     * @description "[can be removed as option, by using remove=1 query param]"
     * @new_line
     */
    public function showAmazonDuplicatesAction()
    {
        $removeMode = (bool)$this->getRequest()->getParam('remove', false);

        /* @var $writeConnection Varien_Db_Adapter_Pdo_Mysql */
        $writeConnection = Mage::getSingleton('core/resource')->getConnection('core_write');

        $listingProduct = Mage::getSingleton('core/resource')->getTableName('m2epro_listing_product');
        $amazonListingProduct = Mage::getSingleton('core/resource')->getTableName('m2epro_amazon_listing_product');

        $subQuery = $writeConnection
            ->select()
            ->from(array('malp' => $amazonListingProduct),
                   array('general_id'))
            ->joinInner(array('mlp' => $listingProduct),
                        'mlp.id = malp.listing_product_id',
                        array('listing_id',
                              'product_id',
                              new Zend_Db_Expr('COUNT(product_id) - 1 AS count_of_duplicates'),
                              new Zend_Db_Expr('MIN(mlp.id) AS save_this_id'),
                        ))
            ->group(array('mlp.product_id', 'malp.general_id', 'mlp.listing_id'))
            ->having(new Zend_Db_Expr('count_of_duplicates > 0'));

        $query = $writeConnection
            ->select()
            ->from(array('malp' => $amazonListingProduct),
                   array('listing_product_id'))
            ->joinInner(array('mlp' => $listingProduct),
                        'mlp.id = malp.listing_product_id',
                        array('status'))
            ->joinInner(array('templ_table' => $subQuery),
                        'mlp.product_id = templ_table.product_id AND
                         malp.general_id = templ_table.general_id AND
                         mlp.listing_id = templ_table.listing_id')
            ->where('malp.listing_product_id <> templ_table.save_this_id')
            ->query();

        $removed = 0;
        $duplicated = array();

        while ($row = $query->fetch()) {

            if ($removeMode) {

                $writeConnection->delete($listingProduct,
                                         array('id = ?' => $row['listing_product_id']));

                $writeConnection->delete($amazonListingProduct,
                                         array('listing_product_id = ?' => $row['listing_product_id']));
                $removed++;
                continue;
            }

            $duplicated[$row['save_this_id']] = $row;
        }

        if (count($duplicated) <= 0) {

            $message = 'There are no duplicates.';
            $removed > 0 && $message .= ' Removed: ' . $removed;

            echo $this->getEmptyResultsHtml($message);
            return;
        }

        $tableContent = <<<HTML
<tr>
    <th>Listing ID</th>
    <th>Magento Product ID</th>
    <th>Count Of Copies</th>
</tr>
HTML;
        foreach ($duplicated as $row) {
            $tableContent .= <<<HTML
<tr>
    <td>{$row['listing_id']}</td>
    <td>{$row['product_id']}</td>
    <td>{$row['count_of_duplicates']}</td>
</tr>
HTML;
        }

        $url = Mage::helper('adminhtml')->getUrl('*/*/*', array('remove' => '1'));
        $html = $this->getStyleHtml() . <<<HTML
<html>
    <body>
        <h2 style="margin: 20px 0 0 10px">Amazon Duplicates
            <span style="color: #808080; font-size: 15px;">(#count# entries)</span>
        </h2>
        <br/>
        <table class="grid" cellpadding="0" cellspacing="0">
            {$tableContent}
        </table>
        <form action="{$url}" method="get" style="margin-top: 1em;">
            <button type="submit">Remove</button>
        </form>
    </body>
</html>
HTML;
        echo str_replace('#count#', count($duplicated), $html);
    }

    //########################################

    /**
     * @title "Fix many same categories templates [eBay]"
     * @description "[remove the same templates and set original templates to the settings of listings products]"
     * @new_line
     */
    public function fixManySameCategoriesTemplatesOnEbayAction()
    {
        $affectedListingProducts = $removedTemplates = 0;
        $statistics = array();
        $snapshots = array();

        foreach (Mage::getModel('M2ePro/Ebay_Template_Category')->getCollection() as $template) {
            /**@var Ess_M2ePro_Model_Ebay_Template_Category $template */

            $shot = $template->getDataSnapshot();
            unset($shot['id'], $shot['create_date'], $shot['update_date']);
            foreach ($shot['specifics'] as &$specific) {
                unset($specific['id'], $specific['template_category_id']);
            }
            $key = md5(Mage::helper('M2ePro')->jsonEncode($shot));

            if (!array_key_exists($key, $snapshots)) {

                $snapshots[$key] = $template;
                continue;
            }

            foreach ($template->getAffectedListingsProducts(false) as $listingsProduct) {
                /**@var Ess_M2ePro_Model_Listing_Product $listingsProduct */

                $originalTemplate = $snapshots[$key];
                $listingsProduct->setData('template_category_id', $originalTemplate->getId())
                                ->save();

                $affectedListingProducts++;
            }

            $template->deleteInstance();
            $statistics['templates'][] = $template->getId();

            $removedTemplates++;
        }

        echo <<<HTML
Templates were removed: {$removedTemplates}.<br>
Listings Product Affected: {$affectedListingProducts}.<br>
HTML;
    }

    //########################################

    /**
     * @title "Add Products into Listing"
     * @description "Mass Action by SKU or Magento Product ID"
     */
    public function addProductsToListingAction()
    {
        $actionUrl = Mage::helper('adminhtml')->getUrl('*/*/processAddProductsToListing');
        $formKey = Mage::getSingleton('core/session')->getFormKey();

        $collection = Mage::getModel('M2ePro/Listing')->getCollection()
                                                      ->addOrder('component_mode');
        $currentOptGroup = null;
        $listingsOptionsHtml = '';

        /** @var Ess_M2ePro_Model_Listing $listing */
        foreach ($collection as $listing) {

            $currentOptGroup != $listing->getComponentMode() && !is_null($currentOptGroup)
                && $listingsOptionsHtml .= '</optgroup>';

            $currentOptGroup != $listing->getComponentMode()
                && $listingsOptionsHtml .= '<optgroup label="'.$listing->getComponentMode().'">';

            $tempValue = "[{$listing->getId()}]  {$listing->getTitle()}]";
            $listingsOptionsHtml .= '<option value="'.$listing->getId().'">'.$tempValue.'</option>';

            $currentOptGroup = $listing->getComponentMode();
        }

        echo <<<HTML
<form method="post" enctype="multipart/form-data" action="{$actionUrl}">

    <input name="form_key" value="{$formKey}" type="hidden" />

    <label style="display: inline-block; width: 150px;">Source:&nbsp;</label>
    <input type="file" accept=".csv" name="source" required /><br/>

    <label style="display: inline-block; width: 150px;">Identifier Type:&nbsp;</label>
    <select style="width: 250px;" name="source_type" required>
        <option value="sku">SKU</option>
        <option value="id">Product ID</option>
    </select><br/>

    <label style="display: inline-block; width: 150px;">Target Listing:&nbsp;</label>
    <select style="width: 250px;" name="listing_id" required>
        <option style="display: none;"></option>
        {$listingsOptionsHtml}
    </select><br/>

    <input type="submit" title="Run Now" onclick="return confirm('Are you sure?');" />
</form>
HTML;
    }

    /**
     * @title "Process Adding Products into Listing"
     * @hidden
     */
    public function processAddProductsToListingAction()
    {
        $sourceType = $this->getRequest()->getPost('source_type', 'sku');
        $listing = Mage::getModel('M2ePro/Listing')->load($this->getRequest()->getPost('listing_id'));

        if (empty($_FILES['source']['tmp_name']) || !$listing) {
            $this->_getSession()->addError('Some required fields are empty.');
            $this->_redirectUrl(Mage::helper('adminhtml')->getUrl('*/*/processAddProductsToListing'));
        }

        $csvParser = new Varien_File_Csv();
        $tempCsvData = $csvParser->getData($_FILES['source']['tmp_name']);

        $csvData = array();
        $headers = array_shift($tempCsvData);
        foreach ($tempCsvData as $csvRow) {
            $csvData[] = array_combine($headers, $csvRow);
        }

        $success = 0;
        foreach ($csvData as $csvRow) {

            $magentoProduct = $sourceType == 'id'
                ? Mage::getModel('catalog/product')->load($csvRow['id'])
                : Mage::getModel('catalog/product')->loadByAttribute('sku', $csvRow['sku']);

            if (!$magentoProduct) {
                continue;
            }

            $listingProduct = $listing->addProduct($magentoProduct, Ess_M2ePro_Helper_Data::INITIATOR_DEVELOPER);
            if ($listingProduct instanceof Ess_M2ePro_Model_Listing_Product) {
                $success++;
            }
        }

        $this->_getSession() ->addSuccess("Success '{$success}' Products.");
        $this->_redirectUrl(Mage::helper('M2ePro/View_Development')->getPageModuleTabUrl());
    }

    //########################################

    private function getEmptyResultsHtml($messageText)
    {
        $backUrl = Mage::helper('M2ePro/View_Development')->getPageModuleTabUrl();

        return <<<HTML
    <h2 style="margin: 20px 0 0 10px">
        {$messageText} <span style="color: grey; font-size: 10px;">
        <a href="{$backUrl}">[back]</a>
    </h2>
HTML;
    }

    //########################################
}
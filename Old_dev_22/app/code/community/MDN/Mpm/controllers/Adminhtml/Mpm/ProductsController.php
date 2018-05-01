<?php

class MDN_Mpm_Adminhtml_Mpm_ProductsController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        if (!Mage::helper('Mpm/Carl')->checkCredentials())
        {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('Mpm')->__('Please configure Carl credentials first'));
            $this->_redirect('adminhtml/system_config/edit', array('section' => 'mpm'));
        }
        else
        {
            Mage::helper('Mpm/Product')->pricingInProgress();
            $this->loadLayout();
            $this->renderLayout();
        }
    }

    public function changeSettingAction()
    {
        $pricingResult = null;
        $sku = $this->getRequest()->getParam('product_id');
        $sku = str_replace('[slash]', '/', $sku);
        $sku = str_replace('[sharp]', '#', $sku);
        $sku = str_replace('[plus]', '+', $sku);

        $productId = Mage::getModel('catalog/product')->getIdBySku($sku);
        
        $channel = $this->getRequest()->getParam('channel');
        $field = $this->getRequest()->getParam('field');
        $value = $this->getRequest()->getParam('value');

        if($field === 'behavior' || $field === 'behaviour') {
            $ruleId = $this->getBehaviorRuleId($sku, $channel);
            $configuration = array('name' => 'product behavior', 'behavior' => $value, 'enable' => 'on', 'type' => 'behavior', 'priority' => 42);

            if($value == 'default') {
                if(!empty($ruleId)) {
                    $pricingResult = Mage::Helper('Mpm/Carl')->deleteRuleProduct($ruleId);
                } else {
                    $pricingResults = Mage::Helper('Mpm/Carl')->repriceProduct($sku);
                    foreach($pricingResults as $pricingResult) {
                        if($pricingResult->channel === $channel) {
                            break;
                        }
                    }
                }
            }else {
                if(empty($ruleId)) {
                    $pricingResult = Mage::helper('Mpm/Carl')->createRuleProduct($sku, $channel, $configuration);
                } else {
                    $pricingResult = Mage::helper('Mpm/Carl')->updateRuleProduct($ruleId, $sku, $channel, $configuration);
                }
            }

            if($pricingResult !== null) {
                Mage::helper('Mpm/PricingImport')->setPricing(
                    $pricingResult->product_id,
                    $pricingResult->channel,
                    $pricingResult->final_price,
                    $pricingResult->shipping_price
                );
            }
        }

        if($pricingResult !== null) {
            $pricingResultFormated = array();
            $pricingResultFormated["product_id"] = $pricingResult->product_id;
            $rulesResult = json_decode($pricingResult->rules_result);

            foreach ($rulesResult as $result) {
                $pricingResultFormated[$result->type] = $result->result;
            }
            $product = Mage::getModel('catalog/product')->load($productId);
            Mage::register('mpm_product', $product);
            Mage::register('mpm_channel', $channel);

            $imageUrl = Mage::getSingleton('Mpm/System_Config_PricingStatus')->getSmileyUrl($pricingResult->status);
            $pricingResult->status_img = '<img src="' . $imageUrl . '" width="24">';
            $pricingResult->margin = $pricingResultFormated["MARGIN"];
            $this->getResponse()->setHeader('Content-type', 'application/json');
            $this->getResponse()->setBody(json_encode($pricingResult));
        }
    }

    public function postMatchingUrlsAction()
    {
        $sku = $this->getRequest()->getParam('product_id');
        $sku = str_replace('[slash]', '/', $sku);
        $sku = str_replace('[sharp]', '#', $sku);
        $sku = str_replace('[plus]', '+', $sku);

        $urls = $this->getRequest()->getParam('urls');
        $urls = str_replace('[slash]', '/', $urls);
        $urls = str_replace('[sharp]', '#', $urls);
        $urls = str_replace('[plus]', '+', $urls);
        $urls = str_replace('[interrogation]', '?', $urls);
        $urls = str_replace('[esperluette]', '&', $urls);
        $urls = explode(',', $urls);

        Mage::helper('Mpm/Carl')->postMatchingByUrls($sku, $urls);
    }

    private function getBehaviorRuleId($productId, $channel)
    {
        $ruleId = null;
        $rules = Mage::helper('Mpm/Carl')->getRulesProduct($productId, $channel);
        foreach($rules as $rule) {
            if($rule->type === 'BEHAVIOR' && $rule->priority === 42) {
                $ruleId = $rule->id;
                break;
            }
        }

        return $ruleId;
    }

    public function offersPopupAction()
    {
        $productId = $this->getRequest()->getParam('product_id');
        $channel = $this->getRequest()->getParam('channel');
        $productId = str_replace('[slash]', '/', $productId);
        $productId = str_replace('[plus]', '+', $productId);

        $pricingCollection = new MDN_Mpm_Model_PricingCollection();
        $pricingCollection->addFieldToFilter('product_id', '"'.$productId.'"');
        $pricingCollection->addFieldToFilter('channel', '"'.$channel.'"');

        $pricingCollection->load();
        foreach($pricingCollection as $product) {
            Mage::register('mpm_product', $product);
            break;
        }

        Mage::register('mpm_channel', $channel);

        $this->loadLayout();
        $this->renderLayout();
    }


    public function offersPopupBlockAction()
    {
        $blockName = $this->getRequest()->getParam('block_name');
        $method = 'offersPopup'.$blockName;
        $productId = $this->getRequest()->getParam('product_id');
        $channel = $this->getRequest()->getParam('channel');
        $productId = str_replace('[slash]', '/', $productId);
        $productId = str_replace('[plus]', '+', $productId);

        $pricingCollection = new MDN_Mpm_Model_PricingCollection();
        $pricingCollection->addFieldToFilter('product_id', '"'.$productId.'"');
        $pricingCollection->addFieldToFilter('channel', '"'.$channel.'"');

        $pricingCollection->load();
        foreach($pricingCollection as $product) {
            Mage::register('mpm_product', $product);
            break;
        }

        Mage::register('mpm_channel', $channel);

        $this->loadLayout();
        $this->$method();
    }

    protected function offersPopupPricing()
    {
        $block = $this->getLayout()->createBlock('Mpm/Products_Tabs_Pricing');
        $this->getResponse()->setBody($block->toHtml());
    }

    protected function offersPopupPricingHistory()
    {
        $block = $this->getLayout()->createBlock('Mpm/Products_Tabs_RepricingHistory');
        $this->getResponse()->setBody($block->toHtml());
    }

    protected function offersPopupInformation()
    {

        $block = $this->getLayout()->createBlock('Mpm/Products_Tabs_Information');
        $this->getResponse()->setBody($block->toHtml());
    }

    protected  function offersPopupHistory()
    {
        $block = $this->getLayout()->createBlock('Mpm/Products_Tabs_History');
        $this->getResponse()->setBody($block->toHtml());
    }

    protected function offersPopupDebug()
    {
        $block = $this->getLayout()->createBlock('Mpm/Products_Tabs_Debug');
        $this->getResponse()->setBody($block->toHtml());
    }

    protected  function offersPopupMatching()
    {
        $block = $this->getLayout()->createBlock('Mpm/Products_Tabs_Matching');
        $this->getResponse()->setBody($block->toHtml());
    }

    public function massChangeBehaviourAction()
    {
        $logIds = $this->getRequest()->getPost('product');
        $newBehaviour = $this->getRequest()->getPost('behaviours');

        foreach($logIds as $logId)
        {
            $log = Mage::getModel('Mpm/PricingLog')->load($logId);
            $channel = $log->getChannel();
            $productId = $log->getproduct_id();
            Mage::getSingleton('Mpm/Product_Setting')->updateField($productId, $channel, 'behaviour', $newBehaviour);
        }

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('Mpm')->__('Behaviours updated'));
        $this->_redirectReferer();
    }

    public function changeFiltersAction()
    {
        $channels = $this->getRequest()->getPost('channel');
        $statuses = $this->getRequest()->getPost('status');
        $behaviours = $this->getRequest()->getPost('behaviour');

        Mage::helper('Mpm/Product')->setProductsGridFilters($statuses, $channels, $behaviours);
    }


    public function CommissionsAction()
    {
        if (!Mage::helper('Mpm/Carl')->checkCredentials())
        {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('Mpm')->__('Please configure Carl credentials first'));
            $this->_redirect('adminhtml/system_config/edit', array('section' => 'mpm'));
        }
        else {
            $this->loadLayout();
            $this->renderLayout();
        }
    }

    public function exportProductGridCsvAction()
    {
        $fileName = 'carl_export_products_'.date('Y_M_D_H_i_s').'.csv';
        $content = $this->getLayout()->createBlock('Mpm/Products_GridV2')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportProductGridExcelAction()
    {
        $fileName = 'carl_export_products_'.date('Y_M_D_H_i_s').'.xls';
        $content = $this->getLayout()->createBlock('Mpm/Products_GridV2')
            ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }


    public function exportCommissionGridCsvAction()
    {
        $fileName = 'carl_export_commission_'.date('Y_M_D_H_i_s').'.csv';
        $content = $this->getLayout()->createBlock('Mpm/Commissions_Grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportCommissionGridExcelAction()
    {
        $fileName = 'carl_export_commission_'.date('Y_M_D_H_i_s').'.xls';
        $content = $this->getLayout()->createBlock('Mpm/Commissions_Grid')
            ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed()
    {
        return true;
    }

}

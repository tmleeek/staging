<?php

class MDN_Mpm_Adminhtml_Mpm_CarlController extends Mage_Adminhtml_Controller_Action
{
    public function ExportCatalogAction()
    {
        try
        {
            Mage::helper('Mpm/Export')->exportCatalog();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('Mpm')->__('Catalog successfully exported'));
        }
        catch(Exception $ex)
        {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('Mpm')->__('An error occured : %s', $ex->getMessage()));
            Mage::logException($ex);
        }

        $this->_redirect('adminhtml/system_config/edit', array('section' => 'mpm'));
    }

    public function ImportOffersAction()
    {
        try
        {
            $report = Mage::helper('Mpm/Product')->synchronizeAllOffers();
            if ($report)
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('Mpm')->__('Offers import executed - report #%s (%s)', $report->getId(), $report->getStatus()));
        }
        catch(Exception $ex)
        {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('Mpm')->__('An error occured : %s', $ex->getMessage()));
        }

        $this->_redirect('adminhtml/Mpm_Report');
    }

    public function ImportCommissionsAction()
    {
        try
        {
            $count = Mage::helper('Mpm/Commission')->synchronizeAll();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('Mpm')->__('Commissions import executed - %s reports', $count));
        }
        catch(Exception $ex)
        {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('Mpm')->__('An error occured : %s', $ex->getMessage()));
        }

        $this->_redirect('adminhtml/Mpm_Report');
    }


    public function ExportDownloadCatalogAction()
    {
        try
        {
            $filePath = Mage::helper('Mpm/Export')->getCatalog();
            $fileName =basename($filePath);
            $this->_prepareDownloadResponse($fileName, file_get_contents($filePath), 'application/xml');
        }
        catch(Exception $ex)
        {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('Mpm')->__('An error occured : %s', $ex->getMessage()));
            $this->_redirect('adminhtml/system_config/edit', array('section' => 'mpm'));
        }
    }

    /**
     * Method to download file on client side
     *
     * @param unknown_type $fileName
     * @param unknown_type $content
     * @param unknown_type $contentType
     * @param unknown_type $contentLength
     */
    protected function _prepareDownloadResponse($fileName, $content, $contentType = 'application/octet-stream', $contentLength = null) {
        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', $contentType, true)
            ->setHeader('Content-Length', strlen($content))
            ->setHeader('Content-Disposition', 'attachment; filename=' . $fileName)
            ->setBody($content);
    }

    /**
     * Ajax grid update
     */
    public function ProductOffersGridAction()
    {
        $productId = $this->getRequest()->getParam('product_id');
        $product = Mage::getModel('catalog/product')->load($productId);
        Mage::register('product', $product);
        $filtersQuery =  explode('&', base64_decode($this->getRequest()->getParam('filter')));
        foreach($filtersQuery as $filterQuery){
            $filterQuery = explode("=", $filterQuery);
            $filters[$filterQuery[0]] = $filterQuery[1];
        }
        if (isset($filters['channel'])) {
            $channel = $filters['channel'];
            unset($filters['channel']);
        } else {
            $channel = false;
        }

        if($this->getRequest()->getParam('sort') !== null){
            $sortBy =  array("field" => $this->getRequest()->getParam('sort'), "dir" => $this->getRequest()->getParam
                    ('dir'));
        }else{
            $sortBy = array();
        }


        $this->loadLayout();
        $block = $this->getLayout()->createBlock('Mpm/Adminhtml_Catalog_Product_Edit_Tab_OfferGrid');
        $block->setChannelToFilter($channel);
        $block->addFilters($filters);
        $block->setSortValue($sortBy);
        $this->getResponse()->setBody($block->toHtml());

    }

    public function UpdateOffersAction()
    {

        try
        {
            $productId = $this->getRequest()->getParam('product_id');
            $product = Mage::getModel('catalog/product')->load($productId);

            Mage::helper('Mpm/Product')->synchronizeOffers($product);

            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('Mpm')->__('Offers updated'));
        }
        catch(Exception $ex)
        {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('Mpm')->__('An error occured : %s', $ex->getMessage()));
        }

        $this->_redirect('adminhtml/catalog_product/edit', array('id' => $productId, 'tab' => 'product_info_tabs_mpm_carl'));
    }

    public function ResetAction()
    {
        try
        {
            Mage::helper('Mpm/Product')->reset();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('Mpm')->__('Date reset successfully executed'));
        }
        catch(Exception $ex)
        {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('Mpm')->__('An error occured : %s', $ex->getMessage()));
        }

        $this->_redirect('adminhtml/system_config/edit', array('section' => 'mpm'));
    }

    public function loadCarlTabPricingAction()
    {
        $product = Mage::getModel('catalog/product')->load($this->getRequest()->getParam('product_id'));
        Mage::register('mpm_product',$product);
        Mage::unregister('mpm_channel');
        Mage::register('mpm_channel', $this->getRequest()->getParam('mpm_channel'));
        Mage::unregister('mpm_currency');
        Mage::register('mpm_currency', $this->getRequest()->getParam('currency'));
        $block = $this->getLayout()->createBlock('Mpm/Products_Tabs_PricingCarl')->setTemplate
            ('Mpm/Products/Tabs/Pricing.phtml');
        $block->setShowRules(false);
        $block->setDisableOffers(true);
        $this->getResponse()->setBody($block->toHtml());
    }

    public function loadCarlTabAllOffersAction()
    {


        $product = Mage::getModel('catalog/product')->load($this->getRequest()->getParam('product_id'));
        Mage::register('product',$product);
        $block = $this->getLayout()->createBlock('Mpm/Adminhtml_Catalog_Product_Edit_Tab_OfferGrid');
        $productOffers = Mage::helper('Mpm/Product')->getOffers($product->getSku());

        $block->setProductOffers($productOffers);
        $this->getResponse()->setBody($block->toHtml());
    }

    public function loadCarlTabOffersSummaryAction()
    {
        $product = Mage::getModel('catalog/product')->load($this->getRequest()->getParam('product_id'));
        Mage::register('product',$product);
        $block = $this->getLayout()->createBlock('Mpm/Adminhtml_Catalog_Product_Edit_Tab_Summary');
        $this->getResponse()->setBody($block->toHtml());
    }

    protected function _isAllowed()
    {
        return true;
    }

    public function cleanAttributesForMissingProductsAction(){

        try{

            Mage::Helper('Mpm/Attribute_Cleaner')->cleanAttributesForMissingProducts();
            Mage::getSingleton('adminhtml/session')->addSuccess('Attributes cleaned');

        }catch(Exception $e){

            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());

        }

        $this->_redirectReferer();

    }

}

<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Adminhtml_Common_Amazon_AccountController
    extends Ess_M2ePro_Controller_Adminhtml_Common_MainController
{
    //########################################

    protected function _initAction()
    {
        $this->loadLayout()
             ->_title(Mage::helper('M2ePro')->__('Configuration'))
             ->_title(Mage::helper('M2ePro')->__('Amazon Accounts'));

        $this->getLayout()->getBlock('head')
             ->setCanLoadExtJs(true)
             ->addCss('M2ePro/css/Plugin/AreaWrapper.css')
             ->addJs('M2ePro/Common/Amazon/AccountHandler.js');

        $this->setPageHelpLink(NULL, NULL, "x/mIIVAQ");

        $this->_initPopUp();

        return $this;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('m2epro_common/configuration');
    }

    //########################################

    public function indexAction()
    {
        return $this->_redirect('*/adminhtml_common_account/index');
    }

    //########################################

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');

        /** @var Ess_M2ePro_Model_Account $account */
        $account = Mage::helper('M2ePro/Component_Amazon')->getModel('Account')->load($id);

        if ($id && !$account->getId()) {
            $this->_getSession()->addError(Mage::helper('M2ePro')->__('Account does not exist.'));
            return $this->indexAction();
        }

        $marketplaces = Mage::helper('M2ePro/Component_Amazon')->getMarketplacesAvailableForApiCreation();
        if ($marketplaces->getSize() <= 0) {
            $message = 'You should select and update at least one Amazon marketplace.';
            $this->_getSession()->addError(Mage::helper('M2ePro')->__($message));
            return $this->indexAction();
        }

        if ($id) {
            Mage::helper('M2ePro/Data_Global')->setValue('license_message', $this->getLicenseMessage($account));
        }

        Mage::helper('M2ePro/Data_Global')->setValue('temp_data', $account);

        $this->_initAction()
             ->_addLeft($this->getLayout()->createBlock('M2ePro/adminhtml_common_amazon_account_edit_tabs'))
             ->_addContent($this->getLayout()->createBlock('M2ePro/adminhtml_common_amazon_account_edit'))
             ->renderLayout();
    }

    //########################################

    public function beforeGetTokenAction()
    {
        // Get and save form data
        // ---------------------------------------
        $accountId = $this->getRequest()->getParam('id', 0);
        $accountTitle = $this->getRequest()->getParam('title', '');
        $marketplaceId = $this->getRequest()->getParam('marketplace_id', 0);
        // ---------------------------------------

        $marketplace = Mage::getModel('M2ePro/Marketplace')->load($marketplaceId);

        try {

            $backUrl = $this->getUrl('*/*/afterGetToken', array('_current' => true));

            $dispatcherObject = Mage::getModel('M2ePro/Amazon_Connector_Dispatcher');
            $connectorObj = $dispatcherObject->getVirtualConnector('account','get','authUrl',
                array('back_url' => $backUrl, 'marketplace' => $marketplace->getData('native_id')));

            $dispatcherObject->process($connectorObj);
            $response = $connectorObj->getResponseData();

        } catch (Exception $exception) {

            Mage::helper('M2ePro/Module_Exception')->process($exception);
            // M2ePro_TRANSLATIONS
            // The Amazon token obtaining is currently unavailable.<br/>Reason: %error_message%
            $error = 'The Amazon token obtaining is currently unavailable.<br/>Reason: %error_message%';
            $error = Mage::helper('M2ePro')->__($error, $exception->getMessage());

            $this->_getSession()->addError($error);

            return $this->indexAction();
        }

        Mage::helper('M2ePro/Data_Session')->setValue('account_id', $accountId);
        Mage::helper('M2ePro/Data_Session')->setValue('account_title', $accountTitle);
        Mage::helper('M2ePro/Data_Session')->setValue('marketplace', $marketplace);

        $this->_redirectUrl($response['url']);
        // ---------------------------------------
    }

    public function afterGetTokenAction()
    {
        $params = $this->getRequest()->getParams();

        if (empty($params)) {
            return $this->indexAction();
        }

        $requiredFields = array(
            'Merchant',
            'Marketplace',
            'MWSAuthToken',
            'Signature',
            'SignedString'
        );

        foreach ($requiredFields as $requiredField) {
            if (!isset($params[$requiredField])) {
                // M2ePro_TRANSLATIONS
                // The Amazon token obtaining is currently unavailable.
                $error = Mage::helper('M2ePro')->__('The Amazon token obtaining is currently unavailable.');
                $this->_getSession()->addError($error);

                return $this->indexAction();
            }
        }

         // Goto account add or edit page
         // ---------------------------------------
        $accountId = (int)Mage::helper('M2ePro/Data_Session')->getValue('account_id', true);

        Mage::helper('M2ePro/Data_Session')->setValue('merchant_id', $params['Merchant']);
        Mage::helper('M2ePro/Data_Session')->setValue('mws_token', $params['MWSAuthToken']);

        $params = array('wizard' => (bool)$this->getRequest()->getParam('wizard', false));
        if ($accountId == 0) {
            $this->_redirect('*/*/new', $params);
        } else {
            $params['id'] = $accountId;
            $this->_redirect('*/*/edit', $params);
        }
        // ---------------------------------------
    }

    //########################################

    public function saveAction()
    {
        if (!$post = $this->getRequest()->getPost()) {
            $this->_redirect('*/*/index');
        }

        $id = $this->getRequest()->getParam('id');

        // Base prepare
        // ---------------------------------------
        $data = array();
        // ---------------------------------------

        // tab: general
        // ---------------------------------------
        $keys = array(
            'title',
            'marketplace_id',
            'merchant_id',
            'token',
        );
        foreach ($keys as $key) {
            if (isset($post[$key])) {
                $data[$key] = $post[$key];
            }
        }
        // ---------------------------------------

        // tab: 3rd party listings
        // ---------------------------------------
        $keys = array(
            'related_store_id',

            'other_listings_synchronization',
            'other_listings_mapping_mode',
            'other_listings_move_mode'
        );
        foreach ($keys as $key) {
            if (isset($post[$key])) {
                $data[$key] = $post[$key];
            }
        }
        // ---------------------------------------

        // Mapping
        // ---------------------------------------
        $tempData = array();
        $keys = array(
            'mapping_general_id_mode',
            'mapping_general_id_priority',
            'mapping_general_id_attribute',

            'mapping_sku_mode',
            'mapping_sku_priority',
            'mapping_sku_attribute',

            'mapping_title_mode',
            'mapping_title_priority',
            'mapping_title_attribute'
        );
        foreach ($keys as $key) {
            if (isset($post[$key])) {
                $tempData[$key] = $post[$key];
            }
        }

        $mappingSettings = array();

        $temp = Ess_M2ePro_Model_Amazon_Account::OTHER_LISTINGS_MAPPING_GENERAL_ID_MODE_CUSTOM_ATTRIBUTE;
        if (isset($tempData['mapping_general_id_mode']) &&
            $tempData['mapping_general_id_mode'] == $temp) {
            $mappingSettings['general_id']['mode'] = (int)$tempData['mapping_general_id_mode'];
            $mappingSettings['general_id']['priority'] = (int)$tempData['mapping_general_id_priority'];
            $mappingSettings['general_id']['attribute'] = (string)$tempData['mapping_general_id_attribute'];
        }

        $temp1 = Ess_M2ePro_Model_Amazon_Account::OTHER_LISTINGS_MAPPING_SKU_MODE_DEFAULT;
        $temp2 = Ess_M2ePro_Model_Amazon_Account::OTHER_LISTINGS_MAPPING_SKU_MODE_CUSTOM_ATTRIBUTE;
        $temp3 = Ess_M2ePro_Model_Amazon_Account::OTHER_LISTINGS_MAPPING_SKU_MODE_PRODUCT_ID;
        if (isset($tempData['mapping_sku_mode']) &&
            ($tempData['mapping_sku_mode'] == $temp1 ||
             $tempData['mapping_sku_mode'] == $temp2 ||
             $tempData['mapping_sku_mode'] == $temp3)) {
            $mappingSettings['sku']['mode'] = (int)$tempData['mapping_sku_mode'];
            $mappingSettings['sku']['priority'] = (int)$tempData['mapping_sku_priority'];

            $temp = Ess_M2ePro_Model_Amazon_Account::OTHER_LISTINGS_MAPPING_SKU_MODE_CUSTOM_ATTRIBUTE;
            if ($tempData['mapping_sku_mode'] == $temp) {
                $mappingSettings['sku']['attribute'] = (string)$tempData['mapping_sku_attribute'];
            }
        }

        $temp1 = Ess_M2ePro_Model_Amazon_Account::OTHER_LISTINGS_MAPPING_TITLE_MODE_DEFAULT;
        $temp2 = Ess_M2ePro_Model_Amazon_Account::OTHER_LISTINGS_MAPPING_TITLE_MODE_CUSTOM_ATTRIBUTE;
        if (isset($tempData['mapping_title_mode']) &&
            ($tempData['mapping_title_mode'] == $temp1 ||
             $tempData['mapping_title_mode'] == $temp2)) {
            $mappingSettings['title']['mode'] = (int)$tempData['mapping_title_mode'];
            $mappingSettings['title']['priority'] = (int)$tempData['mapping_title_priority'];
            $mappingSettings['title']['attribute'] = (string)$tempData['mapping_title_attribute'];
        }

        $data['other_listings_mapping_settings'] = Mage::helper('M2ePro')->jsonEncode($mappingSettings);
        // ---------------------------------------

        // tab: orders
        // ---------------------------------------
        $data['magento_orders_settings'] = array();

        // m2e orders settings
        // ---------------------------------------
        $tempKey = 'listing';
        $tempSettings = !empty($post['magento_orders_settings'][$tempKey])
            ? $post['magento_orders_settings'][$tempKey] : array();

        $keys = array(
            'mode',
            'store_mode',
            'store_id'
        );
        foreach ($keys as $key) {
            if (isset($tempSettings[$key])) {
                $data['magento_orders_settings'][$tempKey][$key] = $tempSettings[$key];
            }
        }
        // ---------------------------------------

        // 3rd party orders settings
        // ---------------------------------------
        $tempKey = 'listing_other';
        $tempSettings = !empty($post['magento_orders_settings'][$tempKey])
            ? $post['magento_orders_settings'][$tempKey] : array();

        $keys = array(
            'mode',
            'product_mode',
            'product_tax_class_id',
            'store_id'
        );
        foreach ($keys as $key) {
            if (isset($tempSettings[$key])) {
                $data['magento_orders_settings'][$tempKey][$key] = $tempSettings[$key];
            }
        }
        // ---------------------------------------

        // order number settings
        // ---------------------------------------
        $tempKey = 'number';
        $tempSettings = !empty($post['magento_orders_settings'][$tempKey])
            ? $post['magento_orders_settings'][$tempKey] : array();

        $data['magento_orders_settings'][$tempKey]['source'] = $tempSettings['source'];

        $prefixKeys = array(
            'mode',
            'prefix',
        );
        $tempSettings = !empty($tempSettings['prefix']) ? $tempSettings['prefix'] : array();
        foreach ($prefixKeys as $key) {
            if (isset($tempSettings[$key])) {
                $data['magento_orders_settings'][$tempKey]['prefix'][$key] = $tempSettings[$key];
            }
        }
        // ---------------------------------------

        // qty reservation
        // ---------------------------------------
        $tempKey = 'qty_reservation';
        $tempSettings = !empty($post['magento_orders_settings'][$tempKey])
            ? $post['magento_orders_settings'][$tempKey] : array();

        $keys = array(
            'days',
        );
        foreach ($keys as $key) {
            if (isset($tempSettings[$key])) {
                $data['magento_orders_settings'][$tempKey][$key] = $tempSettings[$key];
            }
        }
        // ---------------------------------------

        // refund & cancellation
        // ---------------------------------------
        $tempKey = 'refund_and_cancellation';
        $tempSettings = !empty($post['magento_orders_settings'][$tempKey])
            ? $post['magento_orders_settings'][$tempKey] : array();

        $keys = array(
            'refund_mode',
        );
        foreach ($keys as $key) {
            if (isset($tempSettings[$key])) {
                $data['magento_orders_settings'][$tempKey][$key] = $tempSettings[$key];
            }
        }
        // ---------------------------------------

        // fba
        // ---------------------------------------
        $tempKey = 'fba';
        $tempSettings = !empty($post['magento_orders_settings'][$tempKey])
            ? $post['magento_orders_settings'][$tempKey] : array();

        $keys = array(
            'mode',
            'stock_mode'
        );
        foreach ($keys as $key) {
            if (isset($tempSettings[$key])) {
                $data['magento_orders_settings'][$tempKey][$key] = $tempSettings[$key];
            }
        }
        // ---------------------------------------

        // tax settings
        // ---------------------------------------
        $tempKey = 'tax';
        $tempSettings = !empty($post['magento_orders_settings'][$tempKey])
            ? $post['magento_orders_settings'][$tempKey] : array();

        $keys = array(
            'mode'
        );
        foreach ($keys as $key) {
            if (isset($tempSettings[$key])) {
                $data['magento_orders_settings'][$tempKey][$key] = $tempSettings[$key];
            }
        }
        // ---------------------------------------

        // customer settings
        // ---------------------------------------
        $tempKey = 'customer';
        $tempSettings = !empty($post['magento_orders_settings'][$tempKey])
            ? $post['magento_orders_settings'][$tempKey] : array();

        $keys = array(
            'mode',
            'id',
            'website_id',
            'group_id',
            'billing_address_mode',
//            'subscription_mode'
        );
        foreach ($keys as $key) {
            if (isset($tempSettings[$key])) {
                $data['magento_orders_settings'][$tempKey][$key] = $tempSettings[$key];
            }
        }

        $notificationsKeys = array(
//            'customer_created',
            'order_created',
            'invoice_created'
        );
        $tempSettings = !empty($tempSettings['notifications']) ? $tempSettings['notifications'] : array();
        foreach ($notificationsKeys as $key) {
            if (in_array($key, $tempSettings)) {
                $data['magento_orders_settings'][$tempKey]['notifications'][$key] = true;
            }
        }
        // ---------------------------------------

        // status mapping settings
        // ---------------------------------------
        $tempKey = 'status_mapping';
        $tempSettings = !empty($post['magento_orders_settings'][$tempKey])
            ? $post['magento_orders_settings'][$tempKey] : array();

        $keys = array(
            'mode',
            'processing',
            'shipped'
        );
        foreach ($keys as $key) {
            if (isset($tempSettings[$key])) {
                $data['magento_orders_settings'][$tempKey][$key] = $tempSettings[$key];
            }
        }
        // ---------------------------------------

        // invoice/shipment settings
        // ---------------------------------------
        $temp = Ess_M2ePro_Model_Amazon_Account::MAGENTO_ORDERS_INVOICE_MODE_YES;
        $data['magento_orders_settings']['invoice_mode'] = $temp;
        $temp = Ess_M2ePro_Model_Amazon_Account::MAGENTO_ORDERS_SHIPMENT_MODE_YES;
        $data['magento_orders_settings']['shipment_mode'] = $temp;

        $temp = Ess_M2ePro_Model_Amazon_Account::MAGENTO_ORDERS_STATUS_MAPPING_MODE_CUSTOM;
        if (!empty($data['magento_orders_settings']['status_mapping']['mode']) &&
            $data['magento_orders_settings']['status_mapping']['mode'] == $temp) {

            $temp = Ess_M2ePro_Model_Amazon_Account::MAGENTO_ORDERS_INVOICE_MODE_NO;
            if (!isset($post['magento_orders_settings']['invoice_mode'])) {
                $data['magento_orders_settings']['invoice_mode'] = $temp;
            }
            $temp = Ess_M2ePro_Model_Amazon_Account::MAGENTO_ORDERS_SHIPMENT_MODE_NO;
            if (!isset($post['magento_orders_settings']['shipment_mode'])) {
                $data['magento_orders_settings']['shipment_mode'] = $temp;
            }
        }
        // ---------------------------------------

        // ---------------------------------------
        $data['magento_orders_settings'] = Mage::helper('M2ePro')->jsonEncode($data['magento_orders_settings']);
        // ---------------------------------------

        $isEdit = !is_null($id);

        // tab: shipping settings
        // ---------------------------------------
        $keys = array(
            'shipping_mode'
        );
        foreach ($keys as $key) {
            if (isset($post[$key])) {
                $data[$key] = $post[$key];
            }
        }
        // ---------------------------------------

        // tab: vat calculation service
        // ---------------------------------------
        $keys = array(
            'is_vat_calculation_service_enabled',
            'is_magento_invoice_creation_disabled',
        );
        foreach ($keys as $key) {
            if (isset($post[$key])) {
                $data[$key] = $post[$key];
            }
        }

        if (empty($data['is_vat_calculation_service_enabled'])) {
            $data['is_magento_invoice_creation_disabled'] = false;
        }
        // ---------------------------------------

        // Add or update model
        // ---------------------------------------
        $model = Mage::helper('M2ePro/Component_Amazon')->getModel('Account');
        is_null($id) && $model->setData($data);
        !is_null($id) && $model->loadInstance($id)->addData($data);
        $oldData = $model->getOrigData();
        $id = $model->save()->getId();
        // ---------------------------------------

        $model->getChildObject()->setSetting('other_listings_move_settings',
                                             array('synch'),
                                             $post['other_listings_move_synch']);
        $model->getChildObject()->save();

        // Repricing
        // ---------------------------------------
        if (!empty($post['repricing']) && $model->getChildObject()->isRepricing()) {

            /** @var Ess_M2ePro_Model_Amazon_Account_Repricing $repricingModel */
            $repricingModel = $model->getChildObject()->getRepricing();

            $repricingOldData = $repricingModel->getData();

            $repricingModel->addData($post['repricing']);
            $repricingModel->save();

            $repricingNewData = $repricingModel->getData();

            $repricingModel->setProcessRequired($repricingNewData, $repricingOldData);
        }
        // ---------------------------------------

        try {

            // Add or update server
            // ---------------------------------------

            /** @var $accountObj Ess_M2ePro_Model_Account */
            $accountObj = $model;

            if (!$accountObj->isSetProcessingLock('server_synchronize')) {

                /** @var $dispatcherObject Ess_M2ePro_Model_Amazon_Connector_Dispatcher */
                $dispatcherObject = Mage::getModel('M2ePro/Amazon_Connector_Dispatcher');

                if (!$isEdit) {

                    $params = array(
                        'title'            => $post['title'],
                        'marketplace_id'   => (int)$post['marketplace_id'],
                        'merchant_id'      => $post['merchant_id'],
                        'token'            => $post['token'],
                        'related_store_id' => (int)$post['related_store_id']
                    );

                    $connectorObj = $dispatcherObject->getConnector('account', 'add' ,'entityRequester',
                                                                    $params, $id);
                    $dispatcherObject->process($connectorObj);

                } else {

                    $newData = array(
                        'title'            => $post['title'],
                        'marketplace_id'   => (int)$post['marketplace_id'],
                        'merchant_id'      => $post['merchant_id'],
                        'token'            => $post['token'],
                        'related_store_id' => (int)$post['related_store_id']
                    );

                    $params = array_diff_assoc($newData, $oldData);

                    if (!empty($params)) {
                        $connectorObj = $dispatcherObject->getConnector('account', 'update' ,'entityRequester',
                                                                        $params, $id);
                        $dispatcherObject->process($connectorObj);
                    }
                }
            }
            // ---------------------------------------

        } catch (Exception $exception) {

            Mage::helper('M2ePro/Module_Exception')->process($exception);

            // M2ePro_TRANSLATIONS
            // The Amazon access obtaining is currently unavailable.<br/>Reason: %error_message%

            $error = 'The Amazon access obtaining is currently unavailable.<br/>Reason: %error_message%';
            $error = Mage::helper('M2ePro')->__($error, $exception->getMessage());

            $this->_getSession()->addError($error);
            $model->deleteInstance();

            return $this->indexAction();
        }

        $this->_getSession()->addSuccess(Mage::helper('M2ePro')->__('Account was successfully saved'));

        /* @var $wizardHelper Ess_M2ePro_Helper_Module_Wizard */
        $wizardHelper = Mage::helper('M2ePro/Module_Wizard');

        $routerParams = array('id'=>$id);
        if ($wizardHelper->isActive('amazon') &&
            $wizardHelper->getStep('amazon') == 'account') {
            $routerParams['wizard'] = true;
        }

        $this->_redirectUrl(Mage::helper('M2ePro')->getBackUrl('list',array(),array('edit'=>$routerParams)));
    }

    public function deleteAction()
    {
        $this->_forward('delete','adminhtml_common_account');
    }

    //########################################

    public function checkAuthAction()
    {
        $merchantId    = $this->getRequest()->getParam('merchant_id');
        $token         = $this->getRequest()->getParam('token');
        $marketplaceId = $this->getRequest()->getParam('marketplace_id');

        $result = array (
            'result' => false,
            'reason' => null
        );

        if ($merchantId && $token && $marketplaceId) {

            $marketplaceNativeId = Mage::helper('M2ePro/Component_Amazon')
                ->getCachedObject('Marketplace',$marketplaceId)
                ->getNativeId();

            $params = array(
                'marketplace' => $marketplaceNativeId,
                'merchant_id' => $merchantId,
                'token'       => $token,
            );

            try {

                $dispatcherObject = Mage::getModel('M2ePro/Amazon_Connector_Dispatcher');
                $connectorObj = $dispatcherObject->getVirtualConnector('account','check','access',$params);
                $dispatcherObject->process($connectorObj);

                $response = $connectorObj->getResponseData();

                $result['result'] = isset($response['status']) ? $response['status']
                                                               : null;
                if (isset($response['reason'])) {
                    $result['reason'] = Mage::helper('M2ePro')->escapeJs($response['reason']);
                }

            } catch (Exception $exception) {
                $result['result'] = false;
                Mage::helper('M2ePro/Module_Exception')->process($exception);
            }
        }

        return $this->getResponse()->setBody(Mage::helper('M2ePro')->jsonEncode($result));
    }

    //########################################

    private function getLicenseMessage(Ess_M2ePro_Model_Account $account)
    {
        try {
            $dispatcherObject = Mage::getModel('M2ePro/M2ePro_Connector_Dispatcher');
            $connectorObj = $dispatcherObject->getVirtualConnector('account','get','info', array(
                'account' => $account->getChildObject()->getServerHash(),
                'channel' => Ess_M2ePro_Helper_Component_Amazon::NICK,
            ));

            $dispatcherObject->process($connectorObj);
            $response = $connectorObj->getResponseData();
        } catch (Exception $e) {
            return '';
        }

        if (!isset($response['info']['status']) || empty($response['info']['note'])) {
            return '';
        }

        $status = (bool)$response['info']['status'];
        $note   = $response['info']['note'];

        if ($status) {
            return 'MagentoMessageObj.addNotice(\''.$note.'\');';
        }

        $errorMessage = Mage::helper('M2ePro')->__(
            'Work with this Account is currently unavailable for the following reason: <br/> %error_message%',
            array('error_message' => $note)
        );

        return 'MagentoMessageObj.addError(\''.$errorMessage.'\');';
    }

    //########################################
}
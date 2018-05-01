<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   RMA
 * @version   1.0.1
 * @revision  135
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Rma_Helper_Mail
{
	public $emails = array();
	protected function getConfig() {
		return Mage::getSingleton('rma/config');
	}

	protected function getSender() {
		return $this->getConfig()->getNotificationSenderEmail();
	}

    protected function send($templateName, $senderName, $senderEmail, $recipientEmail, $recipientName, $variables, $storeId) {
		if (!$senderEmail || $templateName == 'none') {
			return false;
		}
        // save current design settings
        $currentDesignConfig = clone $this->_getDesignConfig();
        $this->_setDesignConfig(array('area' => 'frontend', 'store' => $storeId));
        $this->_applyDesignConfig();

        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        $variables = array_merge($variables, array(
            'logo_url' => $this->_getLogoUrl($storeId),
            'logo_alt' => $this->_getLogoAlt($storeId),
        ));

        $template = Mage::getModel('core/email_template');
        $template->setDesignConfig(array('area'=>'frontend', 'store'=>$storeId))
                 ->sendTransactional($templateName,
                 array(
                     'name' => $senderName,
                     'email' => $senderEmail,
                 ),
                 $recipientEmail, $recipientName, $variables);
		$text = $template->getProcessedTemplate($variables, true);
		$this->emails[]= array('text'=>$text, 'recipient_email'=>$recipientEmail, 'recipient_name'=>$recipientName);
        $translate->setTranslateInline(true);
        // restore previous design settings
        $this->_setDesignConfig($currentDesignConfig->getData());
        $this->_applyDesignConfig();
		return true;
    }

    public function sendNotificationCustomerEmail($rma, $commentHtml)
    {
    	$templateName = $this->getConfig()->getNotificationCustomerEmailTemplate();

    	$recipientEmail = $rma->getEmail();
    	$recipientName = $rma->getName();
		$variables = array(
            'customer' => $rma->getCustomer(),
            'rma' => $rma,
			'comment' => $commentHtml,
            'store' => $rma->getStore(),
        );

		$senderName = Mage::getStoreConfig("trans_email/ident_{$this->getSender()}/name");
		$senderEmail = Mage::getStoreConfig("trans_email/ident_{$this->getSender()}/email");
		$this->send($templateName, $senderName, $senderEmail, $recipientEmail, $recipientName, $variables, $rma->getStore()->getId());
    }

    public function sendNotificationAdminEmail($rma, $commentHtml)
    {
    	$templateName = $this->getConfig()->getNotificationAdminEmailTemplate();
    	$recipientEmail = $this->getConfig()->getNotificationAdminEmail();
    	$recipientName = '';

        $variables = array(
            'customer' => $rma->getCustomer(),
            'rma' => $rma,
            'comment' => $commentHtml,
            'store' => $rma->getStore(),
        );
		$senderName = Mage::getStoreConfig("trans_email/ident_{$this->getSender()}/name");
		$senderEmail = Mage::getStoreConfig("trans_email/ident_{$this->getSender()}/email");
		$this->send($templateName, $senderName, $senderEmail, $recipientEmail, $recipientName, $variables, $rma->getStore()->getId());
    }

    /**
     * Can parse template and return ready text
     * @param  string $variable  - text with variables like {{var customer.name}}
     * @param  array $variables - array of variables
     * @return string            - ready text
     */
    public function processVariable($variable, $variables)
    {
        $template = Mage::getModel('core/email_template');
        $template->setTemplateText($variable);
        return $template->getProcessedTemplate($variables);
    }

    protected function _getLogoUrl($store)
    {
        $store = Mage::app()->getStore($store);
        $fileName = $store->getConfig('design/email/logo');
        if ($fileName) {
            $uploadDir = Mage_Adminhtml_Model_System_Config_Backend_Email_Logo::UPLOAD_DIR;
            $fullFileName = Mage::getBaseDir('media') . DS . $uploadDir . DS . $fileName;
            if (file_exists($fullFileName)) {
                return Mage::getBaseUrl('media') . $uploadDir . '/' . $fileName;
            }
        }
        return Mage::getDesign()->getSkinUrl('images/logo_email.gif');
    }


    protected function _getLogoAlt($store)
    {
        $store = Mage::app()->getStore($store);
        $alt = $store->getConfig('design/email/logo_alt');
        if ($alt) {
            return $alt;
        }
        return $store->getFrontendName();
    }


    protected $_designConfig;
    protected function _setDesignConfig(array $config)
    {
        $this->_getDesignConfig()->setData($config);
        return $this;
    }

    protected function _getDesignConfig()
    {
        if(is_null($this->_designConfig)) {

            $store = is_object(Mage::getDesign()->getStore())
                ? Mage::getDesign()->getStore()->getId()
                : Mage::getDesign()->getStore();

            $this->_designConfig = new Varien_Object(array(
                'area' => Mage::getDesign()->getArea(),
                'store' => $store
            ));
        }
        return $this->_designConfig;
    }

    protected function _applyDesignConfig()
    {
        $designConfig = $this->_getDesignConfig();
        $design = Mage::getDesign();
        $designConfig->setOldArea($design->getArea())
            ->setOldStore($design->getStore());

        if ($designConfig->hasData('area')) {
            Mage::getDesign()->setArea($designConfig->getArea());
        }
        if ($designConfig->hasData('store')) {
            $store = $designConfig->getStore();
            Mage::app()->setCurrentStore($store);

            $locale = new Zend_Locale(Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $store));
            Mage::app()->getLocale()->setLocale($locale);
            Mage::app()->getLocale()->setLocaleCode($locale->toString());
            if ($designConfig->hasData('area')) {
                Mage::getSingleton('core/translate')->setLocale($locale)
                    ->init($designConfig->getArea(), true);
            }
            $design->setStore($store);
            $design->setTheme('');
            $design->setPackageName('');
        }
        return $this;
    }

    /************************/



}
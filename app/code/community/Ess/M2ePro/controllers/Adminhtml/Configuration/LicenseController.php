<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Adminhtml_Configuration_LicenseController
    extends Ess_M2ePro_Controller_Adminhtml_Configuration_MainController
{
    //########################################

    public function newLicenseAction()
    {
        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();

            $requiredKeys = array(
                'email',
                'firstname',
                'lastname',
                'phone',
                'country',
                'city',
                'postal_code',
            );

            $licenseData = array();
            foreach ($requiredKeys as $key) {

                if (!empty($post[$key])) {
                    $licenseData[$key] = $post[$key];
                    continue;
                }
                return $this->getResponse()->setBody(
                    Mage::helper('M2ePro')->__('You should fill all required fields.')
                );
            }

            $primaryConfig = Mage::helper('M2ePro/Primary')->getConfig();
            $oldLicenseKey = $primaryConfig->getGroupValue(
                '/'.Mage::helper('M2ePro/Module')->getName().'/license/','key'
            );
            $primaryConfig->setGroupValue('/'.Mage::helper('M2ePro/Module')->getName().'/license/','key','');

            $licenseResult = Mage::helper('M2ePro/Module_License')->obtainRecord(
                $licenseData['email'],
                $licenseData['firstname'], $licenseData['lastname'],
                $licenseData['country'], $licenseData['city'],
                $licenseData['postal_code'], $licenseData['phone']
            );

            if ($licenseResult) {
                $registry = Mage::getModel('M2ePro/Registry')->load('/wizard/license_form_data/', 'key');

                $registry->setData('key', '/wizard/license_form_data/');
                $registry->setData('value', Mage::helper('M2ePro')->jsonEncode($licenseData));
                $registry->save();

                $licenseKey = Mage::helper('M2ePro/Primary')->getConfig()->getGroupValue(
                    '/'.Mage::helper('M2ePro/Module')->getName().'/license/','key'
                );
                $this->_getSession()->addSuccess(
                    Mage::helper('M2ePro')->__('The License Key has been successfully created.')
                );
                return $this->getResponse()->setBody(Mage::helper('M2ePro')->jsonEncode(array(
                    'success' => true,
                    'license_key' => $licenseKey
                )));
            } else {
                $primaryConfig->setGroupValue(
                    '/'.Mage::helper('M2ePro/Module')->getName().'/license/','key', $oldLicenseKey
                );
            }
        }

        return $this->_getSession()->addError(
            Mage::helper('M2ePro')->__('Internal Server Error')
        );
    }

    //########################################

    public function confirmKeyAction()
    {
        if (!$this->getRequest()->isAjax() || !$this->getRequest()->isPost()) {
            $this->_getSession()->addSuccess(
                Mage::helper('M2ePro')->__('The License has been successfully saved.')
            );
            return $this->_redirectUrl($this->_getRefererUrl());
        }

        $post = $this->getRequest()->getPost();
        $primaryConfig = Mage::helper('M2ePro/Primary')->getConfig();

        // Save settings
        // ---------------------------------------
        $key = strip_tags($post['key']);
        $primaryConfig->setGroupValue(
            '/'.Mage::helper('M2ePro/Module')->getName().'/license/','key',(string)$key
        );
        // ---------------------------------------

        try {
            Mage::getModel('M2ePro/Servicing_Dispatcher')->processTask(
                Mage::getModel('M2ePro/Servicing_Task_License')->getPublicNick()
            );
        } catch (Exception $e) {
            return $this->_getSession()->addError(
                Mage::helper('M2ePro')->__($e->getMessage())
            );
        }

        $this->_getSession()->addSuccess(
            Mage::helper('M2ePro')->__('The License Key has been successfully updated.')
        );

        return $this->getResponse()->setBody(Mage::helper('M2ePro')->jsonEncode(array('success' => true)));
    }

    //########################################

    public function refreshStatusAction()
    {
        try {
            Mage::getModel('M2ePro/Servicing_Dispatcher')->processTask(
                Mage::getModel('M2ePro/Servicing_Task_License')->getPublicNick()
            );
        } catch (Exception $e) {
            $this->_getSession()->addError(
                Mage::helper('M2ePro')->__($e->getMessage())
            );

            return $this->_redirectUrl($this->_getRefererUrl());
        }

        $this->_getSession()->addSuccess(
            Mage::helper('M2ePro')->__('The License Status has been successfully refreshed.')
        );

        $this->_redirectUrl($this->_getRefererUrl());
    }

    //########################################
}
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


class Mirasvit_Rma_Model_Config
{
    const FIELD_TYPE_TEXT = 'text';
    const FIELD_TYPE_TEXTAREA = 'textarea';
    const FIELD_TYPE_DATE = 'date';
    const FIELD_TYPE_CHECKBOX = 'checkbox';
    const FIELD_TYPE_SELECT = 'select';

    public function getGeneralReturnAddress($store = null)
    {
        return Mage::getStoreConfig('rma/general/return_address', $store);
    }

    public function getGeneralDefaultStatus($store = null)
    {
        return Mage::getStoreConfig('rma/general/default_status', $store);
    }

    public function getPolicyIsActive($store = null)
    {
        return Mage::getStoreConfig('rma/policy/is_active', $store);
    }

    public function getPolicyPolicyBlock($store = null)
    {
        return Mage::getStoreConfig('rma/policy/policy_block', $store);
    }

    public function getNotificationSenderEmail($store = null)
    {
        return Mage::getStoreConfig('rma/notification/sender_email', $store);
    }

    public function getNotificationAdminEmail($store = null)
    {
        return Mage::getStoreConfig('rma/notification/admin_email', $store);
    }

    public function getNotificationCustomerEmailTemplate($store = null)
    {
        return Mage::getStoreConfig('rma/notification/customer_email_template', $store);
    }

    public function getNotificationAdminEmailTemplate($store = null)
    {
        return Mage::getStoreConfig('rma/notification/admin_email_template', $store);
    }


    /************************/

}
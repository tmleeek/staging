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


class Mirasvit_Rma_Block_Adminhtml_Status_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm() {
        $form = new Varien_Data_Form(
            array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                'method' => 'post',
                'enctype' => 'multipart/form-data'
            )
        );

        $status = Mage::registry('current_status');

        $fieldset = $form->addFieldset('edit_fieldset', array('legend'=> Mage::helper('rma')->__('General Information')));
        if ($status->getId()) {
            $fieldset->addField('status_id', 'hidden', array(
                'name'      => 'status_id',
                'value'     => $status->getId(),
            ));
        }
        $fieldset->addField('name', 'text', array(
            'label'     => Mage::helper('rma')->__('Title'),
            'name'      => 'name',
            'value'     => $status->getName(),
        ));
        $fieldset->addField('code', 'text', array(
            'label'     => Mage::helper('rma')->__('Code'),
            'name'      => 'code',
            'value'     => $status->getCode(),
        ));
        $fieldset->addField('sort_order', 'text', array(
            'label'     => Mage::helper('rma')->__('Sort Order'),
            'name'      => 'sort_order',
            'value'     => $status->getSortOrder(),
        ));
        $fieldset->addField('is_active', 'select', array(
            'label'     => Mage::helper('rma')->__('Is Active'),
            'name'      => 'is_active',
            'value'     => $status->getIsActive(),
            'values'    => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray()
        ));
        $fieldset->addField('is_rma_resolved', 'select', array(
            'label'     => Mage::helper('rma')->__('Resolve RMA after obtaining status'),
            'name'      => 'is_rma_resolved',
            'value'     => $status->getIsRmaResolved(),
            'values'    => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray()
        ));
        $fieldset = $form->addFieldset('notification_fieldset', array('legend'=> Mage::helper('rma')->__('Notifications')));
        $fieldset->addField('customer_message', 'textarea', array(
            'label'     => Mage::helper('rma')->__('Email Notification for customer'),
            'name'      => 'customer_message',
            'value'     => $status->getCustomerMessage(),
            'note'      => Mage::helper('rma')->__('leave blank to not send'),
        ));
        $fieldset->addField('history_message', 'textarea', array(
            'label'     => Mage::helper('rma')->__('Message for RMA history'),
            'name'      => 'history_message',
            'value'     => $status->getHistoryMessage(),
        ));
        $fieldset->addField('admin_message', 'textarea', array(
            'label'     => Mage::helper('rma')->__('Email Notification for administrator'),
            'name'      => 'admin_message',
            'value'     => $status->getAdminMessage(),
            'note'      => Mage::helper('rma')->__('leave blank to not send'),
        ));

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /************************/

}
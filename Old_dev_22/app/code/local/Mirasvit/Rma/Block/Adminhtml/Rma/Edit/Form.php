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


require 'Mirasvit/Rma/Block/Adminhtml/Rma/Edit/Renderer/Mfile.php';

class Mirasvit_Rma_Block_Adminhtml_Rma_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('mst_rma/rma/edit/form.phtml');
    }

    public function getRma()
    {
        return Mage::registry('current_rma');
    }

    public function getOrderItemCollection()
    {
        $rma = $this->getRma();
        $order = $rma->getOrder();
        return $order->getItemsCollection();
    }

    public function getGeneralInfoForm()
    {
        $form = new Varien_Data_Form();
        $rma = Mage::registry('current_rma');

        $fieldset = $form->addFieldset('edit_fieldset', array('legend'=> Mage::helper('rma')->__('General Information')));
        if ($rma->getId()) {
            $fieldset->addField('rma_id', 'hidden', array(
                'name'      => 'rma_id',
                'value'     => $rma->getId(),
            ));
        }
        $element = $fieldset->addField('increment_id', 'text', array(
            'label'     => Mage::helper('rma')->__('RMA #'),
            'name'      => 'increment_id',
            'value'     => $rma->getIncrementId(),
        ));

        if (!$rma->getId()) {
            $element->setNote('will be generated automatically, if empty');
        }
        $fieldset->addField('order_id', 'link', array(
            'label'     => Mage::helper('rma')->__('Order #'),
            'name'      => 'order_id',
            'value'     => '#'.$rma->getOrder()->getIncrementId(),
            'href'      => $this->getUrl('adminhtml/sales_order/view', array('order_id'=>$rma->getOrderId()))
        ));
        if ($rma->getCustomerId()) {
            $fieldset->addField('customer', 'link', array(
                'label'     => Mage::helper('rma')->__('Customer'),
                'name'      => 'customer',
                'value'     => $rma->getName(),
                'href'      => Mage::helper('rma/mage')->getBackendCustomerUrl($rma->getCustomerId())
            ));
        } else {
            $fieldset->addField('customer', 'label', array(
                'label'     => Mage::helper('rma')->__('Customer'),
                'name'      => 'customer',
                'value'     => $rma->getName(),
            ));
        }
        $fieldset->addField('status_id', 'select', array(
            'label'     => Mage::helper('rma')->__('Status'),
            'name'      => 'status_id',
            'value'     => $rma->getStatusId(),
            'values'    => Mage::getModel('rma/status')->getCollection()->toOptionArray()
        ));
        $fieldset->addField('return_label', 'mfile', array(
            'label'     => Mage::helper('rma')->__('Upload Return Label'),
            'name'      => 'return_label',
            'attachment'     => $rma->getReturnLabel(),
        ));

        if ($rma->getId()) {
            $fieldset->addField('guest_link', 'text', array(
                'label'     => Mage::helper('rma')->__('External Link'),
                'name'      => 'guest_link',
                'value'     => $rma->getGuestUrl(),
                'readonly' => 'readonly'
            ));
        }
        return $form;
    }

    public function getFieldForm()
    {
        $form = new Varien_Data_Form();
        $rma = Mage::registry('current_rma');
        $fieldset = $form->addFieldset('field_fieldset', array('legend'=> Mage::helper('rma')->__('Additional Information')));
        $collection = Mage::helper('rma/field')->getStaffCollection();
        if (!$collection->count()) {
            return false;
        }
        foreach ($collection as $field) {
            $fieldset->addField($field->getCode(), $field->getType(), Mage::helper('rma/field')->getInputParams($field, true, $rma));
        }
        return $form;
    }


    public function getShippingAddressForm()
    {
        $form = new Varien_Data_Form();
        $rma = Mage::registry('current_rma');

        $fieldset = $form->addFieldset('customer_fieldset', array('legend'=> Mage::helper('rma')->__('Contact Information')));
        $fieldset->addField('firstname', 'text', array(
            'label'     => Mage::helper('rma')->__('First Name'),
            'name'      => 'firstname',
            'value'     => $rma->getFirstname(),
        ));
        $fieldset->addField('lastname', 'text', array(
            'label'     => Mage::helper('rma')->__('Last Name'),
            'name'      => 'lastname',
            'value'     => $rma->getLastname(),
        ));
        $fieldset->addField('company', 'text', array(
            'label'     => Mage::helper('rma')->__('Company'),
            'name'      => 'company',
            'value'     => $rma->getCompany(),
        ));
        $fieldset->addField('telephone', 'text', array(
            'label'     => Mage::helper('rma')->__('Telephone'),
            'name'      => 'telephone',
            'value'     => $rma->getTelephone(),
        ));
        $fieldset->addField('email', 'text', array(
            'label'     => Mage::helper('rma')->__('Email'),
            'name'      => 'email',
            'value'     => $rma->getEmail(),
        ));
        $street = explode("\n", $rma->getStreet());
        $fieldset->addField('street', 'hidden', array(
            'label'     => Mage::helper('rma')->__('Street Address'),
            'name'      => 'street',
            'value'     => $street[0],
        ));
        $fieldset->addField('street2', 'hidden', array(
            // 'label'     => Mage::helper('rma')->__('Street Address'),
            'name'      => 'street2',
            'value'     => isset($street[1])? $street[1]: '',
        ));
        $fieldset->addField('city', 'hidden', array(
            'label'     => Mage::helper('rma')->__('City'),
            'name'      => 'city',
            'value'     => $rma->getCity(),
        ));
        $fieldset->addField('postcode', 'hidden', array(
            'label'     => Mage::helper('rma')->__('Zip/Postcode'),
            'name'      => 'postcode',
            'value'     => $rma->getPostcode(),
        ));
        return $form;
    }


    public function getHistoryHtml()
    {
        $historyBlock = $this->getLayout()->createBlock('rma/adminhtml_rma_edit_form_history', 'rma_history');
        return $historyBlock->toHtml();
    }

    public function getReturnAddressHtml()
    {
        $address =  $this->getRma()->getReturnAddressHtml();
        return $address;
    }

    public function getReasonCollection()
    {
        return Mage::getModel('rma/reason')->getCollection()
            ->addFieldToFilter('is_active', true)
            ->setOrder('sort_order');
    }

    public function getResolutionCollection()
    {
        return Mage::getModel('rma/resolution')->getCollection()
            ->addFieldToFilter('is_active', true)
            ->setOrder('sort_order');
    }

    public function getConditionCollection()
    {
        return Mage::getModel('rma/condition')->getCollection()
            ->addFieldToFilter('is_active', true)
            ->setOrder('sort_order');
    }


    /************************/

}
<?php

class TBT_Milestone_Block_Manage_History_View_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected $_customer = null;
    protected $_customerGroups = null;

    protected function _construct()
    {
        parent::_construct();
        $this->setId('milestone_history_general_form');
        $this->setTitle($this->__("General Information"));

        return $this;
    }

    protected function _prepareForm()
    {
        $form     = new Varien_Data_Form();
        $formData = $this->_prepareFormData();

        $form->setUseContainer(true);

        $basicsFieldset = $form->addFieldset('milestone_history_basics_fieldset', array(
            'legend' => $this->__("Milestone Details")
        ));

        $basicsFieldset->addField('rule_name', 'label', array(
            'name'  => 'rule_name',
            'bold'  => true,
            'label' => $this->__("Milestone Name"),
            'title' => $this->__("Milestone Name"),
        ));

        $basicsFieldset->addField('customer', 'link', array(
            'name'  => 'customer',
            'bold'  => true,
            'href'  => $this->_getCustomerViewUrl(),
            'label' => $this->__("Customer"),
            'title' => $this->__("Customer"),
        ));

        if (isset($formData['condition'])) {
            $basicsFieldset->addField('message', 'label', array(
                'name'  => 'condition_satisfied',
                'bold'  => true,
                'label' => $this->__("Condition Satisfied"),
                'title' => $this->__("Condition Satisfied"),
            ));
        }

        $basicsFieldset->addField('action_type', 'label', array(
            'name'  => 'action_type',
            'bold'  => true,
            'label' => $this->__("Action Type"),
            'title' => $this->__("Action Type"),
        ));

        if (isset($formData['points'])) {
            $basicsFieldset->addField('points', 'label', array(
                'name'  => 'points',
                'bold'  => true,
                'label' => $this->__("Points Rewarded"),
                'title' => $this->__("Points Rewarded"),
                'note'  => "You can find the created points transfer in the <i>Points Rewards</i> tab on the left.",
            ));
        }

        if (isset($formData['from']) && isset($formData['to'])) {
            $basicsFieldset->addField('from', 'label', array(
                'name'  => 'moved_from',
                'bold'  => true,
                'label' => $this->__("Moved From"),
                'title' => $this->__("Moved From"),
            ));
            $basicsFieldset->addField('to', 'label', array(
                'name'  => 'moved_to',
                'bold'  => true,
                'label' => $this->__("Moved To"),
                'title' => $this->__("Moved To"),
            ));
        }

        $basicsFieldset->addField('executed_date', 'label', array(
            'name'  => 'action_type',
            'bold'  => true,
            'label' => $this->__("Date & Time"),
            'title' => $this->__("Date & Time"),
        ));

        $this->setForm($form);
        $form->setValues($formData);

        return parent::_prepareForm();
    }

    protected function _prepareFormData()
    {
        $data = $this->_getCurrentLog()->getData();
        // convert a multidimensional array to simple one
        $data = Mage::helper('tbtcommon/array')->flatten($data);

        if (isset($data['from']) && isset($data['to'])) {
            $data['from'] = $this->_getCustomerGroupName($data['from']);
            $data['to']   = $this->_getCustomerGroupName($data['to']);
        }
        if (isset($data['action_type'])) {
            $typeNames           = Mage::getSingleton('tbtmilestone/rule_action_factory')->getTypeNames();
            $data['action_type'] = $typeNames[$data['action_type']];
        }
        if (isset($data['customer_id'])) {
            $data['customer'] = $this->_getCustomer()->getName();
        }
        if (isset($data['executed_date'])) {
            $localTimeStamp        = Mage::helper('tbtmilestone')->getLocalTimestamp($data['executed_date']);
            $data['executed_date'] = Mage::helper('tbtmilestone')->getMySqlDateString($localTimeStamp);
        }

        return $data;
    }

    protected function _getCurrentLog()
    {
        return Mage::registry('current_milestone_rule_log');
    }

    /**
     * Retrieve a customer group name from the ID
     * @param  int $groupId Customer Group ID
     * @return string       Customer Group Name (in Magento customer group 'code')
     */
    protected function _getCustomerGroupName($groupId)
    {
        $customerGroup = Mage::getModel('customer/group')->load($groupId);
        $customerGroupName = ($customerGroup->getId()) ? $customerGroup->getCode()
            : 'Customer Group Removed (ID: ' . $groupId . ')' ;

        return $customerGroupName;
    }

    /**
     * Retrieve current customer to which this rule log is associated.
     * @return Mage_Customer_Model_Customer
     */
    protected function _getCustomer()
    {
        $ruleLog = $this->_getCurrentLog();
        if (!isset($this->_customer)) {
            $this->_customer = Mage::getModel('customer/customer')->load($ruleLog->getCustomerId());
        }

        return $this->_customer;
    }

    /**
     * Retrieve link for the customer view page.
     * @return string
     */
    protected function _getCustomerViewUrl()
    {
        $customer   = $this->_getCustomer();
        $customerId = $customer->getId();
        $url        = $this->getUrl('adminhtml/customer/edit/', array ('id' => $customerId));

        return $url;
    }
}

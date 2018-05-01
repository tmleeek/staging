<?php

class TBT_Milestone_Block_Manage_Rule_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('milestone_rule_form');
        $this->setTitle($this->__("Milestone Information"));
        return $this;
    }

    protected function _prepareForm()
    {
        $model = $this->_getRule();
        $conditionDetails = $model->getConditionDetails();

        $form = new Varien_Data_Form(array(
            'id'     => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post'
        ));
        $form->setUseContainer(true);

        $basicsFieldset = $form->addFieldset('milestone_basics_fieldset', array(
            'legend' => $this->__("Milestone Settings")
        ));

        if ($model->getId()) {
            $basicsFieldset->addField('rule_id', 'hidden', array(
                'name' => 'rule_id'
            ));
        }

        $basicsFieldset->addField('condition_type', 'select', array(
            'name' => 'condition_type',
            'label' => $this->__("Milestone Type"),
            'required' => true,
            // TODO: getOptionArray
            'options' => Mage::getSingleton('tbtmilestone/rule_condition_factory')->getOptions()
        ));

        $revenueThresholdField = new TBT_Milestone_Block_Widget_Form_Element_Inline(array(
            'name' => 'condition_details',
            'label' => $this->__("Customer Reaches"),
            'element_placeholder' => "{{revenue_threshold}} " . Mage::app()->getLocale()->getCurrency()
        ));
        $revenueThresholdField->setId('condition_details');
        $basicsFieldset->addElement($revenueThresholdField);

        $revenueThresholdField->addField('condition_details_revenue_threshold', 'text', array(
            'name' => 'revenue_threshold',
        ));

        $timeConstraintField = new TBT_Milestone_Block_Widget_Form_Element_Inline(array(
            'name' => 'condition_details[time_constraint]',
            'label' => '{{toggle}}',
            'element_placeholder' => $this->__("within {{duration}} {{duration_type}}"),
            'container_id' => 'condition_details_time_constraint_container'
        ));
        $timeConstraintField->setId('condition_details_time_constraint');
        $basicsFieldset->addElement($timeConstraintField);

        $hasTimeConstraint = (is_array($conditionDetails) && isset($conditionDetails['time_constraint']));
        $timeConstraintField->addType('sectiontoggle', 'TBT_Milestone_Block_Widget_Form_Element_Sectiontoggle');
        $timeConstraintField->addField('condition_details_time_constraint_toggle', 'sectiontoggle', array(
            'name' => 'toggle',
            'label_expand' => $this->__("Add a time constraint"),
            'label_collapse' => $this->__("Remove the time constraint"),
            'section_selector' => '#condition_details_time_constraint_container td.value2',
            'is_expanded' => $hasTimeConstraint,
            'disable_on_collapse' => true
        ));
        $timeConstraintField->addField('condition_details_time_constraint_duration', 'text', array(
            'name' => 'duration',
            'width' => '25px',
            'align' => 'right'
        ));
        $timeConstraintField->addField('condition_details_time_constraint_duration_type', 'select', array(
            'name' => 'duration_type',
            'options' => array(
                'day'   => $this->__("Days"),
                'week'  => $this->__("Weeks"),
                'month' => $this->__("Months"),
                'year'  => $this->__("Years")
            )
        ));

        $basicsFieldset->addType('separator', 'TBT_Milestone_Block_Widget_Form_Element_Separator');
        $basicsFieldset->addField('separator', 'separator', array());

        $basicsFieldset->addField('action_type', 'select', array(
            'name' => 'action_type',
            'label' => $this->__("Milestone Action"),
            'required' => true,
            // TODO: getOptionArray
            'options' => Mage::getSingleton('tbtmilestone/rule_action_factory')->getOptions()
        ));

        $customerGroups = Mage::getResourceModel('customer/group_collection')
            ->addFieldToFilter('customer_group_id', array('neq' => 0));
        $basicsFieldset->addField('action_details_customer_group_id', 'select', array(
            'name' => 'action_details[customer_group_id]',
            'label' => $this->__("Customer Group"),
            'options' => $customerGroups->load()->toOptionHash()
        ));

        $form->addType('sectiontoggle', 'TBT_Milestone_Block_Widget_Form_Element_Sectiontoggle');
        $form->addField('milestone_advanced_fieldset_toggle', 'sectiontoggle', array(
            'label_expand' => $this->__("Show Advanced Settings"),
            'label_collapse' => $this->__("Hide Advanced Settings"),
            'section_selector' => '#milestone_advanced_fieldset'
        ));
        $advancedFieldset = $form->addFieldset('milestone_advanced_fieldset', array());

        $advancedFieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => $this->__("Milestone Name"),
            'required' => true
        ));

        $advancedFieldset->addField('is_enabled', 'select', array(
            'name' => 'is_enabled',
            'label' => $this->__("Status"),
            'value' => '1',
            'options' => array(
                '1' => $this->__("Enabled"),
                '0' => $this->__("Disabled")
            )
        ));

        $websites = Mage::getResourceModel('core/website_collection')
            ->setLoadDefault(true);
        $advancedFieldset->addField('website_ids', 'multiselect', array(
            'name' => 'website_ids',
            'label' => $this->__("Website"),
            'values' => $websites->load()->toOptionArray(),
            'value' => array_keys($websites->getItems())
        ));

        $customerGroups = Mage::getResourceModel('customer/group_collection')
            ->addFieldToFilter('customer_group_id', array('neq' => 0));
        $advancedFieldset->addField('customer_group_ids', 'multiselect', array(
            'name' => 'customer_group_ids',
            'label' => $this->__("Current Customer Group"),
            'values' => $customerGroups->load()->toOptionArray(),
            'value' => array_keys($customerGroups->getItems())
        ));

        if ($model->getId()) {
            $form->setValues($model->getDataForForm());
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return TBT_Milestone_Model_Rule
     */
    protected function _getRule()
    {
        return Mage::registry('current_milestone_rule');
    }
}

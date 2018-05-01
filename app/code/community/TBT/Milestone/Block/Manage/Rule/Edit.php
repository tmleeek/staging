<?php

class TBT_Milestone_Block_Manage_Rule_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected function _construct()
    {
        $this->_controller = 'manage_rule';
        $this->_blockGroup = 'tbtmilestone';

        $this->_formScripts[] = "
            var tbtmilestoneRuleNameParts = {
                condition: '',
                action: ''
            };

            var tbtmilestoneUpdateRuleName = function()
            {
                $('name').value = tbtmilestoneRuleNameParts.condition + ', ' + tbtmilestoneRuleNameParts.action;

                return;
            };

            (function() {
                var lstConditionType = $('condition_type'),
                    txtRevenueThreshold = $('condition_details_revenue_threshold'),
                    txtTimeConstraintDuration = $('condition_details_time_constraint_duration'),
                    lstTimeConstraintDurationType = $('condition_details_time_constraint_duration_type');

                var handler = function(event)
                {
                    if (lstConditionType.getValue() != 'revenue') {
                        return;
                    }

                    var revenueThreshold = txtRevenueThreshold.getValue() || 0;
                    var conditionPart = 'Customer Reaches $' + revenueThreshold + ' in Revenue';
                    if (!txtTimeConstraintDuration.disabled) {
                        var timePart = '(within ' + txtTimeConstraintDuration.getValue() + ' ' + lstTimeConstraintDurationType.select('option:selected')[0].text + ')';
                        conditionPart += ' ' + timePart;
                    }

                    tbtmilestoneRuleNameParts.condition = conditionPart;
                    tbtmilestoneUpdateRuleName();
                };

                // TODO: test the 'input' event in IE
                Event.observe(lstConditionType, 'change', handler);
                Event.observe(txtRevenueThreshold, 'input', handler);
                Event.observe(txtTimeConstraintDuration, 'input', handler);
                Event.observe(lstTimeConstraintDurationType, 'change', handler);
            })();

            (function() {
                var lstActionType = $('action_type'),
                    lstCustomerGroupId = $('action_details_customer_group_id');

                var handler = function()
                {
                    if (lstActionType.getValue() != 'customergroup') {
                        return;
                    }

                    var actionPart = 'Move Customer to Group \'' + lstCustomerGroupId.select('option:selected')[0].text + '\'';
                    tbtmilestoneRuleNameParts.action = actionPart;
                    tbtmilestoneUpdateRuleName();
                };

                Event.observe(lstActionType, 'change', handler);
                Event.observe(lstCustomerGroupId, 'change', handler);
            })();

            Event.observe(document, 'dom:loaded', function() {
                var lstConditionType = $('condition_type'),
                    txtRevenueThreshold = $('condition_details_revenue_threshold'),
                    txtTimeConstraintDuration = $('condition_details_time_constraint_duration'),
                    lstTimeConstraintDurationType = $('condition_details_time_constraint_duration_type');
                var lstActionType = $('action_type'),
                    lstCustomerGroupId = $('action_details_customer_group_id');

                var revenueThreshold = txtRevenueThreshold.getValue() || 0;
                var conditionPart = 'Customer Reaches $' + revenueThreshold + ' in Revenue';
                if (!txtTimeConstraintDuration.disabled) {
                    var timePart = '(within ' + txtTimeConstraintDuration.getValue() + ' ' + lstTimeConstraintDurationType.select('option:selected')[0].text + ')';
                    conditionPart += ' ' + timePart;
                }
                tbtmilestoneRuleNameParts.condition = conditionPart;

                var actionPart = 'Move Customer to Group \'' + lstCustomerGroupId.select('option:selected')[0].text + '\'';
                tbtmilestoneRuleNameParts.action = actionPart;

                tbtmilestoneUpdateRuleName();
            });
        ";

        return parent::_construct();
    }

    protected function _prepareLayout()
    {
        $this->_updateButton('save', 'label', $this->__("Save Milestone"));
        $this->_updateButton('delete', 'label', $this->__("Delete Milestone"));

        return parent::_prepareLayout();
    }

    public function getHeaderText()
    {
        $rule = $this->_getRule();
        if ($rule->getId()) {
            return $this->__("Edit Milestone '%s'", $this->htmlEscape($rule->getName()));
        }

        return $this->__("New Milestone");
    }

    /**
     * @return TBT_Milestone_Model_Rule
     */
    protected function _getRule()
    {
        return Mage::registry('current_milestone_rule');
    }
}

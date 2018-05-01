<?php

class TBT_Milestone_Block_Manage_History_View_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    protected function _construct()
    {
        parent::_construct();

        $this->setId('milestoneRuleLogTabs');
        $this->setDestElementId('view_form');
        $this->setTitle(Mage::helper('tbtmilestone')->__('Milestone Information'));

        return $this;
    }

    protected function _beforeToHtml()
    {
        $this->addTab('milestone_history_details_section', array(
            'label'   => Mage::helper('tbtmilestone')->__('General'),
            'title'   => Mage::helper('tbtmilestone')->__('General'),
            'content' => $this->getLayout()->createBlock('tbtmilestone/manage_history_view_tab_general')->toHtml(),
            'active'  => true,
        ));

        $ruleLog = $this->_getCurrentLog();

        if ($ruleLog->getActionType() == 'grantpoints' && $ruleLog->getMilestoneDetails()) {
            $this->addTab('action_executed_section', array(
                'label'   => Mage::helper('tbtmilestone')->__('Points Rewards'),
                'title'   => Mage::helper('tbtmilestone')->__('Points Rewards'),
                'content' => $this->getLayout()->createBlock('tbtmilestone/manage_history_view_tab_transfers')->toHtml(),
                'active'  => false,
            ));
        }

        return parent::_beforeToHtml();
    }

    protected function _getCurrentLog()
    {
        return Mage::registry('current_milestone_rule_log');
    }
}

<?php

class TBT_Milestone_Block_Manage_Grid_Renderer_ActionType extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value           = $this->_getValue($row);
        $actionTypeNames = $this->_getActionFactory()->getTypeNames();
        $name            = $actionTypeNames[$value];

        return $name;
    }

    /**
     * @return TBT_Milestone_Model_Rule_Action_Factory
     */
    protected function _getActionFactory()
    {
        return Mage::getSingleton('tbtmilestone/rule_action_factory');
    }

}

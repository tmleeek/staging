<?php

class TBT_Milestone_Model_Rule_Condition_Factory extends TBT_Milestone_Model_Rule_Factory_Abstract
{
    protected function _getTypeNode()
    {
        return Mage::getConfig()->getNode('tbtmilestone/rule/conditions');
    }

    protected function _isTypeModelValid($model)
    {
        return ($model instanceof TBT_Milestone_Model_Rule_Condition);
    }
}

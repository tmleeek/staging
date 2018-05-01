<?php

class TBT_Milestone_Block_Manage_Grid_Renderer_Milestone_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected $_customerGroups = null;

    public function render(Varien_Object $row)
    {
        $milestoneDetails = $this->_getValue($row);

        // if $milestoneDetails doesn't exist, it's an older version
        if (is_null($milestoneDetails)) {
            return 'Data Not Available.';
        }

        if ($row->getActionType() == 'customergroup') {
            // if action type is 'customergroup'
            $element = $this->_getCustomerGroupName($milestoneDetails['action']['from']) . '  =>  '
                . $this->_getCustomerGroupName($milestoneDetails['action']['to']);

            return $element;
        }

        if ($row->getActionType() == 'grantpoints') {
            // if action type is 'grantpoints'
            $element = Mage::getModel('rewards/points')->setPoints(1, $milestoneDetails['action']['points']);
        }

        return $element;
    }

    protected function _getCustomerGroupName($groupId)
    {
        if (!isset($this->_customerGroups[$groupId])) {
            $customerGroup = Mage::getModel('customer/group')->load($groupId);
            $this->_customerGroups[$groupId] = ($customerGroup->getId()) ? $customerGroup->getCode()
                : 'Customer Group Removed (ID: ' . $groupId . ')' ;

        }

        return $this->_customerGroups[$groupId];
    }
}

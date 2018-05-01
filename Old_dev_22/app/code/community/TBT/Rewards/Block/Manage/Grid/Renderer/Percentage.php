<?php

class TBT_Rewards_Block_Manage_Grid_Renderer_Percentage extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $percentage = $this->_getValue($row);
        return $percentage . '%';
    }
}

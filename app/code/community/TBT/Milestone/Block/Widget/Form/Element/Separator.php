<?php

class TBT_Milestone_Block_Widget_Form_Element_Separator extends Varien_Data_Form_Element_Abstract
{
    /**
     * @see Varien_Data_Form_Element_Abstract::getHtml()
     */
    public function getHtml()
    {
        return "<tr><td colspan='2'><hr style='margin: 20px 0px;' /></td></tr>";
    }
}

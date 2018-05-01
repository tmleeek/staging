<?php

class MDN_Mpm_Block_Widget_Grid_Column_Renderer_Channel extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        return '<img src="http://bms-performance.com/img/channel/'.$row->getChannel().'.png" width="20">';
    }

}
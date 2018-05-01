<?php

class MDN_Mpm_Block_Widget_Grid_Column_Renderer_Rule_Error extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $html = '';

        if ($row->has_error)
            $html .= '<font color="red">'.$row->error.'</font>';

        return $html;
    }

}
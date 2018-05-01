<?php

class MDN_Mpm_Block_Widget_Grid_Column_Filter_Product_Channel extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Abstract
{

    public function getHtml()
    {
        $html = '<table class="emptytable" border="0">';
        $html .= '<tr><th align="center" width="50%">'.$this->__('Best offer').'</th>';
        $html .= '<th align="center" width="50%">'.$this->__('My price').'</th>';
        $html .= '</tr>';
        $html .= '</table>';
        return $html;
    }
}

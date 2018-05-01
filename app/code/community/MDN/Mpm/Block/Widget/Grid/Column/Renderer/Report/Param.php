<?php

class MDN_Mpm_Block_Widget_Grid_Column_Renderer_Report_Param extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $params = $row->getParam();
        $html = '';
        foreach($params as $k => $v)
        {
            $html .= '<b>'.$k.'</b>: '.$v.'<br>';
        }
        return $html;
    }

}
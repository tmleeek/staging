<?php

class MDN_Mpm_Block_Widget_Grid_Column_Renderer_Product_MyOffer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        if ($row->getfinal_price()) {
            $html = '<font color="' . ($row->getmy_rank() == 1 ? 'green' : ($row->getmy_rank() == 2 ? 'orange' : 'red')) . '">';
            $html .= $row->getfinal_price();
            $html .= '<br>#' . $row->getmy_rank();
            $html .= '</font>';
            return $html;
        }
    }

}
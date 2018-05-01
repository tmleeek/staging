<?php
class Tatva_Customerattributes_Block_Adminhtml_Grid_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        if($row->getData($this->getColumn()->getIndex())==""){
            return "";
        }
        else{
            $html = '<img ';
            $html .= 'id="' . $this->getColumn()->getId() . '" ';
            /*$html .= 'width="45" ';
            $html .= 'height="35" ';*/
            $html .= 'src="' . Mage::getBaseUrl("media")."customer" . $row->getData($this->getColumn()->getIndex()) . '"';
            $html .= 'class="grid-image ' . $this->getColumn()->getInlineCss() . '"/>';
            return $html;
        }
    }
}
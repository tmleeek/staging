<?php

class MDN_Mpm_Block_Widget_Grid_Column_Renderer_Link extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $label = $row->getData($this->getColumn()->getIndex());
        $link = $this->getColumn()->getGrid()->getRowLink($this->getColumn()->getIndex(), $row);
        return '<a href="'.$link.'">'.$label.'</a>';
    }

    public function renderExport(Varien_Object $row)
    {
        return $row->getData($this->getColumn()->getIndex());
    }

}
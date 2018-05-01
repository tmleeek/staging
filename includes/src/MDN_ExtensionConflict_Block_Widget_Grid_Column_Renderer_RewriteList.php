<?php

class MDN_ExtensionConflict_Block_Widget_Grid_Column_Renderer_RewriteList
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $html = '';
        $list = explode(',',$row->getec_rewrite_classes());
        foreach ($list as $class){
            $html .= $class.'<br/>';
        }
        return $html;
    }


}
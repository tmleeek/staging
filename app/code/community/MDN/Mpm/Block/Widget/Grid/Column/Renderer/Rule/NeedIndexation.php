<?php

class MDN_Mpm_Block_Widget_Grid_Column_Renderer_Rule_NeedIndexation extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $rule)
    {
        if(strtotime($rule->updated_at) > strtotime($rule->last_indexation)) {
            $html = '<span style="color:red">'.Mage::helper('Mpm')->__('Reindex required').'</span>';
        } else {
            $html = '<span style="color:green">'.Mage::helper('Mpm')->__('Indexed').'</span>';
        }

        return $html;
    }

}
<?php

class MDN_Mpm_Block_Widget_Grid_Column_Renderer_Rule_Content extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $html = '';
        foreach($row->variablesTranslated as $field) {
            $field->key = str_replace('attributes.global.', '', $field->key);
            if (in_array($field->key, array('error', 'has_error')))
                continue;
            $html.= $field->key.' : '.$field->value.'<br />';
        }
        return $html;
    }

}
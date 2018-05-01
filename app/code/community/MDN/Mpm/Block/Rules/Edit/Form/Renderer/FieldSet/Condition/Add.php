<?php

/**
 * Class MDN_Mpm_Block_Rules_Edit_Form_Renderer_FieldSet_Condition_Add
 *
 * @author    Nicolas Mugnier <contact@boostmyshop.com>
 * @copyright 2015-2016 BoostMyShop (http://www.boostmyshop.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Mpm_Block_Rules_Edit_Form_Renderer_FieldSet_Condition_Add extends MDN_Mpm_Block_Rules_Edit_Form_Renderer_FieldSet_Base {

    /**
     * @return string $html
     */
    public function getElementHtml()
    {
        $html = '';
        $html .= '<table style="width:100%;"><tbody>';
        $html .= '<tr><td style="width:15%;">Add New Condition</td><td style="30%;">'.$this->_getButton().$this->_getSelect().'</td></tr>';
        $html .= '</tbody></table>';

        return $html;
    }

    /**
     * @return string
     */
    protected function _getButton(){
        return '<button id="show-condition-list" onclick="Condition.show(this);" type="button" class="scalable add"><span><span>Add new condition</span></span></button>';
    }

    /**
     * @return string $html
     */
    protected function _getSelect(){

        $html = ' <select id="condition-list" style="display:none;" onchange="Condition.add(this.value);">';
        foreach($this->getUsableFields() as $key => $label){
            $html .= '<option value="'.$key.'">'.$label.'</option>';
        }
        $html .= '</select>';

        return $html;

    }

    /**
     * @param string $fieldName
     * @return array|null
     */
    protected function getUsableFields($fieldName = null)
    {
        $fields = array('');
        foreach($this->getRule()->getRuleConditions() as $field) {
            $fields[$field->name] = $field->translation->name;
        }

        if($fieldName !== null) {
            return isset($fields[$fieldName]) ? $fields[$fieldName] : null;
        }

        return $fields;
    }

}
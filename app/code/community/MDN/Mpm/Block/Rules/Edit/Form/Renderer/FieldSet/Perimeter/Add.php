<?php

/**
 * Class MDN_Mpm_Block_Rules_Edit_Form_Renderer_FieldSet_Perimter_Add
 *
 * @author    Nicolas Mugnier <contact@boostmyshop.com>
 * @copyright 2015-2016 BoostMyShop (http://www.boostmyshop.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Mpm_Block_Rules_Edit_Form_Renderer_FieldSet_Perimeter_Add extends MDN_Mpm_Block_Rules_Edit_Form_Renderer_FieldSet_Base {

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
        return '<button id="show-perimeter-list" onclick="Perimeter.show(this);" type="button" class="scalable add"><span><span>Add new condition</span></span></button>';
    }

    /**
     * @return string $html
     */
    protected function _getSelect(){

        $html = ' <select id="perimeter-list" style="display:none;" onchange="Perimeter.add(this.value);">';
        foreach($this->getUsableFields() as $key => $label){
            $html .= '<option value="'.$key.'">'.$label.'</option>';
        }
        $html .= '</select>';

        return $html;

    }

    /**
     * @return array $fields
     */
    protected function getUsableFields()
    {
        $fields = array('');
        foreach($this->getRule()->perimeter_conditions as $field) {
            $label = str_replace('attributes.global.', '', $field->name);
            $fields[$field->name] = $label;
        }

        return $fields;
    }

}
<?php

class MDN_Mpm_Block_Rules_Edit_Form_Renderer_FieldSet_Base extends Varien_Data_Form_Element_Abstract
{

    protected $fieldType = '';

    public function getRule()
    {
        return Mage::registry('current_rule');
    }

    protected function getFieldRenderer($fieldName, $values = null)
    {
        $field = $this->getFieldByName($fieldName);
        switch($field->type) {
            case 'string':
                $withTokenInput = array('offers.bbw_by_price','offers.bbw');

                if(in_array($field->name, $withTokenInput)){

                    return $this->renderTextWithTokenInput($field->name, $values, Mage::helper('Mpm')->__('Contains'));

                }else{

                    return $this->renderText($field->name, $values, Mage::helper('Mpm')->__('Contains'));

                }

            case 'enum':
                return $this->renderSelect($field->name, $field->values, $values, true);
            case 'boolean':
                return $this->renderSelect($field->name, $this->getYesNo(), $values, false);
            case 'numeric':
                return $this->renderNumeric($field->name, $values);
        }
    }

    protected function renderText($field, $values, $label = 'In')
    {
        $html = $label.' <input type="text" size="50" name="'.$this->fieldType.'['.$field.']" value="'.$values.'" >';

        return $html;
    }

    protected function renderTextWithTokenInput($field, $values, $label = 'In'){

        $id = str_replace('.', '-', $field);
        $html = '<input class="input-text" type="text" id="'.$id.'" name="'.$this->fieldType.'['.$field.']"/>
                <script type="text/javascript">
                    $J(document).ready(function(){

                        $J("#'.$id.'").tokenInput("'.Mage::Helper('adminhtml')->getUrl('adminhtml/Mpm_Seller/tokenInput').'", {
                            theme: "facebook",
                            prePopulate: '.$this->getPrePopulate($values).',
                            hintText: "",
                            preventDuplicates: true,
                            tokenLimit: 1
                        });

                    });

                </script>';

        return $html;

    }

    public function getPrePopulate($sellerId){

        $prePopulate = array();

        if(!empty($sellerId)){

            $sellers = json_decode(Mage::Helper('Mpm/Seller')->getSellersAsJson(), true);
            foreach($sellers as $seller){
                list($channel, $id) = explode(':', $seller['id']);
                if($id == $sellerId){

                    $prePopulate[] = $seller;

                }

            }

        }

        return json_encode($prePopulate);

    }

    protected function renderSelect($field, $allValues, $selectedValues, $isMulti)
    {
        $html = '<select style="width: 500px;" name="'.$this->fieldType.'['.$field.']'.($isMulti ? '[]' : '').'" '.($isMulti ? 'multiple="multiple" size="7"' : '').'>';
        foreach($allValues as $value) {
            if (is_array($selectedValues))
                $selected = in_array($value, $selectedValues);
            else
                $selected = ($selectedValues == $value);

            $html .= '<option value="'.$value.'" '.($selected ? ' selected ' : '').'>'.$value.'</option>';
        }
        $html .= '</select>';

        return $html;
    }

    protected function renderSelectKeyValue($field, $allValues, $selectedValues, $isMulti)
    {
        $html = '<select style="width: 500px;" name="'.$this->fieldType.'['.$field.']'.($isMulti ? '[]' : '').'" '.($isMulti ? 'multiple="multiple" size="7"' : '').'>';
        foreach($allValues as $value => $label) {
            if (is_array($selectedValues))
                $selected = in_array($value, $selectedValues);
            else
                $selected = ($selectedValues == $value);

            $html .= '<option value="'.$value.'" '.($selected ? ' selected ' : '').'>'.Mage::Helper('Mpm')->__($label).'</option>';
        }
        $html .= '</select>';

        return $html;
    }

    protected function renderNumeric($field, $values)
    {
        if(!empty($values) && strpos($values, '..') !== false) {
            list($from, $to) = explode('..', $values);
            $values = array(
                'from' => $from,
                'to' => $to,
            );
        }

        $namePrefix = ''.$this->fieldType.'['.$field.']';
        $from = (isset($values['from']) ? $values['from'] : '');
        $to = (isset($values['to']) ? $values['to'] : '');
        $html = Mage::Helper('Mpm')->__('Between').' <input type="text" size="15" value="'.$from.'" name="'.$namePrefix.'[from]"> '.Mage::Helper('Mpm')->__('and').' <input type="text" size="15"  value="'.$to.'" name="'.$namePrefix.'[to]">';
        $html .= '<br><i>Note : '.Mage::Helper('Mpm')->__('Intervals support only whole numbers (12 instead of 12.1 for instance). If you configure \'between 0 and 12\', it will include every numbers between 0 and 12.99').'</i>';

        return $html;
    }

    protected function getYesNo()
    {
        $result = array();
        $result[] = array('value' => '0', 'label' => Mage::Helper('Mpm')->__('No'));
        $result[] = array('value' => '1', 'label' => Mage::Helper('Mpm')->__('Yes'));
        return $result;
    }

    public static function sortFields($a, $b)
    {
        $al = strtolower($a['label']);
        $bl = strtolower($b['label']);
        if ($al == $bl) {
            return 0;
        }
        return ($al > $bl) ? 1 : -1;
    }

}
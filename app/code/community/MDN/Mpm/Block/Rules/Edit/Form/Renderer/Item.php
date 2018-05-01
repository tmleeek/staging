<?php

/**
 * Class MDN_Mpm_Block_Rules_Edit_Form_Renderer_Item
 *
 * @author    Nicolas Mugnier <contact@boostmyshop.com>
 * @copyright 2015-2016 BoostMyShop (http://www.boostmyshop.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class MDN_Mpm_Block_Rules_Edit_Form_Renderer_Item extends Mage_Core_Block_Template {

    protected $fieldType;

    abstract protected function getFieldByName($fieldName);

    /**
     * @return mixed
     */
    public function getRule()
    {
        return Mage::registry('current_rule');
    }

    /**
     * @param string $fieldName
     * @param string $values
     * @return string
     */
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

    /**
     * @param string $field
     * @param string $values
     * @param string $label
     * @return string
     */
    protected function renderText($field, $values, $label = 'In')
    {
        $html = $label.' <input type="text" size="50" name="'.$this->fieldType.'['.$field.']" value="'.$values.'" >';

        return $html;
    }

    /**
     * @param string $field
     * @param string $values
     * @param string $label
     * @return string
     */
    protected function renderTextWithTokenInput($field, $values, $label = 'In'){

        $id = str_replace('.', '-', $field);
        $html = '<input class="input-text" type="text" id="'.$id.'" name="'.$this->fieldType.'['.$field.']"/>
                <script type="text/javascript">
                    jQuery().ready(function($){

                        $("#'.$id.'").tokenInput("'.Mage::Helper('adminhtml')->getUrl('adminhtml/Mpm_Seller/tokenInput').'", {
                            theme: "facebook",
                            prePopulate: '.$this->getPrePopulate($values, true).',
                            hintText: "",
                            preventDuplicates: true,
                            tokenLimit: 1
                        });

                    });

                </script>';

        return $html;

    }

    /**
     * @param string $sellerId
     * @param boolean $onlyFirst
     * @return string
     */
    public function getPrePopulate($sellerId, $onlyFirst = false){

        $prePopulate = array();

        if(!empty($sellerId)){

            $sellers = json_decode(Mage::Helper('Mpm/Seller')->getSellersAsJson(), true);
            foreach($sellers as $seller){
                list($channel, $id) = explode(':', $seller['id']);
                if($id == $sellerId){

                    $prePopulate[] = $seller;
                    if($onlyFirst === true)
                        break;

                }

            }

        }

        return json_encode($prePopulate);

    }

    /**
     * @param string $field
     * @param array $allValues
     * @param mixed $selectedValues
     * @param boolean $isMulti
     * @return string
     */
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

    /**
     * @param string $field
     * @param array $allValues
     * @param mixed $selectedValues
     * @param boolean $isMulti
     * @return string
     */
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

    /**
     * @param string $field
     * @param mixed $values
     * @return string
     */
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

    /**
     * @return array $result
     */
    protected function getYesNo()
    {
        $result = array();
        $result[] = array('value' => '0', 'label' => Mage::Helper('Mpm')->__('No'));
        $result[] = array('value' => '1', 'label' => Mage::Helper('Mpm')->__('Yes'));
        return $result;
    }

    /**
     * @param string $a
     * @param string $b
     * @return int
     */
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
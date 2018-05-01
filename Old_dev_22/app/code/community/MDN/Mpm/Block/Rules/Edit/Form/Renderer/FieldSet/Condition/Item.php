<?php

/**
 * Class MDN_Mpm_Block_Rules_Edit_Form_Renderer_FieldSet_Condition_Item
 *
 * @author    Nicolas Mugnier <contact@boostmyshop.com>
 * @copyright 2015-2016 BoostMyShop (http://www.boostmyshop.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Mpm_Block_Rules_Edit_Form_Renderer_FieldSet_Condition_Item extends MDN_Mpm_Block_Rules_Edit_Form_Renderer_Item {

    /**
     * @var string
     */
    protected $fieldType = 'condition';

    /**
     * @var string
     */
    protected $_template = "Mpm/Rules/Condition/Item.phtml";

    /**
     * @var string
     */
    protected $_field;

    /**
     * @var string
     */
    protected $_value;

    /**
     * @param string $value
     * @return $this
     */
    public function setValue($value){
        $this->_value = $value;
        return $this;
    }

    /**
     * @param string $field
     * @return $this
     */
    public function setField($field){
        $this->_field = $field;
        return $this;
    }

    /**
     * @return string
     */
    public function getFieldName(){

        return Mage::Helper('Mpm')->__($this->getUsableFields($this->_field));

    }

    /**
     * @return string
     */
    public function getConditionId(){

        return $this->_field;

    }

    /**
     * @return string
     */
    public function renderField(){

        return $this->getFieldRenderer($this->_field, $this->_value);

    }

    /**
     * @param string $fieldName
     * @return string
     */
    protected function getFieldByName($fieldName)
    {
        foreach($this->getRule()->getRuleConditions() as $condition) {
            if($condition->name === $fieldName) {
                return $condition;
            }
        }
        return null;
    }

    /**
     * @param null|string $fieldName
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
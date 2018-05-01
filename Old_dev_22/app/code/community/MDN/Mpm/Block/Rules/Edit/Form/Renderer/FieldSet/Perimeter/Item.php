<?php

/***
 * Class MDN_Mpm_Block_Rules_Edit_Form_Renderer_FieldSet_Perimeter_Item
 *
 * @author    Nicolas Mugnier <contact@boostmyshop.com>
 * @copyright 2015-2016 BoostMyShop (http://www.boostmyshop.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Mpm_Block_Rules_Edit_Form_Renderer_FieldSet_Perimeter_Item extends MDN_Mpm_Block_Rules_Edit_Form_Renderer_Item {

    protected $fieldType = 'perimeter';

    /**
     * @var string
     */
    protected $_template = "Mpm/Rules/Perimeter/Item.phtml";

    /**
     * @return string
     */
    public function getFieldName(){

        return str_replace('attributes.global.', '', $this->getField());

    }

    /**
     * @return string
     */
    public function getConditionId(){

        return $this->getField();

    }

    /**
     * @return string
     */
    public function renderField(){

        return $this->getFieldRenderer($this->getField(), $this->getValue());

    }

    /**
     * @param string $fieldName
     * @return mixed
     */
    protected function getFieldByName($fieldName)
    {
        foreach($this->getRule()->getPerimeterConditions() as $condition) {
            if($condition->name === $fieldName) {
                return $condition;
            }
        }
    }

}
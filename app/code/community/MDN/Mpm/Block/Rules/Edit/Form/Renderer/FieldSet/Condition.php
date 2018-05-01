<?php

/**
 * Class MDN_Mpm_Block_Rules_Edit_Form_Renderer_FieldSet_Condition
 *
 * @author    Nicolas Mugnier <contact@boostmyshop.com>
 * @copyright 2015-2016 BoostMyShop (http://www.boostmyshop.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Mpm_Block_Rules_Edit_Form_Renderer_FieldSet_Condition extends MDN_Mpm_Block_Rules_Edit_Form_Renderer_FieldSet_Base
{

    /**
     * @var string
     */
    protected $fieldType = 'condition';

    /**
     * @return string $html
     */
    public function getElementHtml()
    {

        $condition = $this->getRule()->condition;
        if((Mage::registry('new_condition_field'))) {
            $condition[Mage::registry('new_condition_field')] = '';
        }

        $html = '<div id="rule-conditions">';
        foreach($condition as $field => $value)
        {
            $block = Mage::app()->getLayout()->createBlock('Mpm/Rules_Edit_Form_Renderer_FieldSet_Condition_Item');
            $block->setField($field);
            $block->setValue($value);
            $html .= $block->toHtml();
        }

        $html .= '</div>';

        return $html;
    }

}
<?php

/**
 * Class MDN_Mpm_Block_Rules_Edit_Form_Renderer_FieldSet_Perimeter
 *
 * @author    Nicolas Mugnier <contact@boostmyshop.com>
 * @copyright 2015-2016 BoostMyShop (http://www.boostmyshop.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Mpm_Block_Rules_Edit_Form_Renderer_FieldSet_Perimeter extends MDN_Mpm_Block_Rules_Edit_Form_Renderer_FieldSet_Base
{

    /**
     * @var string
     */
    protected $fieldType = 'perimeter';

    /**
     * @return string $html
     */
    public function getElementHtml()
    {
        $perimeter = $this->getRule()->perimeter;
        if((Mage::registry('new_perimeter_field'))) {
            $perimeter[Mage::registry('new_perimeter_field')] = '';
        }

        $html = '<div id="product-conditions">';
        foreach($perimeter as $field => $value)
        {
            $block = Mage::app()->getLayout()->createBlock('Mpm/Rules_Edit_Form_Renderer_FieldSet_Perimeter_Item');
            $block->setField($field);
            $block->setValue($value);
            $html .= $block->toHtml();
        }

        $html .= '</div>';

        return $html;
    }


}
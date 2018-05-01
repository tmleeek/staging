<?php

/**
 * Class MDN_Mpm_Block_Rules_Edit_Tab_Perimeters
 *
 * @author    Nicolas Mugnier <contact@boostmyshop.com>
 * @copyright 2015-2016 BoostMyShop (http://www.boostmyshop.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Mpm_Block_Rules_Edit_Tab_Perimeters extends MDN_Mpm_Block_Rules_Edit_Tab_Abstract {

    protected function _prepareLayout()
    {
        $form = new Varien_Data_Form();

        $addPerimeterSet = $form->addFieldSet('add_perimeter', array('legend' => 'Add new condition'));
        $addPerimeterSet->addType('add_perimeter', 'MDN_Mpm_Block_Rules_Edit_Form_Renderer_FieldSet_Perimeter_Add');
        $addPerimeterSet->addField('add_perimeter_field', 'add_perimeter', array('label' => '', 'name' => ''));

        $perimeterSet = $form->addFieldset('rule_perimeter', array('legend' => Mage::helper('Mpm')->__('Conditions for products'), 'class' => 'fieldset-wide'));
        $perimeterSet->addType('perimeter', 'MDN_Mpm_Block_Rules_Edit_Form_Renderer_FieldSet_Perimeter');
        $perimeterSet->addField('perimeter_field_id', 'perimeter', array('label' => 'Conditions', 'name' => 'perimeter_field_name'));

        $this->setForm($form);
        parent::_prepareLayout();
    }

}
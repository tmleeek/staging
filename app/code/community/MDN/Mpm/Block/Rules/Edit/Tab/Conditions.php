<?php

/**
 * Class MDN_Mpm_Block_Rules_Edit_Tab_Conditions
 *
 * @author    Nicolas Mugnier <contact@boostmyshop.com>
 * @copyright 2015-2016 BoostMyShop (http://www.boostmyshop.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Mpm_Block_Rules_Edit_Tab_Conditions extends MDN_Mpm_Block_Rules_Edit_Tab_Abstract {

    protected function _prepareLayout()
    {
        $form = new Varien_Data_Form();

        $addPerimeterSet = $form->addFieldSet('add_condition', array('legend' => 'Add new condition'));
        $addPerimeterSet->addType('add_condition', 'MDN_Mpm_Block_Rules_Edit_Form_Renderer_FieldSet_Condition_Add');
        $addPerimeterSet->addField('add_condition_field', 'add_condition', array('label' => '', 'name' => ''));

        $perimeterSet = $form->addFieldSet('rule_condition', array('legend' => Mage::helper('Mpm')->__('Conditions on offers'), 'class' => 'fieldset-wide'));
        $perimeterSet->addType('condition', 'MDN_Mpm_Block_Rules_Edit_Form_Renderer_FieldSet_Condition');
        $perimeterSet->addField('condition_field_id', 'condition', array('label' => '', 'name' => 'condition_field_name'));

        $this->setForm($form);
        parent::_prepareLayout();
    }

}
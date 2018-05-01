<?php

/**
 * Class MDN_Mpm_Block_Rules_Edit_Tab_Details
 *
 * @author    Nicolas Mugnier <contact@boostmyshop.com>
 * @copyright 2015-2016 BoostMyShop (http://www.boostmyshop.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Mpm_Block_Rules_Edit_Tab_Details extends MDN_Mpm_Block_Rules_Edit_Tab_Abstract {

    protected function _prepareLayout()
    {

        $form = new Varien_Data_Form();

        $values = $this->getRule()->getData();
        $fieldset = $form->addFieldset('rule_details', array('legend' => Mage::helper('Mpm')->__('Rules Details'), 'class' => 'fieldset-wide'));
        if(($this->getRule()->getId())) {
            $fieldset->addField('id', 'hidden', array('required'  => true, 'name' => 'id'));
        }

        $fieldset->addField('name', 'text', array(
            'label'     => Mage::helper('Mpm')->__('Name'),
            'required'  => true,
            'name'      => 'name'
        ));

        $fieldset->addField('type', 'hidden', array(
            'label'     => Mage::helper('Mpm')->__('Type'),
            'name'      => 'type'
        ));

        $fieldset->addField('enable', 'select', array(
            'label'     => Mage::helper('Mpm')->__('Enabled'),
            'required'  => true,
            'values'    => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
            'name'      => 'enable'
        ));

        $fieldset->addField('priority', 'select', array(
            'label'     => Mage::helper('Mpm')->__('Priority'),
            'required'  => true,
            'name'      => 'priority',
            'values'    => $this->getPriorityValues(),
            'note'      => Mage::helper('Mpm')->__('If several rules are available for one product, the rule with the highest priority will be used')
        ));

        $fieldset->addField('last_indexation', 'label', array(
            'label'     => Mage::helper('Mpm')->__('Last index date'),
            'name'      => 'last_indexation'
        ));


        if (isset($values['variables']['has_error'])) {
            $fieldset->addField('error', 'label', array(
                'label' => Mage::helper('Mpm')->__('Error'),
                'name' => 'error'
            ));
        }

        $this->setForm($form);
        parent::_prepareLayout();
    }

    /**
     * @return array
     */
    protected function getPriorityValues()
    {
        $values = array();
        for($i=1;$i<=99;$i++) {
            $values[$i] = $i;
        }
        return $values;
    }

}
<?php

/**
 * Class MDN_Mpm_Block_Rules_Edit_Tab_Abstract
 *
 * @author    Nicolas Mugnier <contact@boostmyshop.com>
 * @copyright 2015-2016 BoostMyShop (http://www.boostmyshop.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class MDN_Mpm_Block_Rules_Edit_Tab_Abstract extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * @return mixed
     */
    public function getRule()
    {
        return Mage::registry('current_rule');
    }

    /**
     * @param array $values
     * @return array
     */
    protected function adaptValues(array $values)
    {
        foreach($values as $field => $value) {
            if(is_string($value) && preg_match('/^[0-9]+\.\.[0-9]+$/', $value)) {
                $data = explode('..', $value);
                $values[$field] = array();
                $values[$field]['from'] = $data[0];
                $values[$field]['to'] = $data[1];
            } elseif(in_array($value, array('on', 'off'))) {
                $values[$field] = $value === 'on' ? 1 : 0;
            }
        }

        return $values;
    }

    protected function _prepareLayout()
    {

        $values = $this->getRule()->getData();

        unset($values['variable_conditions']);
        unset($values['perimeter_conditions']);
        unset($values['rule_conditions']);
        if(isset($values['variables']['ignore_sellers']) && !Mage::registry('ignore_sellers_values')) {
            Mage::register('ignore_sellers_values', $values['variables']['ignore_sellers']);
        }

        $keywords = array('perimeter', 'condition');
        foreach($keywords as $keyword) {
            foreach($values[$keyword] as $field => $value) {
                $values[$keyword.'['.$field.']'] = is_array($value) ? implode(',', $value) : $value;
            }
        }
        unset($values['perimeter'], $values['condition']);

        foreach($values['variables'] as $field => $value) {
            $values[$field] = $value;
        }
        unset($values['variables']);

        $this->_form->setValues($this->adaptValues($values));

    }

}
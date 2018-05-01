<?php

/**
 * Class MDN_Mpm_Block_Rules_Edit_Tab_Variables
 *
 * @author    Nicolas Mugnier <contact@boostmyshop.com>
 * @copyright 2015-2016 BoostMyShop (http://www.boostmyshop.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Mpm_Block_Rules_Edit_Tab_Variables extends MDN_Mpm_Block_Rules_Edit_Tab_Abstract {

    protected function _prepareLayout()
    {

        $variables = $this->getRule()->getVariableConditions();
        $ruleType = $this->getRule()->getType();

        if(empty($variables)){
            return;
        }

        $form = new Varien_Data_Form();

        $contentSet = $form->addFieldset('rule_content', array('legend' => Mage::helper('Mpm')->__('Variables'), 'class' => 'fieldset-wide'));
        foreach($variables as $variable) {

            $type = $this->getFieldType($variable);
            $values = $this->getFieldValues($variable, $ruleType);

            $label = ($ruleType == 'adjustment' && $variable->name == 'ignore_shipped_by_marketplace') ? 'Ignore FBA offers (Delivery by Amazon)' : $variable->translation->name;
            $note = $variable->translation->name;

            if($ruleType === 'adjustment' && $variable->name === 'ignore_sellers') {
                $contentSet->addType('seller', 'MDN_Mpm_Block_Rules_Edit_Form_Renderer_Type_Seller');
                $contentSet->addField($variable->name, 'seller', array(
                    'label'     =>  $this->getFieldLabelByRuleType($label, $ruleType),
                    'required'  => true,
                    'name'      => $variable->name,
                    'values'    => $values,
                    'note'   => $this->getNoteForFieldByRuleType($note, $ruleType)
                ));
                continue;
            }

            $contentSet->addField($variable->name, $type, array(
                'label'     =>  $this->getFieldLabelByRuleType($label, $ruleType),
                'required'  => true,
                'name'      => $variable->name,
                'values'    => $values,
                'note'   => $this->getNoteForFieldByRuleType($note, $ruleType)
            ));
        }

        $this->setForm($form);
        parent::_prepareLayout();
    }

    /**
     * @param $variableName
     * @param $ruleType
     * @return mixed
     */
    protected function getFieldLabelByRuleType($variableName, $ruleType){

        $labels = array(
            'cost_shipping' => array(
                'method' => $this->__('shipping grid')
            ),
            'tax_rate' => array(
                'formula' => $this->__('percent')
            ),
            'enable' => array(
                'behaviour' => $this->__('method')
            )
        );

        if(isset($labels[$ruleType][$variableName])){
            $label = $labels[$ruleType][$variableName];
        }else{
            $label = $this->__($variableName);
        }

        return $label;

    }

    /**
     * @param $variableName
     * @param $ruleType
     * @return mixed
     */
    protected function getNoteForFieldByRuleType($variableName, $ruleType){

        $notes = array(
            'cost_shipping' => array(
                'method' => $this->__('coefficent to apply on shipping grid')
            ),
            'tax_rate' => array(
                'formula' => $this->__('percent (20 for 20 %)')
            ),
            'price_without_competitor' => array(
                'value' => $this->__('value / percent')
            ),
            'min_price' => array(
                'formula' => $this->__('Price calculation')
            ),
            'max_price' => array(
                'formula' => $this->__('Price calculation')
            ),
            'enable' => array(
                'behaviour' => $this->__('method')
            )
        );

        if(isset($notes[$ruleType][$variableName])){
            $note = $notes[$ruleType][$variableName];
        }else{
            $note = $this->__($variableName);
        }

        return $note;

    }

    /**
     * @param $field
     * @return string
     */
    private function getFieldType($field)
    {
        switch($field->type) {
            case 'enum':
                $type = 'select';
                break;
            case 'numeric':
                $type = 'text';
                break;
            case 'string':
                $type = 'text';
                break;
            case 'boolean':
                $type = 'select';
                break;
        }

        return $type;
    }

    /**
     * @param $field
     * @param $ruleType
     * @return array|null
     */
    private function getFieldValues($field, $ruleType)
    {
        $values = null;
        if($field->type === 'enum') {
            $values = array();
            foreach ($field->values as $key => $value) {
                $label          = $field->translation->values[$key];
                $values[$value] = Mage::Helper('Mpm')->__($label);
            }
        } elseif($field->type === 'boolean') {
            $values = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();
        }

        return $values;
    }

}
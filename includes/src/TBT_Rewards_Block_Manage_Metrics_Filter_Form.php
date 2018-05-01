<?php

class TBT_Rewards_Block_Manage_Metrics_Filter_Form extends Mage_Adminhtml_Block_Report_Filter_Form
{
    /**
     * Report field visibility
     */
    protected $_fieldVisibility = array();

    /**
     * Report field opions
     */
    protected $_fieldOptions = array();

    /**
     * Extend Magento report filter form and add option to select point transfer statuses.
     *
     * @return TBT_Rewards_Block_Manage_Metrics_Filter_Form
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        $form         = $this->getForm();
        $htmlIdPrefix = 'rewards_metrics_';
        $form->setHtmlIdPrefix($htmlIdPrefix);

        // disable unneeded fields
        $this->setFieldVisibility('show_order_statuses', 0);
        $this->setFieldVisibility('order_statuses', 0);

        /** @var Varien_Data_Form_Element_Fieldset $fieldset */
        $fieldset = $this->getForm()->getElement('base_fieldset');

        if (is_object($fieldset) && $fieldset instanceof Varien_Data_Form_Element_Fieldset) {
            $fieldset->addField('show_transfer_statuses', 'select', array(
                'name'    => 'show_transfer_statuses',
                'label'   => Mage::helper('rewards')->__('Points Status'),
                'options' => array(
                    '0' => Mage::helper('rewards')->__('Any'),
                    '1' => Mage::helper('rewards')->__('Specified'),
                ),
                'note'    => Mage::helper('rewards')->__('Optionally, limit to certain point transfer statuses.'),
            ), 'to');

            $values = Mage::getModel('rewards/transfer_status')->genSelectableStatuses();

            $fieldset->addField('transfer_statuses', 'multiselect', array(
                'name'    => 'transfer_statuses',
                'values'  => $values,
                'display' => 'none'
            ), 'show_transfer_statuses');

            // define field dependencies
            if ($this->getFieldVisibility('show_transfer_statuses') && $this->getFieldVisibility('transfer_statuses')) {
                $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
                    ->addFieldMap("{$htmlIdPrefix}show_transfer_statuses", 'show_transfer_statuses')
                    ->addFieldMap("{$htmlIdPrefix}transfer_statuses", 'transfer_statuses')
                    ->addFieldDependence('transfer_statuses', 'show_transfer_statuses', '1')
                );
            }
        }

        return $this;
    }

    /**
     * Part below is copied from parent (Mage_Adminhtml_Block_Report_Filter_Form) for compatibility with lower
     * version of Magento.
     */

    /**
     * Set field visibility
     *
     * @param string Field id
     * @param bool Field visibility
     */
    public function setFieldVisibility($fieldId, $visibility)
    {
        $this->_fieldVisibility[$fieldId] = (bool)$visibility;
    }

    /**
     * Get field visibility
     *
     * @param string Field id
     * @param bool Default field visibility
     * @return bool
     */
    public function getFieldVisibility($fieldId, $defaultVisibility = true)
    {
        if (!array_key_exists($fieldId, $this->_fieldVisibility)) {
            return $defaultVisibility;
        }
        return $this->_fieldVisibility[$fieldId];
    }

    /**
     * Set field option(s)
     *
     * @param string $fieldId Field id
     * @param mixed $option Field option name
     * @param mixed $value Field option value
     */
    public function setFieldOption($fieldId, $option, $value = null)
    {
        if (is_array($option)) {
            $options = $option;
        } else {
            $options = array($option => $value);
        }
        if (!array_key_exists($fieldId, $this->_fieldOptions)) {
            $this->_fieldOptions[$fieldId] = array();
        }
        foreach ($options as $k => $v) {
            $this->_fieldOptions[$fieldId][$k] = $v;
        }
    }

    /**
     * Initialize form fileds values
     * Method will be called after prepareForm and can be used for field values initialization
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _initFormValues()
    {
        $data = $this->getFilterData()->getData();
        foreach ($data as $key => $value) {
            if (is_array($value) && isset($value[0])) {
                $data[$key] = explode(',', $value[0]);
            }
        }
        $this->getForm()->addValues($data);
        return parent::_initFormValues();
    }

    /**
     * This method is called before rendering HTML
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _beforeToHtml()
    {
        $result = parent::_beforeToHtml();

        /** @var Varien_Data_Form_Element_Fieldset $fieldset */
        $fieldset = $this->getForm()->getElement('base_fieldset');

        if (is_object($fieldset) && $fieldset instanceof Varien_Data_Form_Element_Fieldset) {
            // apply field visibility
            foreach ($fieldset->getElements() as $field) {
                if (!$this->getFieldVisibility($field->getId())) {
                    $fieldset->removeField($field->getId());
                }
            }
            // apply field options
            foreach ($this->_fieldOptions as $fieldId => $fieldOptions) {
                $field = $fieldset->getElements()->searchById($fieldId);
                /** @var Varien_Object $field */
                if ($field) {
                    foreach ($fieldOptions as $k => $v) {
                        $field->setDataUsingMethod($k, $v);
                    }
                }
            }
        }

        return $result;
    }
}

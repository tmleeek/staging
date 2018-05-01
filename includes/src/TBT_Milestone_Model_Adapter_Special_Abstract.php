<?php

/**
 * This is the base class for the adapter which ties the Rewards_Special UI to the Milestone rule system.
 * This implementation adds exactly one extra text field to the triggers fieldset of the Rewards_Special rule system.
 *
 */
abstract class TBT_Milestone_Model_Adapter_Special_Abstract extends TBT_Rewards_Model_Special_Configabstract
{
    protected $_conditionCode = null;

    /**
     * @return label to render in the UI for this rule. Child class must implement.
     */
    abstract public function getConditionLabel();

    /**
     * The condition class name.
     * @example "referral", "orders"
     * @return $string
     */
    public function getConditionClassName()
    {
        $condition = str_replace("tbt_milestone_model_adapter_special_", "", strtolower(get_class($this)));
        return $condition;
    }

    /**
     * @return string condition code in the form of "tbtmilestone_{condition}"
     */
    public function getConditionCode()
    {
       if (!isset($this->_conditionCode)){
           $condition = $this->getConditionClassName();
           $this->_conditionCode = "tbtmilestone_{$condition}";
       }

       return  $this->_conditionCode;
    }

    /**
     * Generates Trigger entry in the interface
     * @see TBT_Rewards_Model_Special_Configabstract::getNewCustomerConditions()
     */
    public function getNewCustomerConditions()
    {
        return array(
             $this->getConditionCode() => $this->getConditionLabel(),
        );
    }

    public function getFieldComments()
    {
        $configLabel = $this->_getFieldConfigurationLabel();
        $configLink = Mage::helper("adminhtml")->getUrl("adminhtml/system_config/edit/section/rewards/milestone");
        if (!empty($configLabel)){
            return "This counts when <strong>{$configLabel}</strong>.<br/>
            <i>You can <a href='{$configLink}' target='_blank'>change this setting</a>.</i>";
        }
    }

    /**
     * Used to insert extra fields into the triggers section of the form UI
     *
     * @param $fieldset Varien_Data_Form_Element_Fieldset
     * @return self
     */
    public function visitAdminTriggers(&$fieldset)
    {
        $element = $fieldset->addField($this->getConditionCode(), 'text', array(
                'name'                => $this->getConditionCode(),
                'required'            => $this->_isFieldRequired(),
                'class'               => ($this->_isFieldPositiveNumber() ? "validate-greater-than-zero " : "").
                                         ($this->_isFieldInteger() ? "validate-digits " : ""),
                'label'               => $this->getFieldLabel(),
                'after_element_html'  => "<p class='note'><span>{$this->getFieldComments()}</span></p>",
        ));
        Mage::getSingleton('rewards/wikihints')->addWikiHint($element, "26701933", "Sweet Tooth Milestones" );

        return $this;
    }

    public function getAdminFormScripts()
    {
        $conditionArray = array(
                                'elementId'    => "rule_{$this->getConditionCode()}",
                                'isRequired'   => $this->_isFieldRequired(),
                           );

        $actionArray = $this->getNewActions();

        $conditionObject = Mage::helper('core')->jsonEncode($conditionArray);
        $actionObject = Mage::helper('core')->jsonEncode($actionArray);

        $script = "
            	sweettooth = (typeof sweettooth === 'undefined') ? {} : sweettooth;
	            sweettooth.milestone = (typeof sweettooth.milestone === 'undefined') ? {} : sweettooth.milestone;
	            sweettooth.milestone.milestoneFields = (typeof sweettooth.milestone.milestoneFields === 'undefined') ? {} : sweettooth.milestone.milestoneFields;
	            sweettooth.milestone.milestoneActions = (typeof sweettooth.milestone.milestoneActions === 'undefined') ? {} : sweettooth.milestone.milestoneActions;

	            sweettooth.milestone.milestoneFields['{$this->getConditionCode()}'] = {$conditionObject};
	            sweettooth.milestone.milestoneActions['{$this->getConditionCode()}'] = {$actionObject};
        ";

        return array($script);
    }

    /**
     * Used to insert extra fields into the actions sections of the form UI
     *
     * @param $fieldset Varien_Data_Form_Element_Fieldset
     * @return self
     */
    public function visitAdminActions(&$fieldset)
    {
        // Only if this condition supports the customer group action
        if (array_key_exists('customergroup', $this->getNewActions())) {
            $customergroupFieldId = 'customer_group_id';
            // Avoid duplicates,
            if ($fieldset->getElements()->searchById($customergroupFieldId) === null){
                $customerGroups = Mage::getResourceModel('customer/group_collection')
                            ->addFieldToFilter('customer_group_id', array('neq' => 0));

                $fieldset->addField($customergroupFieldId, 'select', array(
                        'name' => $customergroupFieldId,
                        'label' => Mage::helper ( 'tbtmilestone' )->__("New Customer Group"),
                        'options' => $customerGroups->load()->toOptionHash(),
                        'required' => true,
                ));
            }
        }

        return $this;
    }

    /**
     * @return array. Additional actions supported by this condition (/trigger)
     * @see TBT_Rewards_Model_Special_Configabstract::getNewActions()
     */
    public function getNewActions()
    {
        return array('customergroup' => Mage::helper ( 'tbtmilestone' )->__ ( 'Change customer group' ));
    }

    /**
     * @return string. Label for the new field added to the trigger
     */
    public function getFieldLabel()
    {
        return $this->getConditionLabel();
    }

    /**
     * @return ture if the "validate-not-negative-number" class should be added to the field for validation.
     */
    protected function _isFieldPositiveNumber()
    {
        return true;
    }

    /**
     * Returns the label for this condition's current trigger configuration in the backend.
     * @return string
     */
    protected function _getFieldConfigurationLabel()
    {
        $configLabel = "";
        $configValue = Mage::getStoreConfig("rewards/milestone/{$this->getConditionClassName()}_trigger");
        if (empty($configValue)){
            return $configLabel;
        }

        $sourceModelClass = Mage::helper('tbtmilestone/config')
        ->getSystemConfigValue("sections/rewards/groups/milestone/fields/{$this->getConditionClassName()}_trigger", 'source_model');
        if (empty($sourceModelClass)){
            return $configLabel;
        }

        $options = Mage::getModel($sourceModelClass)->toOptionArray();
        foreach ($options as $option){
            if ($option['value'] == $configValue){
                $configLabel = $option['label'];
                break;
            }
        }

        return $configLabel;
    }

    /**
     * @return ture if the "validate-integer" class should be added to the field for validation.
     */
    protected function _isFieldInteger()
    {
        return true;
    }

    protected function _isFieldRequired()
    {
        return true;
    }
}

?>
<?php

class MDN_Mpm_Block_Rules_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('MpmRules');
        $this->_parentTemplate = $this->getTemplate();
        $this->setEmptyText(Mage::helper('Mpm')->__('No rule Found'));
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
    }

    /**
     * Load rule's collections
     *
     * @return unknown
     */
    protected function _prepareCollection()
    {
        $rulesCollection = new MDN_Mpm_Model_RulesCollection();

        //sort rules by priority
        $rules = Mage::helper('Mpm/Carl')->getClientRuleByType($this->getRuleType());
        $rulesOrderedByPriority = array();
        foreach ($rules as $ruleIdx => $rule) {
            $rulesOrderedByPriority[$ruleIdx] = $rule->priority;
        }
        array_multisort($rulesOrderedByPriority, SORT_NUMERIC, SORT_ASC, $rules);

        foreach ($rules as $rule) {
            $ruleObject = new Varien_Object();
            $ruleObject->id = $rule->id;
            unset($rule->id);
            unset($rule->translation->id);
            $ruleObject->type = strtolower($rule->type);
            unset($rule->type);
            unset($rule->translation->type);
            $ruleObject->name = $rule->name;
            unset($rule->name);
            unset($rule->translation->name);
            $ruleObject->priority = $rule->priority;
            unset($rule->priority);
            unset($rule->translation->priority);
            $ruleObject->enable = $rule->enable;
            unset($rule->enable);
            unset($rule->translation->enable);
            $ruleObject->condition = $rule->condition;
            unset($rule->condition);
            unset($rule->translation->condition);
            $ruleObject->perimeter = $rule->perimeter;
            unset($rule->perimeter);
            unset($rule->translation->perimeter);
            $ruleObject->last_indexation = $rule->last_indexation;
            unset($rule->last_indexation);
            unset($rule->translation->last_indexation);
            $ruleObject->updated_at = $rule->updated_at;
            unset($rule->updated_at);
            unset($rule->translation->updated_at);

            $ruleObject->has_error = $rule->has_error;
            $ruleObject->error = $rule->error;

            $variables = array();
            foreach ($rule->translation as $field => $value) {
                $variables[$field] = $value;
            }
            $ruleObject->setVariablesTranslated($variables);
            unset($rule->translation);

            $variables = array();
            foreach ($rule as $field => $value) {
                $variables[$field] = $value;
            }
            $ruleObject->setVariables($variables);

            $rulesCollection->addItem($ruleObject);
        }

        $this->setCollection($rulesCollection);
        return parent::_prepareCollection();
    }

    /**
     * D�fini les colonnes du grid
     *
     * @return unknown
     */
    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header' => Mage::helper('Mpm')->__('#'),
            'index' => 'id',
            'width' => '60px',
            'align' => 'center',
            'sortable'  => false
        ));

        $this->addColumn('priority', array(
            'header' => Mage::helper('Mpm')->__('Priority'),
            'index' => 'priority',
            'width' => '60px',
            'align' => 'center',
            'sortable'  => false
        ));

        $this->addColumn('type', array(
            'header' => Mage::helper('Mpm')->__('Rule type'),
            'index' => 'type',
            'type'  => 'options',
            'options' => Mage::getModel('Mpm/System_Config_RuleTypes')->toArrayKey(),
            'width' => '200px',
            'sortable'  => false
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('Mpm')->__('Rule'),
            'index' => 'name',
            'width' => '20%',
            'sortable'  => false
        ));

        $this->addColumn('perimeter', array(
            'header' => Mage::helper('Mpm')->__('Product conditions'),
            'index' => 'perimeter',
            'renderer' => 'MDN_Mpm_Block_Widget_Grid_Column_Renderer_Rule_Perimeter',
            'width' => '20%',
            'sortable'  => false
        ));

        $this->addColumn('content', array(
            'header' => Mage::helper('Mpm')->__('Variables'),
            'index' => 'content',
            'renderer' => 'MDN_Mpm_Block_Widget_Grid_Column_Renderer_Rule_Content',
            'width' => '20%',
            'sortable'  => false
        ));

        $this->addColumn('last_index', array(
            'header' => Mage::helper('Mpm')->__('Last index'),
            'index' => 'last_indexation',
            'type' => 'datetime',
            'width' => '200px',
            'sortable'  => false
        ));

        $this->addColumn('enabled', array(
            'header' => Mage::helper('Mpm')->__('Enabled ?'),
            'index' => 'enable',
            'type' => 'options',
            'options' => array(
                'on' => Mage::helper('catalog')->__('Yes'),
                'off' => Mage::helper('catalog')->__('No'),
            ),
            'align' => 'center',
            'width' => '100px',
            'sortable'  => false
        ));

        $this->addColumn('error', array(
            'header' => Mage::helper('Mpm')->__('Error ?'),
            'index' => 'error',
            'renderer' => 'MDN_Mpm_Block_Widget_Grid_Column_Renderer_Rule_Error',
            'sortable'  => false
        ));

        return parent::_prepareColumns();
    }

    public function getGridParentHtml()
    {
        $templateName = Mage::getDesign()->getTemplateFilename($this->_parentTemplate, array('_relative' => true));
        return $this->fetchView($templateName);
    }

    /**
     *
     */
    public function getNewUrl()
    {
        return $this->getUrl('*/*/New');
    }

    public function getIndexAllUrl()
    {
        return $this->getUrl('*/*/IndexAll');
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/Edit', array('id' => $row->getId()));
    }
}

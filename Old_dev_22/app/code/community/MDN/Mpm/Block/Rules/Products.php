<?php

class MDN_Mpm_Block_Rules_Products extends Mage_Adminhtml_Block_Widget_Grid {

    protected $filters = array();
    protected $sort = array();

    public function getRule()
    {
        return Mage::registry('current_rule');
    }

    public function __construct()
    {
        parent::__construct();
        $this->setId('MpmRulesproducts');
        $this->_parentTemplate = $this->getTemplate();
        $this->setEmptyText(Mage::helper('Mpm')->__('No products Found'));
        $this->setDefaultSort('entity_id', 'desc');
        $this->setUseAjax(true);
    }

    /**
     * Charge la collection des devis
     *
     * @return unknown
     */
    protected function _prepareCollection()
    {
        $productsRuleCollection = new MDN_Mpm_Model_ProductsRuleCollection();

        $productsRuleCollection->setQueryFilter($this->filters);
        $productsRuleCollection->setSort($this->sort);

        $productsRuleCollection->setRuleId(Mage::registry('current_rule')->getId());

        $this->setCollection($productsRuleCollection);
        return parent::_prepareCollection();
    }

    /**
     * D�fini les colonnes du grid
     *
     * @return unknown
     */
    protected function _prepareColumns()
    {
        $this->addColumn('sku', array(
            'header' => Mage::helper('Mpm')->__('Sku'),
            'index' => 'product_id'
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('Mpm')->__('Name'),
            'index' => 'label'
        ));

        $this->addColumn('channel', array(
            'header' => Mage::helper('Mpm')->__('Channel'),
            'index' => 'channel'
        ));

        $this->addColumn('reference', array(
            'header'=> Mage::helper('catalog')->__('Reference'),
            'width' => '100px',
            'index' => 'reference',
        ));

        return parent::_prepareColumns();
    }

    public function getGridParentHtml()
    {
        $templateName = Mage::getDesign()->getTemplateFilename($this->_parentTemplate, array('_relative' => true));
        return $this->fetchView($templateName);
    }

    public function getGridUrl()
    {
        $rule = $this->getRule();
        return $this->getUrl('*/*/productsGrid', array('rule_id' => $rule->getId()));
    }

    /**
     * @param array $filters
     */
    public function setFilters($filters)
    {
        $this->filters = $filters;
    }

    public function setSort($sort)
    {
        $this->sort = $sort;
    }


}

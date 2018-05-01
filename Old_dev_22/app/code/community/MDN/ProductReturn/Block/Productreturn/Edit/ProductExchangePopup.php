<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author     : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_ProductReturn_Block_Productreturn_Edit_ProductExchangePopup extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('ProductExchangeGrid');
        $this->_parentTemplate = $this->getTemplate();
        $this->setEmptyText(Mage::helper('ProductReturn')->__('No Items Found'));
    }

    /**
     * Charge la collection des devis
     *
     * @return unknown
     */
    protected function _prepareCollection()
    {

        $collection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('special_price')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('manufacturer')
            ->addFieldToFilter('type_id', array('in' => array(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE, Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL)))
            ->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');;
        $this->setCollection($collection);

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
            'header' => Mage::helper('ProductReturn')->__('Sku'),
            'index'  => 'sku',
            'width'  => '100px'
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('ProductReturn')->__('Name'),
            'index'  => 'name'
        ));

        $this->addColumn('qty', array(
            'header' => Mage::helper('ProductReturn')->__('Qty'),
            'index'  => 'qty',
        ));

        $this->addColumn('price', array(
            'header'        => Mage::helper('ProductReturn')->__('Price'),
            'index'         => 'price',
            'type'          => 'price',
            'currency_code' => Mage::getStoreConfig('currency/options/base')
        ));

        $this->addColumn('action', array(
            'header'   => Mage::helper('ProductReturn')->__('Action'),
            'index'    => 'name',
            'renderer' => 'MDN_ProductReturn_Block_Widget_Column_Renderer_ProductExchangeSelect',
            'filter'   => false,
            'sortable' => false,
            'align'    => 'center'
        ));

        return parent::_prepareColumns();
    }


    public function getGridParentHtml()
    {
        $templateName = Mage::getDesign()->getTemplateFilename($this->_parentTemplate, array('_relative' => true));

        return $this->fetchView($templateName);
    }

}

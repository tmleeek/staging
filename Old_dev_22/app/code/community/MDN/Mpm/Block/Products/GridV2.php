<?php

class MDN_Mpm_Block_Products_GridV2 extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct()
    {
        parent::__construct();
        $this->setId('MpmProducts');
        $this->_parentTemplate = $this->getTemplate();
        $this->setEmptyText(Mage::helper('Mpm')->__('No Items Found'));
        $this->setDefaultSort('id', 'desc');
    }

    protected function _prepareCollection()
    {
        $this->setCollection(new MDN_Mpm_Model_PricingCollection());

        return parent::_prepareCollection();
    }

    protected function addStatusFilter($collection)
    {
        $collection->addFieldToFilter('status', array('neq' => MDN_Mpm_Model_Pricer::kPricingStatusError));
    }

    protected function _prepareColumns()
    {
        $this->addColumn('sku', array(
            'header' => Mage::helper('Mpm')->__('Sku'),
            'index' => 'product_id',
        ));

        $this->addColumn('name_attribute', array(
            'header' => Mage::helper('Mpm')->__('Product'),
            'index' => 'name'
        ));

        $this->addColumn('category', array(
            'header' => Mage::helper('Mpm')->__('Category'),
            'index' => 'category'
        ));

        if (Mage::helper('Mpm')->getSupplierAttribute()) {
            $this->addColumn('supplier_attribute', array(
                'header' => Mage::helper('Mpm')->__('Supplier'),
                'index' => 'supplier'
            ));
        }

        $this->addColumn('brand', array(
            'header' => Mage::helper('Mpm')->__('Brand'),
            'index' => 'brand'
        ));

        $this->addColumn('qty', array(
            'header'=> Mage::helper('catalog')->__('Qty'),
            'width' => '100px',
            'type'  => 'number',
            'index' => 'stock',
        ));

        $this->addSeparator(1);

        $this->addColumn('matching_status', array(
            'header' => Mage::Helper('Mpm')->__('Matching status'),
            'index' => 'matching_status',
            'type' => 'options',
            'options' => array('associated' => $this->__('Associated'), 'not_associated' => $this->__('Not associated'), 'pending' => $this->__('Pending')),
            'renderer' => 'MDN_Mpm_Block_Widget_Grid_Column_Renderer_Product_MatchingStatus',
            'align' => 'center',
            'column_css_class' => 'bbw_column',
            'frame_callback' => array($this, 'columnCallBack'),
        ));

        $this->addColumn('channel', array(
            'header'=> Mage::helper('catalog')->__('Channel'),
            'index' => 'channel',
            'type' => 'options',
            'options' => Mage::getSingleton('Mpm/System_Config_Channels')->getAllOptions(),
            'align' => 'center',
            'column_css_class' => 'bbw_column'
        ));

        $this->addColumn('reference', array(
            'header'=> Mage::helper('catalog')->__('Reference'),
            'index' => 'reference',
            'align' => 'center',
            'column_css_class' => 'bbw_column'
        ));

        $this->addColumn('bbw_name', array(
            'header'=> Mage::helper('catalog')->__('Positioned seller'),
            'index' => 'bbw_name',
            'renderer' => 'MDN_Mpm_Block_Widget_Grid_Column_Renderer_Product_BbwName',
            'align' => 'center',
            'column_css_class' => 'bbw_column'
        ));

        $this->addColumn('bbw_price', array(
            'header'=> Mage::helper('catalog')->__('Seller price (incl tax)'),
            'index' => 'best_offer',
            'align' => 'center',
            'type'  => 'number',
            'column_css_class' => 'bbw_column',
            'frame_callback' => array($this, 'columnCallBack'),
        ));

        $this->addSeparator(3);

        $this->addColumn('final_cost', array(
            'header' => Mage::helper('Mpm')->__('My cost (excl tax)'),
            'index' => 'base_cost',
            'align' => 'center',
            'type'  => 'number',
            'column_css_class' => 'me_column',
            'frame_callback' => array($this, 'columnCallBack'),
        ));

        $this->addColumn('final_price', array(
            'header'=> Mage::helper('catalog')->__('My price (incl tax)'),
            'width' => '100px',
            'type'  => 'number',
            'index' => 'final_price',
            'align' => 'center',
            'column_css_class' => 'me_column',
            'renderer' => 'MDN_Mpm_Block_Widget_Grid_Column_Renderer_Product_MyPrice',
            'frame_callback' => array($this, 'columnCallBack'),
        ));

        $this->addColumn('margin', array(
            'header'=> Mage::helper('catalog')->__('My margin %'),
            'index' => 'final_margin',
            'width' => '100px',
            'type'  => 'number',
            'column_css_class' => 'me_column',
            'frame_callback' => array($this, 'columnCallBack'),
        ));

        $this->addColumn('target_position', array(
            'header'=> Mage::helper('catalog')->__('Position'),
            'width' => '100px',
            'index' => 'target_position',
            'align' => 'center',
            'type' => 'number',
            'column_css_class' => 'me_column',
            'frame_callback' => array($this, 'columnCallBack'),
        ));

        $this->addSeparator(6);

        $behaviorOptions = Mage::getSingleton('Mpm/System_Config_Behaviour')->toArrayKey();
        array_shift($behaviorOptions);
        $this->addColumn('behavior', array(
            'header'=> Mage::helper('catalog')->__('Behaviour'),
            'index' => 'behavior',
            'type' => 'options',
            'options' => $behaviorOptions,
            'renderer' => 'MDN_Mpm_Block_Widget_Grid_Column_Renderer_Product_Behaviour',
            'column_css_class' => 'action_column'
        ));

        $this->addColumn('margin_for_bbw', array(
            'header'=> Mage::helper('catalog')->__('Induced margin %'),
            'index' => 'margin_for_bbw',
            'width' => '100px',
            'type'  => 'number',
            'column_css_class' => 'action_column',
            'frame_callback' => array($this, 'columnCallBack'),
        ));

        $this->addColumn('status', array(
            'header'=> Mage::helper('catalog')->__('Status'),
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getSingleton('Mpm/System_Config_PricingStatus')->toArrayKey(),
            'renderer' => 'MDN_Mpm_Block_Widget_Grid_Column_Renderer_Product_Status',
            'align' => 'center',
            'column_css_class' => 'action_column',
            'frame_callback' => array($this, 'columnCallBack'),
        ));

        $this->addColumn('error', array(
            'header'=> Mage::helper('catalog')->__('Message'),
            'index' => 'error',
            'type' => 'text',
            'align' => 'center',
        ));

        $this->addSeparator(4);

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'renderer'  => 'MDN_Mpm_Block_Widget_Grid_Column_Renderer_Product_Action',
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true
            ));

        $this->addExportType('*/*/exportProductGridCsv', Mage::helper('Mpm')->__('CSV'));
        $this->addExportType('*/*/exportProductGridExcel', Mage::helper('Mpm')->__('XML Excel'));

        return parent::_prepareColumns();
    }

    private function addSeparator($i)
    {
        $this->addColumn('separator_'.$i,
            array(
                'header'=> '',
                'renderer' => 'MDN_Mpm_Block_Widget_Grid_Column_Renderer_Product_Separator',
                'align' => 'center',
                'filter' => false,
                'sortable' => false,
                'column_css_class' => 'separator_column',
                'is_system' => true
            ));
    }

    public function getGridParentHtml()
    {
        $templateName = Mage::getDesign()->getTemplateFilename($this->_parentTemplate, array('_relative' => true));

        return $this->fetchView($templateName);
    }

    protected function getOptions($attributeCode)
    {
        $product = Mage::getModel('catalog/product');
        $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter($product->getResource()->getTypeId())
            ->addFieldToFilter('main_table.attribute_code', $attributeCode)
            ->load(false);
        $attribute = $attributes->getFirstItem()->setEntity($product->getResource());
        $manufacturers = $attribute->getSource()->getAllOptions(false);
        $retour = array();
        foreach ($manufacturers as $manufacturer) {
            $retour[$manufacturer['value']] = $manufacturer['label'];
        }

        return $retour;
    }

    /**
     * add mass action to assign pruduct to a collection
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('product_id');
        $this->getMassactionBlock()->setFormFieldName('product');

        $behaviours = Mage::getSingleton('Mpm/System_Config_Behaviour')->getAllOptions();

        $this->getMassactionBlock()->addItem('change_behaviour', array(
            'label' => Mage::helper('Mpm')->__('Change behaviour'),
            'url' => $this->getUrl('*/*/massChangeBehaviour', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'behaviours',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('Mpm')->__('Behaviour'),
                    'values' => $behaviours
                )
            )
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        if($row->getstatus() === MDN_Mpm_Model_Pricer::kPricingStatusError)
            return "";

        //stupid hack to support slash in sku
        $productId = str_replace('/', '[slash]', $row->getproduct_id());
        $productId = str_replace('#', '[sharp]', $productId);
        $productId = str_replace('+', '[plus]', $productId);
        return 'javascript:openMyPopup(\''.Mage::helper('adminhtml')->getUrl('*/*/offersPopup', array('product_id' => $productId, 'channel' => $row->getChannel())).'\', \''.$this->__('Offer details').'\')';
    }

    public function columnCallBack($value, $row, $column, $isExport)
    {
        if ($isExport)
            return $value;

        $id = 'cell_'.$row->getproduct_id().'_'.$row->getChannel().'_'.$column->getIndex();
        if (in_array($column->getIndex(), array('margin_for_bbw', 'commission', 'margin'))) {
            $value = number_format($value, 1, '.', '');
        }

        if (in_array($column->getIndex(), array('best_offer', 'base_cost'))) {
            $value = Mage::helper('Mpm/Pricing')->getCurrency($row->channel).' '.$value;
        }

        if ($row->geterror() && ($column->getIndex() != 'status' && $column->getIndex() !== 'best_offer'))
            return;

        if($column->getIndex() == 'margin_for_bbw'){

            $value = (round($value, 0) == '-100') ? 'n/a' : $value;

        }

        return '<span id="'.$id.'">'.$value.'</span>';
    }


    public function getTranslateJson() {
        $translations = array(
            'No rows selected' => $this->__('No rows selected'),
        );
        return Mage::helper('core')->jsonEncode($translations);
    }
}

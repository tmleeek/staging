<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

abstract class Ess_M2ePro_Block_Adminhtml_Common_Listing_Search_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    //########################################

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'       => Mage::helper('M2ePro')->__('Product ID'),
            'align'        => 'right',
            'width'        => '100px',
            'type'         => 'number',
            'index'        => 'entity_id',
            'filter_index' => 'entity_id',
            'frame_callback' => array($this, 'callbackColumnProductId'),
            'filter_condition_callback' => array($this, 'callbackFilterProductId')
        ));

        $this->addColumn('name', array(
            'header'       => Mage::helper('M2ePro')->__('Product Title / Listing / Product SKU'),
            'align'        => 'left',
            'type'         => 'text',
            'index'        => 'name',
            'filter_index' => 'name',
            'frame_callback' => array($this, 'callbackColumnProductTitle'),
            'filter_condition_callback' => array($this, 'callbackFilterTitle')
        ));

        if (!Mage::helper('M2ePro/View_Common_Component')->isSingleActiveComponent()) {
            $options = Mage::helper('M2ePro/View_Common_Component')->getEnabledComponentsTitles();

            $this->addColumn('component_mode', array(
                'header'         => Mage::helper('M2ePro')->__('Channel'),
                'align'          => 'left',
                'width'          => '120px',
                'type'           => 'options',
                'index'          => 'component_mode',
                'filter_index'   => 'component_mode',
                'sortable'       => false,
                'options'        => $options
            ));
        }

        $this->addColumn('online_sku', array(
            'header'       => Mage::helper('M2ePro')->__('SKU'),
            'align'        => 'left',
            'width'        => '150px',
            'type'         => 'text',
            'index'        => 'online_sku',
            'filter_index' => 'online_sku',
            'frame_callback' => array($this, 'callbackColumnSku'),
            'filter_condition_callback' => array($this, 'callbackFilterOnlineSku')
        ));

        $this->addColumn('general_id', array(
            'header'       => Mage::helper('M2ePro')->__('Identifier'),
            'align'        => 'left',
            'width'        => '100px',
            'type'         => 'text',
            'index'        => 'general_id',
            'filter_index' => 'general_id',
            'frame_callback' => array($this, 'callbackColumnGeneralId'),
            'filter_condition_callback' => array($this, 'callbackFilterGeneralId')
        ));

        $this->addColumn('status',
            array(
                'header'=> Mage::helper('M2ePro')->__('Status'),
                'width' => '100px',
                'index' => 'status',
                'filter_index' => 'status',
                'type'  => 'options',
                'sortable'  => false,
                'options' => array(
                    Ess_M2ePro_Model_Listing_Product::STATUS_UNKNOWN    => Mage::helper('M2ePro')->__('Unknown'),
                    Ess_M2ePro_Model_Listing_Product::STATUS_NOT_LISTED => Mage::helper('M2ePro')->__('Not Listed'),
                    Ess_M2ePro_Model_Listing_Product::STATUS_LISTED     => Mage::helper('M2ePro')->__('Active'),
                    Ess_M2ePro_Model_Listing_Product::STATUS_STOPPED    => Mage::helper('M2ePro')->__('Inactive'),
                    Ess_M2ePro_Model_Listing_Product::STATUS_BLOCKED    =>
                        Mage::helper('M2ePro')->__('Inactive (Blocked)')
                ),
                'frame_callback' => array($this, 'callbackColumnStatus')
        ));

        $this->addColumn('goto_listing_item', array(
            'header'    => Mage::helper('M2ePro')->__('Manage'),
            'align'     => 'center',
            'width'     => '50px',
            'type'      => 'text',
            'filter'    => false,
            'sortable'  => false,
            'frame_callback' => array($this, 'callbackColumnActions')
        ));

        return parent::_prepareColumns();
    }

    //########################################

    public function callbackColumnProductId($value, $row, $column, $isExport)
    {
        if (is_null($row->getData('entity_id'))) {
            return Mage::helper('M2ePro')->__('N/A');
        }

        $productId = (int)$row->getData('entity_id');
        $storeId   = (int)$row->getData('store_id');

        $url = $this->getUrl('adminhtml/catalog_product/edit', array('id' => $productId));
        $withoutImageHtml = '<a href="'.$url.'" target="_blank">'.$productId.'</a>';

        $showProductsThumbnails = (bool)(int)Mage::helper('M2ePro/Module')->getConfig()->getGroupValue(
            '/view/','show_products_thumbnails'
        );
        if (!$showProductsThumbnails) {
            return $withoutImageHtml;
        }

        /** @var $magentoProduct Ess_M2ePro_Model_Magento_Product */
        $magentoProduct = Mage::getModel('M2ePro/Magento_Product');
        $magentoProduct->setProductId($productId);
        $magentoProduct->setStoreId($storeId);

        $imageResized = $magentoProduct->getThumbnailImage();
        if (is_null($imageResized)) {
            return $withoutImageHtml;
        }

        $imageHtml = $productId.'<hr/><img style="max-width: 100px; max-height: 100px;" src="'.
            $imageResized->getUrl().'" />';
        $withImageHtml = str_replace('>'.$productId.'<','>'.$imageHtml.'<',$withoutImageHtml);

        return $withImageHtml;
    }

    public function callbackColumnSku($value, $row, $column, $isExport)
    {
        if ($row->getData('status') == Ess_M2ePro_Model_Listing_Product::STATUS_NOT_LISTED) {
            return '<span style="color: gray;">' . Mage::helper('M2ePro')->__('Not Listed') . '</span>';
        }

        $sku = $row->getData('online_sku');

        if (is_null($sku) || $sku === '') {
            return Mage::helper('M2ePro')->__('N/A');
        }

        return $sku;
    }

    public function callbackColumnGeneralId($value, $row, $column, $isExport)
    {
        $generalId = $row->getData('general_id');

        if (empty($generalId)) {

            if ($row->getData('status') != Ess_M2ePro_Model_Listing_Product::STATUS_NOT_LISTED) {
                return '<i style="color:gray;">receiving...</i>';
            }

            $generalIdOwner = $row->getData('general_id_owner');

            if ($generalIdOwner && $row->getData('component_mode') == Ess_M2ePro_Helper_Component_Amazon::NICK) {
                return Mage::helper('M2ePro')->__('New ASIN/ISBN');
            }
            return Mage::helper('M2ePro')->__('N/A');
        }

        $url = '';
        if ($row->getData('component_mode') == Ess_M2ePro_Helper_Component_Amazon::NICK) {
            $url = Mage::helper('M2ePro/Component_Amazon')->getItemUrl($generalId, $row->getData('marketplace_id'));
        } else if ($row->getData('component_mode') == Ess_M2ePro_Helper_Component_Buy::NICK) {
            $url = Mage::helper('M2ePro/Component_Buy')->getItemUrl($generalId);
        }

        return '<a href="'.$url.'" target="_blank">'.$generalId.'</a>';
    }

    public function callbackColumnStatus($value, $row, $column, $isExport)
    {
        switch ($row->getData('status')) {

            case Ess_M2ePro_Model_Listing_Product::STATUS_UNKNOWN:
            case Ess_M2ePro_Model_Listing_Product::STATUS_NOT_LISTED:
                $value = '<span style="color: gray;">'.$value.'</span>';
                break;

            case Ess_M2ePro_Model_Listing_Product::STATUS_LISTED:
                $value = '<span style="color: green;">'.$value.'</span>';
                break;

            case Ess_M2ePro_Model_Listing_Product::STATUS_SOLD:
                $value = '<span style="color: brown;">'.$value.'</span>';
                break;

            case Ess_M2ePro_Model_Listing_Product::STATUS_STOPPED:
                $value = '<span style="color: red;">'.$value.'</span>';
                break;

            case Ess_M2ePro_Model_Listing_Product::STATUS_FINISHED:
                $value = '<span style="color: blue;">'.$value.'</span>';
                break;

            case Ess_M2ePro_Model_Listing_Product::STATUS_BLOCKED:
                $value = '<span style="color: orange;">'.$value.'</span>';
                break;

            default:
                break;
        }

        return $value;
    }

    // ---------------------------------------

    abstract public function callbackColumnProductTitle($value, $row, $column, $isExport);
    abstract public function callbackColumnActions($value, $row, $column, $isExport);

    //########################################

    abstract protected function callbackFilterProductId($collection, $column);
    abstract protected function callbackFilterTitle($collection, $column);
    abstract protected function callbackFilterOnlineSku($collection, $column);
    abstract protected function callbackFilterGeneralId($collection, $column);

    //########################################

    protected function _setCollectionOrder($column)
    {
        $collection = $this->getCollection();
        if ($collection) {

            $columnIndex = $column->getFilterIndex() ? $column->getFilterIndex() : $column->getIndex();

            if ($columnIndex == 'online_sku' || $columnIndex == 'general_id') {
                $collection->getSelect()->order(
                    $columnIndex .' '. strtoupper($column->getDir())
                );
            } else {
                $collection->setOrder($columnIndex, strtoupper($column->getDir()));
            }
        }
        return $this;
    }

    //########################################

    public function getGridUrl()
    {
        return $this->getUrl('*/adminhtml_common_listing/searchGrid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return false;
    }

    //########################################
}
<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

abstract class Ess_M2ePro_Block_Adminhtml_Common_Buy_Listing_Search_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
            'header'         => Mage::helper('M2ePro')->__('Product Title / Listing / Product SKU'),
            'align'          => 'left',
            'type'           => 'text',
            'index'          => 'name',
            'filter_index'   => 'name',
            'frame_callback' => array($this, 'callbackColumnProductTitle'),
            'filter_condition_callback' => array($this, 'callbackFilterTitle')
        ));

        $this->addColumn('sku', array(
            'header'         => Mage::helper('M2ePro')->__('Reference ID'),
            'align'          => 'left',
            'width'          => '150px',
            'type'           => 'text',
            'index'          => 'online_sku',
            'filter_index'   => 'online_sku',
            'frame_callback' => array($this, 'callbackColumnSku'),
            'filter_condition_callback' => array($this, 'callbackFilterOnlineSku')
        ));

        $this->addColumn('general_id', array(
            'header'         => Mage::helper('M2ePro')->__('Rakuten.com SKU'),
            'align'          => 'left',
            'width'          => '100px',
            'type'           => 'text',
            'index'          => 'general_id',
            'filter_index'   => 'general_id',
            'frame_callback' => array($this, 'callbackColumnGeneralId')
        ));

        $this->addColumn('online_qty', array(
            'header'         => Mage::helper('M2ePro')->__('QTY'),
            'align'          => 'right',
            'width'          => '70px',
            'type'           => 'number',
            'index'          => 'online_qty',
            'filter_index'   => 'online_qty',
            'frame_callback' => array($this, 'callbackColumnAvailableQty')
        ));

        $this->addColumn('online_price', array(
            'header'         => Mage::helper('M2ePro')->__('Price'),
            'align'          => 'right',
            'width'          => '70px',
            'type'           => 'number',
            'index'          => 'online_price',
            'filter_index'   => 'online_price',
            'frame_callback' => array($this, 'callbackColumnPrice')
        ));

        $this->addColumn('status', array(
            'header'       => Mage::helper('M2ePro')->__('Status'),
            'width'        => '125px',
            'index'        => 'status',
            'filter_index' => 'status',
            'type'         => 'options',
            'sortable'     => false,
            'options'      => array(
                Ess_M2ePro_Model_Listing_Product::STATUS_NOT_LISTED => Mage::helper('M2ePro')->__('Not Listed'),
                Ess_M2ePro_Model_Listing_Product::STATUS_LISTED     => Mage::helper('M2ePro')->__('Active'),
                Ess_M2ePro_Model_Listing_Product::STATUS_STOPPED    => Mage::helper('M2ePro')->__('Inactive')
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

        $showProductsThumbnails = (bool)(int)Mage::helper('M2ePro/Module')->getConfig()
                                                ->getGroupValue('/view/','show_products_thumbnails');
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

        $onlineSku = $row->getData('online_sku');

        if (is_null($onlineSku) || $onlineSku === '') {
            return Mage::helper('M2ePro')->__('N/A');
        }

        return $onlineSku;
    }

    public function callbackColumnGeneralId($value, $row, $column, $isExport)
    {
        if (empty($value)) {

            if ($row->getData('status') != Ess_M2ePro_Model_Listing_Product::STATUS_NOT_LISTED) {
                return '<i style="color:gray;">receiving...</i>';
            }

            return Mage::helper('M2ePro')->__('N/A');
        }

        $url = Mage::helper('M2ePro/Component_Buy')->getItemUrl($value);
        return '<a href="'.$url.'" target="_blank">'.$value.'</a>';
    }

    public function callbackColumnAvailableQty($value, $row, $column, $isExport)
    {
        if ($row->getData('status') == Ess_M2ePro_Model_Listing_Product::STATUS_NOT_LISTED) {
            return '<span style="color: gray;">' . Mage::helper('M2ePro')->__('Not Listed') . '</span>';
        }

        if (is_null($value) || $value === '') {
            return '<i style="color:gray;">'.Mage::helper('M2ePro')->__('receiving...').'</i>';
        }

        if ($value <= 0) {
            return '<span style="color: red;">0</span>';
        }

        return $value;
    }

    public function callbackColumnPrice($value, $row, $column, $isExport)
    {
        if ($row->getData('status') == Ess_M2ePro_Model_Listing_Product::STATUS_NOT_LISTED) {
            return '<span style="color: gray;">' . Mage::helper('M2ePro')->__('Not Listed') . '</span>';
        }

        if (is_null($value) || $value === '') {
            return '<i style="color:gray;">'.Mage::helper('M2ePro')->__('receiving...').'</i>';
        }

        if ((float)$value <= 0) {
            return '<span style="color: #f00;">0</span>';
        }

        return Mage::app()->getLocale()->currency('USD')->toCurrency($value);
    }

    //----------------------------------------

    abstract public function callbackColumnProductTitle($value, $row, $column, $isExport);
    abstract public function callbackColumnStatus($value, $row, $column, $isExport);
    abstract public function callbackColumnActions($value, $row, $column, $isExport);

    //----------------------------------------

    protected function getProductStatus($status)
    {
        switch ($status) {

            case Ess_M2ePro_Model_Listing_Product::STATUS_NOT_LISTED:
                return '<span style="color: gray;">' . Mage::helper('M2ePro')->__('Not Listed') . '</span>';

            case Ess_M2ePro_Model_Listing_Product::STATUS_LISTED:
                return '<span style="color: green;">' . Mage::helper('M2ePro')->__('Active') . '</span>';

            case Ess_M2ePro_Model_Listing_Product::STATUS_STOPPED:
                return '<span style="color: red;">' . Mage::helper('M2ePro')->__('Inactive') . '</span>';
        }

        return '';
    }

    //########################################

    abstract protected function callbackFilterProductId($collection, $column);
    abstract protected function callbackFilterTitle($collection, $column);
    abstract protected function callbackFilterOnlineSku($collection, $column);

    //########################################

    public function getGridUrl()
    {
        return $this->getUrl('*/adminhtml_common_buy_listing/searchGrid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return false;
    }

    //########################################
}
<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Block_Adminhtml_Common_Listing_Search_Other_Grid
    extends Ess_M2ePro_Block_Adminhtml_Common_Listing_Search_Grid
{
    //########################################

    public function __construct()
    {
        parent::__construct();

        // Initialization block
        // ---------------------------------------
        $this->setId('listingSearchOtherGrid');
        // ---------------------------------------

        // Set default values
        // ---------------------------------------
        $this->setDefaultSort(false);
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        // ---------------------------------------
    }

    //########################################

    protected function _prepareCollection()
    {
        $activeComponents = Mage::helper('M2ePro/View_Common_Component')->getActiveComponents();

        $collection = Mage::getModel('M2ePro/Listing_Other')->getCollection();
        $collection->addFieldToFilter('main_table.component_mode', array('in' => $activeComponents));
        $collection->getSelect()->distinct();

        $collection->getSelect()->joinLeft(
            array('cpe' => Mage::getSingleton('core/resource')->getTableName('catalog_product_entity')),
            '(cpe.entity_id = `main_table`.product_id)',
            array('sku' => 'sku')
        );

        $collection->getSelect()->joinLeft(
            array('alo' => Mage::getResourceModel('M2ePro/Amazon_Listing_Other')->getMainTable()),
            'alo.listing_other_id=main_table.id',
            array()
        );
        $collection->getSelect()->joinLeft(
            array('blo' => Mage::getResourceModel('M2ePro/Buy_Listing_Other')->getMainTable()),
            'blo.listing_other_id=main_table.id',
            array()
        );

        $collection->getSelect()->reset(Zend_Db_Select::COLUMNS);
        $collection->getSelect()->columns(
            array(
                'sku'                => 'cpe.sku',
                'name'               => new Zend_Db_Expr('IF(
                    alo.listing_other_id IS NOT NULL,
                    alo.title,
                    blo.title
                )'),
                'listing_title'      => new Zend_Db_Expr('NULL'),
                'store_id'           => new Zend_Db_Expr(0),
                'account_id'         => 'main_table.account_id',
                'marketplace_id'     => 'main_table.marketplace_id',
                'component_mode'     => 'main_table.component_mode',
                'entity_id'          => 'main_table.product_id',
                'listing_id'         => new Zend_Db_Expr('NULL'),
                'status'             => 'main_table.status',

                'listing_other_id' => new Zend_Db_Expr('IF(
                    alo.listing_other_id IS NOT NULL,
                    alo.listing_other_id,
                    blo.listing_other_id
                )'),
                'online_sku' => new Zend_Db_Expr('IF(
                    alo.listing_other_id IS NOT NULL,
                    alo.sku,
                    blo.sku
                )'),
                'general_id' => new Zend_Db_Expr('IF(
                    alo.listing_other_id IS NOT NULL,
                    alo.general_id,
                    blo.general_id
                )')
            )
        );

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->getColumn('name')->setData('header', Mage::helper('M2ePro')->__('Product Title / Product SKU'));
    }

    //########################################

    public function callbackColumnProductTitle($value, $row, $column, $isExport)
    {
        $title = $row->getData('name');

        if (is_null($title) || $title === '') {
            $value = '<i style="color:gray;">receiving...</i>';
        } else {
            $value = '<span>'.Mage::helper('M2ePro')->escapeHtml($title).'</span>';
        }

        $sku = $row->getData('sku');
        if (!empty($sku)) {

            $sku = Mage::helper('M2ePro')->escapeHtml($sku);
            $skuWord = Mage::helper('M2ePro')->__('SKU');

            $value .= <<<HTML
<br/><strong>{$skuWord}:</strong>&nbsp;
{$sku}
HTML;
        }

        return $value;
    }

    public function callbackColumnActions($value, $row, $column, $isExport)
    {
        $altTitle = Mage::helper('M2ePro')->__('Go to Listing');
        $iconSrc  = $this->getSkinUrl('M2ePro/images/goto_listing.png');

        $sku = $row->getData('online_sku');

        $url = $this->getUrl('*/adminhtml_common_'.$row->getData('component_mode').'_listing_other/view/', array(
            'account'     => $row->getData('account_id'),
            'marketplace' => $row->getData('marketplace_id'),
            'filter'      => base64_encode(
                'title=' . $sku
            )
        ));

        $html = <<<HTML
<div style="float:right; margin:5px 15px 0 0;">
    <a title="{$altTitle}" target="_blank" href="{$url}"><img src="{$iconSrc}" /></a>
</div>
HTML;

        return $html;
    }

    //########################################

    protected function callbackFilterProductId($collection, $column)
    {
        $cond = $column->getFilter()->getCondition();

        if (empty($cond)) {
            return;
        }

        $collection->addFieldToFilter('main_table.product_id', $cond);
    }

    protected function callbackFilterTitle($collection, $column)
    {
        $value = $column->getFilter()->getValue();

        if ($value == null) {
            return;
        }

        $collection->getSelect()->where(
            'alo.title LIKE ? OR blo.title LIKE ? OR cpe.sku LIKE ?', '%'.$value.'%'
        );
    }

    protected function callbackFilterOnlineSku($collection, $column)
    {
        $value = $column->getFilter()->getValue();

        if ($value == null) {
            return;
        }

        $collection->getSelect()->where('alo.sku LIKE ? OR blo.sku LIKE ?', '%'.$value.'%');
    }

    protected function callbackFilterGeneralId($collection, $column)
    {
        $value = $column->getFilter()->getValue();

        if ($value == null) {
            return;
        }

        $collection->getSelect()->where('alo.general_id LIKE ? OR blo.general_id LIKE ?', '%'.$value.'%');
    }

    //########################################
}
<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Block_Adminhtml_Common_Buy_Listing_Search_M2ePro_Grid
    extends Ess_M2ePro_Block_Adminhtml_Common_Buy_Listing_Search_Grid
{
    //########################################

    public function __construct()
    {
        parent::__construct();

        // Initialization block
        // ---------------------------------------
        $this->setId('buyListingSearchM2eProGrid');
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
        /* @var $collection Ess_M2ePro_Model_Mysql4_Magento_Product_Collection */
        $collection = Mage::getConfig()->getModelInstance('Ess_M2ePro_Model_Mysql4_Magento_Product_Collection',
                                                          Mage::getModel('catalog/product')->getResource());

        $collection->getSelect()->distinct();
        $collection->setListingProductModeOn();

        $collection->addAttributeToSelect('sku');
        $collection->addAttributeToSelect('name');

        $collection->joinTable(
            array('lp' => 'M2ePro/Listing_Product'),
            'product_id=entity_id',
            array(
                'id'              => 'id',
                'status'          => 'status',
                'component_mode'  => 'component_mode',
                'listing_id'      => 'listing_id',
                'additional_data' => 'additional_data',
            )
        );
        $collection->joinTable(
            array('blp' => 'M2ePro/Buy_Listing_Product'),
            'listing_product_id=id',
            array(
                'listing_product_id' => 'listing_product_id',
                'general_id'         => 'general_id',
                'online_sku'         => 'sku',
                'online_qty'         => 'online_qty',
                'online_price'       => 'online_price'
            )
        );
        $collection->joinTable(
            array('l' => 'M2ePro/Listing'),
            'id=listing_id',
            array(
                'store_id'       => 'store_id',
                'account_id'     => 'account_id',
                'marketplace_id' => 'marketplace_id',
                'listing_title'  => 'title',
            )
        );

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    //########################################

    public function callbackColumnProductTitle($value, $row, $column, $isExport)
    {
        $title = $row->getData('name');
        $title = Mage::helper('M2ePro')->escapeHtml($title);

        $listingWord  = Mage::helper('M2ePro')->__('Listing');
        $listingTitle = Mage::helper('M2ePro')->escapeHtml($row->getData('listing_title'));
        strlen($listingTitle) > 50 && $listingTitle = substr($listingTitle, 0, 50) . '...';

        $listingUrl = $this->getUrl('*/adminhtml_common_buy_listing/view',
                                    array('id' => $row->getData('listing_id')));

        $value = <<<HTML
<span>{$title}</span>
<br/><hr style="border:none; border-top:1px solid silver; margin: 2px 0px;"/>
<strong>{$listingWord}:</strong>&nbsp;
<a href="{$listingUrl}" target="_blank">{$listingTitle}</a>
HTML;

        $sku     = Mage::helper('M2ePro')->escapeHtml($row->getData('sku'));
        $skuWord = Mage::helper('M2ePro')->__('SKU');

        $value .= <<<HTML
<br/><strong>{$skuWord}:</strong>&nbsp;
{$sku}
HTML;

        /** @var Ess_M2ePro_Model_Listing_Product $listingProduct */
        $listingProductId = (int)$row->getData('listing_product_id');
        $listingProduct = Mage::helper('M2ePro/Component_Buy')->getObject('Listing_Product', $listingProductId);

        /** @var Ess_M2ePro_Model_Buy_Listing_Product_Variation_Manager $variationManager */
        $variationManager = $listingProduct->getChildObject()->getVariationManager();

        if ($variationManager->isVariationProduct() && $variationManager->isVariationProductMatched()) {

            $optionsStr = '';
            $productOptions = $listingProduct->getChildObject()->getVariationManager()->getProductOptions();

            foreach ($productOptions as $attribute => $option) {

                $attribute = Mage::helper('M2ePro')->escapeHtml($attribute);
                !$option && $option = '--';
                $option = Mage::helper('M2ePro')->escapeHtml($option);

                $optionsStr .= <<<HTML
<strong>{$attribute}</strong>:&nbsp;{$option}<br/>
HTML;
            }

            $value .= <<<HTML
<br/>
<div style="font-size: 11px; font-weight: bold; color: grey;">
    {$optionsStr}
</div>
<br/>
HTML;
        }

        return $value;
    }

    public function callbackColumnStatus($value, $row, $column, $isExport)
    {
        $value = $this->getProductStatus($row->getData('status'));

        /** @var Ess_M2ePro_Model_Listing_Product $listingProduct */
        $listingProductId = (int)$row->getData('listing_product_id');
        $listingProduct = Mage::helper('M2ePro/Component_Buy')->getObject('Listing_Product', $listingProductId);

        foreach ($listingProduct->getProcessingLocks() as $lock) {

            switch ($lock->getTag()) {

                case 'list_action':
                    $title = Mage::helper('M2ePro')->__('List in Progress...');
                    $value .= '<br/><span style="color: #605fff">['.$title.']</span>';
                    break;

                case 'relist_action':
                    $title = Mage::helper('M2ePro')->__('Relist in Progress...');
                    $value .= '<br/><span style="color: #605fff">['.$title.']</span>';
                    break;

                case 'revise_action':
                    $title = Mage::helper('M2ePro')->__('Revise in Progress...');
                    $value .= '<br/><span style="color: #605fff">['.$title.']</span>';
                    break;

                case 'stop_action':
                    $title = Mage::helper('M2ePro')->__('Stop in Progress...');
                    $value .= '<br/><span style="color: #605fff">['.$title.']</span>';
                    break;

                case 'stop_and_remove_action':
                    $title = Mage::helper('M2ePro')->__('Stop And Remove in Progress...');
                    $value .= '<br/><span style="color: #605fff">['.$title.']</span>';
                    break;

                default:
                    break;
            }
        }

        return $value;
    }

    public function callbackColumnActions($value, $row, $column, $isExport)
    {
        $altTitle = Mage::helper('M2ePro')->escapeHtml(Mage::helper('M2ePro')->__('Go to Listing'));
        $iconSrc  = $this->getSkinUrl('M2ePro/images/goto_listing.png');

        $url = $this->getUrl('*/adminhtml_common_buy_listing/view/', array(
            'id'     => $row->getData('listing_id'),
            'filter' => base64_encode(
                'product_id[from]=' . (int)$row->getData('entity_id')
                .'&product_id[to]=' . (int)$row->getData('entity_id')
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

        $collection->addFieldToFilter('entity_id', $cond);
    }

    protected function callbackFilterTitle($collection, $column)
    {
        $value = $column->getFilter()->getValue();

        if ($value == null) {
            return;
        }

        $collection->addFieldToFilter(
            array(
                array('attribute'=>'sku','like'=>'%'.$value.'%'),
                array('attribute'=>'name', 'like'=>'%'.$value.'%'),
                array('attribute'=>'listing_title','like'=>'%'.$value.'%'),
            )
        );
    }

    protected function callbackFilterOnlineSku($collection, $column)
    {
        $value = $column->getFilter()->getValue();

        if ($value == NULL) {
            return;
        }

        $collection->getSelect()->where('blp.sku LIKE ?', '%' . $value . '%');
    }

    //########################################
}
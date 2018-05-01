<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Block_Adminhtml_Common_Listing_Search_M2ePro_Grid
    extends Ess_M2ePro_Block_Adminhtml_Common_Listing_Search_Grid
{
    //########################################

    public function __construct()
    {
        parent::__construct();

        // Initialization block
        // ---------------------------------------
        $this->setId('listingSearchM2eProGrid');
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
                'listing_id'      => 'listing_id'
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

        $collection->joinTable(
            array('alp' => 'M2ePro/Amazon_Listing_Product'),
            'listing_product_id=id',
            array(
                'amazon_listing_product_id' => 'listing_product_id',
            ),
            null,
            'left'
        );
        $collection->joinTable(
            array('blp' => 'M2ePro/Buy_Listing_Product'),
            'listing_product_id=id',
            array(
                'buy_listing_product_id' => 'listing_product_id',
            ),
            null,
            'left'
        );

        $collection->getSelect()->columns(
            array(
                'listing_product_id' => new Zend_Db_Expr('IF(
                    alp.listing_product_id IS NOT NULL,
                    alp.listing_product_id,
                    blp.listing_product_id
                )'),
                'online_sku' => new Zend_Db_Expr('IF(
                    alp.listing_product_id IS NOT NULL,
                    alp.sku,
                    blp.sku
                )'),
                'general_id' => new Zend_Db_Expr('IF(
                    alp.listing_product_id IS NOT NULL,
                    alp.general_id,
                    blp.general_id
                )'),
                'general_id_owner' => new Zend_Db_Expr('IF(
                    alp.listing_product_id IS NOT NULL,
                    alp.is_general_id_owner,
                    NULL
                )')
            )
        );

        $collection->getSelect()->where('(
            (`lp`.`component_mode` = "'.Ess_M2ePro_Helper_Component_Amazon::NICK.'"
                AND `alp`.variation_parent_id IS NULL)
            OR `lp`.`component_mode` = "'.Ess_M2ePro_Helper_Component_Buy::NICK.'"
        )');

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

        $listingUrl = Mage::helper('M2ePro/View')->getUrl($row, 'listing', 'view',
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

        $listingProductId = (int)$row->getData('listing_product_id');
        $listingProduct = Mage::helper('M2ePro/Component')->getUnknownObject('Listing_Product', $listingProductId);

        $productOptions = array();

        if ($listingProduct->isComponentModeAmazon()) {

            /** @var Ess_M2ePro_Model_Amazon_Listing_Product_Variation_Manager $variationManager */
            $variationManager = $listingProduct->getChildObject()->getVariationManager();

            if (!$variationManager->isIndividualType()) {

                if ($variationManager->isVariationParent()) {

                    $productAttributes = $variationManager->getTypeModel()->getProductAttributes();

                    $virtualProductAttributes = $variationManager->getTypeModel()->getVirtualProductAttributes();
                    $virtualChannelAttributes = $variationManager->getTypeModel()->getVirtualChannelAttributes();

                    $value .= '<div style="font-size: 11px; font-weight: bold; color: grey;"><br/>';
                    $attributesStr = '';
                    if (empty($virtualProductAttributes) && empty($virtualChannelAttributes)) {
                        $attributesStr = implode(', ', $productAttributes);
                    } else {
                        foreach ($productAttributes as $attribute) {
                            if (in_array($attribute, array_keys($virtualProductAttributes))) {

                                $attributesStr .= '<span style="border-bottom: 2px dotted grey">' . $attribute .
                                    ' (' . $virtualProductAttributes[$attribute] . ')</span>, ';

                            } else if (in_array($attribute, array_keys($virtualChannelAttributes))) {

                                $attributesStr .= '<span>' . $attribute .
                                    ' (' . $virtualChannelAttributes[$attribute] . ')</span>, ';

                            } else {
                                $attributesStr .= $attribute . ', ';
                            }
                        }
                        $attributesStr = rtrim($attributesStr, ', ');
                    }
                    $value .= $attributesStr;
                    $value .= '</div>';
                }
                return $value;
            }

            if ($variationManager->getTypeModel()->isVariationProductMatched()) {
                $productOptions = $variationManager->getTypeModel()->getProductOptions();
            }

        } else {

            if ($listingProduct->getChildObject()->getVariationManager()->isVariationProductMatched()) {
                $productOptions = $listingProduct->getChildObject()->getVariationManager()->getProductOptions();
            }
        }

        if ($productOptions) {

            $optionsStr = '';
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

    public function callbackColumnActions($value, $row, $column, $isExport)
    {
        $altTitle = Mage::helper('M2ePro')->__('Go to Listing');
        $iconSrc  = $this->getSkinUrl('M2ePro/images/goto_listing.png');

        $url = $this->getUrl('*/adminhtml_common_'.$row->getData('component_mode').'_listing/view/',array(
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

        if ($value == null) {
            return;
        }

        $collection->getSelect()->where('alp.sku LIKE ? OR blp.sku LIKE ?', '%'.$value.'%');
    }

    protected function callbackFilterGeneralId($collection, $column)
    {
        $value = $column->getFilter()->getValue();

        if ($value == null) {
            return;
        }

        $collection->getSelect()->where('alp.general_id LIKE ? OR blp.general_id LIKE ?', '%'.$value.'%');
    }

    //########################################
}
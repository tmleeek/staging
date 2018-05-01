<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Block_Adminhtml_Common_Listing_Search_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    const TAB_ID_ALL    = 'all';
    const TAB_ID_AMAZON = 'amazon';
    const TAB_ID_BUY    = 'buy';

    //########################################

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('M2ePro/common/component/tabs/linktabs.phtml');
        $this->setId('commonSearchTabs');
        $this->setDestElementId('search_tabs_container');
    }

    //########################################

    protected function _prepareLayout()
    {
        $activeComponents = Mage::helper('M2ePro/View_Common_Component')->getActiveComponents();

        if (count($activeComponents) > 1) {
            $this->addTab(self::TAB_ID_ALL, $this->getAllTabBlock());
        }

        if (Mage::helper('M2ePro/Component_Amazon')->isActive()) {
            $this->addTab(self::TAB_ID_AMAZON, $this->getAmazonTabBlock());
        }
        if (Mage::helper('M2ePro/Component_Buy')->isActive()) {
            $this->addTab(self::TAB_ID_BUY, $this->getBuyTabBlock());
        }

        $this->setActiveTab($this->getActiveChannelTab());

        return parent::_prepareLayout();
    }

    //########################################

    protected function getAllTabBlock()
    {
        $tab = array(
            'label' => Mage::helper('M2ePro')->__('All Channels'),
            'title' => Mage::helper('M2ePro')->__('All Channels')
        );

        $listingType = $this->getRequest()->getParam(
            'listing_type', Ess_M2ePro_Block_Adminhtml_Listing_Search_Switcher::LISTING_TYPE_M2E_PRO
        );

        if ($this->getActiveChannelTab() == self::TAB_ID_ALL) {

            $gridBlock = $listingType == Ess_M2ePro_Block_Adminhtml_Listing_Search_Switcher::LISTING_TYPE_LISTING_OTHER
                ? $this->getLayout()->createBlock('M2ePro/adminhtml_common_listing_search_other_grid')
                : $this->getLayout()->createBlock('M2ePro/adminhtml_common_listing_search_m2ePro_grid');

            $tab['content'] = $gridBlock->toHtml();

        } else {

            $tab['url'] = $this->getUrl('*/adminhtml_common_listing/index', array(
                'tab'          => Ess_M2ePro_Block_Adminhtml_Common_ManageListings::TAB_ID_SEARCH,
                'channel'      => self::TAB_ID_ALL,
                'listing_type' => $listingType
            ));
        }

        return $tab;
    }

    //########################################

    protected function getAmazonTabBlock()
    {
        $title = Mage::helper('M2ePro/Component_Amazon')->getTitle();

        $tab = array(
            'label' => $title,
            'title' => $title
        );

        $listingType = $this->getRequest()->getParam(
            'listing_type', Ess_M2ePro_Block_Adminhtml_Listing_Search_Switcher::LISTING_TYPE_M2E_PRO
        );

        if ($this->getActiveChannelTab() == self::TAB_ID_AMAZON) {

            $gridBlock = $listingType == Ess_M2ePro_Block_Adminhtml_Listing_Search_Switcher::LISTING_TYPE_LISTING_OTHER
                ? $this->getLayout()->createBlock('M2ePro/adminhtml_common_amazon_listing_search_other_grid')
                : $this->getLayout()->createBlock('M2ePro/adminhtml_common_amazon_listing_search_m2ePro_grid');

            $tab['content'] = $gridBlock->toHtml();

        } else {
            $tab['url'] = $this->getUrl('*/adminhtml_common_listing/index', array(
                'tab'          => Ess_M2ePro_Block_Adminhtml_Common_ManageListings::TAB_ID_SEARCH,
                'channel'      => self::TAB_ID_AMAZON,
                'listing_type' => $listingType
            ));
        }

        return $tab;
    }

    //########################################

    protected function getBuyTabBlock()
    {
        $title = Mage::helper('M2ePro/Component_Buy')->getTitle();

        $tab = array(
            'label' => $title,
            'title' => $title
        );

        $listingType = $this->getRequest()->getParam(
            'listing_type', Ess_M2ePro_Block_Adminhtml_Listing_Search_Switcher::LISTING_TYPE_M2E_PRO
        );

        if ($this->getActiveChannelTab() == self::TAB_ID_BUY) {

            $gridBlock = $listingType == Ess_M2ePro_Block_Adminhtml_Listing_Search_Switcher::LISTING_TYPE_LISTING_OTHER
                ? $this->getLayout()->createBlock('M2ePro/adminhtml_common_buy_listing_search_other_grid')
                : $this->getLayout()->createBlock('M2ePro/adminhtml_common_buy_listing_search_m2ePro_grid');

            $tab['content'] = $gridBlock->toHtml();

        } else {
            $tab['url'] = $this->getUrl('*/adminhtml_common_listing/index', array(
                'tab'          => Ess_M2ePro_Block_Adminhtml_Common_ManageListings::TAB_ID_SEARCH,
                'channel'      => self::TAB_ID_BUY,
                'listing_type' => $listingType
            ));
        }

        return $tab;
    }

    //########################################

    protected function getActiveChannelTab()
    {
        $activeTab = $this->getRequest()->getParam('channel');
        if (is_null($activeTab)) {
            Mage::helper('M2ePro/View_Common_Component')->isAmazonDefault() && $activeTab = self::TAB_ID_AMAZON;
            Mage::helper('M2ePro/View_Common_Component')->isBuyDefault()    && $activeTab = self::TAB_ID_BUY;
        }

        return $activeTab;
    }

    //########################################
}
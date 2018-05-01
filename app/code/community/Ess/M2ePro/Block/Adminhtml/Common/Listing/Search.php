<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Block_Adminhtml_Common_Listing_Search extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    //########################################

    public function __construct()
    {
        parent::__construct();

        // Initialization block
        // ---------------------------------------
        $this->setId('listingSearch');
        $this->_blockGroup = 'M2ePro';

        $listingType = $this->getRequest()->getParam('listing_type', false);
        $otherType   = Ess_M2ePro_Block_Adminhtml_Listing_Search_Switcher::LISTING_TYPE_LISTING_OTHER;

        $this->_controller = $listingType == $otherType ? 'adminhtml_common_listing_search_other'
                                                        : 'adminhtml_common_listing_search_m2ePro';
        // ---------------------------------------

        // Set header text
        // ---------------------------------------
        $this->_headerText = '';
        // ---------------------------------------

        // Set buttons actions
        // ---------------------------------------
        $this->removeButton('back');
        $this->removeButton('reset');
        $this->removeButton('delete');
        $this->removeButton('add');
        $this->removeButton('save');
        $this->removeButton('edit');
        // ---------------------------------------
    }

    //########################################

    protected function _toHtml()
    {
        /** @var Ess_M2ePro_Block_Adminhtml_Common_Listing_Search_Tabs $tabsBlock */
        $tabsBlock = $this->getLayout()->createBlock('M2ePro/adminhtml_common_listing_search_tabs');
        $tabsIds   = $tabsBlock->getTabsIds();

        $hideChannels = '';
        if (count($tabsIds) <= 1) {
            $hideChannels = ' style="display: none"';
        }

        $helpBlock = $this->getLayout()->createBlock('M2ePro/adminhtml_common_listing_search_help');

        $switcherParams = array(
            'controller_name' => $this->getRequest()->getControllerName(),
            'action_name'     => 'index',
            'action_params'   => array(
                'tab' => Ess_M2ePro_Block_Adminhtml_Common_ManageListings::TAB_ID_SEARCH,
            )
        );

        if ($channel = $this->getRequest()->getParam('channel', false)) {
            $switcherParams['action_params']['channel'] = $channel;
        }

        /** @var Ess_M2ePro_Block_Adminhtml_Listing_Search_Switcher $searchSwitcher */
        $searchSwitcher = $this->getLayout()->createBlock('M2ePro/adminhtml_listing_search_switcher', '',
                                                          $switcherParams);

        if (!Mage::helper('M2ePro/View_Common')->is3rdPartyShouldBeShown(Ess_M2ePro_Helper_Component_Amazon::NICK) &&
            !Mage::helper('M2ePro/View_Common')->is3rdPartyShouldBeShown(Ess_M2ePro_Helper_Component_Buy::NICK)) {

            $searchSwitcher->showOtherOption = false;
        }

        return $helpBlock->toHtml() . <<<HTML
<div class="content-header skip-header" {$hideChannels}>
    <table cellspacing="0">
        <tr>
            <td>{$tabsBlock->toHtml()}</td>
            <td class="form-buttons">{$this->getButtonsHtml()}</td>
        </tr>
    </table>
</div>
<div class="filter_block">
    {$searchSwitcher->toHtml()}
</div>
<div id="search_tabs_container"></div>
HTML;

    }

    //########################################
}
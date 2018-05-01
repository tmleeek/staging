<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Block_Adminhtml_Common_Configuration_General
    extends Ess_M2ePro_Block_Adminhtml_Common_Component_Tabs_Container
{
    //########################################

    public function __construct()
    {
        parent::__construct();

        // Initialization block
        // ---------------------------------------
        $this->setId('general');
        $this->_blockGroup = 'M2ePro';
        $this->_controller = 'adminhtml_common_general';
        // ---------------------------------------

        // Form id of marketplace_general_form
        // ---------------------------------------
        $this->tabsContainerId = 'edit_form';
        // ---------------------------------------

        $this->_headerText = '';

        $this->setTemplate(NULL);

        // ---------------------------------------
        $this->_addButton('save', array(
            'label'     => Mage::helper('M2ePro')->__('Save'),
            'onclick'   => 'editForm.submit();',
            'class'     => 'save save_configuration_general'
        ));
        // ---------------------------------------
    }

    protected function initializeTabs()
    {
        $this->initializeAmazon();
    }

    //########################################

    protected function getActiveTab()
    {
        return self::TAB_ID_AMAZON;
    }

    //########################################

    protected function getAmazonTabBlock()
    {
        if (!$this->getChild('amazon_tab')) {
            $this->setChild(
                'amazon_tab',
                $this->getLayout()->createBlock('M2ePro/adminhtml_common_amazon_configuration_general_form')
            );
        }
        return $this->getChild('amazon_tab');
    }

    protected function getBuyTabBlock()
    {

    }

    //########################################

    protected function _componentsToHtml()
    {
        $tabsCount = count($this->tabs);

        if ($tabsCount <= 0) {
            return '';
        }

        $formBlock = $this->getLayout()->createBlock('M2ePro/adminhtml_common_configuration_general_form');
        count($this->tabs) == 1 && $formBlock->setChildBlockId($this->getSingleBlock()->getContainerId());

        $tabsContainer = $this->getTabsContainerBlock();
        $tabsContainer->setDestElementId($this->tabsContainerId);

        foreach ($this->tabs as $tabId) {
            $tab = $this->prepareTabById($tabId);
            $tabsContainer->addTab($tabId, $tab);
        }

        $tabsContainer->setActiveTab($this->getActiveTab());

        return <<<HTML
<div class="content-header skip-header">
    <table cellspacing="0">
        <tr>
            <td>{$tabsContainer->toHtml()}</td>
            <td class="form-buttons">{$this->getButtonsHtml()}</td>
        </tr>
    </table>
</div>
{$formBlock->toHtml()}
HTML;

    }

    protected function getTabsContainerDestinationHtml()
    {
        return '';
    }

    //########################################

    protected function getTabsContainerBlock()
    {
        if (is_null($this->tabsContainerBlock)) {
            $this->tabsContainerBlock = $this->getLayout()->createBlock('M2ePro/adminhtml_common_marketplace_tabs');
        }

        return $this->tabsContainerBlock;
    }

    //########################################
}
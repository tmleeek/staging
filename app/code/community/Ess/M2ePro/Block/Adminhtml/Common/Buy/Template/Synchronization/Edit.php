<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Block_Adminhtml_Common_Buy_Template_Synchronization_Edit
    extends Ess_M2ePro_Block_Adminhtml_Common_Template_Edit
{
    //########################################

    public function __construct()
    {
        parent::__construct();

        // Initialization block
        // ---------------------------------------
        $this->setId('buyTemplateSynchronizationEdit');
        $this->_blockGroup = 'M2ePro';
        $this->_controller = 'adminhtml_common_buy_template_synchronization';
        $this->_mode = 'edit';
        // ---------------------------------------

        // Set header text
        // ---------------------------------------
        $helper = Mage::helper('M2ePro');
        if (!Mage::helper('M2ePro/View_Common_Component')->isSingleActiveComponent()) {
            $componentName = Mage::helper('M2ePro/Component_Buy')->getTitle();
            $headerTextEdit = $helper->__("Edit %component_name% Synchronization Policy", $componentName);
            $headerTextAdd = $helper->__("Add %component_name% Synchronization Policy", $componentName);
        } else {
            $headerTextEdit = $helper->__("Edit Synchronization Policy");
            $headerTextAdd = $helper->__("Add Synchronization Policy");
        }

        if (Mage::helper('M2ePro/Data_Global')->getValue('temp_data')
            && Mage::helper('M2ePro/Data_Global')->getValue('temp_data')->getId()
        ) {
            $this->_headerText = $headerTextEdit;
            $this->_headerText .= ' "'.$this->escapeHtml(
                Mage::helper('M2ePro/Data_Global')->getValue('temp_data')->getTitle()).'"';
        } else {
            $this->_headerText = $headerTextAdd;
        }
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

        // ---------------------------------------
        $url = Mage::helper('M2ePro')->getBackUrl('list');
        $this->_addButton('back', array(
            'label'     => Mage::helper('M2ePro')->__('Back'),
            'onclick'   => 'BuyTemplateSynchronizationHandlerObj.back_click(\'' . $url . '\')',
            'class'     => 'back'
        ));
        // ---------------------------------------

        if (Mage::helper('M2ePro/Data_Global')->getValue('temp_data')
            && Mage::helper('M2ePro/Data_Global')->getValue('temp_data')->getId()
        ) {
            // ---------------------------------------
            $this->_addButton('duplicate', array(
                'label'     => Mage::helper('M2ePro')->__('Duplicate'),
                'onclick'   => 'BuyTemplateSynchronizationHandlerObj.duplicate_click'
                    .'(\'common-buy-template-synchronization\')',
                'class'     => 'add M2ePro_duplicate_button'
            ));
            // ---------------------------------------

            // ---------------------------------------
            $this->_addButton('delete', array(
                'label'     => Mage::helper('M2ePro')->__('Delete'),
                'onclick'   => 'BuyTemplateSynchronizationHandlerObj.delete_click()',
                'class'     => 'delete M2ePro_delete_button'
            ));
            // ---------------------------------------
        }

        // ---------------------------------------
        $this->_addButton('save', array(
            'label'     => Mage::helper('M2ePro')->__('Save'),
            'onclick'   => 'BuyTemplateSynchronizationHandlerObj.save_click('
                . '\'\','
                . '\'' . $this->getSaveConfirmationText() . '\','
                . '\'' . Ess_M2ePro_Block_Adminhtml_Common_Template_Grid::TEMPLATE_SYNCHRONIZATION . '\''
                . ')',
            'class'     => 'save'
        ));
        // ---------------------------------------

        // ---------------------------------------
        $this->_addButton('save_and_continue', array(
            'label'     => Mage::helper('M2ePro')->__('Save And Continue Edit'),
            'onclick'   => 'BuyTemplateSynchronizationHandlerObj.save_and_edit_click('
                . '\'\','
                . '\'buyTemplateSynchronizationEditTabs\','
                . '\'' . $this->getSaveConfirmationText() . '\','
                . '\'' . Ess_M2ePro_Block_Adminhtml_Common_Template_Grid::TEMPLATE_SYNCHRONIZATION . '\''
                . ')',
            'class'     => 'save'
        ));
        // ---------------------------------------
    }

    //########################################
}
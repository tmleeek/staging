<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   RMA
 * @version   1.0.1
 * @revision  135
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Rma_Block_Adminhtml_Rma_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct ()
    {
        parent::__construct();
        $this->_objectId = 'rma_id';
        $this->_controller = 'adminhtml_rma';
        $this->_blockGroup = 'rma';


        $this->_updateButton('save', 'label', Mage::helper('rma')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('rma')->__('Delete'));


        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('rma')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action + 'back/edit/');
            }
        ";
        return $this;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

    public function getRma()
    {
        if (Mage::registry('current_rma') && Mage::registry('current_rma')->getId()) {
            return Mage::registry('current_rma');
        }
    }

    public function getHeaderText ()
    {
        if ($rma = $this->getRma()) {
            return Mage::helper('rma')->__("RMA #%s - %s", $rma->getIncrementId(), $rma->getStatus()->getName());
        } else {
            return Mage::helper('rma')->__('Create New RMA');
        }
    }

    /************************/

}
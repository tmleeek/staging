<?php

/**
 * Class MDN_Mpm_Block_Rules_Edit
 *
 * @author    Nicolas Mugnier <contact@boostmyshop.com>
 * @copyright 2015-2016 BoostMyShop (http://www.boostmyshop.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Mpm_Block_Rules_Edit extends Mage_Adminhtml_Block_Widget_Container
{

    /**
     * @var string
     */
    protected $_template = 'Mpm/Rules/Edit.phtml';

    /**
     * @return string
     */
    public function getHeader()
    {
        if($this->getRule()) {
            return Mage::helper('Mpm')->__("Edit Rule '%s' (%s)", $this->escapeHtml($this->getRule()->getName()), $this->getRuleTypeName());
        } else {
            return Mage::helper('Mpm')->__('New Rule');
        }
    }

    /**
     * @return mixed
     */
    public function getRule()
    {
        return Mage::registry('current_rule');
    }

    /**
     * @return mixed
     */
    public function getRuleTypeName()
    {
        return Mage::getSingleton('Mpm/System_Config_RuleTypes')->translate($this->getRule()->getType());
    }

    /**
     * @return \Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {

        $this->setChild('back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Back'),
                    'onclick'   => 'setLocation(\''
                        . $this->getUrl('*/*/', array('_current'=>true)).'\')',
                    'class' => 'back'
                ))
        );

        $this->setChild('reset_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Reset'),
                    'onclick'   => 'setLocation(\''.$this->getUrl('*/*/*', array('_current'=>true)).'\')'
                ))
        );

        $this->setChild('delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Delete'),
                    'onclick'   => 'confirmSetLocation(\'' . $this->__('Are you sure ?')
                        . '\', \'' . $this->getUrl('*/*/delete', array('id' => Mage::registry('current_rule')->getId())) . '\')',
                    'class'  => 'delete'
                ))
        );

        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Save'),
                    'onclick'   => 'ruleForm.submit()',
                    'class' => 'save'
                ))
        );

        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    /**
     * @return string
     */
    public function getCancelButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    /**
     * @return string
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    /**
     * @return string
     */
    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

}

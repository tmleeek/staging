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



class Varien_Data_Form_Element_Mfile extends Varien_Data_Form_Element_Abstract
{
    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct($data)
    {
        parent::__construct($data);
        $this->setType('file');
    }

    /**
     * Return element html code
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = '';
        if ($attachment = $this->getAttachment()) {
            $html .= "<a href='{$attachment->getUrl()}'>{$attachment->getName()}</a><br>";
        }
        $this->setClass('input-file');
        $html .= parent::getElementHtml();
        $html .= $this->_getDeleteCheckbox();

        return $html;
    }

    /**
     * Return html code of delete checkbox element
     *
     * @return string
     */
    protected function _getDeleteCheckbox()
    {
        $html = '';
        if ($attachment = $this->getAttachment()) {
            $label = Mage::helper('core')->__('Delete File');
            $html .= '<span class="delete-image">';
            $html .= '<input type="checkbox"'
                . ' name="' . parent::getName() . '[delete]" value="1" class="checkbox"'
                . ' id="' . $this->getHtmlId() . '_delete"' . ($this->getDisabled() ? ' disabled="disabled"': '')
                . ' style="margin-left: -25px;"/>';
            $html .= '<label for="' . $this->getHtmlId() . '_delete"'
                . ($this->getDisabled() ? ' class="disabled"' : '') . '> ' . $label . '</label>';
            $html .= $this->_getHiddenInput();
            $html .= '</span>';
        }

        return $html;
    }

    /**
     * Return html code of hidden element
     *
     * @return string
     */
    protected function _getHiddenInput()
    {
        return '<input type="hidden" name="' . parent::getName() . '[value]" value="' . $this->getValue() . '" />';
    }

    /**
     * Return name
     *
     * @return string
     */
    public function getName()
    {
        return  $this->getData('name');
    }
}

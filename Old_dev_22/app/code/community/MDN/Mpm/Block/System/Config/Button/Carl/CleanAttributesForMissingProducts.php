<?php

/**
 * Class MDN_Mpm_Block_System_Config_Button_Carl_CleanAttributesForMissingProducts
 *
 * @author    Nicolas Mugnier <contact@boostmyshop.com>
 * @copyright 2015-2016 BoostMyShop (http://www.boostmyshop.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Mpm_Block_System_Config_Button_Carl_CleanAttributesForMissingProducts extends Mage_Adminhtml_Block_System_Config_Form_Field {

    /**
     * @param \Varien_Data_Form_Element_Abstract $element
     * @return mixed
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $url = $this->getUrl('adminhtml/Mpm_Carl/CleanAttributesForMissingProducts');

        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('scalable')
            ->setLabel(Mage::helper('Mpm')->__('Clean'))
            ->setOnClick("setLocation('$url')")
            ->toHtml();

        return $html;
    }

}
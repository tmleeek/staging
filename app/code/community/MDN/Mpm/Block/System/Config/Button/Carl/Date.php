<?php

/**
 * Class MDN_Mpm_Block_System_Config_Button_Carl_Date
 *
 * @author    Nicolas Mugnier <contact@boostmyshop.com>
 * @copyright 2015-2016 BoostMyShop (http://www.boostmyshop.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Mpm_Block_System_Config_Button_Carl_Date extends Mage_Adminhtml_Block_System_Config_Form_Field {

    /**
     * @param \Varien_Data_Form_Element_Abstract $element
     * @return string
     * @throws \Exception
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $date = new Varien_Data_Form_Element_Date();
        $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        $data = array(
            'name'      => $element->getName(),
            'html_id'   => $element->getId(),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
        );
        $date->setData($data);
        $date->setValue($element->getValue(), $format);
        $date->setFormat($format);
        $date->setForm($element->getForm());

        return $date->getElementHtml();
    }

}
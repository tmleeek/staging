<?php
/**
 * Addonline
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline (http://www.addonline.fr)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Addonline_SoColissimo
 *
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */

class Addonline_SoColissimo_Block_Adminhtml_System_Config_Button_Colissimobox extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    /*
     * The Button text can be translated in theses locales
     */
    protected $colissimoLocales = array('fr', 'en', 'de');

    /**
     * {@inherit}
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $url = 'https://www.colissimo.entreprise.laposte.fr/';

        /*
         * we get the magento back-office session language to determine
         * the locale of the Colissimo Back-office link
         */
        $codeLocale = Mage::getSingleton('adminhtml/session')->getLocale();
        $codeLocaleArray = explode('_', $codeLocale);
        $urlCodeLocale = $codeLocaleArray[0];
        if (in_array($urlCodeLocale, $this->colissimoLocales)) {
            $url .= $urlCodeLocale;
        } else {
            $url .= 'en';
        }


        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('scalable')
            ->setLabel(Mage::helper('socolissimo')->__('Access Colissimo Box'))
            ->setOnClick("window.open('$url')")
            ->toHtml();

        return $html;
    }
}
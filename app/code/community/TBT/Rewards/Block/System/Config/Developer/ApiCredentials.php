<?php

/**
 * WDCA
 * 
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   WDCA
 * @package    TBT_Enhancedgrid
 * @copyright  Copyright (c) 2014 Sweet Tooth Inc. (http://www.sweettoothrewards.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class TBT_Rewards_Block_System_Config_Developer_ApiCredentials extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    const HTML_ID_SUFFIX_APIKEY = '_apikey';
    const HTML_ID_SUFFIX_SECRETKEY = '_secretkey';
    
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
	{
	    $html = '';
	    
	    if ($this->_checkElement($element, self::HTML_ID_SUFFIX_APIKEY)) {
	        $html = Mage::getStoreConfig('rewards/platform/apikey');
	        $html = $html ? $html : '<i>' . $this->__("Not Set") . '</i>';
	    } else if ($this->_checkElement($element, self::HTML_ID_SUFFIX_SECRETKEY)) {
	        $html = Mage::helper('core')->decrypt(Mage::getStoreConfig('rewards/platform/secretkey'));
	        $html = $html ? $html : '<i>' . $this->__("Not Set") . '</i>';
	    }
	    
	    return $html;
	}
	
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
	    $id = $element->getId();
	    $element->setScopeLabel('');
	    
	    $element->setId($id . self::HTML_ID_SUFFIX_APIKEY);
	    $element->setLabel($this->__("API Key"));
	    $html = parent::render($element);
	    
	    $element->setId($id . self::HTML_ID_SUFFIX_SECRETKEY);
	    $element->setLabel($this->__("Secret Key"));
	    $html .= parent::render($element);
	    
	    return $html;
	}
	
	protected function _checkElement(Varien_Data_Form_Element_Abstract $element, $htmlIdSuffix)
	{
	    return substr_compare($element->getId(), $htmlIdSuffix, -strlen($htmlIdSuffix), strlen($htmlIdSuffix)) === 0;
	}
}

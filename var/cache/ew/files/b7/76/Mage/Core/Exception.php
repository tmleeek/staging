<?php
#########################################################################################################################################################
# NOTICE - READ ME!!!
#########################################################################################################################################################
/**

This file is only a copy of the original file. A stack trace / exception containing this file does NOT indicate an error with Extendware.
No source code content has been modified from the original file. The only change has been in the hierachy of the classes.
Here is information about this file: 

Original Class: Mage_Core_Exception
Original File: /data/devazb/public_html/public_html/app/code/core/Mage/Core/Exception.php

*/
#########################################################################################################################################################




?><?php
/**
 * Magento
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
 * @category    Mage
 * @package     Mage_Core
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Magento Core Exception
 *
 * This class will be extended by other modules
 *
 * @category   Mage
 * @package    Mage_Core
 */
class  Mage_Core_ExceptionOverriddenClass  extends Exception
{
    protected $_messages = array();

    public function addMessage(Mage_Core_Model_Message_Abstract $message)
    {
        if (!isset($this->_messages[$message->getType()])) {
            $this->_messages[$message->getType()] = array();
        }
        $this->_messages[$message->getType()][] = $message;
        return $this;
    }

    public function getMessages($type='')
    {
        if ('' == $type) {
            $arrRes = array();
            foreach ($this->_messages as $messageType => $messages) {
                $arrRes = array_merge($arrRes, $messages);
            }
            return $arrRes;
        }
        return isset($this->_messages[$type]) ? $this->_messages[$type] : array();
    }

    /**
     * Set or append a message to existing one
     *
     * @param string $message
     * @param bool $append
     * @return Mage_Core_Exception
     */
    public function setMessage($message, $append = false)
    {
        if ($append) {
            $this->message .= $message;
        } else {
            $this->message = $message;
        }
        return $this;
    }
}

?><?php
if (class_exists('Extendware_EWCore_Model_Override_Mage_Core_Exception_Bridge', false) === false) {
	abstract class Extendware_EWCore_Model_Override_Mage_Core_Exception_Bridge extends Mage_Core_ExceptionOverriddenClass  {

	}
} else {
	if (class_exists('Mage', false) === true) {
		Mage::log('Bridge class (Extendware_EWCore_Model_Override_Mage_Core_Exception_Bridge) encountered twice');
	}
}
?><?php
class Mage_Core_Exception extends Extendware_EWCore_Model_Override_Mage_Core_Exception {

}
?>
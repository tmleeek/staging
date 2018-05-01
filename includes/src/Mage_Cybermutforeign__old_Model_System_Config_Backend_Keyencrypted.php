<?php
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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Encrypted config field backend model
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Cybermutforeign_Model_System_Config_Backend_Keyencrypted extends Mage_Core_Model_Config_Data
{
    /**
     * Enter description here...
     *
     */
    protected function _beforeSave()
    {
		$path = $_FILES['groups']['tmp_name']['cybermutforeign_payment']['fields']['key_encrypted']['value'];

		$filecontent = file_get_contents($path);
		@unlink($path);

		if (preg_match('/.*([0-9a-zA-Z]{40}).*/i', $filecontent, $m)) {
			$value = $m[1];
		} else {
			exit(Mage::helper('cybermutforeign')->__('Error while getting the key'));
		}

        if (!empty($value) && ($encrypted = Mage::helper('core')->encrypt($value))) {
            $this->setValue($encrypted);
        }
    }

}

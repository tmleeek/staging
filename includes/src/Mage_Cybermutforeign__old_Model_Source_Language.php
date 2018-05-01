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
 * @package    Mage_Cybermutforeign
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Cybermutforeign Allowed languages Resource
 *
 * @category   Mage
 * @package    Mage_Cybermutforeign
 * @name       Mage_Cybermutforeign_Model_Source_Language
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Cybermutforeign_Model_Source_Language
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'EN', 'label' => Mage::helper('cybermutforeign')->__('English')),
            array('value' => 'FR', 'label' => Mage::helper('cybermutforeign')->__('French')),
            array('value' => 'DE', 'label' => Mage::helper('cybermutforeign')->__('German')),
            array('value' => 'IT', 'label' => Mage::helper('cybermutforeign')->__('Italian')),
            array('value' => 'ES', 'label' => Mage::helper('cybermutforeign')->__('Spain')),
            array('value' => 'NL', 'label' => Mage::helper('cybermutforeign')->__('Dutch')),
        );
    }
}




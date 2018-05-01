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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Cybermutforeign
 * @copyright  Copyright (c) 2010 Quadra Informatique (http://www.quadra-informatique.fr/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order Statuses source model
 */
class Mage_Cybermutforeign_Model_System_Config_Source_Order_Status
{
    // set null to enable all possible
    protected $_stateStatuses = array();

    public function toOptionArray()
    {
        if ($this->_stateStatuses) {
            $statuses = Mage::getSingleton('sales/order_config')->getStateStatuses($this->_stateStatuses);
        }
        else {
            $statuses = Mage::getSingleton('sales/order_config')->getStatuses();
        }
        $options = array();
        $options[] = array(
        	   'value' => '',
        	   'label' => Mage::helper('adminhtml')->__('-- Please Select --')
        	);
        foreach ($statuses as $code=>$label) {
        	$options[] = array(
        	   'value' => $code,
        	   'label' => $label
        	);
        }
        return $options;
    }
}
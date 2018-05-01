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
 * Redirect to Cybermutforeign
 *
 * @category    Mage
 * @package     Mage_Cybermutforeign
 * @name        Mage_Cybermutforeign_Block_Standard_Redirect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Cybermutforeign_Block_Redirect extends Mage_Core_Block_Abstract
{

	public function _toHtml()
	{
		$standard = Mage::getModel('cybermutforeign/payment');
        $form = new Varien_Data_Form();
        $form->setAction($standard->getCybermutforeignUrl())
            ->setId('cybermutforeign_payment_checkout')
            ->setName('cybermutforeign_payment_checkout')
            ->setMethod('POST')
            ->setUseContainer(true);
        foreach ($standard->setOrder($this->getOrder())->getStandardCheckoutFormFields() as $field => $value) {
            $form->addField($field, 'hidden', array('name' => $field, 'value' => $value));
        }

        $formHTML = $form->toHtml();

        $html = '<html><body>';
        $html.= $this->__('You will be redirected to Cybermutforeign in a few seconds.');
        $html.= $formHTML;
        $html.= '<script type="text/javascript">document.getElementById("cybermutforeign_payment_checkout").submit();</script>';
        $html.= '</body></html>';

        if ($standard->getConfigData('debug_flag')) {
            Mage::getModel('cybermutforeign/api_debug')
                ->setRequestBody($formHTML)
                ->save();
        }

        return $html;
    }
}
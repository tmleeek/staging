<?php
/**
 * Gls_Unibox extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Gls
 * @package    Gls_Unibox
 * @copyright  Copyright (c) 2013 webvisum GmbH
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Webvisum
 * @package    Gls_Unibox
 */
//extends Mage_Adminhtml_Block_Sales_Order_Shipment_View_Form
class Gls_Unibox_Block_Adminhtml_Sales_Order_Shipment_View_Form extends MDN_Colissimo_Block_Adminhtml_Sales_Order_Shipment_View_Form
{
	public function _prepareLayout(){
		return parent::_prepareLayout();
	}

    public function getLabelUrl($id){
        return $this->getUrl('*/glsunibox/printlabel', array(
            'glslabel_id' => $id
        ));
    }
}
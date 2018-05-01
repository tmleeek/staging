<?php
/**
 * WDCA - Sweet Tooth Instore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the SWEET TOOTH (TM) INSTORE
 * License, which extends the Open Software License (OSL 3.0).
 * The Sweet Tooth Instore License is available at this URL: 
 * http://www.sweettoothrewards.com/instore/sweet_tooth_instore_license.txt
 * The Open Software License is available at this URL: 
 * http://opensource.org/licenses/osl-3.0.php
 * 
 * DISCLAIMER
 * 
 * By adding to, editing, or in any way modifying this code, WDCA is 
 * not held liable for any inconsistencies or abnormalities in the 
 * behaviour of this code. 
 * By adding to, editing, or in any way modifying this code, the Licensee
 * terminates any agreement of support offered by WDCA, outlined in the 
 * provided Sweet Tooth Instore License. 
 * Upon discovery of modified code in the process of support, the Licensee 
 * is still held accountable for any and all billable time WDCA spent 
 * during the support process.
 * WDCA does not guarantee compatibility with any other framework extension. 
 * WDCA is not responsbile for any inconsistencies or abnormalities in the
 * behaviour of this code if caused by other framework extension.
 * If you did not receive a copy of the license, please send an email to 
 \* support@sweettoothrewards.com or call 1.855.699.9322, so we can send you a copy 
 * immediately.
 * 
 * @category   [TBT]
 * @package    [TBT_Rewardsinstore]
 * @copyright  Copyright (c) 2011 Sweet Tooth (http://www.sweettoothrewards.com)
 * @license    http://www.sweettoothrewards.com/instore/sweet_tooth_instore_license.txt
 */

/**
 * Generally used for accessing the Instore webapp using as short a URL as possible.
 *
 * @category   TBT
 * @package    TBT_Rewardsinstore
 * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */
class TBT_Billboard_BillboardController extends Mage_Adminhtml_Controller_Action
{
    public function showAction()
    {
        $this->loadLayout();
        
        $blockKey = $this->getRequest()->has('billboardKey') ?
            urldecode($this->getRequest()->get('billboardKey')) :
            'tbtbillboard/billboard';
        
        $block = $this->getLayout()->createBlock($blockKey)
            ->addData($this->getRequest()->getParams());
        $this->getLayout()->setBlock('billboard', $block);
        
        $this->renderLayout();
    }
}

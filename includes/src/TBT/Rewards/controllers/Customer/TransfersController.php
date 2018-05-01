<?php

/**
 * WDCA - Sweet Tooth
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the WDCA SWEET TOOTH POINTS AND REWARDS 
 * License, which extends the Open Software License (OSL 3.0).

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
 * provided Sweet Tooth License. 
 * Upon discovery of modified code in the process of support, the Licensee 
 * is still held accountable for any and all billable time WDCA spent 
 * during the support process.
 * WDCA does not guarantee compatibility with any other framework extension. 
 * WDCA is not responsbile for any inconsistencies or abnormalities in the
 * behaviour of this code if caused by other framework extension.
 * If you did not receive a copy of the license, please send an email to 
 * support@sweettoothrewards.com or call 1.855.699.9322, so we can send you a copy 
 * immediately.
 * 
 * @category   [TBT]
 * @package    [TBT_Rewards]
 * @copyright  Copyright (c) 2014 Sweet Tooth Inc. (http://www.sweettoothrewards.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer Controller
 *
 * @category   TBT
 * @package    TBT_Rewards
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */

include_once(Mage::getModuleDir('controllers', 'TBT_Rewards') . DS . 'Front' . DS . 'AbstractController.php');
class TBT_Rewards_Customer_TransfersController extends TBT_Rewards_Front_AbstractController
{
    public function gridAction()
    {
        $request = $this->getRequest();
        $type = $request->getParam('type', 'earnings');

        $customer = Mage::getSingleton('rewards/session')->getSessionCustomer();
        Mage::register('customer', $customer);

        $this->loadLayout();

        $grid = $this->getLayout()->createBlock("rewards/customer_transfers_{$type}_grid");
        if ($request->getParam('limit') !== null) {
            $grid->setLimit($request->getParam('limit'));
        }
        if ($request->getParam('page') !== null) {
            $grid->setPage($request->getParam('page'));
        }
        if ($request->getParam('sort') !== null) {
            $grid->setSort($request->getParam('sort'));
        }
        if ($request->getParam('dir') !== null) {
            $grid->setDir($request->getParam('dir'));
        }
        if ($request->getParam('filter') !== null) {
            $grid->setFilter($request->getParam('filter'));
        }

        $this->getResponse()->setBody($grid->toHtml());

        return $this;
    }

    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
        if (!Mage::helper('rewards/config')->getIsCustomerRewardsActive()) {
            $this->norouteAction();
            return;
        }
    }
}

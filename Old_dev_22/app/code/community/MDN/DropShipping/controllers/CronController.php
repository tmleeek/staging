<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Nicolas MUGNIER
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_DropShipping_CronController extends Mage_Adminhtml_Controller_Action {

    /**
     * Run drop ship cron task manually 
     */
    public function runDropShipAction() {

        try {

            Mage::getModel('DropShipping/Observer')->sendDropShipOrders();

            Mage::getSingleton('adminhtml/session')->addSuccess('Job done');
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage() . ' : ' . $e->getTraceAsString());
        }

        $this->_redirect('DropShipping/Admin/Grid');
    }

}

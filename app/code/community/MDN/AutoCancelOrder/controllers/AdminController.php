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
 * @author : ALLAIRE Benjamin
 * @mail : benjamin@boostmyshop.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_AutoCancelOrder_AdminController extends Mage_Adminhtml_Controller_Action {

    /**
     * when clicking on button apply from config page
     * call helper data to cancel order with parameters from
     * config
     */
    public function applyOnOrderAction() {

        try {
            Mage::helper('AutoCancelOrder')->apply();
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Apply on orders done'));
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());
        }

        //confirm & redirect
        $this->_redirect('adminhtml/system_config/edit', array('section' => 'autocancelorder'));
    }

    /**
     * load and render layout to show logs when clicking on button from config page
     * block are created dynamicaly
     */
    public function showLogsAction() {

        $this->loadLayout();
        $this->renderLayout();
    }

    /*
     * when clicking on a log entry, redirect on order view
     * 
     */

    public function viewOrderAction() {

        try {
            // get the id of log entry
            $aocId = $this->getRequest()->getParam('log_id');

            // get the order id
            $orderId = Mage::helper('AutoCancelOrder')->getOrderIdToView($aocId);
            
            // message redirect
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__("Try to hold this order if they can't be canceled"));
            
            
            //redirect to order view
            $this->_redirect('adminhtml/sales_order/view/', array('order_id' => $orderId)); // .../sales_order/view/order_id/23...  
            
        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());
            $this->_redirect('AutoCancelOrder/admin/showLogs');
        }
 
    
    }
    
    /**
     * redirect on the config url  
     */
    public function backToConfigAction(){
        $this->_redirect('adminhtml/system_config/edit', array('section' => 'autocancelorder'));
    }

    
    /**
     * clear logs!
     * delete * from 'auto_cancel_order_log'
     */
    public function clearLogsAction(){
        
        // get all logs
        $logs = Mage::getModel('AutoCancelOrder/Log')->getCollection();
        
        foreach($logs as $log){
            //delete logs
            $log->delete($log->getaco_id());
        }

        //confirm
        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Logs deleted'));
        //Redirect
        $this->_redirect('*/*/showLogs');
       
    }
}
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
 * @copyright  Copyright (c) 2013 Boostmyshop (http://www.boostmyshop.com)
 * @author : Guillauem SARRAZIN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_HealthyERP_Block_Adminhtml_System_Config_Probe_Ordernotconsideredyet extends MDN_HealthyERP_Block_Adminhtml_System_Config_Probe_Abstract
{

    const ALERT = 10;

    const DEFAULT_ACTION = 'consider';

    private $_orderListNotYetConsidered = null;
    private $_orderCountNotYetConsidered = 0;
   

     private static function getErrorsList(){
       return mage::getModel('AdvancedStock/Observer')->getOrdersNotYetConsidered()->getAllIds();
     }

    /**
     * Get the list of the order noy yet considered by ERP
     * ERP check every minute the orders not completed and not cancelled and try to allow them a prepration warehosue and reserteh product
     * Once it's done, the field stock_updated is set to 1 for this order and ERP will not check anymore the order.
     *
     * @return type
     */
    protected function checkProbe()
    {

      $this->_orderListNotYetConsidered = self::getErrorsList();

	  $this->_orderCountNotYetConsidered = count($this->_orderListNotYetConsidered);
            

      if($this->_orderCountNotYetConsidered==0){
        $status = parent::STATUS_OK;
      }
      if($this->_orderCountNotYetConsidered>0 && $this->_orderCountNotYetConsidered <self::ALERT){
        $status = parent::STATUS_PARTIAL;
      }
      if($this->_orderCountNotYetConsidered > self::ALERT){
        $status = parent::STATUS_NOK;
      }
      return $status;
    }
    


    protected function getActions()
    {
      $actions = array();
      
      $label = $this->__('Consider them Now');
      $action = self::DEFAULT_ACTION;
      $openMode = parent::OPEN_URL_NEW_WINDOWS;

      switch($this->_indicator_status){
        case parent::STATUS_OK :
          break;
        case parent::STATUS_PARTIAL :
        case parent::STATUS_NOK :
           $actions[] = array($label, $action, $openMode);
           break;
      }

      return $actions;
    }    

    protected function getCurrentSituation()
    {
      $situation = '';
      if (Mage::getStoreConfig('healthyerp/options/display_basic_message')){
        $situation = $this->__('Order count').' : '.$this->_orderCountNotYetConsidered.'<br/>';

        switch($this->_indicator_status){
          case parent::STATUS_OK :
             break;
          case parent::STATUS_NOK :
          case parent::STATUS_PARTIAL :
             if (Mage::getStoreConfig('healthyerp/options/display_advanced_message')){
              if($this->_orderCountNotYetConsidered>0){
                 $situation .= $this->__('Order list').' : <br/>';
                 foreach($this->_orderListNotYetConsidered as $orderId){
                         $urlInfo = array('url' => 'adminhtml/sales_order/view', 'param' => array('order_id' => $orderId));
                         $url = $this->getUrl($urlInfo['url'], $urlInfo['param']);
                         $situation .= '<a href="' . $url . '" target="_blank">#' . $orderId . '</a><br/>';
                 }

              }
             }
            break;
          default:
             $situation .= $this->__(parent::DEFAULT_STATUS_MESSAGE);
             break;
        }
      }

      return $situation;
    }

    public static function fixIssue($action){

      $redirect = true;

      $orderIdListToConsiderList = self::getErrorsList();

      
      if(count($orderIdListToConsiderList)>0){
        $helper = mage::helper('BackgroundTask');

        $taskGroupCode = 'force_consider_order';
        $helper->AddGroup($taskGroupCode,
                          mage::helper('HealthyERP')->__('Force to consider order by ERP'),
                          'adminhtml/system_config/edit/section/healthyerp');

        $priority = 5;

        foreach ($orderIdListToConsiderList as $orderId){
          if($orderId>0){
            $helper->AddTask('Force consider order #'.$orderId,
                           'AdvancedStock/Sales_Order', 'updateStocksForOneOrder', $orderId,
                           $taskGroupCode, false, $priority);
          }
        }

        //set debug to off to avoid crash        
        if (Mage::getStoreConfig('advancedstock/cron/debug')){          
          Mage::getConfig()->saveConfig('advancedstock/cron/debug', 0);
          Mage::getConfig()->cleanCache();
        }
        
        $helper->ExecuteTaskGroup($taskGroupCode);
        $redirect = false;
      }
      
      return $redirect;
    }

}
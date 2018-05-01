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
class MDN_HealthyERP_Block_Adminhtml_System_Config_Probe_UnconsistantProductAvailibilityStatus extends MDN_HealthyERP_Block_Adminhtml_System_Config_Probe_Abstract
{

    const DEFAULT_ACTION = 'refresh';

    private $_productIdListToFix = null;
    private $_productCountToFix = null;

    /**
     * Check if a product got a product availibility status witch is not consistant with his current stock status
     * 
     * @return type
     */
    private static function getErrorsList(){
      $tablePa = Mage::helper('HealthyERP')->getPrefixedTableName('product_availability');
      $tableCisi = Mage::helper('HealthyERP')->getPrefixedTableName('cataloginventory_stock_item');
      
      $sql='SELECT
                pa_product_id,
                pa_available_qty,
                pa_status,
                pa_is_saleable
            FROM
                '.$tablePa.' pa,
                '.$tableCisi.' cisi
            WHERE
                cisi.product_id=pa.pa_product_id
                AND cisi.manage_stock = 1
                AND (0=1';
      
            //product availability status sellable but product out of stock
            $sql .= ' OR ';
            $sql .= '(pa_is_saleable = 1 and is_in_stock = 0)';
      
            //available quantity positive but product out of stock
            $sql .= ' OR ';
            $sql .= '(pa_available_qty > 0 and is_in_stock = 0)';
            
            //product instock but not sellable
            $sql .= ' OR ';
            $sql .= '(pa_is_saleable = 0 and is_in_stock = 1)';
                                    
            $sql .= '
                    )
            ';

      return mage::getResourceModel('sales/order_item_collection')->getConnection()->fetchAll($sql);
    }

    protected function checkProbe() {

      $status = parent::STATUS_UNKNOWN;

      $this->_productIdListToFix = self::getErrorsList();
      $this->_productCountToFix = count($this->_productIdListToFix);

      if($this->_productCountToFix==0){
        $status = parent::STATUS_OK;
      }else{
        $status = parent::STATUS_NOK;
      }

      return $status;
    }

  

    protected function getCurrentSituation()
    {

      $situation = '';
      if (Mage::getStoreConfig('healthyerp/options/display_basic_message')){
        $situation = $this->__('Product which the Availibility Status is not consistant').' : '.$this->_productCountToFix.'<br/>';
        switch($this->_indicator_status){
          case parent::STATUS_OK :
             break;
          case parent::STATUS_NOK :
          case parent::STATUS_PARTIAL :
             if (Mage::getStoreConfig('healthyerp/options/display_advanced_message')){
              if($this->_productCountToFix>0){
                 $situation .= $this->__('Product list').' : <br/>';
                 foreach ($this->_productIdListToFix as $diffItem) {
                    $productId = $diffItem['pa_product_id'];
                    $paAvailableQty = $diffItem['pa_available_qty'];
                    $status = $diffItem['pa_status'];
                    $paIsSaleable = $diffItem['pa_is_saleable'];
                    $situation .= "Product#".$productId." : AvailableQty=".$paAvailableQty." Status=".$status." Saleable=".$paIsSaleable."<br/>";
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

    /**
     * Refresh the product availibility status for all product in error
     * 
     * @param type $productListToFix
     * @param type $action
     * @return boolean
     */
    public static function fixIssue($action){

       $redirect = true;
       
       $productListToFix = self::getErrorsList();
     
       $log = '';
       $helper = mage::helper('SalesOrderPlanning/ProductAvailabilityStatus');

       foreach ($productListToFix as $diffItem) {
            try {
               $helper->RefreshForOneProduct($diffItem['pa_product_id']);               
            }
            catch (Exception $ex) {
                $log = $ex->getMessage();
            }
        }


      return $redirect;
    }

}
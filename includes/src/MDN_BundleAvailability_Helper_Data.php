<?php

class MDN_BundleAvailability_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Return availability message for bundle (based on cart)
     *
     * @param $productId
     */
    public function getAvailabilityMessageForBundle($bundleId)
    {
        $debug = array();
        $bundle = Mage::getModel('catalog/product')->load($bundleId);
        $products = $this->getSubProducts($bundle);
        $debug[] = count($products).' sub products';
        $worstDate = null;
        $worstMsg = null;
        foreach($products as $productId)
        {
            $debugString = "#".$productId;
            $productAvailabilityStatus = mage::getModel('SalesOrderPlanning/ProductAvailabilityStatus')->load($productId, 'pa_product_id');
            if ($productAvailabilityStatus)
            {
                $productDate = $productAvailabilityStatus->getEstimatedDateForQty(1, time());
                $debugString .= ' = '.$productDate;
                $debugString .= ' | '.$productAvailabilityStatus->getMessage();
                if (($worstDate == null) || ($worstDate < $productDate))
                {
                    $worstDate = $productDate;
                    $worstMsg = $productAvailabilityStatus->getMessage();
                }
            }
            $debug[] = $debugString;
        }
        $debug[] = 'Final message : '.$worstMsg;

        //return implode('<br>', $debug);
        return $worstMsg;
    }

     public function getAvailabilityMessageForBundleviewandlistpage($bundleId)
    {
        $debug = array();
        $model= Mage::getModel('catalog/product');
        $bundle =$model->load($bundleId);
        $products = $this->getSubProducts($bundle);
        $debug[] = count($products).' sub products';
        $worstDate = null;
        $worstMsg = null;
        $max_qty_from_bundle=1;
        $max_qty_from_bundle= $this->getmaxqtyforbundle($bundle);
        $default_qty=1;
        $model_mdn=mage::getModel('SalesOrderPlanning/ProductAvailabilityStatus');

        foreach($products as $productId)
        {
            $productAvailabilityStatus = $model_mdn->load($productId, 'pa_product_id');
            if(intval($productAvailabilityStatus['pa_available_qty']) < ($default_qty * $max_qty_from_bundle) )
            {
               $worstMsg=mage::helper('SalesOrderPlanning/ProductAvailabilityRange')->getLabel($storeId, $productAvailabilityStatus->getpa_supply_delay());
            }
            else
            {

                 $product_simple='';
                 $product_simple=$model->load($productId);

                 if ($productAvailabilityStatus)
                    {
                      $productDate = $productAvailabilityStatus->getEstimatedDateForQty(1, time());
                      $debugString .= ' = '.$productDate;
                      $debugString .= ' | '.$productAvailabilityStatus->getMessage();
                      if (($worstDate == null) || ($worstDate < $productDate))
                      {
                          $worstDate = $productDate;
                          $worstMsg = $productAvailabilityStatus->getMessage();
                      }
                   }
                   $debug[] = $debugString;

            }

        }
        $debug[] = 'Final message : '.$worstMsg;

        //return implode('<br>', $debug);
        return $worstMsg;
    }

     public function getAvailabilityMessageForBundleCustomtatva($bundleId,$cartitemqty)
    {
        $debug = array();
        $max_qty_from_bundle = array();
        $model=Mage::getModel('catalog/product');
        $bundle = $model->load($bundleId);
        $products = $this->getSubProducts($bundle);
        $debug[] = count($products).' sub products';
        //$max_qty_from_bundle=1;
        $max_qty_from_bundle= $this->getqtyforbundle($bundle);
        $worstDate = null;
        $worstMsg = null;
        $storeId = $storeId = mage::app()->getStore()->getCode();

        foreach($products as $productId)
        {
             $product_simple='';
             $product_simple=$model->load($productId);
             $productAvailabilityStatus = mage::getModel('SalesOrderPlanning/ProductAvailabilityStatus')->load($productId, 'pa_product_id');

             $max_bundle_qty = $max_qty_from_bundle[$productId];
           // echo  $product_simple->getStockItem()->getQty(); echo '--'; echo $cartitemqty * $max_qty_from_bundle; echo '<br>';
            if(intval($productAvailabilityStatus['pa_available_qty']) < ($cartitemqty * $max_bundle_qty) )
            {

              $worstMsg=mage::helper('SalesOrderPlanning/ProductAvailabilityRange')->getLabel($storeId, $productAvailabilityStatus->getpa_supply_delay());
              break;
            }
            else
            {
              if ($productAvailabilityStatus)
              {
                $productDate = $productAvailabilityStatus->getEstimatedDateForQty(1, time());
                $debugString .= ' = '.$productDate;
                $debugString .= ' | '.$productAvailabilityStatus->getMessage();
                if (($worstDate == null) || ($worstDate < $productDate))
                {
                    $worstDate = $productDate;
                    $worstMsg = $productAvailabilityStatus->getMessage();
                }
             }
             $debug[] = $debugString;
            }
        }
        $debug[] = 'Final message : '.$worstMsg;

        //return implode('<br>', $debug);
        return $worstMsg;
    }
    /**
     * @param $debug
     */
    protected function getSubProducts($bundle)
    {
        $selectionCollection = $bundle->getTypeInstance(true)->getSelectionsCollection(
            $bundle->getTypeInstance(true)->getOptionsIds($bundle), $bundle
        );
        $bundled_items = array();
        foreach($selectionCollection as $option)
        {
            $bundled_items[] = $option->product_id;
        }
        return $bundled_items;
    }

    protected function getmaxqtyforbundle($bundle)
    {
    	$max= 1;
        $selectionCollection = $bundle->getTypeInstance(true)->getSelectionsCollection(
            $bundle->getTypeInstance(true)->getOptionsIds($bundle), $bundle
        );
        $bundled_items_qty = array();
        foreach($selectionCollection as $option)
        {
            $bundled_items_qty[] = $option->selection_qty;
        }

        $max=max($bundled_items_qty);
        return intval($max);
    }


    protected function getqtyforbundle($bundle)
    {
    	$max= 1;
        $selectionCollection = $bundle->getTypeInstance(true)->getSelectionsCollection(
            $bundle->getTypeInstance(true)->getOptionsIds($bundle), $bundle
        );
        $bundled_items_qty = array();
        foreach($selectionCollection as $option)
        {

            $bundled_items_qty[$option->entity_id] = $option->selection_qty;
        }

        return $bundled_items_qty;
    }

   public function getBundleStockDetailForlengow($bundleId)
   {
        $debug = array(); $result=array();
        $model= Mage::getModel('catalog/product');
        $bundle =$model->load($bundleId);
        $products = $this->getSubProducts($bundle);
        $debug[] = count($products).' sub products';
        $worstDate = null;
        $worstMsg = null;
        $max_qty_from_bundle=1;
        $max_qty_from_bundle= $this->getmaxqtyforbundle($bundle);
        $default_qty=1;
        $model_mdn=mage::getModel('SalesOrderPlanning/ProductAvailabilityStatus');

        foreach($products as $productId)
        {

            $productAvailabilityStatus = $model_mdn->load($productId, 'pa_product_id');
            if(intval($productAvailabilityStatus['pa_available_qty']) < ($default_qty * $max_qty_from_bundle) )
            {  
               $worstMsg=mage::helper('SalesOrderPlanning/ProductAvailabilityRange')->getLabel($storeId, $productAvailabilityStatus->getpa_supply_delay());
               $result['available_stock']=$productAvailabilityStatus->getMessageForLengow();
               $result['stock_quantity']=$productAvailabilityStatus->getpa_available_qty() ;
               $result['delivery_time']=$productAvailabilityStatus->getMessageForLengowdeliverytime();
               $result['delivery_description']=$productAvailabilityStatus->getDeliveryDescForLengow();
               break;
            }
            else
            {

                 $product_simple='';
                 $product_simple=$model->load($productId);

                 if ($productAvailabilityStatus)
                    {
                      $productDate = $productAvailabilityStatus->getEstimatedDateForQty(1, time());
                      $debugString .= ' = '.$productDate;
                      $debugString .= ' | '.$productAvailabilityStatus->getMessage();
                      if (($worstDate == null) || ($worstDate < $productDate))
                      {
                          $worstDate = $productDate;
                          $result['available_stock']=$productAvailabilityStatus->getMessageForLengow();
                         $result['stock_quantity']=$productAvailabilityStatus->getpa_available_qty() ;
                         $result['delivery_time']=$productAvailabilityStatus->getMessageForLengowdeliverytime();
                         $result['delivery_description']=$productAvailabilityStatus->getDeliveryDescForLengow();
                      }
                   }
                  // $debug[] = $debugString;

            }

        }

        return $result;
   }

}
<?php
/*
 * 1997-2012 Quadra Informatique
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0) that is available
 * through the world-wide-web at this URL: http://www.opensource.org/licenses/OSL-3.0
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to ecommerce@quadra-informatique.fr so we can send you a copy immediately.
 *
 *  @author Quadra Informatique <ecommerce@quadra-informatique.fr>
 *  @copyright 1997-2012 Quadra Informatique
 *  @version Release: $Revision: 2.0.4 $
 *  @license http://www.opensource.org/licenses/OSL-3.0  Open Software License (OSL 3.0)
 */

class Quadra_Cybermut_Model_Observer
{
    /**
     *  Can redirect to Cybermut payment
     */
    public function initRedirect(Varien_Event_Observer $observer)
    {
        Mage::getSingleton('checkout/session')->setCanRedirect(true);
    }

    /**
     *  Return Orders Redirect URL
     *
     *  @return	  string Orders Redirect URL
     */
    public function multishippingRedirectUrl(Varien_Event_Observer $observer)
    {
        if (Mage::getSingleton('checkout/session')->getCanRedirect()) {
            $orderIds = Mage::getSingleton('core/session')->getOrderIds();
            $orderIdsTmp = $orderIds;
            $key = array_pop($orderIdsTmp);
            $order = Mage::getModel('sales/order')->loadByIncrementId($key);

            if (!(strpos($order->getPayment()->getMethod(), 'cybermut') === false)) {
                Mage::getSingleton('checkout/session')->setRealOrderIds(implode(',', $orderIds));
                Mage::app()->getResponse()->setRedirect(Mage::getUrl('cybermut/payment/redirect'));
            }
        } else {
            Mage::getSingleton('checkout/session')->unsRealOrderIds();
        }

        return $this;
    }

    /**
     *  Disables sending email after the order creation
     *
     *  @return	  updated order
     */
    public function disableEmailForMultishipping(Varien_Event_Observer $observer)
    {  // echo "mauli"; exit;
        $order = $observer->getOrder();


        /* bundle start */
        foreach ($order->getItemsCollection() as $item)
        {
          $optionsArr='';

          //echo "<pre>"; print_r($item); exit;
          if($item->getProductType()=='bundle')
          {
             $qty='';
             $qty=$item->getQtyOrdered();
             $optionsArr = $item->getProductOptions();
             if (count($optionsArr['options']) > 0) {
                foreach ($optionsArr['options'] as $option) {
                // echo "<pre>"; print_r($option);
                  $sku=''; $p_id='';

                  $objModel = Mage::getModel('catalog/product_option_value')->load($option['option_value']);
                  $sku=$objModel->getSku();

                  if($sku!='')
                  {
                     $p_id = Mage::getModel('catalog/product')->getIdBySku(trim($sku));
                     $product_colls=Mage::getModel('catalog/product')->load($p_id);

                     if($p_id!='' && $qty!='')
                     {
                     $this->updateStock($p_id,$qty);
                     }
                  }

               }
           }
          }

        }
        /* bundle end */
        if (!(strpos($order->getPayment()->getMethod(), 'cybermut') === false)) {
            $order->setCanSendNewEmailFlag(false)->save();
        }

        return $this;
    }

    public function updateStock($id, $qty)
   {
    $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($id);
    $write = Mage::getSingleton("core/resource")->getConnection("core_write");
    if($stockItem!='')
    {
     if($stockItem['is_in_stock']!=0)
     {
        $temp=0; $p_qty=0;
        $temp=$stockItem['qty'];
        if($temp!=0)
        {
         $p_qty=$temp - $qty;
        }
         if($p_qty!=0)
         {
            $sql_data1 = "UPDATE `cataloginventory_stock_item`  set qty = '".$p_qty."' where  product_id ='".$id."'";
            if($sql_data1)
             {
               $write->query($sql_data1);
             }
         }
     }
    }
   }

}

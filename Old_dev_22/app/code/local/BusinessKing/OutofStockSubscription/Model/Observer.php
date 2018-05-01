<?php

/**
 * @category    BusinessKing
 * @package     BusinessKing_OutofStockSubscription
 */
class BusinessKing_OutofStockSubscription_Model_Observer
{
	const OUTOFSTOCKSUBSCRIPTION_MAIL_TEMPLATE = 'outofstock_subscription';
	
	public function sendEmailToOutofStockSubscription($observer)
    {  
        $product = $observer->getEvent()->getProduct();

		if ($product) {
			if ($product->getStockItem()) {
				$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());

		           //$isInStock = $product->getStockItem()->getIsInStock();
				$isInStock = $stockItem->getIsInStock();

			    if ($isInStock>=1) {
			    	$subscriptions = Mage::getResourceModel('outofstocksubscription/info')->getSubscriptions($product->getId());
			    	if (count($subscriptions) > 0) {
			    		
					//$prodUrl = $product->getProductUrl();
					$prodUrl = Mage::getBaseUrl();
					$prodUrl = str_replace("/index.php", "", $prodUrl);
					$prodUrl = $prodUrl.$product->getData('url_path');

			    		$storeId = Mage::app()->getStore()->getId();
		            	
		            	// get email template    
			    		$emailTemplate = Mage::getStoreConfig('outofstocksubscription/mail/template', $storeId);
						if (!is_numeric($emailTemplate)) {
							$emailTemplate = self::OUTOFSTOCKSUBSCRIPTION_MAIL_TEMPLATE;
						}
				
						$translate = Mage::getSingleton('core/translate');
							
			    		foreach ($subscriptions as $subscription) {
			    			
			    			$translate->setTranslateInline(false);	
			               	Mage::getModel('core/email_template')
					            ->setDesignConfig(array('area'=>'frontend', 'store'=>$storeId))
					            ->sendTransactional(
					                $emailTemplate,
					                'support',
					                $subscription['email'],
					                '',
					                array(
					                	'product'     => $product->getName(),
					                	'product_url' => $prodUrl,			                	
					                ));			
					        $translate->setTranslateInline(true);
					        
					        Mage::getResourceModel('outofstocksubscription/info')->deleteSubscription($subscription['id']);
			    		}
			    	}			
			    }
			}
            /* bundle product image save */
            if($product->getTypeId()=='bundle')
            {
              $id=$product->getEntityId();
              $write = Mage::getSingleton("core/resource")->getConnection("core_write");
              $read= Mage::getSingleton('core/resource')->getConnection('core_read');
              if($id!='')
            {
                $option_id='';
                $option_id_sql='SELECT option_id FROM `catalog_product_option` WHERE `product_id` = '.$id;
                $option_id=$read->FetchOne($option_id_sql);

              if($option_id!='')
              {
               $option_data=array();
               $option_data_sql='SELECT option_type_id,sku FROM `catalog_product_option_type_value` WHERE `option_id` ='.$option_id;
               $option_data[]=$read->FetchAll($option_data_sql);
              }

             if(is_array($option_data))
             {
               foreach($option_data[0] as $datas)
               {
                  $simple_sku='';   $option_type_id='';

                  $simple_sku=$datas['sku'];
                  $option_type_id=$datas['option_type_id'];
                  if($simple_sku!='')
                  {
                    $simple_product_id =Mage::getModel("catalog/product")->getIdBySku($simple_sku);
                    if($simple_product_id)
                    {
                      $image='';
                      $simple_product=Mage::getModel('catalog/product')->load($simple_product_id);
                      $image=$simple_product->getImage();
                      $check_id='';
                      if($option_type_id!='')
                      {
                      $check='SELECT option_type_image_id FROM `optionimages_product_option_type_image` WHERE `option_type_id` ='.$option_type_id;
                      $check_id=$read->FetchOne($check);
                      }
                      if($image!='' && $check_id=='')
                      {

                           $sql = "INSERT INTO `optionimages_product_option_type_image` (option_type_id,store_id,image)
    		                                          VALUES ('".$option_type_id."', '0','".$image."')";
                           $write->query($sql);
                      }
                      if($check_id!='')
                      {
                        $sql_update="UPDATE optionimages_product_option_type_image SET `image` ='".$image."' WHERE option_type_id=".$option_type_id;
                        $write->query($sql_update);
                      }
                    }
                  }
                }
              }

             }
           }
		}
        //return $this;
    }

	public function cancelOrderItem($observer)
    {
        $item = $observer->getEvent()->getItem();
         /* stock bundle canel start */
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
        /* stock bundle canle end */
        $productId = $item->getProductId();
		if ($productId) {
    		$subscriptions = Mage::getResourceModel('outofstocksubscription/info')->getSubscriptions($productId);
	    	if (count($subscriptions) > 0) {
	    		
	    		$product = Mage::getModel('catalog/product')->load($productId);
				$prodUrl = Mage::getBaseUrl();
				$prodUrl = str_replace("/index.php", "", $prodUrl);
				$prodUrl = $prodUrl.$product->getData('url_path');

	    		$storeId = Mage::app()->getStore()->getId();
            	
            	// get email template    	
	    		$emailTemplate = Mage::getStoreConfig('outofstocksubscription/mail/template', $storeId);
				if (!is_numeric($emailTemplate)) {
					$emailTemplate = self::OUTOFSTOCKSUBSCRIPTION_MAIL_TEMPLATE;
				}
				 
				$translate = Mage::getSingleton('core/translate');
					
	    		foreach ($subscriptions as $subscription) {
	    			
	    			$translate->setTranslateInline(false);	
	               	Mage::getModel('core/email_template')
			            ->setDesignConfig(array('area'=>'frontend', 'store'=>$storeId))
			            ->sendTransactional(
			                $emailTemplate,
			                'support',
			                $subscription['email'],
			                '',
			                array(
			                	'product'     => $product->getName(),
			                	'product_url' => $prodUrl,			                	
			                ));			
			        $translate->setTranslateInline(true);
			        
			        Mage::getResourceModel('outofstocksubscription/info')->deleteSubscription($subscription['id']);
	    		}
	    	}
		}
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
           $p_qty=$temp + $qty;
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

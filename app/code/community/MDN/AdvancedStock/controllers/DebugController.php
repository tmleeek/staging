<?php

class MDN_AdvancedStock_DebugController extends Mage_Adminhtml_Controller_Action {

    public function DebugAction() {

        //force Stock Update for x products

        Varien_Profiler::disable();
        ini_set("memory_limit","1024M");
        ini_set('max_execution_time', 600);

        $productCollection = mage::getModel('catalog/product')->getCollection();        

        $updateCount = 0;
        $loopCount = 0;
        
        //HOW TO USE /UTILISATION
        //will force stock update x products by batch
        //1000 by 1000 bor example
        
        //PARAM 1 :
        //IF mode background task = false will do the re calculations instantantly
        $modeBackgroundTask = false;
        
        //launch 1
        $updateMax = 1000;
        $begin = 0;
        
        //launch 2
        //$updateMax = 1000;
        //$begin = 1000;
        
        //etc ...
        
        
        $taskPriority = 10;

        echo "<br>BEGIN Update";
        foreach($productCollection as $product) {
            $loopCount ++;
            
            if($loopCount>$updateMax)
                break;

            if($loopCount<$begin)
                continue;

            $pid = $product->getId();

            $message = '';

            try {
                
                if($pid>0){

                    if(!$modeBackgroundTask){

                        //force reservation update
                        mage::helper('AdvancedStock/Product_Base')->updateStocks($product);

                        //launch event to allow other updates for product (for example, purchase module handles this event to update waiting for delivery date and quantities
                        Mage::dispatchEvent('advancedstock_product_force_stocks_update_requested', array('product' => $product));

                        $message = 'DONE';

                    }else{

                         mage::helper('BackgroundTask')->AddTask('UpdateStock for product #' . $pid,
                            'AdvancedStock/Product_Base',
                            'updateStocksFromProductId',
                            $pid,
                            null,
                            false,
                            $taskPriority);

                         $message = 'Planned';
                    }
                    
                }
            }catch(Exception $ex){
                echo "<br>Exception for update stock for  product #$pid".$ex->getMessage();
            }

           echo "<br>Update $message for PID=".$product->getId()." [Count=".$updateCount."/".$updateMax." Begin at=".$begin."]";
           $updateCount ++;
           
        }
        die("<br><b>Update Stock Planned or Processed on $updateCount products");       
    }

}

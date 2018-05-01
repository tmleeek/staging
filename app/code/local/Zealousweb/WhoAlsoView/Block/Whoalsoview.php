<?php

class Zealousweb_WhoAlsoView_Block_Whoalsoview extends Mage_Catalog_Block_Product_View
{    
    protected $getsetting = null;
    
    public function __construct() {   
        $this->getsetting = Mage::getSingleton('whoalsoview/getsetting');
    }    
    
    public function lastviewproduct($pro_sku)
    {
        /*$collectiondata = Mage::getModel('whoalsoview/whoalsoview')->getCollection();
        $collectiondata->getSelect()
                    ->columns('GROUP_CONCAT(product_id FROM who_also_view))');
        echo $collectiondata->getSelect()->__toString();die();*/
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table = $resource->getTableName('who_also_view');
        $query = "SELECT *,GROUP_CONCAT(product_id) AS products FROM $table where find_in_set($pro_sku,product_id)";
        
        $collectiondata = $readConnection->fetchAll($query);
        //echo "<pre>";print_r($collectiondata);die();
        $skus = array();
        $newarray = array();
        $proskus = array();
        foreach($collectiondata as $key=>$value)
        {
            $skus = explode(",",$value['products']);
            foreach($skus as $key => $value_sku) 
            {
                array_push($newarray, $value_sku);
            }
        }
       foreach($newarray as $data)
       {
           if($data!=$pro_sku)
           {
               $proskus[] = $data;
           }
       }
       return array_unique($proskus);
        //echo "<pre>";print_r($proskus);die();
        /*$product_sku_count = array();
        if(!($this->getsetting->isEnabled())) {
            return $product_sku_count;
        }
        $user_session = $_SESSION['MosViewUser'];
        $Model = Mage::getModel('whoalsoview/whoalsoview');
        $connection = $Model->getCollection();
        $connection->addFilter('product_sku', $pro_sku);
        //$connection->addFieldToFilter('session_cod',array('neq'=>$user_session)); 
        $all_data = $connection->getData();
        $product_session = array();
        foreach($all_data as $data)
        {
            $connection_2 = $Model->getCollection();
            $connection_2->addFilter('session_cod', $data['session_cod']);
            $connection_2->addFieldToFilter('product_sku',array('neq'=>$pro_sku)); 
            $connection_2->getData();
            array_push($product_session, $connection_2->getData());
        } 
       
        $product_sku_array = array();
        foreach($product_session as $key => $prodct_data){
            foreach($prodct_data as $step_prodct){
                $cat_data_id =  explode(',',$step_prodct['product_categories']); 
                $result = array_intersect($cat_id, $cat_data_id);
                if($this->getsetting->getshowCatProductOnly()){ 
                if(!empty($result)){ array_push($product_sku_array, $step_prodct['product_id']); }
                }
                else {
                   array_push($product_sku_array, $step_prodct['product_id']);
                }
            }
        }
        $product_sku_array_mini = $product_sku_array;
        foreach($product_sku_array_mini as $key=>$procuts_array)
        {
                $tmp = array_count_values($product_sku_array);
                $cnt = $tmp[$procuts_array]; 
                $product_sku_count[$procuts_array]=$cnt;
        }
        arsort($product_sku_count);
        return $product_sku_count;  */
    }
    
    public function displaytitle()
    {
       return $this->getsetting->getDisplayTitle();
    }
    
    public function displayproductlength()
    {
       return $this->getsetting->getMaxProductDisplay();
    }
    
    public function showInStock()
    {
       return $this->getsetting->getshowInStockProduct();
    }
   
    
}
<?php
/**
 * Shop By Brands
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitmanufacturers
 * @version      3.3.1
 * @license:     zAuKpf4IoBvEYeo5ue8Cll0eto0di8JUzOnOWiuiAF
 * @copyright:   Copyright (c) 2014 AITOC, Inc. (http://www.aitoc.com)
 */
/**
* @copyright  Copyright (c) 2010 AITOC, Inc. 
*/
class Aitoc_Aitmanufacturers_Model_Observer
{
    protected static $_brandRegex = '/manufacturer|brand/i';
    
    protected $_activeStateFilters = array();
    
    /**
     * 
     * @param Varien_Event_Observer $observer
     * @author vlasenko@aitoc.com
     */
    public function onCatalogProductCollectionLoadBefore(Varien_Event_Observer $observer)
    {
        /* @var $collection Varien_Data_Collection_Db */
        $collection = $observer->getEvent()->getCollection();        
        
        /**
         * @author vlasenko
         * add second sort parameter in case of aitmanufacturers_sort is not set
         */
        
        if (Mage::helper('aitmanufacturers')->canUseLayeredNavigation(Mage::registry('shopby_attribute'), true))
        {
            $order = $collection->getSelect()->getPart(Zend_Db_Select::ORDER);            
            if (isset($order[0][1]))
            {
                $collection->getSelect()->order('e.entity_id ' . $order[0][1]);
            }
        }
    }
    
    public function rewrite_urls(){
        //Mage::getSingleton('adminhtml/session')->addNotice('observer ready');
        if(Mage::getSingleton('adminhtml/session')->getData('aitmanufacturers_update_stores')===true){
            Mage::register('aitmanufacturers_update_get_stores', true);
            Mage::register('aitmanufacturers_fillout_inprogress',true);
            Mage::getModel('aitmanufacturers/aitmanufacturers')->getCollection()->save();
            Mage::getSingleton('adminhtml/session')->setData('aitmanufacturers_update_stores',false);
        }
    } 
    
    public function removeCategoryPosition(Varien_Event_Observer $observer)
    {        
        if (!Mage::helper('aitmanufacturers')->isLNPEnabled() && Mage::helper('aitmanufacturers')->canUseLayeredNavigation(Mage::registry('shopby_attribute'), true))
        {
            $select = $observer->getEvent()->getCollection()->getSelect();//echo "<pre>";print_r($observer);
            $order = $select->getPart('order');
            $select->setPart('order', array());

            Mage::app()->getLayout()->createBlock('catalog/layer_view', 'aitmanufacturers.leftnav2', array('template' => 'catalog/layer/view.phtml'));
            $select->setPart('order', $order);
            
            $select = $observer->getEvent()->getCollection()->getSelect();
            
            $from = $select->getPart(Zend_Db_Select::FROM);
            $select->setPart(Zend_Db_Select::FROM,$from);
            /**
             * There was a code that adds additional categories in the select condition.
             * You can find it on the graveyard or contact AITOC support )
             */
        }
        
    }
    
    /**
     * @param Varien_Event_Observer $observer 
     */
    public function removeCategoryId(Varien_Event_Observer $observer)
    {
        if (Mage::helper('aitmanufacturers')->canUseLayeredNavigation(Mage::registry('shopby_attribute'), true) && (!Mage::app()->getRequest()->getParam('cat') || (Mage::app()->getRequest()->getParam('cat') == 'clear')))
        {    
            $select = $observer->getEvent()->getCollection()->getSelect();            
            $this->_removeTableFromSelect($select);
        }
    }
    
    protected function _removeTableFromSelect($select)
    {
        $columns = $select->getPart('columns');
        
        if ($columns)
        {
            foreach ($columns as $columnKey => $column)
            {
                if (isset($column[0]) && ('cat_index' == $column[0]))
                {
                    unset($columns[$columnKey]);
                }
            }

            $select->setPart('columns', $columns);
        }

        $from = $select->getPart(Zend_Db_Select::FROM);
        unset($from['cat_index']);
        $select->setPart(Zend_Db_Select::FROM, $from);  
        
        $where = $select->getPart(Zend_Db_Select::WHERE);
        if(!empty($where))
        {
            foreach($where as $key => $condition)
            {
                if(preg_match('/\(cat_index/',$condition) > 0)
                {
                    unset($where[$key]);
                }
            }
            $select->setPart(Zend_Db_Select::WHERE, $where); 
        }   

        $order = $select->getPart(Zend_Db_Select::ORDER);
        if(!empty($order))
        {
            foreach($order as $key => $condition)
            {
                if(preg_match('/cat_index/',$condition[0]) > 0)
                {
                    unset($order[$key]);
                }
            }
            $select->setPart(Zend_Db_Select::ORDER, $order); 
        }        
    }
    
    /**
     *
     * @param Varien_Event_Observer $observer 
     */
    public function setBrandId(Varien_Event_Observer $observer)
    {
        $helper = $observer->getEvent()->getHelper();        
        
        //var_dump(Mage::app()->getRequest()->get('shopby_attribute'));
        if (!Mage::registry('shopby_attribute'))
        {
            Mage::register('shopby_attribute', Mage::app()->getRequest()->get('shopby_attribute'));
        }

        if (Mage::helper('aitmanufacturers')->isLNPEnabled() && Mage::helper('aitmanufacturers')->canUseLayeredNavigation(Mage::registry('shopby_attribute'), true))
        {            
            if (Mage::getSingleton('core/session')->getAitocManufacturersCurrentManufacturerId())
            {
                $helper->setParam(Mage::registry('shopby_attribute'), Mage::getSingleton('core/session')->getAitocManufacturersCurrentManufacturerId());
                $helper->setParam('shopby_attribute', Mage::registry('shopby_attribute'));
            }
        }
    }
    
    /**
     *
     * @param Varien_Event_Observer $observer 
     */
    public function removeBrandFilters(Varien_Event_Observer $observer)
    {
        if (!Mage::registry('shopby_attribute'))
        {
            $attributeCode = Mage::app()->getRequest()->get('shopby_attribute');
        } else {
            $attributeCode = Mage::registry('shopby_attribute');
        }
        
        if (Mage::helper('aitmanufacturers')->canUseLayeredNavigation($attributeCode, true))
        {
            $block = $observer->getEvent()->getLayerViewBlock();
            $filters = $block->getFilters();
            for ($i = 0; $i < count($filters); $i++)
            {   
                $attr = $filters[$i]->getAttributeModel();
                if(is_object($attr))
                {    
                    if ($attributeCode == $attr->getData('attribute_code'))
                    {                    
                        $block->unsetFilter($i);                    
                    }
                }      

            }
        }
    }
    
    /**
     *
     * @param Varien_Event_Observer $observer 
     */
    public function removeStateBrandFilters(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('aitmanufacturers')->isLNPEnabled() && Mage::helper('aitmanufacturers')->canUseLayeredNavigation(Mage::registry('shopby_attribute'), true))
        {
            $block = $observer->getEvent()->getLayerStateBlock();
            $filters = $block->getActiveFilters();
            
            for ($i = 0; $i < count($filters); $i++)
            {
                $attr = '';
                if ($filters[$i]->getFilter()->hasAttributeModel())
                {
                    $attr = $filters[$i]->getFilter()->getAttributeModel();
                }
                // remove duplicates
                if (in_array($filters[$i]->getName(), $this->_activeStateFilters))
                {
                    $block->unsetActiveFilter($i);
                    continue;
                }
                if(is_object($attr))
                {
                    if (Mage::registry('shopby_attribute') == $attr->getAttributeCode())
                    {
                        $block->unsetActiveFilter($i);
                        continue;
                    }                      
                }

                $this->_activeStateFilters[] = $filters[$i]->getName();
            }
        }
    }    
    
    public function checkUrlRewrite(Varien_Event_Observer $observer)
    {
          $request = Mage::app()->getFrontController()->getRequest();
          /*$cvarchartable='core_url_rewrite';
          $id_path='';$key_id='';
          $write = Mage::getSingleton("core/resource")->getConnection("core_write");
          $read = Mage::getSingleton('core/resource')->getConnection('core_read');
          $store_id= Mage::app()->getStore()->getId();
          $check_brand_en = stripos($request->getPathInfo(),"brand-collection");
          $check_brand_fr = stripos($request->getPathInfo(),"marque-collection");
         if($check_brand_en!==false || $check_brand_fr!==false)
         {
         if($check_brand_en == true)
         {
           $temp_keyword='brand-collection';
           $str_arr=strrev($request->getPathInfo());
       	   $arr=explode('/',$str_arr);
         }

         if($check_brand_fr == true)
        {
           $temp_keyword='marque-collection';
       	   $str_arr=strrev($request->getPathInfo());
       	   $arr=explode('/',$str_arr);
        }
        if($arr)
        {
          if($arr[0]!='')
          {
            $temp=$arr[0];
          }
          else
          {
           $temp=$arr[1];
          }

          if($temp!='' && $temp)
          {
            $temp=strrev($temp);
            $temp_key=explode('.html',$temp);
            $url_temp_key=$temp_key[0];

            if($url_temp_key && $url_temp_key!='')
            {
             $coll_id_col=Mage::getModel('aitmanufacturers/aitmanufacturers')->getCollection();
             $coll_id_col->addFieldToFilter('url_key',$url_temp_key);
             foreach($coll_id_col as $col)
             {
              $key_id=$col->getId();
             }
            }
          }
          if($key_id && $key_id!='')
          {
              $requestUrl='brands/index/view/id/'.$key_id;
              $id_path='brands/collection/'.$key_id;
              $target_path=substr($request->getPathInfo(), 1);
              $data='';
              if($id_path)
              {
                $select = "select request_path from ".$cvarchartable." where id_path= '".$id_path."' LIMIT 1";
                $data=$read->FetchOne($select);
              }
              if($data=='')
              {
                $insert_url_key = "INSERT INTO ".$cvarchartable." (store_id, id_path, request_path, target_path, is_system)
    		                                          VALUES ('".$store_id."','".$id_path."', '".$target_path."', '".$requestUrl."', '0');";
                $write->query($insert_url_key);
                header("Location: " . $target_path);
              }
          }

        }
       }*/

        if (isset($_GET['___from_store']))
        {
            $urlRewrite = $observer->getEvent()->getDataObject();

            if ((get_class($urlRewrite) == 'Mage_Core_Model_Url_Rewrite') && (strpos($urlRewrite->getIdPath(),'brands/') !== false))
            {
                $incorrectId = substr(strrchr($urlRewrite->getIdPath(), "/"), 1);
                $collection = $urlRewrite->getCollection()->setPageSize(1);

                $collection->getSelect()
                                ->join(array('brandurl' => $urlRewrite->getResource()->getTable('aitmanufacturers/aitmanufacturers_stores')),'main_table.id_path = CONCAT(\'brands/\' , brandurl.id)','')
                                ->join(array('brandurl2' => $urlRewrite->getResource()->getTable('aitmanufacturers/aitmanufacturers_stores')),'brandurl2.manufacturer_id = brandurl.manufacturer_id','')
                                ->where('brandurl.store_id = ?', Mage::app()->getStore($_GET['___store'])->getId())
                                ->where('brandurl2.store_id = ?', Mage::app()->getStore($_GET['___from_store'])->getId())
                                ->where('brandurl2.id = ?', $incorrectId);
                if ($collection->getFirstItem()->getData('request_path'))
                {
                    $targetUrl = Mage::app()->getRequest()->getBaseUrl(). '/' . $collection->getFirstItem()->getData('request_path');

                    Mage::app()->getRequest()->setDispatched(true);
                    Mage::app()->getResponse()
                        ->clearAllHeaders()
                        ->setRedirect($targetUrl)
                        ->sendResponse();
                    exit();
                }
            }
        }

        return true;
    }

}
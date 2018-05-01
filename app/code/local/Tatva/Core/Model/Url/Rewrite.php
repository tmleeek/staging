<?php
class Tatva_Core_Model_Url_Rewrite extends Mage_Core_Model_Url_Rewrite {

    /**
     * Implement logic of custom rewrites
     *
     * @param   Zend_Controller_Request_Http $request
     * @param   Zend_Controller_Response_Http $response
     * @return  Mage_Core_Model_Url
     */
    public function rewrite(Zend_Controller_Request_Http $request=null, Zend_Controller_Response_Http $response=null)
	{
        //echo Zend_Controller_Request_Http." ".Zend_Controller_Response_Http;
		$admin = stripos(Mage::helper('core/url')->getCurrentUrl(),"admin");
	  
	if($admin === false)
	{
        $brand_data = array();
        if (!Mage::isInstalled()) {
            return false;
        }
        if (is_null($request)) {
            $request = Mage::app()->getFrontController()->getRequest();
        }
        if (is_null($response)) {
            $response = Mage::app()->getFrontController()->getResponse();
        }
        if (is_null($this->getStoreId()) || false === $this->getStoreId()) {
            $this->setStoreId(Mage::app()->getStore()->getId());
        }
	 
       //add by nisha
        $check_brand_en = stripos($request->getPathInfo(),"brand");
        $check_brand_fr = stripos($request->getPathInfo(),"marque");

        $check_list_en = eregi("list", $request->getPathInfo());  
        $check_list_fr = eregi("liste", $request->getPathInfo());
        /*$var_list = eregi_replace("brandname", "brand",$request->getPathInfo());
		echo $var_list;exit;*/
       //for marque & display_mode - true

       $check_brand_url = eregi(".html", $request->getPathInfo());  
       
       if($check_brand_en == true)
       {
       	 $arr = explode("/brand/",$request->getPathInfo());
       }
       if($check_brand_fr == true)
       {
       	 $arr = explode("/marque/",$request->getPathInfo());
       }   
	   
       if($check_list_en == true && $check_brand_en == true)
       {
       	 $arr1 = explode("/list/",$arr[1]);
       }

      if($check_list_fr == true && $check_brand_fr == true)
       {
       	 $arr1 = explode("/liste/",$arr[1]);
       }

       
        
//http://127.0.0.1/az_boutique_live/fr/marque/Amefa/petits-prix.html        
//petits-prix.html?display_mode=list&marque=37    
   
         $var_querystring = $this->_getQueryString(); 
         if(($check_brand_en == true or $check_brand_fr == true) && ($check_list_en == false && $check_list_fr == false) && ($check_brand_url == false) && (count($arr) == 2))
         {
         	$brand_fr_url = substr($arr[1], 0, -1);
         	$brand_data = Mage::getModel('bm/manage')->getCollection()
						->addFieldToFilter('brand_url_fr', array('eq' => $brand_fr_url))
						->getData();
						
			if(isset($brand_data[0]['brand_name_fr']))
	  			$request->setPathInfo("brand/index/view/brandname/".$brand_data[0]['brand_name_fr']."/");
			else
			{
				header("Location: " . Mage::getBaseUrl());exit;
			}
         }
         else if(($check_list_en == true && $check_brand_en == true)  or ($check_list_fr == true && $check_brand_fr == true)) 
         {
         	$brand_data = Mage::getModel('bm/manage')->getCollection()
						->addFieldToFilter('brand_url_fr', array('eq' => $arr1[0]))
						->getData();
			
			if(isset($brand_data[0]['brand_name_fr']))
	  			$request->setPathInfo("brand/index/list/brandname/".$brand_data[0]['brand_name_fr']);
			else
			{
				header("Location: " . Mage::getBaseUrl());exit;
			}
         }
        else if($check_brand_url == true && ($check_brand_en == true or $check_brand_fr == true))
         {
          $arr_brand_html = explode("/",$request->getPathInfo());
          
		  $brand_data = Mage::getModel('bm/manage')->getCollection()
						->addFieldToFilter('brand_url_fr', array('eq' => $arr_brand_html[2]))
						->getData();
						
		  	if(isset($brand_data[0]['brand_name_fr']))
				$brand=Mage::getModel('brand/brand_cms')->getBrandId($brand_data[0]['brand_name_fr']);
		  	else
		  	{
				header("Location: " . Mage::getBaseUrl());exit;
			}
         
          //echo $arr_brand_html[3].'?display_mode=list&marque='.$brand;exit;
          $var_querystring = 'display_mode=list&marque='.$brand;
          $sub_url =  $arr_brand_html[3];
          if(isset($arr_brand_html[4]))
          {
          	 $sub_url = $arr_brand_html[3].'/'.$arr_brand_html[4];
          }

         if(isset($arr_brand_html[5]))
          {
          	 $sub_url = $arr_brand_html[3].'/'.$arr_brand_html[4].'/'.$arr_brand_html[5];
          }
          $request->setPathInfo($sub_url);

         
         }



        $requestCases = array();
        $requestPath = trim($request->getPathInfo(), '/');



        /**
         * We need try to find rewrites information for both cases
         * More priority has url with query params
         */
        if ($queryString = $var_querystring) { 
            $requestCases[] = $requestPath . '?' . $queryString;
            $requestCases[] = $requestPath;
        } else {
            $requestCases[] = $requestPath;
        }

        $this->loadByRequestPath($requestCases);

        /**
         * Try to find rewrite by request path at first, if no luck - try to find by id_path
         */
        if (!$this->getId() && isset($_REQUEST['___from_store'])) {
            try {
                $fromStoreId = Mage::app()->getStore($_REQUEST['___from_store']);
            } catch (Exception $e) {
                return false;
            }

            $this->setStoreId($fromStoreId)->loadByRequestPath($requestCases);
            if (!$this->getId()) 
			{
            	return false;
            }
            $this->setStoreId(Mage::app()->getStore()->getId())->loadByIdPath($this->getIdPath());
        }

        if (!$this->getId()) 
		{
			$requested_url = $_SERVER['REQUEST_URI'];
			if( strpos($requested_url,".html")!==false)
			{
				$old_url = explode('/',$requested_url);		
				$count = count($old_url);
				$new_url = '';
				if((strpos($requested_url,"fr")!==false || strpos($requested_url,"profr")!==false || strpos($requested_url,"en")!==false || strpos($requested_url,"proen")!==false) && $count > 2 )
				{
					for($i=2; $i<$count; $i++)
					{
						if($i < $count-1)
							$new_url = $new_url.$old_url[$i].'/';
						else	
							$new_url = $new_url.$old_url[$i];
					}
			   }
			   elseif($count > 2)
			   {
			   		for($i=2; $i<$count; $i++)
					{
						if($i < $count-1)
							$new_url = $new_url.$old_url[$i].'/';
						else	
							$new_url = $new_url.$old_url[$i];
					}
				}
				else
				{
					$new_url =  $old_url[1];
				}
				
				$new_url =  substr_replace($new_url, "", -5, 5);
				$url_new = Mage::getResourceModel('sqlicore/url_rewrite')->getUrl($new_url); 
				
				/*print_r($url_new);
				exit;*/
				$data = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
				
				if(!empty($url_new))
				{
					$store_detail = Mage::getModel('core/store')->load($url_new['store_id']);
					
					$new_url = $data.$store_detail->getCode().'/'.$url_new['request_path'];
					
					header('HTTP/1.1 301 Moved Permanently');
		            header("Location: " . $new_url);
					exit;
				}
				
			}
			
			return false;
        }


        $request->setAlias(self::REWRITE_REQUEST_PATH_ALIAS, $this->getRequestPath());
        $external = substr($this->getTargetPath(), 0, 6);
        $isPermanentRedirectOption = $this->hasOption('RP');
		
		if ($external === 'http:/' || $external === 'https:') {
            if ($isPermanentRedirectOption) {
                header('HTTP/1.1 301 Moved Permanently');
            }
            header("Location: " . $this->getTargetPath());
            exit;
        } else {
            $targetUrl = $request->getBaseUrl() . '/' . $this->getTargetPath();
        }
        $isRedirectOption = $this->hasOption('R');
        if ($isRedirectOption || $isPermanentRedirectOption) {
            if (Mage::getStoreConfig('web/url/use_store') && $storeCode = Mage::app()->getStore()->getCode()) {
                $targetUrl = $request->getBaseUrl() . '/' . $storeCode . '/' . $this->getTargetPath();
            }
            if ($isPermanentRedirectOption) {
                header('HTTP/1.1 301 Moved Permanently');
            }
            header('Location: ' . $targetUrl);
            exit;
        }

        if (Mage::getStoreConfig('web/url/use_store') && $storeCode = Mage::app()->getStore()->getCode()) {
            $targetUrl = $request->getBaseUrl() . '/' . $storeCode . '/' . $this->getTargetPath();
        }

        if ($queryString = $var_querystring) {
               $targetUrl .= '?' . $queryString;
        }

        $request->setRequestUri($targetUrl);
        $request->setPathInfo($this->getTargetPath());

        return true;
	}
	else
	{    
		if (!Mage::isInstalled()) {
            return false;
        }
        if (is_null($request)) {
            $request = Mage::app()->getFrontController()->getRequest();
        }
        if (is_null($response)) {
            $response = Mage::app()->getFrontController()->getResponse();
        }
        if (is_null($this->getStoreId()) || false===$this->getStoreId()) {
            $this->setStoreId(Mage::app()->getStore()->getId());
        }

        $requestCases = array();
        $requestPath = trim($request->getPathInfo(), '/');

        /**
         * We need try to find rewrites information for both cases
         * More priority has url with query params
         */
        if ($queryString = $this->_getQueryString()) {
            $requestCases[] = $requestPath .'?'.$queryString;
            $requestCases[] = $requestPath;
        }
        else {
            $requestCases[] = $requestPath;
        }

        $this->loadByRequestPath($requestCases);

        /**
         * Try to find rewrite by request path at first, if no luck - try to find by id_path
         */
        if (!$this->getId() && isset($_GET['___from_store'])) {
            try {
                $fromStoreId = Mage::app()->getStore($_GET['___from_store']);
            }
            catch (Exception $e) {
                return false;
            }

            $this->setStoreId($fromStoreId)->loadByRequestPath($requestCases);
            if (!$this->getId()) {
                return false;
            }
            $this->setStoreId(Mage::app()->getStore()->getId())->loadByIdPath($this->getIdPath());
        }

        if (!$this->getId()) {
            return false;
        }


        $request->setAlias(self::REWRITE_REQUEST_PATH_ALIAS, $this->getRequestPath());
        $external = substr($this->getTargetPath(), 0, 6);
        $isPermanentRedirectOption = $this->hasOption('RP');
        if ($external === 'http:/' || $external === 'https:') {
            if ($isPermanentRedirectOption) {
                header('HTTP/1.1 301 Moved Permanently');
            }
            header("Location: ".$this->getTargetPath());
            exit;
        } else {
            $targetUrl = $request->getBaseUrl(). '/' . $this->getTargetPath();
        }
        $isRedirectOption = $this->hasOption('R');
        if ($isRedirectOption || $isPermanentRedirectOption) {
            if (Mage::getStoreConfig('web/url/use_store') && $storeCode = Mage::app()->getStore()->getCode()) {
                $targetUrl = $request->getBaseUrl(). '/' . $storeCode . '/' .$this->getTargetPath();
            }
            if ($isPermanentRedirectOption) {
                header('HTTP/1.1 301 Moved Permanently');
            }
            header('Location: '.$targetUrl);
            exit;
        }

        if (Mage::getStoreConfig('web/url/use_store') && $storeCode = Mage::app()->getStore()->getCode()) {
                $targetUrl = $request->getBaseUrl(). '/' . $storeCode . '/' .$this->getTargetPath();
            }

        if ($queryString = $this->_getQueryString()) {
        	$targetUrl .= '?'.$queryString;
        }

        $request->setRequestUri($targetUrl);
        $request->setPathInfo($this->getTargetPath());

        return true;
	}
    }

}

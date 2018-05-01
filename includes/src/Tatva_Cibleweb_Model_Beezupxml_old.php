<?php

class Tatva_Cibleweb_Model_Beezupxml1 extends Mage_Core_Model_Abstract {

	static $PAGE_SIZE = 100;

	private $_storeModel = null;
	private $_entityIypeId    = null;
	private $_attributeImage = null;

	const CIBLEWEB_CONFIG_ENABLE       = 'cibleweb-config/config/enable';
	const CIBLEWEB_CONFIG_TYPE         = 'cibleweb-config/config/type';
	const CIBLEWEB_CONFIG_FILENAME     = 'cibleweb-config/config/filename';
	const CIBLEWEB_CONFIG_PATH         = 'cibleweb-config/config/path';
	const CIBLEWEB_CONFIG_HOT          = 'cibleweb-config/config/hot';
	const CIBLEWEB_CONFIG_USERNAME     = 'cibleweb-config/config/username';
	const CIBLEWEB_CONFIG_PASSWORD     = 'cibleweb-config/config/password';
	const CIBLEWEB_CONFIG_PASSIVEMODE  = 'cibleweb-config/config/passivemode';

	const LOG_FILE_NAME                = 'Export-Cibleweb';
	const CSV_DELIMITEUR   			   = ',';
	const PRODUCT					   = 1;
	const IMAGE 					   = 2;


	protected function _checkConfig(){
		if( ! $this->_config( self::CIBLEWEB_CONFIG_ENABLE ) ){
			Mage::log ( 'Cibleweb - Géneration catalog : La fonctionnalité est désactivée ', Zend_log::INFO, $this->getLogFile() );
			return false ;
		}
		if( ! $this->_config( self:: CIBLEWEB_CONFIG_TYPE ) ||
		    ! $this->_config( self:: CIBLEWEB_CONFIG_FILENAME ) ) {
			Mage::log ( "Le type de transfert et/ou le nom du fichier sont mal configurés : system > configuration > ciblweb > iziflux > configuration", Zend_Log::ERR );
			return false;
		}
		return true;
	}

	protected function _config( $path ){
		return Mage::getStoreConfig ( $path , $this->_store()->getId() );
	}

	protected function _store(){
		if( ! $this->_storeModel ){
			$this->_storeModel = Mage::getModel('core/store')->load('fr');
		}
		return $this->_storeModel;

	}

	public function getDelimiter(){
		return self::CSV_DELIMITEUR ;
	}

	public function getLogFile (){
		return date('Ymd').'-'.self::LOG_FILE_NAME.'.log';
	}

	public function getCompleteUrl( $link ,$type ) {
		$storeId = '3';
	    $baseUrl = Mage::app()->getStore(  $storeId )->getBaseUrl();
		if( $type == 1 ){
			return $baseUrl . $link ;
		}
		if( $type == 2 ){
			if( $link == 'no_selection' ) return NULL;
			return $baseUrl . 'media/catalog/product' . $link;
		}
	}

	public function runSaveCatalog(){
            
		if(!$this->_checkConfig()){
			return;
		}
                
                ini_set("display_errors", 1);
                error_reporting(E_ALL);
                ini_set ( 'memory_limit', '4096M' );
                //ini_set('max_execution_time', 0);
                set_time_limit(-1);
		Mage::log ( '---------------------', Zend_log::INFO, $this->getLogFile());
		Mage::log ( 'Cibleweb - Géneration catalog : DEBUT >>> ', Zend_log::INFO, $this->getLogFile() );

		switch ( $this->_config(self:: CIBLEWEB_CONFIG_TYPE)){
                    
            case Tatva_Cibleweb_Model_System_Config_Source_Type::LOCAL :
                
				if (  ! $this->_config( self:: CIBLEWEB_CONFIG_PATH ) ) {
					Mage::log ( " le chemin du répertoire de stockage est mal configuré : system > configuration > ciblweb > iziflux > configuration", Zend_Log::ERR );
				}
                                     // echo "sdfdf";die();
			    $data = $this->_getProductData();
                      
                $this->saveCsvFileLocal( $data );
                
				break;

			case Tatva_Cibleweb_Model_System_Config_Source_Type::FTP :
				if (  ! $this->_config( self:: CIBLEWEB_CONFIG_HOT ) ||
					  ! $this->_config( self:: CIBLEWEB_CONFIG_USERNAME ) ||
					  ! $this->_config( self:: CIBLEWEB_CONFIG_PASSWORD ) ) {
					  Mage::log ( " le hôte et/ou le login et/ou le pass sont mal configurés : system > configuration > ciblweb > iziflux > configuration", Zend_Log::ERR );
				}
				$data = $this->_getProductData();
				$this->saveCsvFileRemote( $data );
				break;
		}
		Mage::log ( 'Cibleweb - Géneration catalog : FIN >>> ', Zend_log::INFO, $this->getLogFile() );
	}

public function xml_escape($s)
{
    $s = html_entity_decode($s, ENT_QUOTES, 'UTF-8');
    $s = htmlspecialchars($s, ENT_QUOTES, 'UTF-8', false);
    return $s;
}

	public function saveCsvFileLocal( $datas ){
		$root = $this->_config( self::CIBLEWEB_CONFIG_PATH);

		if( ! $this->createDir( $root )){
			return ;
		}

		 //$pathFile = $root.$this->_config( self::CIBLEWEB_CONFIG_FILENAME);

		 $pathFile = $_SERVER['DOCUMENT_ROOT'].'beezup/'.'beezup.xml';
         try{


/*$test_array = array (
  '<![CDATA[bla]]>' => 'blub',
  'foo' => 'bar',
  'another_array' => array (
    'stack' => 'overflow',
  ),
);
*/
//$pathFile_1 = $_SERVER['DOCUMENT_ROOT'].'lengow/'.'test1.xml';

      $xmlnode = new Varien_Simplexml_SimpleXMLExtended('<products/>');




            $data[]=Mage::helper('cibleweb')->xmlLine();
            //$xmlDocument = new Varien_SimpleXml_Element('<products />');
            $i=0;
            $count_product=count($datas);
            $count=count($data[0]);
//echo '<pre>';print_r($datas);exit;
           foreach($datas as $keys => $title)
           {

             $xml = $xmlnode->addChild('product');
             //catagory
             $xml->catagory = NULL;
             if($datas[$keys]['catagory'])
             {
               $val=$datas[$keys]['catagory'];
               $val = str_replace('&','&amp;',$val);
               $xml->catagory->addCData($val);
             }


             //unique_id
              $xml->unique_id= NULL;
             if($datas[$keys]['unique_id'])
             {
               $val=$datas[$keys]['unique_id'];
               $xml->unique_id->addCData($val);
             }


             //title
              $xml->title= NULL;
              if($datas[$keys]['title'])
             {
               $val=$datas[$keys]['title'];
               $val = str_replace('&','&amp;',$val);
               $xml->title->addCData($val);
             }

             $xml->description=NULL;
             if($datas[$keys]['description'])
             {
               $val=$datas[$keys]['description'];
               $val = str_replace('&','&amp;',$val);
               $xml->description->addCData($val);
             }


             //prix
              $xml->prix=NULL;
              if($datas[$keys]['prix'])
             {
               $val=$datas[$keys]['prix'];
               $val = str_replace('&','&amp;',$val);
               $xml->prix->addCData($val);
             }

             //product url
             $xml->product_URL= NULL;
              if($datas[$keys]['product_URL'])
             {
               $val=$datas[$keys]['product_URL'];
               $val = str_replace('&','&amp;',$val);
               $xml->product_URL->addCData($val);
             }

             //image url
              $xml->image_URL = NULL;
              if($datas[$keys]['image_URL'])
             {
               $val=$datas[$keys]['image_URL'];
               $val = str_replace('&','&amp;',$val);
               $xml->image_URL->addCData($val);
             }


             //'shipping_costs',
              $xml->shipping_costs = NULL;
              if($datas[$keys]['shipping_costs'])
             {
               $val=$datas[$keys]['shipping_costs'];
               $val = str_replace('&','&amp;',$val);
               $xml->shipping_costs->addCData($val);
             }


             
              $xml->available_stock = NULL;
              if($datas[$keys]['available_stock'])
             {
               $val=$datas[$keys]['available_stock'];
               $val = str_replace('&','&amp;',$val);
               $xml->available_stock->addCData($val);
             }

			//'available_stock',
             /*$xml->available_stock = NULL;
             if($datas[$keys]['available_stock'])
             {
               $val=$datas[$keys]['available_stock'];
               $val = str_replace('&','&amp;',$val);
                $xml->available_stock->addCData($val);
             }*/

             $xml->stock_quantity = NULL;
             if($datas[$keys]['unique_id'])
             {
              $val=$datas[$keys]['stock_quantity'];
                $xml->stock_quantity->addCData($val);
             }

			//'delivery_time',
             $xml->delivery_time = NULL;
             if($datas[$keys]['delivery_time'])
             {
               $val=$datas[$keys]['delivery_time'];
               $xml->delivery_time->addCData($val);
             }


            //'delivery_description',
            if (is_numeric($datas[$keys]['delivery_description'])) 
             {
                 $datas[$keys]['delivery_description'] = "Shipped within ".$datas[$keys]['delivery_description']." days";
             } 
            $xml->delivery_description =NULL;
             if($datas[$keys]['delivery_description'])
             {
               $val=$datas[$keys]['delivery_description'];
               $val = str_replace('&','&amp;',$val);
               $xml->delivery_description->addCData($val);
             }


			//'warrenty',
             $xml->warrenty = NULL;
             if($datas[$keys]['warrenty'])
             {
               $val=$datas[$keys]['warrenty'];
               $val = str_replace('&','&amp;',$val);
               $xml->warrenty->addCData($val);
             }

            // echo $datas[$keys]['model_refrence'];
			//'model_refrence',
             $xml->model_refrence = NULL;
             if($datas[$keys]['model_refrence'])
             {
               $val=$datas[$keys]['model_refrence'];
               $xml->model_refrence->addCData($val);
             }

			//'D3E',
              $xml->D3E = NULL;
             if($datas[$keys]['D3E'])
             {
               $val=$datas[$keys]['D3E'];
               $xml->D3E->addCData($val);
             }


			//'brand',
            $xml->brand = NULL;
             if($datas[$keys]['brand'])
             {
               $val=$datas[$keys]['brand'];
               $val = str_replace('&','&amp;',$val);
               $xml->brand->addCData($val);
             }

            //'catagories',
             $xml->catagories = NULL;
             if($datas[$keys]['catagories'])
             {
               $val=$datas[$keys]['catagories'];
               $val = str_replace('&','&amp;',$val);
               $xml->catagories->addCData($val);
             }

			//'ean',
             $xml->ean = NULL;
             if($datas[$keys]['ean'])
             {
               $val=$datas[$keys]['ean'];
               $xml->ean->addCData($val);
             }


			//'currency',
             $xml->currency = NULL;
             if($datas[$keys]['currency'])
             {
               $val=$datas[$keys]['currency'];
               $xml->currency->addCData($val);
             }


			//'state',
            $xml->state= NULL;
             if($datas[$keys]['state'])
             {
               $val=$datas[$keys]['state'];
               $xml->state->addCData($val);
             }


             //'discount_ad',
             $xml->discount_ad = NULL;
             if($datas[$keys]['discount_ad'])
             {
               $val=$datas[$keys]['discount_ad'];
               $val = str_replace('&','&amp;',$val);
               $xml->discount_ad->addCData($val);
             }


            //'discount_code',
              $xml->discount_code = NULL;
             if($datas[$keys]['discount_code'])
             {
               $val=$datas[$keys]['discount_code'];
               $val = str_replace('&','&amp;',$val);
               $xml->discount_code->addCData($val);
             }

            //'discount_description',
            $xml->discount_description = NULL;
             if($datas[$keys]['discount_description'])
             {
               $val=$datas[$keys]['discount_description'];
               $val = str_replace('&','&amp;',$val);
               $xml->discount_description->addCData($val);
             }


            //'discount_begining_date',
             $xml->discount_begining_date = NULL;
             if($datas[$keys]['discount_begining_date'])
             {
               $val=$datas[$keys]['discount_begining_date'];
               $xml->discount_begining_date->addCData($val);
             }


            //'discount_end_date',
            $xml->discount_end_date = NULL;
             if($datas[$keys]['discount_end_date'])
             {
               $val=$datas[$keys]['discount_end_date'];
               $xml->discount_end_date->addCData($val);
             }


            //'crossed_price',
            $xml->crossed_price = NULL;
             if($datas[$keys]['crossed_price'])
             {
               $val=$datas[$keys]['crossed_price'];
               $xml->crossed_price->addCData($val);
             }

            //'discount_percentage',
            $xml->discount_percentage = NULL;
             if($datas[$keys]['discount_percentage'])
             {
               $val=$datas[$keys]['discount_percentage'];
               $xml->discount_percentage->addCData($val);
             }

            //'promotion_type',
              $xml->promotion_type = NULL;
             if($datas[$keys]['promotion_type'])
             {
               $val=$datas[$keys]['promotion_type'];
               $val = str_replace('&','&amp;',$val);
               $xml->promotion_type->addCData($val);
             }


            //'second_hand_product',
              $xml->second_hand_product= NULL;
               $val=$datas[$keys]['second_hand_product'];
               $xml->second_hand_product->addCData($val);

            // 'sales',
            $xml->sales = NULL;
            if($datas[$keys]['sales'] == 0 || $datas[$keys]['sales'] == 1)
             {
               $val=$datas[$keys]['sales'];
               $xml->sales->addCData($val);
             }


             // 'bundle',
               $xml->bundle = NULL;
            if($datas[$keys]['bundle'])
             {
               $val=$datas[$keys]['bundle'];
               $xml->bundle->addCData($val);
             }



            //'weight',
              $xml->weight = NULL;
             if($datas[$keys]['weight'])
             {
               $val=$datas[$keys]['weight'];
               $xml->weight->addCData($val);
             }


			//'color',
              $xml->color = NULL;
             if($datas[$keys]['color'])
             {
               $val=$datas[$keys]['color'];
               $val = str_replace('&','&amp;',$val);
               $xml->color->addCData($val);
             }


			//'material',
               $xml->material = NULL;
             if($datas[$keys]['material'])
             {
               $val=$datas[$keys]['material'];
               $val = str_replace('&','&amp;',$val);
               $xml->material->addCData($val);
             }


			//'size',
            $xml->size = NULL;
             if($datas[$keys]['size'])
             {
               $val=$datas[$keys]['size'];
               $xml->size->addCData($val);
             }


			//'type'
             $xml->type = NULL;
             if($datas[$keys]['type'])
             {
               $val=$datas[$keys]['type'];
               $xml->type->addCData($val);
             }

//echo $datas[$keys]['unique_id'].'=='.$pathFile.'<br>';
           }
		     $pathFile = $_SERVER['DOCUMENT_ROOT'].'/beezup/'.'beezup.xml';
             $xmlnode->saveXML($pathFile);
          }
          catch ( Exception $e ) {
            Mage::logException($e);
            throw new Exception ($e->getMessage());
        }
    }

	public function saveCsvFileRemote( $datas ){


	    $paramConnecFtp             = array();
        $paramConnecFtp['host']     = $this->_config( self::CIBLEWEB_CONFIG_HOT      );
        $paramConnecFtp['user']     = $this->_config( self::CIBLEWEB_CONFIG_USERNAME );
        $paramConnecFtp['password'] = $this->_config( self::CIBLEWEB_CONFIG_PASSWORD );
		if( $mode = $this->_config( self::CIBLEWEB_CONFIG_PASSIVEMODE ) ){
			$paramConnecFtp['passive'] = $mode;
		}
		//- Etablir la connexion
        $ftp = new Varien_Io_Ftp();
		if( ! $ftp->open( $paramConnecFtp ) ){
			Mage::log ( "Cibleweb - Géneration catalog : Echec de connexion FTP ", Zend_log::INFO, $this->getLogFile() );
			return;
		}
		//- Enregistrement du fichier chez cibleweb...
		try {
			$ftp->write( $this->_config( self::CIBLEWEB_CONFIG_FILENAME ) , $datas );
		}catch(Exception $e){
			Mage::log ( $e->getMessage() , Zend_log::INFO, $this->getLogFile() );
    	}

    }

    /**
	 * Création du répertoire de stockage
	 * @param chemin du répertoire
	 * @return bool : true si le répertoire est bien créé, false sinon
     */
	protected function createDir( $dir ) {
		if (! is_dir ( $dir )) {
			if (! mkdir ( $dir, 0755, true )) {
				Mage::log ( "Erreur lors de la création du répertoire de stockage \"$dir\"", Zend_Log::ERR );
				return false;
			}
		}
		return true;
	}

	#########################
	#####   BUILD DATA  #####
	#########################

    protected function _getProductData(){
	   $lastPageNumber = $this->getProductCollection()->getLastPageNumber() ; 

	   //$lastPageNumber = 10;
		// Initialisation
		$data = array();
		if( $this->_config( self:: CIBLEWEB_CONFIG_TYPE) == Tatva_Cibleweb_Model_System_Config_Source_Type::FTP ){
			$data = "";
		}
                
        //$data[] = Mage::helper('cibleweb')->xmlLine();
        for($pageNumber = 1; $pageNumber <= $lastPageNumber; $pageNumber ++) {
            $productCollection = $this->getProductCollection( true , $pageNumber );
            
           // foreach ($productCollection as $product){
                try{
                     // Mage::log($product->getEntityId(),null,"lengow.log");
                     $line = array();
                     //$id=$product->getEntityId();
                     $prod = Mage::getModel('catalog/product')->load(4156);   // echo $id.'<br>';
                     //echo "<pre>";print_r($prod->getData());die();
                     $deee=$prod->getDeee();
                     $tax_id=$prod->getTaxClassId();
                     
                     $product = $prod;
                   /*  echo  $percent = Mage::getSingleton('tax/calculation')->getRate($request->setProductClassId($taxclassid));        */
                    //catagory
                     /* if($this->getCategoryName($product))
                      {
                        $line['catagory'] = $this->getCategoryName($product);
                      }
                      else
                      {
                       $line['catagory']='';
                      }*/

                    //Id produit
                    if($product->getId())
                    {
                      $line['unique_id'] = $prod->getId();
                    }
                    else
                    {
                      $line['unique_id']=' ';
                    }


                    //dénomination concise
                    //echo $prod->getName();                    die();
                    if($product->getName())
                    {
                    	$color_label = '';
						$gamme_collection = '';
						$productModel = Mage::getModel('catalog/product');
						if($prod['gamme_collection_new'] != '')
						{
							//$gamme_collection = ' - '.$prod['gamme_collection_new'];
							$attr = $productModel->getResource()->getAttribute("gamme_collection_new");
							if ($attr->usesSource()) {
							    $gamme_collection = $attr->getSource()->getOptionText($prod->getGammeCollectionNew());
							}
							if($gamme_collection != '')
							{
								$gamme_collection = ' - '.$gamme_collection;
							}
						}





                       $attr = $productModel->getResource()->getAttribute("manufacturer");
						if ($attr->usesSource()) {
						    $manufacture_label_1 = $attr->getSource()->getOptionText($prod->getManufacturer());
						}
						if($manufacture_label_1 != '')
						{
							$manufacture_label = ' - '.$manufacture_label_1;
						}
					  //echo Mage::getResourceModel('catalog/product')->getAttributeRawValue($product->getId(), 'marque');exit;
					  $line['title'] = $product->getName().$gamme_collection.$manufacture_label;
                    }
                    else
                    {
                      $line['title']='';
                    }
                    //description complète
                    if($prod['meta_description'])
                    {
					 $line['description'] = $prod['meta_description'];
                    }
                    else
                    {
                      $line['description']='';
                    }


                    //price
                    $store = $this->_store()->getId();
                    $price=Mage::app()->getHelper('tax')->getPrice($product, $product->getPrice(), true, null, null, null, $store , null) ;

                    $promo = $this->getPromotion($product);
                    $new_price=$promo['new_price'];
                    if($new_price)
                    {
                     $cur_price=$new_price;
                    }
                    else
                    {
					$cur_price = $price;
                    }
                       /*echo $discountedPrice = Mage::app()->getHelper('tax')->getPrice($product, $product->getFinalPrice(), true, null, null, null, $store , null);*/
                    $deee_price=0;
                    if($deee[0]['value'])
	                {
    	                $classTax = Mage::getModel('tax/class')->setClassType(Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT)->load('TVA normale','class_name');
            			$request = Mage::getSingleton('tax/calculation')->getRateRequest();
            			$request->setCountryId('FR');
            			$request->setProductClassId($tax_id);
            			$tax = Mage::getSingleton('tax/calculation')->getRate($request);
    					
    					$deee_price = number_format($deee[0]['value'] * (1 + ($tax / 100)),2);
	                }
					  $final_prix=0;
					  $final_prix=$cur_price +$deee_price;
					  //$final_prix=$this->getcurrencyinpound($final_prix);
                                          $final_prix=$cur_price;
                      $line['prix'] =number_format($final_prix,2,",","");
                    
					
					
					 //$url = $this->getCompleteUrl( $product->getUrlPath() , self::PRODUCT  );
                      $url = $product->getProductUrl();
                    if($url)
                    {
                       	$line['product_URL'] = $url;
                    }
                    else
                    {
                       $line['product_URL']=' ';
                    }

                    //image url
                    if($product->getThumbnail())
                    {
                       $storeId = '3';
	                   $baseUrl = Mage::app()->getStore(  $storeId )->getBaseUrl();
                       $line['image_URL']=$baseUrl.'media/catalog/product'.$product->getThumbnail();
                    }
                    else
                    {
                       $line['image_URL']=' ';
                    }

                    //frais de port
                      if($product->getTypeId() != 'virtual')
                      {

					            $classTax = Mage::getModel('tax/class')->setClassType(Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT)->load('TVA 20%','class_name');
        			    		$request = Mage::getSingleton('tax/calculation')->getRateRequest();
        			    		$request->setCountryId('FR');
        			    		$request->setProductClassId($classTax->getId());
								//comment by nisha $tax = Mage::getSingleton('sqlitax/calculation')->getRate($request);
        			    		$tax = Mage::getSingleton('tax/calculation')->getRate($request);
								//$resTax = number_format($tax / 100 ,3,',','');
								$result = $this->getShippingAmount($product->getWeight(),$tax);
								$result=$this->getcurrencyinpound($result);
								$result=number_format($result,2,",","");
					     $line['shipping_costs'] = $result;
                      }
                      else
                      {
                         $line['shipping_costs'] = '';
                      }

                  
				    $cat_name = '';
                    if($this->getCategoriesName($product))
                    {//echo 'yes';exit;
                      $path= $this->getCategoriesName($product);
                      $ids = explode('/', $path);
                       unset($ids[0]);
                       //echo "<pre>"; print_r($ids);
                       foreach($ids as $id)
                       {
                          $name=Mage::getModel('catalog/category')->setStoreId(3)->load($id)->getName();
                          if($id!=2)
                          {
                           $cat_name.=$name.' > ';
                          }
                       }
				       /*$cat = $this->getCategoriesName($product);
                       $size=count($cat);
                       for($i=0; $i<$size; $i++)
                       {


                          $cat_name.= $cat[$i].' > ';
                       }  */

                         $line['catagories'] = substr($cat_name,0,strlen($cat_name)-3);
                    }
                    else
                    {
                       $line['catagories'] = '';
                    }
//echo '<pre>';print_r($line);exit;
                    //available stock
                    $itemStock = Mage::getModel('cataloginventory/stock_item')->loadByProduct ( $product ) ;

					 /*comment by nisha$statusStock = $product->getStatusStock();

					if($statusStock == Mage::helper('Tatvainventory')->__('In stock')){
						//Disponibilité du produit
						$deliveryDays = $product->getDeliveryDays() ;
						$line['delivery_time'] = $deliveryDays;

						//statut de disponibilité du produit
						$line['available_stock'] = 'En stock';

					}elseif($statusStock == Mage::helper('Tatvainventory')->__('Underway replenishment')){
						//Disponibilité du produit
						$deliveryDays = $product->getDeliveryDays() ;
						if(!$product->getIsVirtual()){
							$deliveryDays += 1 + 3;
						}
						$line['delivery_time'] = $deliveryDays;

						//statut de disponibilité du produit
						$line['available_stock'] = 'En cours de réapro.';
					}elseif($statusStock == Mage::helper('Tatvainventory')->__('On order')){
						//Disponibilité du produit
						$deliveryDays = $product->getDeliveryDays() ;
						if(!$product->getIsVirtual()){
							$deliveryDays += 1 + 3;
						}
						$line['delivery_time'] = $deliveryDays;

						//statut de disponibilité du produit
						$line['available_stock'] = 'Sur commande';
					}*/


              $stock_qty='';  $availabilityMessage=''; $estimateddate='';
              $productAvailabilityStatus = Mage::getModel('SalesOrderPlanning/ProductAvailabilityStatus')->load($prod['entity_id'], 'pa_product_id');
              //echo "<pre>"; print_r($productAvailabilityStatus->getData()); exit;
              $bundle_data=array();

              if ($prod['type_id'] == 'bundle')
              {

                  //$availabilityMessage= Mage::helper('BundleAvailability')->getAvailabilityMessageForBundle($prod['entity_id']);
                  //$bundle_data= Mage::helper('BundleAvailability')->getBundleStockDetailForbeezup($prod['entity_id']) ;
                  $bundle_data= Mage::helper('BundleAvailability')->getBundleStockDetailForlengow($prod['entity_id']) ;
                  $availabilityMessage=''; $stock_qty_bundle=''; $delivery_time_bundle='';  $delivery_desc='';

                  $line['available_stock']=$bundle_data['available_stock'];
                  $line['stock_quantity']=$bundle_data['stock_quantity'] ;
                  $line['delivery_time']=$bundle_data['delivery_time'] ;
                  $line['delivery_description']=$bundle_data['delivery_description'] ;
                  //echo $line['available_stock'].'1';die();
              }
              else
              {
                   $availabilityMessage = '';
                  if ($productAvailabilityStatus) {
                    $availabilityMessage = $productAvailabilityStatus->getMessageForbeezup();
                 }
                 $line['available_stock']=$availabilityMessage;
                 $line['stock_quantity']=$productAvailabilityStatus->getpa_available_qty() ;
                 $line['delivery_time']=$productAvailabilityStatus->getMessageForBeezupdeliverytime();
                 $line['delivery_description']=$productAvailabilityStatus->getDeliveryDescForbeezup();
                 //echo $line['available_stock'].'2';die();
              }
             //echo $line['delivery_description'];die();
              //echo get_class($productAvailabilityStatus);die();




                    //Garantie
					$value = $prod['garantie_produit'];
					if(!$value){
						$value = 0;
					}

                    $line['warrenty'] = $value;

                    //model refrence
                    if($prod['sku'])
                    {
                     $line['model_refrence'] = $prod['sku'];
                     }
                     else
                     {
                       $line['model_refrence']='';
                     }

                     //deee
					 if(!empty($deee))
					 {
	                     if($deee[0]['value'])
	                     {
	                            $classTax = Mage::getModel('tax/class')->setClassType(Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT)->load('TVA normale','class_name');
        			    		$request = Mage::getSingleton('tax/calculation')->getRateRequest();
        			    		$request->setCountryId('FR');
        			    		$request->setProductClassId($tax_id);
        			    	    $tax = Mage::getSingleton('tax/calculation')->getRate($request);
								//$resTax = number_format($tax / 100 ,3,',','');
								
								$deee_val=$deee[0]['value'] * (1 + ($tax / 100));
								$deee_val=$this->getcurrencyinpound($deee_val);
								$result_1 = number_format($deee_val,2);

	                       $line['D3E']=$result_1;
	                     }
					 }
                     else
                     {
                     $line['D3E']='';
                     }
                    //brand name
					$line['brand'] = $manufacture_label_1;

                   //ena


                    if($prod['ean13'])
                    {
                      $line['ean'] = $prod['ean13'];
                    }
                    else
                    {
                     $line['ean'] = '';
                    }
                     
                    //currency
                    $line['currency']= Mage::app()->getStore()->getCurrentCurrencyCode();
                    $line['currency']= 'GBP';
                     $promo = $this->getPromotion($product);
                     //echo "<pre>";print_r($promo);die();
                    //discount_ad
                    if($promo[4]=='1')
                    {
                      $line['discount_ad']='Promotion !';
                    }
                     elseif($promo[4]=='2')
                    {
                      $line['discount_ad']='Sale !';
                    }
                     elseif($promo[4]=='3')
                    {
                      $line['discount_ad']='Sale !';
                    }
                     elseif($promo[4]=='4')
                    {
                      $line['discount_ad']='Sale !';
                    }
                     elseif($promo[4]=='5')
                    {
                      $line['discount_ad']='Sale !';
                    }
                    else
                    {
                      $line['discount_ad']='';
                    }
                    //echo $line['discount_ad'];die();
                    //Catégorie
				   //$line[] = $this->getCategoryName($product);

                    //state
                    $line['state']=$product->getStatus();

                    //discount code
                    $line['discount_code']='';

                    //dicount decription
                    $line['discount_description']='';

                    //promotion
                    $promo = $this->getPromotion($product);
                    //start date
                    if($promo)
                    {
                    $line['discount_begining_date']=$promo[0];
                    $line['discount_end_date']=$promo[1];
                    $line['crossed_price']=$promo[2];
                    $line['discount_percentage']=$promo[3];
                    $line['promotion_type']=$promo[4];
                    }
                    //second hand
                    $second=0;
                    $line['second_hand_product']=$second;
                    //sales

                    if($promo[4]=='1')
                    {

                      $val=0;
                    }
                    else
                    {
                      $val=0;
                    }
                    $line['sales']=$val;
                    //bundle
                    if($prod->getTypeId()=='bundle')
                    {

                    $line['bundle']=1;
                    }
                    else
                    {
                     $line['bundle']='';
                    }
                    //weight
                    if($prod['weight'])
                    {
                      $line['weight']= $prod['weight'];
                    }
                    else
                    {
                     $line['weight']='';
                    }
                    if($prod['colour'])
                    {
                      $line['color']= $this->getselectAttributevalue('colour',$prod['colour']);
                    }
                    else
                    {
                     $line['color']='';
                    }
                    //material
                    if($prod['materiaux'])
                    {
					 $line['material'] =$this->getselectAttributevalue('materiaux',$prod['materiaux']);
                    }
                    else
                    {
                    $line['material']='';
                    }
                    if($prod['dimensions'])
                    {
                     $line['size'] = $prod['dimensions'];
                    }
                    elseif($prod['longueur'] || $prod['largeur'] || $prod['hauteur'] || $prod['diametre'])
                    {
                      $line['size']=$prod['longueur'] +  $prod['largeur']+ $prod['hauteur']+ $prod['diametre'];
                    }
                    else
                    {
                     $line['size']='';
                    }

                    //  type
                    if($prod['type_id'])
                    {
                     $line['type'] = $prod['type_id'];
                    }
                    else
                    {
                        $line['type'] ='';
                    }
                    //echo $line["delivery_description"];die();
                    $str = $line["available_stock"];
                    if ((strpos($str,"En stock") !== false)) 
                    {
                        $line["available_stock"] = "In stock";
                    }
                    if ((strpos($str,"En cours") !== false)) 
                    {
                        $line["available_stock"] = "Out of stock";
                    }
                    //echo  "<pre>"; print_r($line); exit;
                     //$data[] = $line;
                     if( $this->_config( self:: CIBLEWEB_CONFIG_TYPE) == Tatva_Cibleweb_Model_System_Config_Source_Type::FTP ){  echo "in";
						$data .= implode( self::CSV_DELIMITEUR , $line )."\n";
					}else{
						$data[] = $line;
					}

					Mage::log ( "ADD > produit id={$product->getId()}", Zend_log::INFO, $this->getLogFile() );
					//unset( $line  );
				}catch(Exception $e){  Mage::log($product->getEntityId().'--'.$e->getMessage(),null,"lengow_system_exeption.log");
					Mage::log ( "ERREUR > produit id={$product->getId()} : " . $e->getMessage(), Zend_log::INFO, $this->getLogFile() );
					Mage::logException($e);
					unset( $line );
				}
		  	}
		//}
        echo  "<pre>"; print_r($data); exit;
		return $data;

	}

	public function getselectAttributevalue($attr,$att_ids)
    {
     $att_label=array();
     $att_array='';  $result='';

     $att_array =explode(',',$att_ids);

     foreach($att_array as $att_array_id)
     {
       $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $attr)->setStoreId($storeId);
       foreach ($attribute->setStoreId(4)->getSource()->getAllOptions(false) as $option){
       if($option['value']==$att_array_id)
        {
          $att_label[]= $option['label'];
        }
       }
     }
     $result=implode(',',$att_label);
     //echo "<pre>";print_r($result);
     return $result;
    }

	/**
	 * Retourne le montant des frais de port
	 * @return int
	 */
	public function getShippingAmount($weight,$tax){

        $amount = 0;
        $final_shipp_amount=9.90;
       if(Mage::getStoreConfig('carriers/colissimo/active')== 1 )
       {
        	$code='colissimo';
          	$carrierModel = Mage::getStoreConfig('carriers/'.$code.'/model');
      		$shipping = Mage::getModel($carrierModel);

      		//pays par défaut
      		$countryId = Mage::getStoreConfig('general/country/default');

      		$rule = Mage::getModel('tatvashipping/rule')
          		->getCollection()
            	->addShippingFilter($shipping->getCode())
          		->addCountryFilter($countryId)
          		->addWeightFilter($weight)
          		->getFirstItem(); 

          	if($rule && $rule->getId()){
          		 $amount = $rule->getAmount()* (1 + ($tax / 100));
				 

          	}
		  
            if(number_format($amount,2) <= 9.90)
            {
              $final_shipp_amount=$amount;
            }
			
			
       }


        
    	return $final_shipp_amount;
	}

	public function getProductCollection( $filter = false  , $pageNumber = NULL ){

		$collection = Mage::getModel ("catalog/product")->getCollection();
		//$collection->setStoreId(1);
		$collection->addAttributeToFilter('status','1');
	    //$collection->addFieldToFilter ('entity_id',5294);
		$collection->addTaxPercents();
                
		//add by nisha
		//Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

		//$collection->setPageSize ( Tatva_Cibleweb_Model_ExportCatalog::$PAGE_SIZE);
                $collection->setPageSize(Tatva_Cibleweb_Model_ExportCatalog::$PAGE_SIZE);
		if($filter){
			$collection->addAttributeToSelect ('*');
		    //$collection->addFieldToFilter ('entity_id',5294);
			$collection->setOrder('entity_id', 'ASC');
			$collection->setCurPage($pageNumber);
		}
         //echo "<pre>"; print_r($collection->getData()); exit;
		return 	$collection ;
	}

	public function getCategoryName( $product ){
		 $catIds  =  $product->getCategoryIds();
        
		 $categorys = Mage::getSingleton('catalog/category')->getCollection()
		             ->setStoreId(3)
					 ->addAttributeToSelect( 'name' )
					 ->addAttributeToFilter( 'entity_id',array( 'in'=>$catIds ) )
					 ->addAttributeToFilter( 'level',4 );

		if($categorys->getSize()){
		    $id=$categorys->getFirstItem()->getEntityId();
			 return Mage::getModel('catalog/category')->setStoreId(3)->load($id)->getName();
		}else{
		    $categorys = Mage::getSingleton('catalog/category')->getCollection()
			         ->setStoreId(3)
					 ->addAttributeToSelect( 'name' )
					 ->addAttributeToFilter( 'entity_id',array( 'in'=>$catIds ) )
					 ->addAttributeToFilter( 'level',3 );
			if( $categorys->getSize() ){
		       $id=$categorys->getFirstItem()->getEntityId();
			    return Mage::getModel('catalog/category')->setStoreId(3)->load($id)->getName();
		    }else{
		    	$categorys = Mage::getSingleton('catalog/category')->getCollection()
				     ->setStoreId(3)
					 ->addAttributeToSelect( 'name' )
					 ->addAttributeToFilter( 'entity_id',array( 'in'=>$catIds ) )
					 ->addAttributeToFilter( 'level',2 );
				if( $categorys->getSize() ){
			       $id=$categorys->getFirstItem()->getEntityId();
				    return Mage::getModel('catalog/category')->setStoreId(3)->load($id)->getName();
			    }else{
		        	return;
			    }
		    }
		}

	}
    	public function getCategoriesName( $product ){
		 /*$catIds  =  $product->getCategoryIds();
         //echo $categorys->getSize();
         //echo "<pre>"; print_r($catIds);
		$categorys = Mage::getSingleton('catalog/category')->getCollection()
					 ->addAttributeToSelect( 'name' )
					 ->addAttributeToFilter( 'entity_id',array( 'in'=>$catIds ) );
        foreach($categorys as $cat)
           {
            $cats[]=$cat->getName();

           }

*/
         $path = "";
        $catIds  =  $product->getCategoryIds();
         //echo $categorys->getSize();
         //echo "<pre>"; print_r($catIds);
         $categorys = Mage::getSingleton('catalog/category')->getCollection()
		             ->setStoreId(3)
					 ->addAttributeToSelect( 'name' )
					 ->addAttributeToFilter( 'entity_id',array( 'in'=>$catIds ) )
					 ->addAttributeToFilter( 'level',6 );

         if($categorys->getSize()){
		     $cats=$categorys->getFirstItem()->getEntityId();
             $path= $categorys->getFirstItem()->getPath();

		}
        else
        {
		 $categorys = Mage::getSingleton('catalog/category')->getCollection()
		             ->setStoreId(3)
					 ->addAttributeToSelect( 'name' )
					 ->addAttributeToFilter( 'entity_id',array( 'in'=>$catIds ) )
					 ->addAttributeToFilter( 'level',5 );

		  if($categorys->getSize()){
		     $cats=$categorys->getFirstItem()->getEntityId();
             $path= $categorys->getFirstItem()->getPath();

		}
		else
		{

	   	$categorys = Mage::getSingleton('catalog/category')->getCollection()
		             ->setStoreId(3)
					 ->addAttributeToSelect( 'name' )
					 ->addAttributeToFilter( 'entity_id',array( 'in'=>$catIds ) )
					 ->addAttributeToFilter( 'level',4 );

		if($categorys->getSize()){
		     $cats=$categorys->getFirstItem()->getEntityId();
             $path= $categorys->getFirstItem()->getPath();

		}else{
		    $categorys = Mage::getSingleton('catalog/category')->getCollection()
			         ->setStoreId(3)
					 ->addAttributeToSelect( 'name' )
					 ->addAttributeToFilter( 'entity_id',array( 'in'=>$catIds ) )
					 ->addAttributeToFilter( 'level',3 );
			if( $categorys->getSize() ){
		       $cats=$categorys->getFirstItem()->getEntityId();
               $path= $categorys->getFirstItem()->getPath();
		    }else{
		    	$categorys = Mage::getSingleton('catalog/category')->getCollection()
				     ->setStoreId(3)
					 ->addAttributeToSelect( 'name' )
					 ->addAttributeToFilter( 'entity_id',array( 'in'=>$catIds ) )
					 ->addAttributeToFilter( 'level',2 );
				if( $categorys->getSize() ){
			      $cats=$categorys->getFirstItem()->getEntityId();
                  $path= $categorys->getFirstItem()->getPath();
			    }
               }
		    }
		}
	  }

         return $path;
    }
	public function getBrandName($brandId ){
		$storeId = '3' ;
		$productEntityTypeId = Mage::getModel ( 'eav/entity' )->setType ( 'catalog_product' )->getTypeId ();
		$brandAttribute = Mage::getModel('eav/entity_attribute')->loadByCode($productEntityTypeId,'manufacturer');
		$brands = Mage::getModel('eav/entity_attribute_option')	->getCollection()
																->addFieldToFilter('attribute_id',array('='=>$brandAttribute->getAttributeId()))
																->setStoreFilter($storeId, false)
																->setIdFilter( $brandId )
																->load();
		return $brands->getFirstItem()->getValue();
	}

	public function getEntityTypeId() {
		if( ! $this->_entityIypeId ){
			$this->_entityIypeId = Mage::getModel('eav/config')->getEntityType( 'catalog_product' )->getEntityTypeId();
		}

		return $this->_entityIypeId ;
	}


	public function getCompleteDescription( $product ){

	    $productId = $product->getEntityId();
		$attrubtesCollection = Mage::getModel('eav/entity_attribute')
							   ->getCollection()
							   ->setEntityTypeFilter( $this->getEntityTypeId() )
							   ->addFieldToFilter( 'attribute_code',array('nin'=>Mage::helper('cibleweb')->notSelectedAttributes()))
							   ->addFieldToFilter( 'is_visible', 1 );
	    $completedesc =  $product->getShortDescription();
		$completedesc = str_replace(array("\r\n\s","\s", "\n", "\r")," ",$completedesc);

		return $completedesc;
	}

	public function getPromotion($product){
		$promo = array();
		$customerGroupId = 0;
		$catalogRuleProducts = Mage::getModel('catalogrule/rule_product_price')
								->getCollection()
								->addFieldToFilter('main_table.website_id',$this->_store()->getWebsiteId())
								->addFieldToFilter('main_table.customer_group_id',$customerGroupId)
								;
                                 /*echo "mauli".$catalogRuleProducts->getSelect(); exit;  */
		$catalogRuleProducts->getSelect()->where('main_table.product_id = ?', $product->getId());

		$tableName = Mage::getModel('catalogrule/rule_product_price')->getResource()->getTable('catalogrule/rule_product');
		$catalogRuleProducts->getSelect()
			->from(array('rule_product' => $tableName), 'rule_id')
			->where ('rule_product.product_id = main_table.product_id ')
			->where('rule_product.customer_group_id = ?',$customerGroupId)
			->where('rule_product.website_id = ?',$this->_store()->getWebsiteId());
//echo $this->_store()->getWebsiteId(); exit;

		if(!$catalogRuleProducts->getSize()){

//echo "test"; exit;
			//prix remisé
			$promo[] = "";
			$promo[] = "";
			$promo[] = "";
			$promo[] = "";
			$promo[] = "";
			$promo['new_price'] = "";
		}else{
			//$catalogRule = $catalogRuleProducts->getFirstItem();
			foreach($catalogRuleProducts as $catalogRule){
				$rule = Mage::getModel('catalogrule/rule')->load($catalogRule->getRuleId());
				
				$oldPrice = Mage::app()->getHelper('tax')->getPrice($product, $product->getPrice(), true, null, null, null, $this->_store()->getId() , null) ;
				$newPrice = Mage::app()->getHelper('tax')->getPrice($product, $catalogRule->getRulePrice(), true, null, null, null, $this->_store()->getId() , null) ;
                 $oldPrice=$this->getcurrencyinpound($oldPrice);
                 $newPrice=$this->getcurrencyinpound($newPrice);
				//date/heure de début promotion
				$fromDate = date ('d/m/Y');
				if($rule->getFromDate()){
				     $fromDate = $toDate = date ('Y-m-d', strtotime($rule->getFromDate())).' 00:00:00';
				}
				$promo[] = $fromDate;

				//date/heure de fin promotion
				$toDate = "";
				if($rule->getToDate()){
				  
                    $toDate = date ('Y-m-d', strtotime($rule->getToDate())).' 23:59:00';
				}

				$promo[] = $toDate;

				//prix TTC avant promotion
				$promo[] = number_format($oldPrice,2,",","");;

				//promotion (montant en euros)
					$promo['new_price'] = number_format($newPrice,2,",","");

				//pourcentage de la démarque
				if($rule->getSimpleAction() == 'by_percent' || $rule->getSimpleAction() == 'to_percent'){
					$promo[] = $rule->getDiscountAmount();
				}else{
					$percent = 100 - ($newPrice * 100 / $oldPrice);
					$promo[] = $percent;
				}
				//prix remisé
				if($rule->getSimpleAction() == 'by_percent' || $rule->getSimpleAction() == 'to_percent'){
					//$promo[] = $oldPrice - $newPrice;
				}else{
					//$promo[] = $rule->getDiscountAmount();
				}

				//add by nisha
				$promo[] = $rule->getpromotion_type() == 'NULL' ? '' : $rule->getpromotion_type();

				break;
			}
		}

		return $promo;
	}


    public function getAvailableSupplientTotalQty($sku)
    {
      $read= Mage::getSingleton('core/resource')->getConnection('core_read');
      $total_qty=0;
      $sql_qty="SELECT available_qty FROM `erp_view_supplyneeds_base` WHERE `sku` LIKE '".$sku."'";
      if($sql_qty)
      {
        $available_qty[]=$read->fetchAll($sql_qty);
      }

      if(is_array($available_qty))
      {
          foreach($available_qty[0] as $qty)
          {
             $total_qty+=$qty['available_qty'];
          }
      }
      return $total_qty;
    }
	
	public function getcurrencyinpound($price)
	{
		 $from = 'EUR';
		 $to = 'GBP';
		 return Mage::helper('directory')->currencyConvert($price, $from, $to);
					  

	}

}
<?php
/**
 * created : 02 oct. 2009
 * 
 * @category Tatva
 * @package Tatva_Cibleweb
 * @author emchaabelasri
 * @copyright Tatva - 2009 - http://www.Tatva.com
 * 
 * EXIG : REF-004
 * REG  : MARK-201
 */

/**
 * Géneration et enregistrement, en local ou sur un serveur en se connectant en FTP, 
 * de la catalog produit. Le fichier utilisé est de format CSV.  
 * 
 * @package Tatva_Cibleweb
 */
class Tatva_Cibleweb_Model_ExportCatalog extends Mage_Core_Model_Abstract {
	
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
	const CSV_DELIMITEUR   			   = '|';
	const PRODUCT					   = 1;
	const IMAGE 					   = 2;
	
	/**
	 * Check the configuration data 
	 */
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
	
	/**
     * Return configuration data 
     * @param $path string, path of the field
     * @return string | int
	 */
	protected function _config( $path ){
		return Mage::getStoreConfig ( $path , $this->_store()->getId() );		
	}
	/**
	 * Retrieve AZ Store Model 
	 * @return Mage_Core_Model_Store
	 */
	protected function _store(){
		if( ! $this->_storeModel ){
			$this->_storeModel = Mage::getModel('core/store')->load('fr');
		}
		return $this->_storeModel;
			
	}
	/**
	 * return CSV delimiter 
	 * @return string 	
	 */
	public function getDelimiter(){
		return self::CSV_DELIMITEUR ;
	}
	/**
	 * return log file name
	 * @return string 
	 */
	public function getLogFile (){
		return date('Ymd').'-'.self::LOG_FILE_NAME.'.log';
	}
	/**
	 * Return complete url image or product
	 * @param $link string : url link
	 * @param $type int 1|2 
	 * @return string  
	 */
	public function getCompleteUrl( $link ,$type ) {
		$storeId = $this->_store()->getStoreId();
		$baseUrl = Mage::app()->getStore(  $storeId )->getBaseUrl();
		if( $type == 1 ){
			return $baseUrl . $link ;
		}
		if( $type == 2 ){
			if( $link == 'no_selection' ) return NULL;
			return $baseUrl . 'media/catalog/product' . $link;
		}
	}

	/**
	 * run save catalog 
	 * @param  void
	 * @return void 
	 */
	public function runSaveCatalog(){ 
		if( ! $this->_checkConfig() ){
			return;
		}		
		// Run export catalog 
		ini_set ( 'memory_limit', '2048M' );
		Mage::log ( '---------------------', Zend_log::INFO, $this->getLogFile());
		Mage::log ( 'Cibleweb - Géneration catalog : DEBUT >>> ', Zend_log::INFO, $this->getLogFile() );
		
		switch ( $this->_config( self:: CIBLEWEB_CONFIG_TYPE )  ){
			
			case Tatva_Cibleweb_Model_System_Config_Source_Type::LOCAL :
				if (  ! $this->_config( self:: CIBLEWEB_CONFIG_PATH ) ) {
					Mage::log ( " le chemin du répertoire de stockage est mal configuré : system > configuration > ciblweb > iziflux > configuration", Zend_Log::ERR );				
				}
			    $data = $this->_getProductData();
				//$data = '';
			   	$this->saveCsvFileLocal ( $data ); 				
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
	
	/**
	 *  save csv file in local server
	 *  @param array : product data 
	 *  @return void
	 */
	public function saveCsvFileLocal( $datas ){
		$root = $this->_config( self::CIBLEWEB_CONFIG_PATH);
		if( ! $this->createDir( $root ) ){
			return ;
		}
		//$pathFile = $_SERVER['DOCUMENT_ROOT'].'/az_boutique'.$root.$this->_config( self::CIBLEWEB_CONFIG_FILENAME);
		 $pathFile = $root.$this->_config( self::CIBLEWEB_CONFIG_FILENAME);
		//echo $pathFile = $root.$this->_config( self::CIBLEWEB_CONFIG_FILENAME);
	     $pathProductFile = '/data/apache/htdocs/magento/brioude/product.csv';
	 
		try{
            $csvObject  = new Varien_File_Csv();
            $csvObject->setDelimiter( $this->getDelimiter() );
            $csvObject->setEnclosure(' ');
			$csvObject->saveData($pathFile, $datas);
			$csvObject->saveData($pathProductFile, $datas);
            
			
        } catch ( Exception $e ) {
            Mage::logException($e);
            throw new Exception ($e->getMessage());
        }
		
    }
	/**
	 *  save csv file in remote server 
	 *  @param array : product data 
	 *  @return void
	 */
	public function saveCsvFileRemote( $datas ){
		//- Paramètres de connexion FTP
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
		$lastPageNumber = $this->getProductCollection()->getLastPageNumber () ;
		
		//$lastPageNumber = 1;
		// Initialisation
		$data = array();
		if( $this->_config( self:: CIBLEWEB_CONFIG_TYPE) == Tatva_Cibleweb_Model_System_Config_Source_Type::FTP ){
			$data = "";
		}

        $data[] = Mage::helper('cibleweb')->firstLine();
	   
		for($pageNumber = 1; $pageNumber <= $lastPageNumber; $pageNumber ++) {
  
			$productCollection = $this->getProductCollection( true , $pageNumber );
			
		   	//$myFile = "test_sagar.txt";
			//$sagar = fopen($myFile, 'a') or die("can't open file");


			foreach (  $productCollection as $product ){ 
				try{

					$line = array();

					//Id produit
					$line[] = $product->getId();
				   
					// sagar shah
				   	//$sa_prd_id = $product->getId();
//					$stringData = $sa_prd_id."\n\n";
//					fwrite($sagar, $stringData);
					//sagar shah
					
					//dénomination concise
					$line[] = $product->getName();
					
					//dénomination subjective
					$line[] = $product->getName();
					
					////description concise
					$shortDescription = $product->getShortDescription();
					$shortDescription = str_replace(array("\r\n\s","\s", "\n", "\r")," ",$shortDescription);
					$line[] = $shortDescription;
					
					//description complète
					//echo $product
					//comment by nisha}
					$line[] = $this->getCompleteDescription( $product );
					
				   // Photo 1,2,3,4,5
					$images = $this->getImageGallery( $product );
					$i = 1;
					foreach( $images as $image ){
						$line[] = $image;
						$i++;
						if($i == 6){
							break;
						}
					}
					for($j=$i;$j<=5;$j++){
						$line[] = "";
						
					}
					
					//URL fiche produit
					$line[] = $this->getCompleteUrl( $product->getUrlPath() , self::PRODUCT  );;
                    //$line[] = "http://www.az-boutique.fr/fr/catalog/product/view/id/".$product->getId();
					
					//Marque 
					$line[] = $this->getBrandName( $product->getMarque() );;
					 
					//Catégorie
					$line[] = $this->getCategoryName( $product );
					
					//Id Référence
					$line[] = $product->getSku();
	
					$itemStock = Mage::getModel('cataloginventory/stock_item')->loadByProduct ( $product ) ;
					
					$statusStock = $product->getStatusStock();
					$deliveryDays = '';
					
					
					if($statusStock == Mage::helper('Tatvainventory')->__('In stock')){
						//Disponibilité du produit
						$deliveryDays = $product->getDeliveryDays() ;
						$line[] = $deliveryDays;
						
						//statut de disponibilité du produit
						$line[] = 'En stock';
					
					}elseif($statusStock == Mage::helper('Tatvainventory')->__('Underway replenishment')){
						//Disponibilité du produit
						$deliveryDays = $product->getDeliveryDays() ;
						if(!$product->getIsVirtual()){
							$deliveryDays += 1 + 3;
						}
						$line[] = $deliveryDays;
						
						//statut de disponibilité du produit
						$line[] = 'En cours de réapro.';
					}elseif($statusStock == Mage::helper('Tatvainventory')->__('On order')){ 
						//Disponibilité du produit
						$deliveryDays = $product->getDeliveryDays() ;
						if(!$product->getIsVirtual()){
							$deliveryDays += 1 + 3;
						}
						$line[] = $deliveryDays;
					
						//statut de disponibilité du produit
						$line[] = 'Sur commande';
					}
					
					//prix TTC
					$store = $this->_store()->getId();
					$line[] = Mage::app()->getHelper('tax')->getPrice($product, $product->getPrice(), true, null, null, null, $store , null) ;
								
					//Ecotaxe
					$line[] = '0';
					
					//Garantie
					$value = $product->getGarantie();
					if(!$value){
						$value = 0;
					}
					$line[] = $value;
					
					//droit de rétractation 
					$line[] = 'N';
				   
					// Quantité
					if($product->getTypeId() == 'bundle'){
						
						$bundle = Mage::getModel('bundle/product_type');
						$tabIds = $bundle->getChildrenIds($product->getId());
						$qty = -1;
						
						foreach($tabIds as $tab ){
							if(!empty($tab)){
								foreach($tab as $idChild ){
									$child = Mage::getModel('catalog/product')->load($idChild);
									$itemStockChild = Mage::getModel('cataloginventory/stock_item')->loadByProduct ( $child ) ;
								
									if( ($qty != -1 && $qty < $itemStockChild->getQty()) || ( $qty == -1 ) ) {
										$qty = $itemStockChild->getQty();
									}
								}
							}	
						}
						
						if($qty == -1){
							$qty = 0;
						}
						$line[] = $qty;
					
					}else{
						$line[] = $itemStock->getQty();
					}
				   
					//délai de livraison
					$line[] =  $product->getAvailabilityDays();
					
					//unité délai de livraison
					$line[] = "jour";
					
					//délai d’expédition
					$line[] = $product->getDeliveryDays();
					
					//unité délai d’expédition
					$line[] = "jour";
					
					//frais de port
					$line[] = $product->getShippingAmount();
	
					//référence constructeur (optionnel)
					$line[] = "";
					
					//prix public généralement constaté (optionnel)
					$line[] = "";
					
					//ean13 (optionnel)
					$ean = "";

                    if($product->getEan13() != "")
                    {
                      $ean = $product->getEan13();
                    }
                    /*else
                    {
                        $collection = Mage::getModel('Tatvainventory/item')->getCollection()
                                      ->removeAZBoutiqueFilter()
                		              ->addProductIdFilter($product->getId());
                        if($collection->count() > 0)
                        {
                        $ean_collection = $collection->getFirstItem();

                        $ean = $ean_collection->getSupplierReference();

                        }

                    }*/

					$line[] = $ean;
					
					//mots-clefs (optionnel)
					$meta = str_replace(array("\r\n\s","\s", "\n", "\r")," ",$product->getMetaKeyword());
					$line[] = $meta;
					
					//-------- PROMOTION

					$promo = $this->getPromotion($product);

					$statuses = $promo;
					
					//add by nisha
					/*foreach ($statuses as $key => $value) {
			            if ($key == 6) {
			                unset($statuses[$key]);
			            }
			        }*/
					unset($statuses[6]);
					$statuses = array_values($statuses);
				    
					$line = array_merge($line,$statuses);
					  		
					//genre
					$line[] = "";
					
					//matiere
					$line[] = "";
					
					//couleur
					$line[] = "";
					
					//taille
					$line[] = "";
					
					//pointure
					$line[] = "";
					
					//collection
					$line[] = $product->getGammeCollection();
					
					//delai de repprovisonnement
					$line[] = $product->getAvailabilityDays();
					$var_promo = '0';
					if(isset($promo[6]))
					{
						$var_promo = $promo[6];
					}
					//promtion
					
					$line[] = $var_promo;
				   	
			   		/**
					 * Si le mode de transfert est en local  -> former un talbeau
					 * Si le mode de transfert est FTP       -> former une chaine de caractère
					 */
					if( $this->_config( self:: CIBLEWEB_CONFIG_TYPE) == Tatva_Cibleweb_Model_System_Config_Source_Type::FTP ){
						$data .= implode( self::CSV_DELIMITEUR , $line )."\n";
					}else{
						$data[] = $line;
					}				
					Mage::log ( "ADD > produit id={$product->getId()}", Zend_log::INFO, $this->getLogFile() );
					unset( $line  );
				}catch(Exception $e){
					Mage::log ( "ERREUR > produit id={$product->getId()} : " . $e->getMessage(), Zend_log::INFO, $this->getLogFile() );
					Mage::logException($e);
					unset( $line  );
				}
			}
			//fclose($sagar);
		}
		return $data;		
		
	}
	
	
	
	/**
	 * Return product collection by page if the filter parameter is set to true 
	 * @param  $filter bool    : is used to add other filters 
	 * @param  $pageNumber int : number of collection page 
	 * @return $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
	 */
	public function getProductCollection( $filter = false  , $pageNumber = NULL ){
		$collection = Mage::getModel ( "catalog/product" )->getCollection ();
		$collection->addStoreFilter($this->_store());
		$collection->addAttributeToFilter('status','1','inner');
		$collection->addTaxPercents();
		//$collection->addFinalPrice();
		$collection->setPageSize ( Tatva_Cibleweb_Model_ExportCatalog::$PAGE_SIZE );
		if( $filter ){
			$collection->addAttributeToSelect ( 
				array ( 
					'media_gallery',
					'sku',
					'category_ids',
					'description',
					'short_description',
					'price',
					'marque',
					'name',
					'image',
					'small_image',
					'thumbnail',
					'url_path',
					'weight',
					'color',
					'meta_keyword',
					'garantie',
					'gamme_collection',
					'tax_class_id',
                    'ean13'
				) 
			);
			$collection->setOrder ('entity_id', 'ASC');
			$collection->setCurPage ( $pageNumber );
		}
		//Mage::log($collection->getSelect()->__toString());
		return 	$collection ;	
	}
	
	
	/**
	 * Retoune les noms des categorie auquelles appartient le produit
	 * @param $product Mage_Catalog_Model_Product
	 * @return string  Noms des categories 
	 */
	public function getCategoryName( $product ){
		$catIds  =  $product->getCategoryIds();
		$categorys = Mage::getSingleton('catalog/category')->getCollection()
					 ->addAttributeToSelect( 'name' )
					 ->addAttributeToFilter( 'entity_id',array( 'in'=>$catIds ) )
					 ->addAttributeToFilter( 'level',4 );
		if( $categorys->getSize() ){
		    return $categorys->getFirstItem()->getName();
		}else{
		    $categorys = Mage::getSingleton('catalog/category')->getCollection()
					 ->addAttributeToSelect( 'name' )
					 ->addAttributeToFilter( 'entity_id',array( 'in'=>$catIds ) )
					 ->addAttributeToFilter( 'level',3 );		    
			if( $categorys->getSize() ){
		       return $categorys->getFirstItem()->getName();
		    }else{
		    	$categorys = Mage::getSingleton('catalog/category')->getCollection()
					 ->addAttributeToSelect( 'name' )
					 ->addAttributeToFilter( 'entity_id',array( 'in'=>$catIds ) )
					 ->addAttributeToFilter( 'level',2 );	
				if( $categorys->getSize() ){
			       return $categorys->getFirstItem()->getName();
			    }else{	
		        	return;
			    }
		    }
		}
            			
	}
	/**
	 * Retoune le nom de la marque du produit
	 * @param int $brand : id de la marque 
	 * @return string    : nom de la marque 
	 */
	public function getBrandName( $brandId ) {
		$storeId = $this->_store()->getId() ;
		$productEntityTypeId = Mage::getModel ( 'eav/entity' )->setType ( 'catalog_product' )->getTypeId ();
		$brandAttribute = Mage::getModel('eav/entity_attribute')->loadByCode($productEntityTypeId,Mage::helper('brand')->getBrandAttributeCode());
		$brands = Mage::getModel('eav/entity_attribute_option')	->getCollection()
																->addFieldToFilter('attribute_id',array('='=>$brandAttribute->getAttributeId()))
																->setStoreFilter($storeId, false)
																->setIdFilter( $brandId )
																->load();
		return $brands->getFirstItem()->getValue();
	}
	/**
	 * Returne les url de la gallery images du produit
	 * @param $product Mage_Catalog_Model_Product
	 * @return array  url images
	 */
	public function getImageGallery( $product ){
		Mage::getSingleton('catalog/product_attribute_backend_media')->setAttribute( $this->getAttributeImage() )->afterLoad( $product );
		$photo = array();
		$gallery = $product->getMediaGalleryImages()->getItems();
		foreach( $gallery as $image  ){
			$photo[] =	$image->getUrl();
		}
		$counVoidFiled = 5 - count($gallery);
		for( $i=1; $i<=$counVoidFiled ; $i++ ){
			$photo[] = '';
		}
		return $photo ; 	
	}
	/**
     * return catalog product entity type id, utilisé par la fonction 
     * de recuperation de l'obejet de l'attrube "media_gallery"
     * @param void
     * @return ind entity type id 
	 */
	public function getEntityTypeId() {
		if( ! $this->_entityIypeId ){
			$this->_entityIypeId = Mage::getModel('eav/config')->getEntityType( 'catalog_product' )->getEntityTypeId();
		}
		return $this->_entityIypeId ;
	}
	
	/**
     * returne l'objet eav_attribute de l'attribut media_gallery, il est utilisé par la fonction de recupération de la gallery images
     * @param void 
     * @return objet Mage_Catalog_Model_Resource_Eav_Attribute
	 */
	public function getAttributeImage (  ){
		if( ! $this->_attributeImage ){
			$this->_attributeImage = Mage::getModel('eav/config')->getAttribute( $this->getEntityTypeId() , 'media_gallery');
		}
		return $this->_attributeImage ;	
	}
	
	
	/**
	 * retrieve product attribute value  
	 * @param Mage_Catalog_Model_Product
	 * @return string
	 */
	public function getCompleteDescription( $product ){
		$productId = $product->getId();
		$attrubtesCollection = Mage::getModel('eav/entity_attribute')
							   ->getCollection()
							   ->setEntityTypeFilter( $this->getEntityTypeId() )
							   ->addFieldToFilter( 'attribute_code',array('nin'=>Mage::helper('cibleweb')->notSelectedAttributes()))
							   ->addFieldToFilter( 'is_visible', 1 );
		
		$completedesc = '';
		$completedesc =  $product->getDescription(). ";";
		$completedesc = str_replace(array("\r\n\s","\s", "\n", "\r")," ",$completedesc);
		//$completedesc = preg_replace('/<([\/\s\r\n]*)()([\/\s\r\n]*)>/si', '$1$2$3##', $data);
		
		foreach( $attrubtesCollection as $att ){
		
			if($att->getAttributeCode() != 'deee')
			{
				if( $value  = Mage::getSingleton('cibleweb/attribute_product')->selectValue($productId, $att->getAttributeCode(), $this->_store()->getId() ) ){
					$completedesc .= Mage::helper('catalog')->__( $att->getFrontendLabel() );
					$completedesc .= ":";
					$value = str_replace(array("\r\n\s","\s", "\n", "\r")," ",$value);
					$completedesc .= $value;	
					$completedesc .= "; ";
				}
			} 	
		}
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
		$catalogRuleProducts->getSelect()->where('main_table.product_id = ?', $product->getId());
		
		$tableName = Mage::getModel('catalogrule/rule_product_price')->getResource()->getTable('catalogrule/rule_product');
		$catalogRuleProducts->getSelect()
			->from(array('rule_product' => $tableName), 'rule_id')
			->where ('rule_product.product_id = main_table.product_id ')
			->where('rule_product.customer_group_id = ?',$customerGroupId)
			->where('rule_product.website_id = ?',$this->_store()->getWebsiteId());
			
							
		if(!$catalogRuleProducts->getSize()){
			//date/heure de début promotion
			$promo[] = "";
			
			//date/heure de fin promotion
			$promo[] = "";
			
			//prix TTC avant promotion
			$promo[] = "";
			
			//promotion (montant en euros)
			$promo[] = "";
			
			//pourcentage de la démarque
			$promo[] = "";
			
			//prix remisé
			$promo[] = "";
		}else{  	
			//$catalogRule = $catalogRuleProducts->getFirstItem();
			foreach($catalogRuleProducts as $catalogRule){
				$rule = Mage::getModel('catalogrule/rule')->load($catalogRule->getRuleId());
				$oldPrice = Mage::app()->getHelper('tax')->getPrice($product, $product->getPrice(), true, null, null, null, $this->_store()->getId() , null) ;
				$newPrice = Mage::app()->getHelper('tax')->getPrice($product, $catalogRule->getRulePrice(), true, null, null, null, $this->_store()->getId() , null) ;
				
				//date/heure de début promotion
				$fromDate = date ('d/m/Y');
				if($rule->getFromDate()){
					//$date = new Zend_Date($rule->getFromDate());
					//$fromDate =  date ('d/m/Y', $date->getTimestamp());
                    //echo 'from=='.$fromDate =  date ('Y/m/d h:i:s', $date->getTimestamp());
                    $fromDate = $rule->getFromDate();
				}
				$promo[] = $fromDate;

				//date/heure de fin promotion
				$toDate = "";
				if($rule->getToDate()){
					//$date = new Zend_Date($rule->getToDate());
                    //echo '<br>time=='.$rule->getToDate();
                    //echo '<br>stampp='.$date->getTimestamp();
                    //echo '<br>to=='.$toDate =  date ('Y/m/d h:i:s', $date->getTimestamp());
					//$toDate =  date ('d/m/Y', $date->getTimestamp());
                    $toDate = $rule->getToDate();
				}

				$promo[] = $toDate;
				
				//prix TTC avant promotion
				$promo[] = $oldPrice;
				
				//promotion (montant en euros)
				$promo[] = $newPrice;
				
				//pourcentage de la démarque
				if($rule->getSimpleAction() == 'by_percent' || $rule->getSimpleAction() == 'to_percent'){
					$promo[] = $rule->getDiscountAmount();
				}else{
					$percent = 100 - ($newPrice * 100 / $oldPrice);
					$promo[] = $percent;
				}
				//prix remisé
				if($rule->getSimpleAction() == 'by_percent' || $rule->getSimpleAction() == 'to_percent'){
					$promo[] = $oldPrice - $newPrice;
				}else{
					$promo[] = $rule->getDiscountAmount();
				}
				
				//add by nisha
				$promo[] = $rule->getpromotion_type() == 'NULL' ? '' : $rule->getpromotion_type();
				
				break;
			}
		}				
		return $promo;
	}
	
	
}

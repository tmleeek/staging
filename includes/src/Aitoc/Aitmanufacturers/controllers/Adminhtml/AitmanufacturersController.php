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
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

class Aitoc_Aitmanufacturers_Adminhtml_AitmanufacturersController extends Mage_Adminhtml_Controller_Action
{

    private $_attributeCode = null;

    protected $_bEdtiPage = false;
    
    protected $_data = null;
    
    
    public function gridAction()
    {
         $this->getResponse()->setBody(
            $this->getLayout()->createBlock('aitmanufacturers/adminhtml_aitmanufacturers_edit_tab_product')->toHtml()
        );
    }
    
    protected function _init()
    {


            if (!is_null($this->getRequest()->getParam('store'))){
    	        Mage::getSingleton('adminhtml/session')->setData('aitmanufacturers_store', $this->_getStoreId());
    	    }
    	    elseif (!is_null(Mage::getSingleton('adminhtml/session')->getData('aitmanufacturers_store'))){
    	        $this->getRequest()->setParam('store', Mage::getSingleton('adminhtml/session')->getData('aitmanufacturers_store'));
    	    }
            Mage::register('store_id', $this->_getStoreId());


    }
    
    protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('catalog/aitmanufacturers')
			->_addBreadcrumb(Mage::helper('aitmanufacturers')->__(Mage::getModel('aitmanufacturers/config')->getAttributeName($this->_getAttributeCode()).' Pages Manager'), Mage::helper('aitmanufacturers')->__(Mage::getModel('aitmanufacturers/config')->getAttributeName($this->_getAttributeCode()).' Pages Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
        if (!$this->_getAttributeCode())
        {
            $this->_redirect('*/*/attribute/');
        }
	    $this->_init();
		$this->_initAction()
			->renderLayout();
	}
	
	protected function _getStoreId()
	{
	    if (Mage::app()->isSingleStoreMode()) {
	        return Mage::app()->getStore(true)->getId();
	    }
	    $storeId = Mage::app()->getStore((int) $this->getRequest()->getParam('store', 0))->getId();
	    if (!($config = Mage::getModel('aitmanufacturers/config')->getScopeConfig($this->_getAttributeCode(), 'store', $storeId))  || !$config['is_active'])
	    {
         //echo "<pre>"; print_r($this->getRequest()->getPost()); exit;
	        $stores = Mage::app()->getStores();
	        foreach ($stores as $store)
	        {
	            $storeId = $store->getId();
	            if (($config = Mage::getModel('aitmanufacturers/config')->getScopeConfig($this->_getAttributeCode(), 'store', $storeId))  && $config['is_active'])
        	    {
        	        parent::_redirect('*/*/*/', array('store' => $storeId, 'attributecode' => $this->_getAttributeCode()));
        	    }
	        }
	    }
	    return $storeId;
	}
	
	protected function _redirect($path, $arguments = array())
	{
	    $arguments['store'] = $this->_getStoreId();
	    $arguments['attributecode'] = $this->_getAttributeCode();
	    if (!$arguments['attributecode'])
	    {
	        parent::_redirect('*/attribute/');
	    }
	    parent::_redirect($path, $arguments);
	}

	protected function _getAttributeCode()
	{
	    if (!$this->_attributeCode)
	    {
	        $this->_attributeCode = $this->getRequest()->getParam('attributecode');
	    }
        return $this->_attributeCode;
	}
	
	public function fillOutAction(){
	    $this->_init();
	    $model  = Mage::getModel('aitmanufacturers/aitmanufacturers');
	    $storeId = $this->_getStoreId();
	    $attributeCode = $this->_getAttributeCode();
	    if (!$attributeCode)
	    {
	       $this->_redirect('*/attribute/',array());
	    }
	    
	    try {
    	    $model->fillOut($storeId, $attributeCode);
    	    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('aitmanufacturers')->__(Mage::getModel('aitmanufacturers/config')->getAttributeName($this->_getAttributeCode()).' Pages were successfully filled out'));
	    } catch (Exception $e) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('aitmanufacturers')->__('There was an error during the process'));
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		}
    	$this->_redirect('*/*/', array('store' => $storeId));
	}

	public function editAction() {
	    $this->_init();
        $session = Mage::getModel('adminhtml/session');
        $sort = $this->getRequest()->getParam('sort');
        $dir = $this->getRequest()->getParam('dir'); 
        if(!empty($sort))
        {
            $session->setSort($sort);
        }
        if(!empty($dir))
        {
            $session->setDir($dir);
        } 
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('aitmanufacturers/aitmanufacturers')->load($id);
       $write = Mage::getSingleton("core/resource")->getConnection("core_write");
		if ($delete = $this->getRequest()->getParam('delete')){
		    switch ($delete){
		        case 'small_logo':
				 $params = $this->getRequest()->getParams();
		            unset($params['delete']);
		            
		            $filename = $model->getData($delete);
		            $path = Mage::getBaseDir('media') . DS . 'aitmanufacturers' . DS . ($delete == 'small_logo'?'logo'.DS:($delete == 'list_image'?'list'.DS:''));
		            if (file_exists($path.$filename)){
		                @unlink($path.$filename);
		            }
		            
					$sql = "UPDATE aitmanufacturers SET `small_logo` = '' WHERE id = ".$id;
					$write->query($sql);
		            $this->_redirect('*/*/*/', $params);
		            return; 
		        case 'list_image':
				  $params = $this->getRequest()->getParams();
		            unset($params['delete']);
		            if ($model->getFeatured() && 'image' == $delete){
		                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('aitmanufacturers')->__('Image must be uploaded for Featured Attribute'));
                        Mage::getSingleton('adminhtml/session')->setFormData($model->getData());
                        $this->_redirect('*/*/*/', $params);
                        return;
		            }
		            $filename = $model->getData($delete);
		            $path = Mage::getBaseDir('media') . DS . 'aitmanufacturers' . DS . ($delete == 'small_logo'?'logo'.DS:($delete == 'list_image'?'list'.DS:''));
		            if (file_exists($path.$filename)){
		                @unlink($path.$filename);
		            }
		            $sql = "UPDATE aitmanufacturers SET list_image = '' WHERE id = ".$id;
					$write->query($sql);

		            $this->_redirect('*/*/*/', $params);
		            return;
		        case 'image':
		            $params = $this->getRequest()->getParams();
		            unset($params['delete']);
		            if ($model->getFeatured() && 'image' == $delete){
		                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('aitmanufacturers')->__('Image must be uploaded for Featured Attribute'));
                        Mage::getSingleton('adminhtml/session')->setFormData($model->getData());
                        $this->_redirect('*/*/*/', $params);
                        return;
		            }
		            $filename = $model->getData($delete);
		            $path = Mage::getBaseDir('media') . DS . 'aitmanufacturers' . DS . ($delete == 'small_logo'?'logo'.DS:($delete == 'list_image'?'list'.DS:''));
		            if (file_exists($path.$filename)){
		                @unlink($path.$filename);
		            }
		            $sql = "UPDATE aitmanufacturers SET image = '' WHERE id = ".$id;
					$write->query($sql);

		            $this->_redirect('*/*/*/', $params);
		            return;
		    }
		}
       
		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {

				$model->setData($data);
			}

			Mage::register('aitmanufacturers_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('catalog/aitmanufacturers');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Attributes Pages Manager'), Mage::helper('aitmanufacturers')->__(Mage::getModel('aitmanufacturers/config')->getAttributeName($this->getRequest()->get('attributecode')).' Pages Manager'));
			//$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('aitmanufacturers/adminhtml_aitmanufacturers_edit'))
				->_addLeft($this->getLayout()->createBlock('aitmanufacturers/adminhtml_aitmanufacturers_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('aitmanufacturers')->__('Attribute does not exist'));
			$this->_redirect('*/*/');
		}
	}

	public function newAction() {
		$this->_forward('edit');
	}

	public function saveAction()
    {
		$this->_init();
        $gamme_collection='';
        $read= Mage::getSingleton('core/resource')->getConnection('core_read');

        if ($this->_data = $this->getRequest()->getPost())
        {   //echo "<pre>"; print_r($this->_data);exit;
            $this->_prepareProductsBeforeSaveBrand();

            $model = Mage::getModel('aitmanufacturers/aitmanufacturers');

		    $manufacturer = $model->getManufacturerName($this->_data['manufacturer_id'],$this->_data['stores'][0]);
		    if (empty($this->_data['title'])){
                $this->_data['title'] = $manufacturer;
            }
            if (!empty($this->_data['url_key'])){
                $urlKey = Mage::helper('aitmanufacturers')->toUrlKey($this->_data['url_key']);
            }
            else {
                $urlKey = Mage::helper('aitmanufacturers')->toUrlKey($manufacturer);
            }
            $this->_data['url_key'] = $urlKey;

			$this->_uploadBrandImages('image', NULL, 'image');
            $this->_uploadBrandImages('small_logo', 'logo', 'logo');
            $this->_uploadBrandImages('list_image', 'list', 'attribute icon');
            $model->setData($this->_data)
				->setId($this->getRequest()->getParam('id'));

            /* multiselect attribute collectio__start */
            if(is_array($this->_data['collection']))
            {

              $gamme_collection = implode(",", $this->_data['collection']);
              $model->setData('collection',$gamme_collection);
            }
            /* multiselect attribute collectio__ends */

            /* selected store ids__start */
            /*if(($this->_data['stores']))
            {
              $selected_ids = implode(",", $this->_data['stores']);
              $model->setData('stores',$selected_ids);

            }*/
            /* selected store ids__ends */
			try {
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('aitmanufacturers')->__(Mage::getModel('aitmanufacturers/config')->getAttributeName($this->_getAttributeCode()).' Page was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId(), 'attributecode' => $this->getRequest()->getParam('attributecode')));
					return;
				}
				if($this->_bEdtiPage)
                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id'), 'attributecode' => $this->getRequest()->getParam('attributecode')));
                else
                    $this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($this->_data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id'), 'attributecode' => $this->getRequest()->getParam('attributecode')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('aitmanufacturers')->__('Unable to find '.Mage::getModel('aitmanufacturers/config')->getAttributeName($this->_getAttributeCode()).' Page to save'));
       	$this->_redirect('*/*/');
	}

    protected function _prepareProductsBeforeSaveBrand()
    {
        /* !AITOC_MARK:manufacturer_collection */
        $session = Mage::getModel('adminhtml/session');
        $attributeCode = $this->_getAttributeCode();


        //saving orders

        $manufacturer = Mage::getModel('aitmanufacturers/aitmanufacturers')->load($this->_data['manufacturer_id']);
        $productIds = $manufacturer->getProductsByManufacturer($this->_data['manufacturer_id'],$this->_data['stores'][0], Mage::getModel('aitmanufacturers/config')->getAttributeId($attributeCode));


        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            ->addAttributeToSelect('sort')  
            ->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1' . (
                    ((string)Mage::getConfig()->getModuleConfig('Aitoc_Aitquantitymanager')->active == 'true') ?
                    (' AND {{table}}.website_id='.Mage::App()->getStore($storeId)->getWebsite()->getId()) :
                    ''
                ),
                'left')
            ->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $storeId)
            ->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $storeId)
            ->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $storeId)
            ->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $storeId)
            ->joinAttribute('sort', 'catalog_product/aitmanufacturers_sort', 'entity_id', null, 'left', $storeId)
            ->addFieldToFilter('entity_id',array('in'=>$productIds));
            
        if(!empty($this->_data['custom_name']))
        {
            $collection->addFieldToFilter('custom_name',array('like'=> '%'.$this->_data['custom_name'].'%'));
        }
        if(!empty($this->_data['product_order']['from']))
        {
            $collection->addFieldToFilter('aitmanufacturers_sort',array('gteq'=> $this->_data['product_order']['from']));     
        }
        if(!empty($this->_data['product_order']['to']))
        {
            $collection->addFieldToFilter('aitmanufacturers_sort',array('lteq'=> $this->_data['product_order']['to']));     
        }
        $sort =  $session->getData('aitmanufacturersGridsort');
        $dir =  $session->getData('aitmanufacturersGriddir'); 
        if(!empty($sort))
        {
            if($sort == 'product_order')
            {
                $sort = 'sort';
            }
            else
            {
                $sort = 'custom_name';
            }
            $collection->addAttributeToSort($sort,$dir);     
        }

        $limit = $this->_data['limit'];
        $page = $this->_data['page'];
        $first = $limit*($page-1);
        $ind = 0;
        $dataInd = 0;
            
        $_updateIds = array();
            
        foreach($collection as $item)
        {  
            if(($ind < ($page)*$limit) && ($ind >= ($page-1)*$limit))
            {
                $item->addAttributeUpdate('aitmanufacturers_sort', '9999', Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID);
                $sort = $item->getData('aitmanufacturers_sort');
                if(empty($sort))
                {
                     $item->addAttributeUpdate('aitmanufacturers_sort','9999',$storeId); 
                     $_updateIds[$item->getId()] = '9999';
                }
                
                if(!empty($this->_data['product_order'][$dataInd]))
                    {
                        if($this->_data['product_order'][$dataInd]>0)
                        {
                             $item->addAttributeUpdate('aitmanufacturers_sort',$this->_data['product_order'][$dataInd],$storeId);
                             $_updateIds[$item->getId()] = $this->_data['product_order'][$dataInd];
                        }
                    }
                $dataInd++;               
            }               
            $ind++;   
        }
            
        // update attributes in flat table if needed
        $_hlp = Mage::helper('catalog/product_flat');
        /* @var $_hlp Mage_Catalog_Helper_Product_Flat */
        if ($_updateIds AND $_hlp->isEnabled($storeId))
        {
            $_flatIndexer = Mage::getSingleton('catalog/product_flat_indexer');
            /* @var $_flatIndexer Mage_Catalog_Model_Product_Flat_Indexer */
            $_flatIndexer->updateAttribute('aitmanufacturers_sort', $storeId, array_keys($_updateIds));
        }

    }
    
    /**
         *Uploading brand images
         *
         *@param string $imageName
         *@param string $pathDirectoryName
         *@param string $textNameForErrorMessages
          */
    protected function _uploadBrandImages($imageName, $pathDirectoryName = NULL, $textNameForErrorMessages)
    {
        if (isset($_FILES[$imageName]['name']) && $_FILES[$imageName]['name'] != '') 
        {
            try 
            {	
                $model = Mage::getModel('aitmanufacturers/aitmanufacturers');
                $attributeCode = $this->_getAttributeCode();
                $uploader = new Varien_File_Uploader($imageName);
                $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                $uploader->setAllowRenameFiles(false);
                // Set the file upload mode 
                // false -> get the file directly in the specified folder
                // true -> get the file in the product like folders 
                //file.jpg will go in something like /media/f/i/file.jpg)
                $uploader->setFilesDispersion(false);
                
                if(!is_null($pathDirectoryName))
                {
                    $path = Mage::getBaseDir('media') . DS . 'aitmanufacturers' . DS . $pathDirectoryName . DS;
                }
                else
                {
                    $path = Mage::getBaseDir('media') . DS . 'aitmanufacturers';
                }
                
                if (Mage::helper('aitmanufacturers')->getConfigParam('rename_pic', $attributeCode))
                    $logoName = md5($_FILES[$imageName]['name'].time()) . '.' . substr(strrchr($_FILES[$imageName]['name'], '.'), 1);
                else 
                    $logoName = $_FILES[$imageName]['name'];

                if($model->isBrandsImageExists($imageName, $this->getRequest()->getParam('id'), $logoName))
                {
                    throw new Exception(Mage::helper('aitmanufacturers')->__(ucfirst($textNameForErrorMessages) . ' with the same name already exists. Rename please'));
                }	
						   	
                $uploader->save($path, $logoName);

            }
            catch (Exception $e) 
            {
                $logoName = '';
                $sError = $e->getMessage();
                if($sError == 'Disallowed file type.')
                $sError = Mage::helper('aitmanufacturers')->__('Can not upload ' . $textNameForErrorMessages . ' ') . $sError . Mage::helper('aitmanufacturers')->__(' jpg, jpeg, gif, png allowed only');
                Mage::getSingleton('adminhtml/session')->addError($sError);
                $this->_bEdtiPage = true;
            }
	        
            if (isset($logoName))
            {
                //this way the name is saved in DB
                $this->_data[$imageName] = $logoName;
            }
        }
    }
 
	public function deleteAction() {
	    $this->_init();
	    $attributeCode = $this->_getAttributeCode();
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('aitmanufacturers/aitmanufacturers');
				$model->deletePictures($this->getRequest()->getParam('id'));
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('aitmanufacturers')->__(Mage::getModel('aitmanufacturers/config')->getAttributeName($this->_getAttributeCode()).' Page was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $this->_init();
        $attributeCode = $this->_getAttributeCode();
        $aitmanufacturersIds = $this->getRequest()->getParam('aitmanufacturers');
        if(!is_array($aitmanufacturersIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('aitmanufacturers')->__('Please select '.Mage::getModel('aitmanufacturers/config')->getAttributeName($attributeCode).' Page(s)'));
        } else {
            try {
                foreach ($aitmanufacturersIds as $aitmanufacturersId) {
                	Mage::getModel('aitmanufacturers/aitmanufacturers')->deletePictures($aitmanufacturersId);
                    $aitmanufacturers = Mage::getModel('aitmanufacturers/aitmanufacturers')->load($aitmanufacturersId);
                    $aitmanufacturers->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($aitmanufacturersIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $this->_init();
        $attributeCode = $this->_getAttributeCode();
        $aitmanufacturersIds = $this->getRequest()->getParam('aitmanufacturers');
        if(!is_array($aitmanufacturersIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('aitmanufacturers')->__('Please select '.Mage::getModel('aitmanufacturers/config')->getAttributeName($this->_getAttributeCode()).' Page(s)'));
        } else {
            try {
                foreach ($aitmanufacturersIds as $aitmanufacturersId) {
                    $aitmanufacturers = Mage::getSingleton('aitmanufacturers/aitmanufacturers')
                        ->load($aitmanufacturersId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($aitmanufacturersIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function attributeAction()
    {
        $this->_init();
		$this->_initAction()
			->renderLayout();
    }
    
    /*public function exportCsvAction()
    {
        $fileName   = 'aitmanufacturers.csv';
        $content    = $this->getLayout()->createBlock('aitmanufacturers/adminhtml_aitmanufacturers_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'aitmanufacturers.xml';
        $content    = $this->getLayout()->createBlock('aitmanufacturers/adminhtml_aitmanufacturers_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }*/

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
    
}
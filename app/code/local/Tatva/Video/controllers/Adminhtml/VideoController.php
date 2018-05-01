<?php

class Tatva_Video_Adminhtml_VideoController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        if($this->getRequest()->getParam('popup')) {
            $this->loadLayout('popup');
        } else {
            $this->loadLayout()
                ->_setActiveMenu('catalog/product')
                ->_addBreadcrumb(Mage::helper('catalog')->__('Catalog'), Mage::helper('catalog')->__('Catalog'))
                ->_addBreadcrumb(Mage::helper('catalog')->__('Manage Videos'), Mage::helper('tatvavideo')->__('Manage Videos'))
            ;
        }
        return $this;
    }
    

    public function gridOnlyVideoAndSupplierAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('tatvavideo/adminhtml_catalog_product_edit_tab_video')
                ->toHtml()
        );
    }
        

    public function editAction()
    {
        $id = $this->getRequest()->getParam('video_item_id');
        $model = Mage::getModel('tatvavideo/item');
		
		if($id){
			$model->load($id);
		}else{
			$model->setProductId($this->getRequest()->getParam('product_id'));
		}
		if(!$model->getDateStart()){
			$model->setDateStart(new Zend_Date(Mage::getModel('core/date')->gmtTimestamp()));
		}
		
        Mage::register('video_item_data', $model);

        $this->_initAction()
            ->_addBreadcrumb($id ? Mage::helper('tatvavideo')->__('Edit Video') : Mage::helper('tatvavideo')->__('New Video'), $id ? Mage::helper('tatvavideo')->__('Edit Video') : Mage::helper('tatvavideo')->__('New Video'))
            ->_addContent($this->getLayout()->createBlock('tatvavideo/adminhtml_catalog_product_edit_tab_video_edit'))
            ->_addJs(
                $this->getLayout()->createBlock('adminhtml/template')
                    ->setIsPopup((bool)$this->getRequest()->getParam('popup'))
            )
            ->renderLayout();
    }

	/**
     * Sauvegarde d'un stock
     */ 
	public function saveAction()
    {

    	$data = $this->getRequest()->getPost();
    	$productId = $this->getRequest()->getParam('product_id');
        if ($data) {
            $redirectBack   = $this->getRequest()->getParam('back', false);
             $model = Mage::getModel('tatvavideo/item');
            /* @var $model Sqli_Video_Model_Item */

            $id = $this->getRequest()->getParam('video_item_id');

            if ($id) {
                $model->load($id);
            }
            $model->setData($data);

            try {
            	
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('tatvavideo')->__('Video was successfully saved'));

				
                if ($this->getRequest()->getParam('popup')) {
                	$product = Mage::getModel('catalog/product')->load($productId);
                    $this->_redirect('tatvavideo/adminhtml_video/addVideo', array(
                        '_current' => true
                    ));
                } else
                if ($redirectBack) {
                    $this->_redirect('admin/catalog_product/edit', 
                  		array(
                    		'id'       	=> $productId,
                    		'_current'	=>true
                  		));
                } else {
                    $this->_redirect('admin/catalog_product/', array());
                }

                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setAttributeData($data);
                $this->_redirect('tatvavideo/adminhtml_video/addVideo', array(
                        '_current' => true
                    ));
                return;
            }
        }
        $this->_redirect('admin/catalog_product/edit', 
                  array(
                    'id'       	=>$productId,
                    '_current'	=>true
                  ));
    }
    
    public function addVideoAction()
    {
        $this->_getSession()->addNotice(Mage::helper('catalog')->__('Please click on Close Window button if it won\'t be closed automatically'));
        $this->loadLayout('popup');
        $this->_addContent(
            $this->getLayout()->createBlock('tatvavideo/adminhtml_catalog_product_video_created')
        );
        $this->renderLayout();
    }
	/**
     * Suppression d'un stock
     */ 
    public function deleteAction()
    {
    	$id = $this->getRequest()->getParam('video_item_id');
        if ($id) {
             $model = Mage::getModel('tatvavideo/item');
            /* @var $model Sqli_Video_Model_Item */

             $model->load($id);
             
            try {
            	$productId = $model->getProductId();
                $model->delete();
   
                
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('tatvavideo')->__('Video was successfully deleted'));
                
                if ($this->getRequest()->getParam('popup')) {
                	$product = Mage::getModel('catalog/product')->load($productId);
                    $this->_redirect('tatvavideo/adminhtml_video/addVideo', array(
                        '_current' => true
                    ));
                } else
                    $this->_redirect('admin/catalog_product/edit',
                  		array(
                    		'id'       	=> $productId,
                    		'_current'	=>true
                  		));

                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('video_item_id' => $this->getRequest()->getParam('video_item_id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tatvavideo')->__('Unable to find an video to delete'));
        $this->_redirect('*/*/');
    }

    public function validateAction()
    {
    	
	        $response = new Varien_Object();

        $this->getResponse()->setBody($response->toJson());
    }

}

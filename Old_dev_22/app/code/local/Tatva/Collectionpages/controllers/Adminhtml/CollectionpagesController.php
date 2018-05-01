<?php

class Tatva_Collectionpages_Adminhtml_CollectionpagesController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('collectionpages/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Fundraise Manager'), Mage::helper('adminhtml')->__('Fundraise Manager'));
		
		return $this;
	}
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

    public function massCreatepagesAction()
    {
        $collectionpagesIds = $this->getRequest()->getParam('collectionpages');
        if(!is_array($collectionpagesIds)) {
          $count=0;
          $count=count($collectionpagesIds);
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($collectionpagesIds as $collectionpagesId) {
                    $status=0; $notcreate=''; $create='';   $title=''; $url_key='';
                    $write = Mage::getSingleton("core/resource")->getConnection("core_write");
                    $collectionpages = Mage::getModel('collectionpages/collectionpages')->load($collectionpagesId);
                    $status= $collectionpages->getStatus();
                    $option_id=$collectionpages->getOptionId();
                    $option_value=$collectionpages->getOptionValue();
                    if($status==2)
                    {
                      $create.=$collectionpagesId.',';

                      $allStores = Mage::app()->getStores();
                      foreach ($allStores as $_eachStoreId => $val)
                      {
                      /* save data */
                      $model = Mage::getModel('aitmanufacturers/aitmanufacturers');
                      //echo "<pre>"; print_r($model->getCollection()->getData());exit;
                      $_storeId = Mage::app()->getStore($_eachStoreId)->getId();
                      $title=$model->getManufacturerName($option_id,$_storeId);
                      $url_key=Mage::helper('aitmanufacturers')->toUrlKey($title);

                      $model->setData('title',$title);
                      $model->setData('manufacturer_id',$option_id);
                      $model->setData('root_template','two_columns-left');
                      $model->setData('url_key',$url_key);
                      $model->setData('status','1');
                      $model->save();

                       $colls=$model->getCollection()->addFieldToFilter('manufacturer_id',$option_id);
                       foreach($colls as $coll)
                       {
                         $id=$coll->getId();
                       }
                         if($id && $option_id)
                         {
                          $sql="INSERT INTO `aitmanufacturers_stores` SET `manufacturer_id` = '".$option_id."' , store_id='".$_storeId."',id='".$id."'";
  	                      $write->query($sql);
                         }
                       }
                       $model = Mage::getModel('aitmanufacturers/aitmanufacturers')->load($option_id);
                       if($model->getData())
                       {
                         $collectionpages->setData('status',1);
                         $collectionpages->save();
                       }
                    }
                    else
                    {
                      $notcreate.=$collectionpagesId.',';
                    }
                }
                if($create=='' && $count>1)
                {
                  $create= substr($create,0,-1);
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully Collection Page(s) creatred-->'.$create, count($collectionpagesIds)
                    )
                );
                }

                if($notcreate=='' && $count>1)
                {
                 $notcreate= substr($notcreate,0,-1);
                 Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('adminhtml')->__(
                        'Cannot Create Page For-->'.$notcreate
                    )
                );
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction()
    {
        $fileName   = 'collectionpages.csv';
        $content    = $this->getLayout()->createBlock('collectionpages/adminhtml_collectionpages_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'collectionpages.xml';
        $content    = $this->getLayout()->createBlock('collectionpages/adminhtml_collectionpages_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

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
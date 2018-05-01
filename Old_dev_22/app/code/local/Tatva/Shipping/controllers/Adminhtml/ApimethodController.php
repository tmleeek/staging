<?php
/**
 * 
 * @package Tatva_Shipping
 */
class Tatva_Shipping_Adminhtml_ApimethodController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction()
    {
		$this->loadLayout()
			->_setActiveMenu('tatva')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 	
	public function indexAction()
    {
		$this->_initAction()
			->_addContent($this->getLayout()->createBlock('tatvashipping/adminhtml_apimethod'))
			->renderLayout();
	}

	public function editAction()
    {
		$id     = $this->getRequest()->getParam('shipping_apimethod_id');

		$model  = Mage::getModel('tatvashipping/apimethod')->load($id);

		if ($id && !$model->getId())
        {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tatvashipping')->__('Shipping Data does not exist'));
			$this->_redirect('*/*/');
		}
        else
        {
			
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data))
            {
				$model->setData($data);
			}
			Mage::register('shipping_apimethod_data', $model);
	
			$this->loadLayout();
			$this->_setActiveMenu('tatva');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->_addContent($this->getLayout()->createBlock('tatvashipping/adminhtml_apimethod_edit'));

			$this->renderLayout();
		} 
	}
	
	public function saveAction()
    {
		if ($data = $this->getRequest()->getPost())
        {
			$datamodel = Mage::getModel('tatvashipping/apimethod');
            try
            {

                if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '')
				{
				    $shipping_api_id = $this->getRequest()->getParam('shipping_apimethod_id');
				    if( $shipping_api_id > 0 )
                    {
					    $model = Mage::getModel('tatvashipping/apimethod')->load($shipping_api_id);
						if($model->getfilename() != "")
                        {
						    $imageUrl = Mage::getBaseDir('media').DS."shippingicons".DS."original".DS.$model->getfilename();
						    $imageResized = Mage::getBaseDir('media').DS."shippingicons".DS."thumbnail".DS.$model->getfilename();

							if(file_exists($imageUrl))
                            {
							    unlink($imageUrl);
								unlink($imageResized);
                            }
                        }
					}

                    $uploader = new Varien_File_Uploader('filename');
					$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));

					$uploader->setAllowRenameFiles(false);

					$uploader->setFilesDispersion(false);

					$filedet = pathinfo($_FILES['filename']['name']);

					// We set media as the upload dir
					$path = Mage::getBaseDir('media').DS.'shippingicons'.DS.'original'.DS;
					$uploader->save($path, $filedet['filename'].'.'.$filedet['extension'] );
					$original_image_path =  $path.$filedet['filename'].'.'.$filedet['extension'];
					list($original_image_width, $original_image_height, $type, $attr) = getimagesize($original_image_path);
					// actual path of image
					$imageUrl = Mage::getBaseDir('media').DS."shippingicons".DS."original".DS.$filedet['filename'].$date.'.'.$filedet['extension'];
					$file = $filedet['filename'].$date.'.'.$filedet['extension'];
					// path of the resized image to be saved
					// here, the resized image is saved in media/resized folder

					$thumbnail_imageUrl = Mage::getBaseDir('media').DS."shippingicons".DS."thumbnail".DS.$file;

					// resize image only if the image file exists and the resized image file doesn't exist
					// the image is resized proportionally with the width/height 135px
					if(file_exists($imageUrl))
                    {
					    $imageObj = new Varien_Image($imageUrl);
						$imageObj->constrainOnly(TRUE);
						$imageObj->keepAspectRatio(TRUE);
						$imageObj->keepFrame(FALSE);
						$imageObj->resize(100, 100);
						$imageObj->save($thumbnail_imageUrl);
					}
                }

                $data['filename'] = $filedet['filename'].'.'.$filedet['extension'];

                $datamodel->setData($data)
				->setId($this->getRequest()->getParam('shipping_apimethod_id'));

				$datamodel->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('tatvashipping')->__('Shipping data is successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back'))
                {
					$this->_redirect('*/*/edit', 
						array(
							'shipping_apimethod_id' => $datamodel->getId(),
						));
					return;
				}
				$this->_redirect('*/*/');
				return;
            }
            catch (Exception $e)
            {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array(
                	'shipping_apimethod_id' => $this->getRequest()->getParam('shipping_apimethod_id')
                ));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tatvashipping')->__('Unable to find shipping data to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction()
    {

		if( $this->getRequest()->getParam('shipping_apimethod_id') > 0 )
        {
			try
            {
				$model = Mage::getModel('tatvashipping/apimethod');
				 
				$model->setId($this->getRequest()->getParam('shipping_apimethod_id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('tatvashipping')->__('Shipping Data was successfully deleted'));
				$this->_redirect('*/*/');
			}
            catch (Exception $e)
            {
				$this->_redirect('*/*/edit',
					array(
						'shipping_apimethod_id' => $this->getRequest()->getParam('shipping_apimethod_id'),
				));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction()
    {
        $shippingapimethods = $this->getRequest()->getParam('shipping_apimethod_id');
        if(!is_array($shippingapimethods))
        {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        }
        else
        {
            try
            {
                foreach ($shippingapimethods as $shippingapimethod)
                {
                    $shippingapimethodid = Mage::getModel('tatvashipping/apimethod')->load($shippingapimethod);
                    $shippingapimethodid->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($shippingapimethods)
                    )
                );
            }
            catch (Exception $e)
            {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    /**
     * Validate form
     *
     */
    public function validateAction()
    {
    	try
        {
	        $response = new Varien_Object();
	        $response->setError(false);
	        $data = $this->getRequest ()->getPost ();
	        $shippingId = $this->getRequest()->getParam('shipping_apimethod_id');
	        $shippingmethod = $this->getRequest()->getParam('shipping_method_code');
	        
	    	if (Mage::getModel('tatvashipping/apimethod')->exists($shippingId, $shippingmethod))
            {
				Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'tatvashipping' )->__ ( 'Shipping Data already exists' ) );
	            $this->_initLayoutMessages('adminhtml/session');      
	            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());	
	            $response->setError(true);
	    	}
    	}
        catch(Exception $e)
        {
    		$response->setMessage($e->getMessage());	
    		$response->setError(false);
    	}

        $this->getResponse()->setBody($response->toJson());
    }

    public function exportCsvAction()
    {
        $fileName   = 'apimethod.csv';
        $content    = $this->getLayout()->createBlock('tatvashipping/adminhtml_apimethod_grid')
            ->getCsv();

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
<?php
/**
 * 
 * @package Tatva_Shipping
 */

class Tatva_Shipping_Adminhtml_MarketmethodController extends Mage_Adminhtml_Controller_action
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
			->_addContent($this->getLayout()->createBlock('tatvashipping/adminhtml_marketmethod'))
			->renderLayout();
	}

	public function editAction()
    {
		$id     = $this->getRequest()->getParam('shipping_marketmethod_id');

		$model  = Mage::getModel('tatvashipping/marketmethod')->load($id);

		if ($id && !$model->getId())
        {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tatvashipping')->__('Shipping rule does not exist'));
			$this->_redirect('*/*/');
		}
        else
        {

			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data))
            {
				$model->setData($data);
			}
			Mage::register('shipping_marketmethod_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('tatva');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->_addContent($this->getLayout()->createBlock('tatvashipping/adminhtml_marketmethod_edit'));

			$this->renderLayout();
		}
	}

	public function saveAction()
    {
		if ($data = $this->getRequest()->getPost())
        {
            /*print_r($data);
            exit;*/
			$datamodel = Mage::getModel('tatvashipping/marketmethod');
            try
            {
                $data['countries_ids'] = join(",",$data['countries_ids']);
                $datamodel->setData($data)
				->setId($this->getRequest()->getParam('shipping_marketmethod_id'));

				$datamodel->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('tatvashipping')->__('Shipping rule is successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back'))
                {
					$this->_redirect('*/*/edit',
						array(
							'shipping_marketmethod_id' => $datamodel->getId(),
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
                	'shipping_marketmethod_id' => $this->getRequest()->getParam('shipping_marketmethod_id')
                ));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tatvashipping')->__('Unable to find shipping rule to save'));
        $this->_redirect('*/*/');
	}

	public function deleteAction()
    {

		if( $this->getRequest()->getParam('shipping_marketmethod_id') > 0 )
        {
			try
            {
				$model = Mage::getModel('tatvashipping/marketmethod');

				$model->setId($this->getRequest()->getParam('shipping_marketmethod_id'))
					->delete();

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('tatvashipping')->__('Shipping rule successfully deleted'));
				$this->_redirect('*/*/');
			}
            catch (Exception $e)
            {
				$this->_redirect('*/*/edit',
					array(
						'shipping_marketmethod_id' => $this->getRequest()->getParam('shipping_marketmethod_id'),
				));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction()
    {
        $shippingmarketmethods = $this->getRequest()->getParam('shipping_marketmethod_id');
        if(!is_array($shippingmarketmethods))
        {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        }
        else
        {
            try
            {
                foreach ($shippingmarketmethods as $shippingmarketmethod)
                {
                    $shippingmarketmethodid = Mage::getModel('tatvashipping/marketmethod')->load($shippingmarketmethod);
                    $shippingmarketmethodid->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($shippingmarketmethods)
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
            $method_id = $this->getRequest()->getParam('shipping_marketmethod_id');
            $shipping_code_amazon = $this->getRequest()->getParam('shipping_code_amazon');
            $shipping_code_ebay = $this->getRequest()->getParam('shipping_code_ebay');
            $weight_from = $this->getRequest()->getParam('market_weight_from');
            $weight_to = $this->getRequest()->getParam('market_weight_to');
            $total_from = $this->getRequest()->getParam('market_ordertotal_from');
            $total_to = $this->getRequest()->getParam('market_ordertotal_to');
            $market_shipping_code = $this->getRequest()->getParam('market_shipping_code');
            $countries = $this->getRequest()->getParam('countries_ids');
            $countries_ids = $countries[0];

	    	if (Mage::getModel('tatvashipping/marketmethod')->exists($method_id, $shipping_code_amazon, $shipping_code_ebay, $weight_from, $weight_to, $total_from, $total_to,$market_shipping_code,$countries_ids))
            {
				Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'tatvashipping' )->__ ( 'Shipping rule already exists' ) );
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
        $fileName   = 'marketmethod.csv';
        $content    = $this->getLayout()->createBlock('tatvashipping/adminhtml_marketmethod_grid')
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
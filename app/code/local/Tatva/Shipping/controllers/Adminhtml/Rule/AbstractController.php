<?php


/**
 * 
 * @package Tatva_Shipping
 */

abstract class Tatva_Shipping_Adminhtml_Rule_AbstractController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('tatva')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 	
	public function editAction() {
		$id     = $this->getRequest()->getParam('shipping_rule_id');
		$shippingcode = $this->getRequest()->getParam('shipping_code');

		$model  = Mage::getModel('tatvashipping/rule')->load($id);

		if (($model->getId() || $id == 0) && $shippingcode) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			$model->setShippingCode($shippingcode);
			Mage::register('shipping_rule_data', $model);
	
			$this->loadLayout();
			$this->_setActiveMenu('tatva');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->_addContent($this->getLayout()->createBlock('tatvashipping/adminhtml_rule_edit'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tatvashipping')->__('Rule does not exist'));
			$this->_redirect('*/*/');
		}
	}
	
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			$shippingcode = $this->getRequest()->getParam('shipping_code');
			$model = Mage::getModel('tatvashipping/rule');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('shipping_rule_id'))
				->setShippingCode($shippingcode);
			
			try {
				
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('tatvashipping')->__('Rule was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', 
						array(
							'shipping_rule_id' => $model->getId(),
							'shipping_code' => $shippingcode
						));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array(
                	'shipping_rule_id' => $this->getRequest()->getParam('shipping_rule_id'),
                	'shipping_code' => $shippingcode
                ));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tatvashipping')->__('Unable to find rule to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		$shippingcode = $this->getRequest()->getParam('shipping_code');
		
		if( $this->getRequest()->getParam('shipping_rule_id') > 0 ) {
			try {
				$model = Mage::getModel('tatvashipping/rule');
				 
				$model->setId($this->getRequest()->getParam('shipping_rule_id'))
					->delete();		 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('tatvashipping')->__('Rule was successfully deleted'));
				$this->_redirect('*/*/');
				
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', 
					array(
						'shipping_rule_id' => $this->getRequest()->getParam('shipping_rule_id'),
						'shipping_code' => $shippingcode
				));
			}
		}
		$this->_redirect('*/*/');
	}
	
    /**
     * Validate form
     *
     */
    public function validateAction()
    {
    	try{
	        $response = new Varien_Object();
	        $response->setError(false);
	        $data = $this->getRequest ()->getPost ();
	        $id = $this->getRequest()->getParam('shipping_rule_id');
	        $shippingcode = $this->getRequest()->getParam('shipping_code');

	        
	        if ($data['weight_min'] > $data['weight_max']) {
	        	Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'tatvashipping' )->__ ( 'The minimum weight must be less than the maximum weight.' ) );
	        	$this->_initLayoutMessages('adminhtml/session');      
	            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());	
	            $response->setError(true);
	        }
	        //REG BO-713
	    	if (Mage::getModel('tatvashipping/rule')->exists($id, $shippingcode, $data['weight_min'],$data['weight_max'], $data['areas_ids'])) {
				Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'tatvashipping' )->__ ( 'Rule already exists' ) );
	            $this->_initLayoutMessages('adminhtml/session');      
	            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());	
	            $response->setError(true);
	    	}
    	}catch(Exception $e){
    		Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage() );
    		$this->_initLayoutMessages('adminhtml/session');      
    		$response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());	
    		$response->setError(false);
    	}
        $this->getResponse()->setBody($response->toJson());
    }

}
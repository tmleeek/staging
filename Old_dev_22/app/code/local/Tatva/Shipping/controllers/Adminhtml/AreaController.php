<?php
/**
 * 
 * @package Tatva_Shipping
 */
class Tatva_Shipping_Adminhtml_AreaController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('tatva')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 	
	public function indexAction() { 
		$this->_initAction()
			->_addContent($this->getLayout()->createBlock('tatvashipping/adminhtml_area'))
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('shipping_area_id');

		$model  = Mage::getModel('tatvashipping/area')->load($id);

		if ($id && !$model->getId()) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tatvashipping')->__('Area does not exist'));
			$this->_redirect('*/*/');
		}else{
			
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			Mage::register('shipping_area_data', $model);
	
			$this->loadLayout();
			$this->_setActiveMenu('tatva');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->_addContent($this->getLayout()->createBlock('tatvashipping/adminhtml_area_edit'));

			$this->renderLayout();
		} 
	}
	
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			$model = Mage::getModel('tatvashipping/area');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('shipping_area_id'));
			try {
				
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('tatvashipping')->__('Area was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', 
						array(
							'shipping_area_id' => $model->getId(),
						));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array(
                	'shipping_area_id' => $this->getRequest()->getParam('shipping_area_id')
                ));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tatvashipping')->__('Unable to find area to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		
		if( $this->getRequest()->getParam('shipping_area_id') > 0 ) {
			try {
				$model = Mage::getModel('tatvashipping/area');
				 
				$model->setId($this->getRequest()->getParam('shipping_area_id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('tatvashipping')->__('Area was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tatvashipping')->__('To delete the Area please delete the rule for the first'));
				$this->_redirect('*/*/edit', 
					array(
						'shipping_area_id' => $this->getRequest()->getParam('shipping_area_id'),
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
	        $id = $this->getRequest()->getParam('shipping_area_id');
	        $areacode = $this->getRequest()->getParam('area_code');
	        
	    	if (Mage::getModel('tatvashipping/area')->exists($id, $areacode)) {
				Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'tatvashipping' )->__ ( 'Area already exists' ) );
	            $this->_initLayoutMessages('adminhtml/session');      
	            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());	
	            $response->setError(true);
	    	}
    	}catch(Exception $e){
    		$response->setMessage($e->getMessage());	
    		$response->setError(false);
    	}

        $this->getResponse()->setBody($response->toJson());
    }
}
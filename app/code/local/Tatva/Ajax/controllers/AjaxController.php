<?php

class Tatva_Ajax_AjaxController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->_redirect('checkout/onepage', array('_secure'=>true));
    }

    public function productoptionsAction(){
		$productId = $this->getRequest()->getParam('product_id');
        
		// Prepare helper and params
		$viewHelper = Mage::helper('catalog/product_view');

		$params = new Varien_Object();
		$params->setCategoryId(false);
		$params->setSpecifyOptions(false);

		// Render page
		try {
			$viewHelper->prepareAndRender($productId, $this, $params);
		} catch (Exception $e) {
			if ($e->getCode() == $viewHelper->ERR_NO_PRODUCT_LOADED) {
				if (isset($_GET['store'])  && !$this->getResponse()->isRedirect()) {
					$this->_redirect('');
				} elseif (!$this->getResponse()->isRedirect()) {
					$this->_forward('noRoute');
				}
			} else {
				Mage::logException($e);
				$this->_forward('noRoute');
			}
		}
	}

     public function headercartAction()
     {
       	$this->loadLayout();
		$this->renderLayout();
     }

     public function headercartdeleteAction()
     {
        if ($this->getRequest()->getParam('btn_lnk')){
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                try {
                    Mage::getSingleton('checkout/cart')->removeItem($id)
                      ->save();
                } catch (Exception $e) {
                    Mage::getSingleton('checkout/session')->addError($this->__('Cannot remove item'));
                }
            }

            $this->loadLayout();
            $this->_initLayoutMessages('checkout/session');
            $this->renderLayout();
        } else {
            $backUrl = $this->_getRefererUrl();
            $this->getResponse()->setRedirect($backUrl);
        }
     }

     public function headercartupdateAction()
     {
        try {
            $cartData = array($_POST['item'] => array('qty' => $_POST['qty']));

            if (is_array($cartData)) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                foreach ($cartData as $index => $data) {
                    if (isset($data['qty'])) {
                        $cartData[$index]['qty'] = $filter->filter(trim($data['qty']));
                    }
                }
                $cart = $this->_getCart();
                if (! $cart->getCustomerSession()->getCustomer()->getId() && $cart->getQuote()->getCustomerId()) {
                    $cart->getQuote()->setCustomerId(null);
                }

                $cartData = $cart->suggestItemsQty($cartData);

                $cart->updateItems($cartData)
                    ->save();

                $this->_getSession()->setCartWasUpdated(true);

                $this->loadLayout();
                $this->renderLayout();
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError(Mage::helper('core')->escapeHtml($e->getMessage()));
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot update shopping cart.'));
            Mage::logException($e);
        }
     }

     public function _getCart()
      {
        return Mage::getSingleton('checkout/cart');
      }

     public function _getSession()
      {
        return Mage::getSingleton('checkout/session');
      }

     public function _getQuote()
      {
        return $this->_getCart()->getQuote();
      }

}
?>
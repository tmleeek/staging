<?php
class Tatva_Ajax_WhishlistController extends Mage_Core_Controller_Front_Action
{
	public function compareAction(){
		$response = array();

		if ($productId = (int) $this->getRequest()->getParam('product')) {
			$product = Mage::getModel('catalog/product')
			->setStoreId(Mage::app()->getStore()->getId())
			->load($productId);

			if ($product->getId()) {
				Mage::getSingleton('catalog/product_compare_list')->addProduct($product);
				$response['status'] = 'SUCCESS';
				$response['message'] = $this->__('The product %s has been added to comparison list.', Mage::helper('core')->escapeHtml($product->getName()));
				Mage::register('referrer_url', $this->_getRefererUrl());
				Mage::helper('catalog/product_compare')->calculate();
				Mage::dispatchEvent('catalog_product_compare_add_product', array('product'=>$product));
				$this->loadLayout();
				$sidebar_block = $this->getLayout()->getBlock('catalog.compare.sidebar');
				$sidebar_block->setTemplate('ajax/catalog/product/compare/sidebar.phtml');
				$sidebar = $sidebar_block->toHtml();
				$response['sidebar'] = $sidebar;
			}
		}
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
		return;
	}

	protected function _getWishlist()
	{
		$wishlist = Mage::registry('wishlist');
		if ($wishlist) {
			return $wishlist;
		}

		try {
			$wishlist = Mage::getModel('wishlist/wishlist')
			->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);
			Mage::register('wishlist', $wishlist);
		} catch (Mage_Core_Exception $e) {
			Mage::getSingleton('wishlist/session')->addError($e->getMessage());
		} catch (Exception $e) {
			Mage::getSingleton('wishlist/session')->addException($e,
			Mage::helper('wishlist')->__('Cannot create wishlist.')
			);
			return false;
		}

		return $wishlist;
	}
	public function addAction()
	{
		$response = array();
		if (!Mage::getStoreConfigFlag('ajax/wishlistcompare/enabledpro')) {
			$response['status'] = 'ERROR';
			$response['message'] = $this->__('Wishlist Has Been Disabled By Admin');
            $response['login_url_if_error']= Mage::getUrl('customer/account/login/');
		}
		if(!Mage::getSingleton('customer/session')->isLoggedIn()){
			$response['status'] = 'ERROR';
			$response['message'] = $this->__('Please Login First');
            $response['login_url_if_error']= Mage::getUrl('customer/account/login/');
		}

		if(empty($response)){
			$session = Mage::getSingleton('customer/session');
			$wishlist = $this->_getWishlist();
			if (!$wishlist) {
				$response['status'] = 'ERROR';
				$response['message'] = $this->__('Unable to Create Wishlist');
                $response['login_url_if_error']= Mage::getUrl('customer/account/login/');
			}else{

				$productId = (int) $this->getRequest()->getParam('product');
				if (!$productId) {
					$response['status'] = 'ERROR';
					$response['message'] = $this->__('Product Not Found');
				}else{

					$product = Mage::getModel('catalog/product')->load($productId);
					if (!$product->getId() || !$product->isVisibleInCatalog()) {
						$response['status'] = 'ERROR';
						$response['message'] = $this->__('Cannot specify product.');
					}else{

						try {
							$requestParams = $this->getRequest()->getParams();
							$buyRequest = new Varien_Object($requestParams);

							$result = $wishlist->addNewItem($product, $buyRequest);
							if (is_string($result)) {
								Mage::throwException($result);
							}
							$wishlist->save();
							$itemCollection = Mage::getModel('wishlist/item')->getCollection()->addFieldToFilter('wishlist_id',$wishlist->getId())->addFieldToFilter('product_id',$productId);

						    $newCollArr=$itemCollection->getData();

						     $wishlist_item_id = $newCollArr[0]['wishlist_item_id'];


							Mage::dispatchEvent(
                				'wishlist_add_product',
							array(
			                    'wishlist'  => $wishlist,
			                    'product'   => $product,
			                    'item'      => $result
							)
							);

							Mage::helper('wishlist')->calculate();
							// New added
							$counter_wishlist= Mage::helper('wishlist')->getItemCount();
							$response['counter'] =   $counter_wishlist;

							// Prepare Remove Url
							$removeUrl=Mage::getBaseUrl()."wishlist/index/remove/item/".$wishlist_item_id;
							$response['removeUrl']=$removeUrl;

							// Take Product Id
							$response['productId']=$productId;

							$message = $this->__('%1$s has been added to your wishlist.', $product->getName(), $referer);
							$response['status'] = 'SUCCESS';
							$response['message'] = $message;

							Mage::unregister('wishlist');

							$this->loadLayout();
							$toplink = $this->getLayout()->getBlock('top.links')->toHtml();
							$sidebar_block = $this->getLayout()->getBlock('wishlist_sidebar');
							$sidebar = $sidebar_block->toHtml();
							$response['toplink'] = $toplink;
							$response['sidebar'] = $sidebar;
                        }
						catch (Mage_Core_Exception $e) {
							$response['status'] = 'ERROR';
							$response['message'] = $this->__('An error occurred while adding item to wishlist: %s', $e->getMessage());
						}
						catch (Exception $e) {
							mage::log($e->getMessage());
							$response['status'] = 'ERROR';
							$response['message'] = $this->__('An error occurred while adding item to wishlist.');
						}
					}
				}
			}

		}

		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
		return;
	}
	public function removeAction()
		{

          $id = (int) $this->getRequest()->getParam('item');
          $item = Mage::getModel('wishlist/item')->load($id);

          if (!$item->getId()) {
              return $this->norouteAction();
          }
          $wishlist = $this->_getWishlist($item->getWishlistId());
          if (!$wishlist) {
              return $this->norouteAction();
          }
          try {
                $item->delete();
                $wishlist->save();
                $response['status'] = 'SUCCESS';

                // New added
                $counter_wishlist= Mage::helper('wishlist')->getItemCount();
                $response['counter'] =   $counter_wishlist;  

                // Prepare Remove Url
                $addUrl=Mage::getBaseUrl()."wishlist/index/add/product/".$item->getProductId();
                $response['addUrl']=$addUrl;
                $response['prod_id']=$item->getProductId();

                $this->loadLayout();
                $toplink = $this->getLayout()->getBlock('top.links')->toHtml();
    			$sidebar_block = $this->getLayout()->getBlock('wishlist_sidebar');
    			$sidebar = $sidebar_block->toHtml();
    			$response['toplink'] = $toplink;
    			$response['sidebar'] = $sidebar;
               }
          catch (Mage_Core_Exception $e)
               {
                Mage::getSingleton('customer/session')->addError(
                    $this->__('An error occurred while deleting the item from wishlist: %s', $e->getMessage())
                );
                $response['status'] = 'ERROR';
               }
          catch(Exception $e)
              {
                Mage::getSingleton('customer/session')->addError(
                    $this->__('An error occurred while deleting the item from wishlist.')
                );
                $response['status'] = 'ERROR';
               }
           Mage::helper('wishlist')->calculate();
         $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
		}

    public function fromcartAction()
        {
            $wishlist = $this->_getWishlist();
            if (!$wishlist) {
                return $this->norouteAction();
            }
            $itemId = (int) $this->getRequest()->getParam('item');

            /* @var Mage_Checkout_Model_Cart $cart */
            $cart = Mage::getSingleton('checkout/cart');
            $session = Mage::getSingleton('checkout/session');

            try{
                $item = $cart->getQuote()->getItemById($itemId);
                if (!$item) {
                    Mage::throwException(
                        Mage::helper('wishlist')->__("Requested cart item doesn't exist")
                    );
                }

                $productId  = $item->getProductId();
                $buyRequest = $item->getBuyRequest();

                $wishlist->addNewItem($productId, $buyRequest);

                $productIds[] = $productId;
                $cart->getQuote()->removeItem($itemId);
                $cart->save();
                Mage::helper('wishlist')->calculate();
                $productName = Mage::helper('core')->escapeHtml($item->getProduct()->getName());
                $wishlistName = Mage::helper('core')->escapeHtml($wishlist->getName());
                $session->addSuccess(
                    Mage::helper('wishlist')->__("%s has been moved to wishlist %s", $productName, $wishlistName)
                );
                $wishlist->save();
            } catch (Mage_Core_Exception $e) {
                $session->addError($e->getMessage());
            } catch (Exception $e) {
                $session->addException($e, Mage::helper('wishlist')->__('Cannot move item to wishlist'));
            }

            return $this->_redirectUrl(Mage::helper('checkout/cart')->getCartUrl());
        }
}


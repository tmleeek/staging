<?php 
/**
 * Controller bound to order objects
 * @author Arnaud P <arnaud@boostmyshop.com>
 * @version 1.1.0
 * @package MDN\Colissimo\controllers\Adminhtml\Sales
 */

class MDN_Colissimo_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Ajax function used to retrieve an order's items as JSON
	 * @author Arnaud P <arnaud@boostmyshop.com>
	 * @version 1.1.0
	 * @param void
	 * @return void
	 */
	public function getItemsAction()
	{
		$orderId = $this->getRequest()->getParam('order_id');

		$order = Mage::getModel('sales/order')->load($orderId);

		if ($order->getId() > 0) {
			header('Content-type: application/json');
			
			$i = 0;
			foreach ($order->getItemsCollection() as $item) {
				$items[$i]['sku'] = $item->getsku();
				$items[$i]['qty_ordered'] = $item->getqty_ordered();
				$items[$i]['name'] = $item->getname();
				$items[$i]['weight'] = $item->getweight();
				$items[$i]['price'] = $item->getprice();
				$items[$i]['order_item_id'] = $item->getId();
				$items[$i]['product_id'] = $item->getproduct_id();

				$i++;
			}

			echo json_encode($items);
		}

		die();
	}
}
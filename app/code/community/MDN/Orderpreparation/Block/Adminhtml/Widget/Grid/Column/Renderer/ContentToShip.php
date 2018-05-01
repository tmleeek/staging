<?php

/*
 * Display content to ship in selected orders
 */

class MDN_Orderpreparation_Block_Adminhtml_Widget_Grid_Column_Renderer_ContentToShip extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    private $_itemsToShip = null;

    /**
     *
     * @param Varien_Object $row
     * @return string 
     */
    public function render(Varien_Object $row) {
        $retour = '';

        $order = $row;
        $OrderToPrepare = mage::getModel('Orderpreparation/ordertoprepare')->load($order->getId(), 'order_id');

        //Build string with content to ship
        $this->_itemsToShip = mage::getModel('Orderpreparation/ordertoprepare')->GetItemsToShip($order->getId());
        
        $lines = array();
        $tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        foreach ($this->_itemsToShip as $item) {
            $orderItem = $item->getSalesOrderItem();
            $productId = $orderItem->getproduct_id();
            $name = $orderItem->getName();
            $name .= mage::helper('AdvancedStock/Product_ConfigurableAttributes')->getDescription($productId);
            if (($orderItem->getProductType() == 'configurable') || ($orderItem->getProductType() == 'bundle'))
                $lines[] = array('sku' => $orderItem->getSku(), 'style' => 'italic', 'color' => '#0000FF', 'label' => '<i>' . $item->getqty() . 'x ' . $name . '</i>');
            else {
                if ($item->getSalesOrderItem()->getparent_item_id())
                    $lines[] = array('sku' => $orderItem->getSku(), 'style' => '', 'color' => 'black', 'label' => $tab . $item->getqty() . 'x ' . $name);
                else
                    $lines[] = array('sku' => $orderItem->getSku(), 'style' => 'bold', 'color' => 'black', 'label' => $item->getqty() . 'x ' . $name);
            }

            //add remove button (if allowed)
            if ($this->canRemoveItem($order, $orderItem, $OrderToPrepare))
            {
                $url = $this->getUrl('OrderPreparation/OrderPreparation/removeItem', array('order_item_id' => $orderItem->getId()));
                $lines[count($lines) - 1]['label'] .= '&nbsp;<a href="'.$url.'"><img src="' . $this->getSkinUrl('images/cancel_icon.gif') . '"></a>';
            }
        }

        $suffix = '';
        $prefix = '';

        //don't use table because Magento rewrite class using js
        $skuWidth = '180px';
        $even = '#ffffff';
        $odd = '#f6f6f6';
        $css = 'display: table-cell; border-style: solid; border-width: 1px; border-color: '.$odd.'; padding-bottom: 5px; padding: 5px;';
        $count = 0;
        $retour .= '<div style="display: table; table-layout: fixed;">';
        foreach($lines as $line)
        {
            $count ++;
            if(array_key_exists('style', $line)){
              switch($line['style'])
              {
                  case 'bold':
                      $prefix = '<b>';
                      $suffix = '</b>';
                      break;
                  case 'italic':
                      $prefix = '<i>';
                      $suffix = '</i>';
                      break;
                  case 'stroke':
                      $prefix = '<s>';
                      $suffix = '</s>';
                      break;
                  default:
                      $prefix = '';
                      $suffix = '';
                      break;
              }
            }
            $bgcolor = ($count%2) ? $even : $odd;
            $retour .= '<div style="display: table-row;">';
            $retour .= '<div style="'.$css.' background:'.$bgcolor.'; width: '.$skuWidth.'"><font color="'.$line['color'].'">'.$prefix.$line['sku'].$suffix.'</font></div>';
            $retour .= '&nbsp;<div style="'.$css.' background:'.$bgcolor.';"><font color="'.$line['color'].'">'.$prefix.$line['label'].$suffix.'</font></div>';
            $retour .= '</div>';
        }
        $retour .= '<div/>';
        
        
        return $retour;
    }

    /**
     * Return true if operator can manually remove item from preparation
     */
    protected function canRemoveItem($order, $orderItem, $orderToPrepare) {
        //if order is shipped, return false
        if ($orderToPrepare->getshipment_id())
            return false;

        //if item is alone in order, return false
        if ($this->getNoParentCount() == 1)
            return false;

        //if item has parent, return false
        if ($orderItem->getparent_item_id())
            return false;

        return true;
    }

    /**
     * Return number of products to prepare without parents
     */
    protected function getNoParentCount() {
        $count = 0;
        foreach ($this->_itemsToShip as $item) {
            if (!$item->getSalesOrderItem()->getparent_item_id())
                $count++;
        }

        return $count;
    }

}
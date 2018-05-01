<?php

class TBT_Rewardssocial_Block_Purchase_Items extends Mage_Checkout_Block_Onepage_Success
{
    protected static $_widgetsNodes = null;

    public function getOrderItemsCollection()
    {
        $items = $this->_getOrder()->getAllVisibleItems();
        return $items;
    }

    public function getOrderedProduct($item)
    {
        return Mage::getModel('catalog/product')->load($item->getProductId());
    }

    public function getColumnCount() {
        return 3;
        // return Mage::getStoreConfig('ordershare/design/columncount');
    }

    public function getSocialWidgetsHtml($product)
    {
        $widgets = $this->getLayout()->createBlock('rewardssocial/purchase_widgets')
            ->setData('product', $product)
            ->setData('order_id', $this->_getOrder()->getId());

        $blocks = $this->_getWidgetsChildren($product);

        foreach ($blocks as $name => $block) {
            $widgets->append($block, $name);
        }
        $html = $widgets->toHtml();

        return $html;
    }

    protected function _getOrder()
    {
        return Mage::getModel('sales/order')->loadByIncrementId($this->getOrderId());
    }

    protected function _getWidgetsChildren($product)
    {
        $nodes = $this->_getWidgetsNodes();
        // exit if no nodes found
        if (sizeof($nodes) <= 0) {
            return array();
        }

        $blocks = array();
        foreach ($nodes as $node) {
            foreach ($node->children() as $child) {
                if (!isset($child['type']) || !isset( $child['name']) || !isset( $child['template'])) {
                    continue;
                }

                $nameInLayout = (string) $child['name'] . $product->getId();
                $block = $this->getLayout()
                    ->createBlock((string) $child['type'], $nameInLayout)
                    ->setTemplate((string) $child['template'])
                    ->setData('product', $product)
                    ->setData('order_id', $this->_getOrder()->getId());

                $blocks[$nameInLayout] = $block;
            }
        }

        return $blocks;
    }

    protected function _getWidgetsNodes()
    {
        if (self::$_widgetsNodes !== null) {
            return self::$_widgetsNodes;
        }

        self::$_widgetsNodes = $this->getLayout()->getXPath("//reference[@name='rewardssocial.checkout.purchase.widgets']");

        // Make sure we don't reset it to null so it doesn't try to load it again since null assumes that we have not loaded it yet.
        if(self::$_widgetsNodes === null) {
            self::$_widgetsNodes = array();
        }

        return self::$_widgetsNodes;
    }

    /**
     * Wrapper for standart strip_tags() function with extra functionality for html entities
     *
     * @param string $data
     * @param string $allowableTags
     * @param bool $escape
     * @return string
     */
    public function stripTags($data, $allowableTags = null, $escape = false)
    {
        $result = strip_tags($data, $allowableTags);
        return $escape ? $this->escapeHtml($result, $allowableTags) : $result;
    }
}

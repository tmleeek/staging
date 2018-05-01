<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shopping cart item render block
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class TBT_Rewards_Block_Checkout_Cart_Item_Points extends TBT_Rewards_Block_Checkout_Cart_Item_Renderer {


    protected function _beforeToHtml() {
        $this->_assureItem();

        return parent::_beforeToHtml();;
    }

    protected function _toHtml()
    {
        if(!Mage::getStoreConfigFlag('rewards/autointegration/shopping_cart_item_points')) {
            return "";
        }

        return parent::_toHtml();
    }

    /**
     * Checks to see if an item model has been set for the parent block.  If so, grabs the item model and sets it for itself.
     */
    protected function _assureItem()
    {
        if ($this->getParentBlock()) {
            $item = $this->getParentBlock()->getItem();
        }

        if(!empty($item)) {
            $this->setItem($item);
        }

        return $this;
    }

    /**
     * Fetches the child block HTML for all this sub-components to display (redeeming, spending, etc) as defined in the layout XML.
     * @return string HTML
     */
    public function getChildrenHtml() {

        $html = "";

        // Fetch points redmeption data
        $hasRedeemed = $this->hasRedemptions();
        $redeemed_points_data = $this->getRedemptionData();

        // Fetch points earning data
        $hasEarned = $this->hasEarnings();
        $earned_points_data = $this->getEarningData();


        // Grab the HTML of each of the child blocks
        foreach ($this->_getChildrenBlocks() as $child) {

            $child->setRedemptionData( $redeemed_points_data );
            $child->setEarningData( $earned_points_data );
            $child->setItem( $this->getItem() );

            $html .= $child->toHtml();
        }

        return $html;

    }


    /**
     * Fetches all the child blocks for this block to be displayed.
     * IF the block has a priority value set into it's data using (getPriority) this will try to display it in the order of it's priority, lowest number ot highest number.
     * @return array(TBT_Rewards_Block_Checkout_Cart_Item_Points)
     */
    protected function _getChildrenBlocks() {
        // prepare the data and the priority values
        $blocks = array();
        $last_priority = 0;

        // Collect the child blocks and set the index to the display priority
        foreach ($this->_children as $child) {
            if ( ! ($child instanceof TBT_Rewards_Block_Checkout_Cart_Item_Points) ) {
                continue;
            }

            $priority = $child->getPriority();
            if(empty($priority)) $priority = $last_priority;;
            $last_priority = $priority+1;

            $blocks[$priority] = $child;
        }

        // sort by the array key (key is priority value).
        ksort($blocks);

        return $blocks;

    }

}
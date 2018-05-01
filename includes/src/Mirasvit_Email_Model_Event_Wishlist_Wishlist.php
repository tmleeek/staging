<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Trigger Email Suite
 * @version   1.0.1
 * @revision  168
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Email_Model_Event_Wishlist_Wishlist extends Mirasvit_Email_Model_Event_Abstract
{
    const EVENT_CODE = 'wishlist_wishlist|';

    public function getEventsGroup()
    {
        return __('Wishlist');
    }

    public function getEvents()
    {
        $result = array();

        $result[self::EVENT_CODE.'productadded'] = __('Product was added to wishlist');
        $result[self::EVENT_CODE.'shared']       = __('Wishlist shared');

        return $result;
    }

    public function findEvents($eventCode, $from)
    {
        return array();
    }

    public function observer($eventCode, $observer)
    {
        if ($eventCode == self::EVENT_CODE.'productadded') {
            $product  = $observer->getProduct();
            $wishlist = $observer->getWishlist();
            $customer = Mage::getModel('customer/customer')->load($wishlist->getCustomerId());

            $key = array();
            $key[] = $customer->getEmail();
            $key[] = $wishlist->getId();
            $key[] = $product->getId();

            $args = array(
                'time'              => time(),
                'customer_email'    => $customer->getEmail(),
                'customer_name'     => $customer->getName(),
                'customer_id'       => $customer->getId(),
                'store_id'          => $wishlist->getStore()->getId(),
                'product_id'        => $product->getId(),
                'wishlist_id'       => $wishlist->getId(),
            );

            $this->dispatchEvent($eventCode, $key, $args);
        } elseif ($eventCode == self::EVENT_CODE.'shared') {
            $wishlist = $observer->getWishlist();
            $customer = Mage::getModel('customer/customer')->load($wishlist->getCustomerId());

            $key = array();
            $key[] = $customer->getEmail();
            $key[] = $wishlist->getId();

            $args = array(
                'time'              => time(),
                'customer_email'    => $customer->getEmail(),
                'customer_name'     => $customer->getName(),
                'customer_id'       => $customer->getId(),
                'store_id'          => $wishlist->getStore()->getId(),
                'wishlist_id'       => $wishlist->getId(),
            );

            $this->saveEvent($eventCode, $key, $args);
        }
    }
}
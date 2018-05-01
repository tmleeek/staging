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


class Mirasvit_Email_Model_Trigger_Chain extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('email/trigger_chain');
    }

    public function getDays()
    {
        return intval($this->getDelay() / 60 / 60 / 24);
    }

    public function getHours()
    {
        return intval($this->getDelay() / 60 / 60) - $this->getDays() * 24;
    }

    public function getMinutes()
    {
        return intval($this->getDelay() / 60) - $this->getDays() * 24 * 60 - $this->getHours() * 60;
    }

    public function getCoupon()
    {
        if ($this->getCouponEnabled()) {
            $rule = Mage::getModel('salesrule/rule')->load($this->getCouponSalesRuleId());

            if ($rule->getId()) {
                $generator = Mage::getSingleton('salesrule/coupon_codegenerator', array('length' => 5));
                $rule->setCouponCodeGenerator($generator);
                $code =  'EML'.$rule->getCouponCodeGenerator()->generateCode();

                $expirationDate = false;
                if ($this->getCouponExpiresDays()) {
                    $expirationDate = time() + $this->getCouponExpiresDays() * 24 * 60 * 60;
                }

                $coupon = Mage::getModel('salesrule/coupon');
                $coupon->setRule($rule)
                    ->setCode($code)
                    ->setIsPrimary(false)
                    ->setUsageLimit(1)
                    ->setUsagePerCustomer(1)
                    ->setExpirationDate($expirationDate)
                    ->setType(1)
                    ->save();

                return $coupon;
            }
        }

        return false;
    }

    public function getCrossSells($args)
    {
        if ($this->getCrossSellsEnabled()) {
            $crossType = $this->getCrossSellsTypeId();
            $productIds = array();

            switch ($crossType) {
                case Mirasvit_Email_Model_System_Source_CrossSell::MAGE_CROSS:
                case Mirasvit_Email_Model_System_Source_CrossSell::MAGE_RELATED:
                case Mirasvit_Email_Model_System_Source_CrossSell::MAGE_UPSELLS:
                    // base Products
                    $baseProducts = array();
                    if (isset($args['order'])) {
                        foreach ($args['order']->getAllVisibleItems() as $item) {
                            $baseProducts[] = $item->getProduct();
                        }
                    }

                    if (isset($args['quote']) && count($baseProducts) == 0) {
                        foreach ($args['quote']->getAllVisibleItems() as $item) {
                            $baseProducts[] = $item->getProduct();
                        }
                    }

                    if (isset($args['customer']) && count($baseProducts) == 0) {
                        $orders = Mage::getModel('sales/order')
                            ->getCollection()
                            ->addAttributeToFilter('customer_id', $args['customer']->getId());
                        foreach ($orders as $order) {
                            foreach ($order->getAllVisibleItems() as $item) {
                                $baseProducts[] = $item->getProduct();
                            }
                        }
                    }

                    foreach ($baseProducts as $product) {
                        $crossIds = array();
                        if ($product) {
                            if ($crossType == Mirasvit_Email_Model_System_Source_CrossSell::MAGE_CROSS) {
                                $crossIds = $product->getCrossSellProductIds();
                            } elseif ($crossType == Mirasvit_Email_Model_System_Source_CrossSell::MAGE_RELATED) {
                                $crossIds = $product->getRelatedProductIds();
                            } elseif ($crossType == Mirasvit_Email_Model_System_Source_CrossSell::MAGE_UPSELLS) {
                                $crossIds = $product->getUpSellProductIds();
                            }
                        }

                        $productIds = array_merge($crossIds, $productIds);
                    }

                    break;

                case Mirasvit_Email_Model_System_Source_CrossSell::AW_WBTAB:
                    if (Mage::helper('email')->isWBTABInstalled()) {
                        $orderProductIds = array();
                        foreach ($baseProducts as $product) {
                            $orderProductIds[] = $product->getId();
                        }
                        $productIds = Mage::getModel('relatedproducts/api')
                            ->getRelatedProductsFor($orderProductIds, $storeId);
                        $productIds = array_keys($productIds);
                    }
                    break;

                case Mirasvit_Email_Model_System_Source_CrossSell::AW_ARP2:
                    if (Mage::helper('email')->isARP2Installed() && class_exists('AW_Autorelated_Model_Api')) {
                        $arp2Collection = Mage::getModel('awautorelated/blocks')->getCollection()
                            ->addTypeFilter(AW_Autorelated_Model_Source_Type::SHOPPING_CART_BLOCK)
                            ->addStatusFilter()
                            ->addDateFilter()
                            ->setPriorityOrder('DESC');
                        $ids = $arp2Collection->getAllIds();
                        if (count($ids) > 0) {
                            foreach ($ids as $arp2Block) {
                                $block = Mage::getModel('awautorelated/blocks')->load($arp2Block);
                                $productIds = array_merge($productIds, Mage::getModel('awautorelated/api')
                                        ->getRelatedProductsForShoppingCartRule($arp2Block, $quoteId));
                            }
                        }
                    }
                    break;
            }

            shuffle($productIds);

            if (count($productIds)) {
                $collection = Mage::getModel('catalog/product')->setStoreId($args['store_id'])->getCollection()
                    ->addFieldToFilter('entity_id', array('in' => $productIds))
                    ->addAttributeToSelect('thumbnail')
                    ->addAttributeToSelect('small_image')
                    ->addAttributeToSelect('image')
                    ->addAttributeToSelect('name')
                    ->addMinimalPrice()
                    ->addFinalPrice()
                    ->addTaxPercents()
                    ->addStoreFilter()
                    ->addUrlRewrite();

                Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
                Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($collection);

                $collection->getSelect()->reset('order');

                $crossBlock = Mage::app()->getLayout()->createBlock('email/cross')
                    ->setCollection($collection);

                return $crossBlock->toHtml();
            }

            return false;
        }
    }

    public function getTemplate()
    {
        $template = null;
        $info = explode(':', $this->getTemplateId());

        switch ($info[0]) {
            case 'emaildesign':
                $template = Mage::getModel('emaildesign/template')->load($info[1]);
                break;

            case 'email':
                $template = Mage::getModel('core/email_template')->load($info[1]);
                break;

            case 'newsletter':
                $template = Mage::getModel('newsletter/template')->load($info[1]);
                break;
        }

        return $template;
    }

    public function prepareArgs($uniqEventKey, &$args)
    {
        $coupon = $this->getCoupon();
        if ($coupon) {
            $args['coupon'] = $coupon;
        }

        $quoteId = false;

        $crossSells = $this->getCrossSells($args);
        // $crossSells = $this->getCrossSells($crossProducts, $args['store_id'], $quoteId);
        if ($crossSells) {
            $args['cross_sells'] = $crossSells;
        }

        return $this;
    }
}
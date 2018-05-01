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


class Mirasvit_Email_IndexController extends Mage_Core_Controller_Front_Action
{
    public function unsubscribeAction()
    {
        if ($code = $this->getRequest()->getParam('code')) {
            $queue = Mage::getModel('email/queue')->loadByUniqKeyMd5($code);

            if (!$queue) {
                Mage::getSingleton('core/session')->addError($this->__('Wrong unsubscription link'));
                $this->_redirect('/');
                return;
            }

            $args = $queue->getArgs();

            $queueCollection = Mage::getModel('email/queue')->getCollection()
                ->addFieldToFilter('recipient_email', $args['customer_email'])
                ->addFieldToFilter('status', Mirasvit_Email_Model_Queue::STATUS_PENDING);
            foreach ($queueCollection as $item) {
                $item->unsubscribe();
            }

            Mage::getSingleton('core/session')->addSuccess($this->__('You have been successfully unsubscribed from receiving these messages.'));
        }

        $this->_redirect('/');
    }

    public function restoreCartAction()
    {
        if ($code = $this->getRequest()->getParam('code')) {
            $queue = Mage::getModel('email/queue')->loadByUniqKeyMd5($code);

            if (!$queue) {
                Mage::getSingleton('core/session')->addError($this->__('The cart for restore not found.'));
                $this->_redirect('/');
                return;
            }

            $args = $queue->getArgs();

            $customer = Mage::getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->load($args['customer_id']);

            $session = Mage::getSingleton('customer/session');
            if ($session->isLoggedIn() && $customer->getId() != $session->getCustomerId()) {
                $session->logout();
            }

            try {
                $session->setCustomerAsLoggedIn($customer);
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($this->__('Please confirm your account.'));
                $this->_redirect('/');
            }

            $this->getResponse()->setRedirect(Mage::getUrl('checkout/cart'));
        } else {
            Mage::getSingleton('core/session')->addError($this->__('The cart for restore not found.'));
            $this->_redirect('/');
        }
    }

    public function resumeAction()
    {
        if ($code = $this->getRequest()->getParam('code')) {
            $queue = Mage::getModel('email/queue')->loadByUniqKeyMd5($code);

            if ($queue) {
                $args = $queue->getArgs();

                $customer = Mage::getModel('customer/customer')
                    ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                    ->load($args['customer_id']);

                $session = Mage::getSingleton('customer/session');
                if ($session->isLoggedIn() && $customer->getId() != $session->getCustomerId()) {
                    $session->logout();
                }

                try {
                    $session->setCustomerAsLoggedIn($customer);
                } catch (Exception $e) {
                }
            }
        }

        if ($to = $this->getRequest()->getParam('to')) {
            $this->getResponse()->setRedirect($to);
        } else {
            $this->getResponse()->setRedirect('/');
        }
    }

    public function viewAction()
    {
        if ($code = $this->getRequest()->getParam('code')) {
            $queue = Mage::getModel('email/queue')->loadByUniqKeyMd5($code);

            if (!$queue) {
                Mage::getSingleton('core/session')->addError($this->__('The email not found.'));
                $this->_redirect('/');
                return;
            }

            echo $queue->getContent();
        } else {
            Mage::getSingleton('core/session')->addError($this->__('The cart for restore not found.'));
            $this->_redirect('/');
        }
    }

    public function captureAction()
    {
        $type = $this->getRequest()->getParam('type');
        $value = $this->getRequest()->getParam('value');

        $quote = Mage::getModel('checkout/cart')->getQuote();
        if ($quote->getBillingAddress() && $quote->getBillingAddress()->getId()) {
            $billing = $quote->getBillingAddress();
            $billing->setData($type, $value)
                ->save();
        }
    }
}

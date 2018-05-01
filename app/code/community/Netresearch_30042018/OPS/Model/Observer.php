<?php
/**
 * @category   OPS
 * @package    Netresearch_OPS
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @copyright  Copyright (c) 2013 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Netresearch_OPS_Model_Observer
 *
 * @author     André Herrn <andre.herrn@netresearch.de>
 * @copyright  Copyright (c) 2013 Netresearch GmbH & Co. KG
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Netresearch_OPS_Model_Observer
{
    
    /**
     * Get one page checkout model
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }

    public function getAdminSession()
    {
        return Mage::getSingleton('admin/session');
    }

    public function isAdminSession()
    {

        if ($this->getAdminSession()->getUser()) {
            return 0 < $this->getAdminSession()->getUser()->getUserId();
        }
        return false;
    }

    public function getHelper($name=null)
    {
        if (is_null($name)) {
            return Mage::helper('ops');
        }
        return Mage::helper('ops/' . $name);
    }

    /**
     * trigger ops payment
     */
    public function checkoutTypeOnepageSaveOrderBefore($observer)
    {
        $quote = $observer->getQuote();
        $order = $observer->getOrder();
        $code = $quote->getPayment()->getMethodInstance()->getCode();

        try {
            if ('ops_cc' == $code 
                && $quote->getPayment()->getMethodInstance()->hasBrandAliasInterfaceSupport($quote->getPayment(), 1)
                && true === Mage::getModel('ops/config')->isAliasManagerEnabled()
                ) {
                $alias = $quote->getPayment()->getAdditionalInformation('alias');
                 if (0 < strlen(trim($alias)) &&
                     is_numeric($quote->getPayment()->getAdditionalInformation('cvc'))

                     && false === Mage::helper('ops/alias')->isAliasValidForAddresses(
                         $quote->getCustomerId(),
                         $alias,
                         $quote->getBillingAddress(),
                         $quote->getShippingAddress(),
                         $quote->getStoreId()
                     )) {
                    $this->getOnepage()->getCheckout()->setGotoSection('payment');
                    Mage::throwException(
                        $this->getHelper()->__('Invalid payment information provided!')
                    );
                 }
                $this->confirmAliasPayment($order, $quote);
            } elseif ('ops_cc' == $code && (
                $quote->getPayment()->getMethodInstance()->hasBrandAliasInterfaceSupport($quote->getPayment(), 1)
                || $this->isAdminSession()
            )) {
                $this->confirmAliasPayment($order, $quote);
            } elseif ('ops_directDebit' == $code) {
                $this->confirmDdPayment($order, $quote, $observer);
            }
        } catch (Exception $e) {
            $quote->setIsActive(true);
            $this->getOnepage()->getCheckout()->setGotoSection('payment');
            throw new Mage_Core_Exception($e->getMessage());
        }
    }

    public function salesModelServiceQuoteSubmitSuccess($observer)
    {
        $quote = $observer->getQuote();
        if (true === $this->isCheckoutWithAliasOrDd($quote->getPayment()->getMethodInstance()->getCode())) {
            $quote = $observer->getQuote();
            $quote->getPayment()
                ->setAdditionalInformation('checkoutFinishedSuccessfully', true)
                ->save();
        }
    }

    /**
     * set order status for orders with OPS payment
     */
    public function checkoutTypeOnepageSaveOrderAfter($observer)
    {
        $quote = $observer->getQuote();
        if (true === $this->isInlinePayment($quote->getPayment())) {
            $order = $observer->getOrder();

            /* if there was no error */
            if (true === $quote->getPayment()->getAdditionalInformation('checkoutFinishedSuccessfully')) {
                $opsResponse = $quote->getPayment()->getAdditionalInformation('ops_response');
                if ($opsResponse) {
                    Mage::helper('ops/payment')->applyStateForOrder($order, $opsResponse);
                    Mage::helper('ops/alias')->setAliasActive($quote, $order);

                } else {
                    Mage::helper('ops/payment')->handleUnknownStatus($order);
                }
                $quote->getPayment()->setAdditionalInformation('alreadyProcessed', true);
                $quote->getPayment()->unsAdditionalInformation('checkoutFinishedSuccessfully');
                $quote->getPayment()->save();
            } else {
                $this->handleFailedCheckout($quote, $order);
            }
        }
    }
    
   

    /**
     * set order status for orders with OPS payment
     */
    public function checkoutSubmitAllAfter($observer)
    {

        $quote = $observer->getQuote();
        $order = $observer->getOrder();
        if (true !== $quote->getPayment()->getAdditionalInformation('alreadyProcessed')) {
            if ($this->isAdminSession() || (true === $this->isInlinePayment($quote->getPayment())
                && !is_null($quote->getPayment()->getAdditionalInformation('checkoutFinishedSuccessfully')))
            ) {
                /* if there was no error */
                if (true === $quote->getPayment()->getAdditionalInformation('checkoutFinishedSuccessfully')) {
                    $opsResponse = $quote->getPayment()->getAdditionalInformation('ops_response');
                    if ($opsResponse) {
                        Mage::helper('ops/payment')->applyStateForOrder($order, $opsResponse);
                    } elseif ($this->isAdminSession()) {
                          Mage::helper('ops/payment')->handleUnknownStatus($order);
                    }
                } else {
                    $this->handleFailedCheckout($quote, $order);
                }
            }
            if (true === $this->isCheckOutWithExistingTxId($quote->getPayment()->getMethodInstance()->getCode())) {
                $order->getPayment()->setAdditionalInformation('paymentId', $quote->getPayment()->getOpsPayId())->save();
            }
        }
    }

    public function salesModelServiceQuoteSubmitFailure($observer)
    {
        $quote = $observer->getQuote();
        if (true === $this->isCheckoutWithAliasOrDd($quote->getPayment()->getMethodInstance()->getCode())) {
            $this->handleFailedCheckout(
                $observer->getQuote(),
                $observer->getOrder()
            );
        }
    }

    public function handleFailedCheckout($quote)
    {
        if (true === $this->isInlinePayment($quote->getPayment())) {
            $opsResponse = $quote->getPayment()->getAdditionalInformation('ops_response');
            if ($opsResponse) {
                $this->getHelper()->log('Cancel Ogone Payment because Order Save Process failed.');
                $amount = $this->getHelper('payment')->getBaseGrandTotalFromSalesObject($quote);
                //Try to cancel order only if the payment was ok
                if (Mage::helper('ops/payment')->isPaymentAccepted($opsResponse['STATUS'])) {
                    if (true === $this->getHelper('payment')->isPaymentAuthorizeType($opsResponse['STATUS'])) { 
                        //do a void
                        $params = array (
                            'OPERATION' => Netresearch_OPS_Model_Payment_Abstract::OPS_DELETE_AUTHORIZE_AND_CLOSE,
                            'ORDERID'   => Mage::getSingleton('ops/config')->getConfigData('devprefix').$quote->getId(),
                            'AMOUNT'    => $this->getHelper()->getAmount($amount)
                        );
                    }

                    if (true === $this->getHelper('payment')->isPaymentCaptureType($opsResponse['STATUS'])) { 
                        //do a refund
                        $params = array (
                            'OPERATION' => Netresearch_OPS_Model_Payment_Abstract::OPS_REFUND_FULL,
                            'ORDERID'   => Mage::getSingleton('ops/config')->getConfigData('devprefix').$quote->getId(),
                            'AMOUNT'    => $this->getHelper()->getAmount($amount)
                        );
                    }
                    $url = Mage::getModel('ops/config')->getDirectLinkGatewayPath($quote->getStoreId());
                    Mage::getSingleton('ops/api_directlink')->performRequest($params, $url, $quote->getStoreId());
                }
            }
        }
    }

    protected function getQuoteCurrency($quote)
    {
        if ($quote->hasForcedCurrency()) {
            return $quote->getForcedCurrency()->getCode();
        } else {
            return Mage::app()->getStore($quote->getStoreId())->getBaseCurrencyCode();
        }
    }

    public function confirmAliasPayment($order, $quote)
    {
        $requestParams = Mage::helper('ops/creditcard')->getDirectLinkRequestParams($quote, $order);
        Mage::helper('ops/alias')->cleanUpAdditionalInformation($quote->getPayment(), true);
        return $this->performDirectLinkRequest($quote, $requestParams, $quote->getStoreId());
        
    }

    public function confirmDdPayment($order, $quote)
    {
        /** @var Netresearch_OPS_Helper_DirectDebit $directDebitHelper */
        $directDebitHelper = Mage::helper('ops/directDebit');
        $requestParams = Mage::app()->getRequest()->getParam('ops_directDebit');
        $directDebitHelper->handleAdminPayment($quote, $requestParams);
        $requestParams = $directDebitHelper->getDirectLinkRequestParams($quote, $order, $requestParams);
        return $this->performDirectLinkRequest($quote, $requestParams, $quote->getStoreId());
    }

    public function performDirectLinkRequest($quote, $params, $storeId = null)
    {
        $url = Mage::getModel('ops/config')->getDirectLinkGatewayOrderPath($storeId);
        $response = Mage::getSingleton('ops/api_directlink')->performRequest($params, $url, $storeId);
        /**
         * allow null as valid state for creating the order with status 'pending'
         */
        if (!is_null($response['STATUS']) 
            && Mage::helper('ops/payment')->isPaymentFailed($response['STATUS'])
           ) {
            throw new Mage_Core_Exception($this->getHelper()->__('Ogone Payment failed'));
        }
        $quote->getPayment()->setAdditionalInformation('ops_response', $response)->save();
        
    }

    /**
     * Check if checkout was made with OPS CreditCart or DirectDebit
     *
     * @deprecated
     * @return boolean
     */
    protected function isCheckoutWithAliasOrDd($code)
    {
        if ('ops_cc' == $code || 'ops_directDebit' == $code || 'ops_alias' == $code)
            return true;
        else
            return false;
    }

    /**
     * checks if the selected payment supports inline mode
     *
     * @param $payment - the payment to check
     *
     * @return - true if it's support inline mode, false otherwise
     */
    protected function isInlinePayment($payment)
    {
        $result = false;
        $code = $payment->getMethodInstance()->getCode();
        if (($code == 'ops_cc'
                && $payment->getMethodInstance()
                    ->hasBrandAliasInterfaceSupport(
                        $payment
                    )
            )
            || $code == 'ops_directDebit'
        ) {
            $result = true;
        }
        return $result;
    }
    
    /**
     * Check if checkout was made with OPS CreditCart or DirectDebit
     *
     * @return boolean
     */
    protected function isCheckoutWithExistingTxId($code)
    {
        if ('ops_opsid' == $code)
            return true;
        else
            return false;
    }

    /**
     * get payment operation code
     *
     * @param Mage_Sales_Model_Order $order
     *
     * @return string
     */
    public function _getPaymentAction($order)
    {
        $operation = Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZE_ACTION;

        // different capture operation name for direct debits
        if ('Direct Debits DE' == $order->getPayment()->getAdditionalInformation('PM')
            || 'Direct Debits AT' == $order->getPayment()->getAdditionalInformation('PM')
        ) {
            if ('authorize_capture' == Mage::getModel('ops/config')->getPaymentAction($order->getStoreId())) {
                return Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZE_CAPTURE_ACTION;
            }
            return Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZE_ACTION;
        }
        // no RES for Direct Debits NL, so we'll do the final sale
        if ('Direct Debits NL' == $order->getPayment()->getAdditionalInformation('PM')) {
            if ('authorize_capture' == Mage::getModel('ops/config')->getPaymentAction($order->getStoreId())) {
                return Netresearch_OPS_Model_Payment_Abstract::OPS_CAPTURE_DIRECTDEBIT_NL;
            }
            return Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZE_ACTION;
        }

        if ('authorize_capture' == Mage::getModel('ops/config')->getPaymentAction($order->getStoreId())) {
            $operation = Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZE_CAPTURE_ACTION;
        }

        return $operation;
    }

    public function checkoutTypeOnepageSavePaymentAfter()
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $payment = $quote->getPayment();
        $paymentParams = Mage::app()->getRequest()->getParam('payment');

        $alias = null;
        if (array_key_exists('ops_alias', $paymentParams)) {
            $alias = $paymentParams['ops_alias'];
        }
        if (false == is_null($alias)) {
            $payment->setAdditionalInformation('alias', $alias);
        }
        $payment->save();
    }
    
    /**
     * Replace order cancel comfirm message of Magento by a custom message from Ogone
     * 
     * @param Varien_Event_Observer $observer
     * @return Netresearch_OPS_Model_Observer
     */
    public function updateOrderCancelButton(Varien_Event_Observer $observer)
    {
        /* @var $block Mage_Adminhtml_Block_Template */
        $block = $observer->getEvent()->getBlock();
        
        //Stop if block is not sales order view
        if ($block->getType() != 'adminhtml/sales_order_view') {
            return $this;
        }
        
        //If payment method is one of the Ogone-ones and order can be cancelled manually
        if ($block->getOrder()->getPayment()->getMethodInstance() instanceof Netresearch_OPS_Model_Payment_Abstract
            && true === $block->getOrder()->getPayment()->getMethodInstance()->canCancelManually($block->getOrder())) {
            //Build message and update cancel button
            $message = Mage::helper('ops')->__(
                "Are you sure you want to cancel this order? Warning: Please check the payment status in the back-office of Ogone before. By cancelling this order you won\\'t be able to update the status in Magento anymore."
            );
            $block->updateButton(
                'order_cancel',
                'onclick',
                'deleteConfirm(\''.$message.'\', \'' . $block->getCancelUrl() . '\')'
            );
        }
        return $this;
    }

    /**
     *
     * appends a checkbox for closing the transaction if it's a Ogone payment
     * 
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function appendCheckBoxToRefundForm($observer)
    {
        $html = '';
        /*
         * show the checkbox only if the credit memo create page is displayed and
         * the refund can be done online and the payment is done via Ogone
         */
        if ($observer->getBlock() instanceof Mage_Adminhtml_Block_Sales_Order_Creditmemo_Totals
            && $observer->getBlock()->getParentBlock() 
                instanceof Mage_Adminhtml_Block_Sales_Order_Creditmemo_Create_Items
            && $observer->getBlock()->getParentBlock()->getCreditmemo()->getOrder()->getPayment()
            && $observer->getBlock()->getParentBlock()->getCreditmemo()->getOrder()->getPayment()->getMethodInstance()
                instanceof Netresearch_OPS_Model_Payment_Abstract
            && $observer->getBlock()->getParentBlock()->getCreditmemo()->canRefund()
            && $observer->getBlock()->getParentBlock()->getCreditmemo()->getInvoice()
            && $observer->getBlock()->getParentBlock()->getCreditmemo()->getInvoice()->getTransactionId()
        ) {
            $transport = $observer->getTransport();
            $block     = $observer->getBlock();
            $layout    = $block->getLayout();
            $html      = $transport->getHtml();
            $checkBoxHtml = $layout->createBlock(
                'ops/adminhtml_sales_order_creditmemo_totals_checkbox', 
                'ops_refund_checkbox'
            )
                ->setTemplate('ops/sales/order/creditmemo/totals/checkbox.phtml')
                ->renderView();
            $html = $html . $checkBoxHtml;
            $transport->setHtml($html);
        }
        return $html;
    }

    /**
     *
     * fetch the creation of credit memo event and display warning message when
     * - credit memo could be done online
     * - payment is a Ogone payment
     * - Ogone transaction is closed
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function showWarningForClosedTransactions($observer)
    {
        $html = '';
        /**
         * - credit memo could be done online
         * - payment is a Ogone payment
         * - Ogone transaction is closed
         */
        if ($observer->getBlock() instanceof Mage_Adminhtml_Block_Sales_Order_Creditmemo_Create
            && $observer->getBlock()->getCreditmemo()->getOrder()->getPayment()
            && $observer->getBlock()->getCreditmemo()->getOrder()->getPayment()->getMethodInstance()
                instanceof Netresearch_OPS_Model_Payment_Abstract
            && $observer->getBlock()->getCreditmemo()->getInvoice()
            && $observer->getBlock()->getCreditmemo()->getInvoice()->getTransactionId()
            && false === $observer->getBlock()->getCreditmemo()->canRefund()
        ) {
            $transport = $observer->getTransport();
            $block     = $observer->getBlock();
            $layout    = $block->getLayout();
            $html      = $transport->getHtml();
            $warningHtml = $layout->createBlock(
                'ops/adminhtml_sales_order_creditmemo_closedTransaction_warning', 
                'ops_closed-transaction-warning'
            )
                ->renderView();
            $html      = $warningHtml . $html;
            $transport->setHtml($html);
        }
        return $html;
    }

    
    /**
     * triggered by cron for deleting old payment data from the additional payment information
     * @param $observer
     */
    public function cleanUpOldPaymentData($observer)
    {
        Mage::helper('ops/quote')->cleanUpOldPaymentInformation();
    }
}

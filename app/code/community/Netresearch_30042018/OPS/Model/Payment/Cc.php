<?php
/**
 * Netresearch_OPS_Model_Payment_Cc
 *
 * @package
 * @copyright 2011 Netresearch
 * @author    Thomas Kappel <thomas.kappel@netresearch.de>
 * @license   OSL 3.0
 */
class Netresearch_OPS_Model_Payment_Cc
    extends Netresearch_OPS_Model_Payment_Abstract
{
    /** Check if we can capture directly from the backend */
    protected $_canBackendDirectCapture = true;

    /** @var bool $_canUseInternal capture directly from the backend */
    protected $_canUseInternal = true;

    /** info source path */
    protected $_infoBlockType = 'ops/info_cc';

    /** @var string $_formBlockType define a specific form block */
    protected $_formBlockType = 'ops/form_cc';

    /** payment code */
    protected $_code = 'ops_cc';

    /** ops payment code */
    public function getOpsCode($payment=null) {
        $opsBrand = $this->getOpsBrand($payment);
        if ('PostFinance card' == $opsBrand) {
            return 'PostFinance Card';
        }
        if ('UNEUROCOM' == $this->getOpsBrand($payment)) {
            return 'UNEUROCOM';
        }
        return 'CreditCard';
    }

    public function getOpsBrand($payment=null) {
        if (is_null($payment)) {
            $payment = Mage::getSingleton('checkout/session')->getQuote()->getPayment();
        }
        return $payment->getAdditionalInformation('CC_BRAND');
    }

    public function getOrderPlaceRedirectUrl($payment=null)
    {
        if ($this->hasBrandAliasInterfaceSupport($payment)) {
            if ('' == $this->getOpsHtmlAnswer($payment))
                return false; // Prevent redirect on cc payment
            else
                return Mage::getModel('ops/config')->get3dSecureRedirectUrl();
        }
        return parent::getOrderPlaceRedirectUrl();
    }

    /**
     * only some brands are supported to be integrated into onepage checkout
     *
     * @return array
     */
    public function getBrandsForAliasInterface()
    {
        $brands = Mage::getModel('ops/config')->getInlinePaymentCcTypes();
        return $brands;
    }

    /**
     * if cc brand supports ops alias interface
     *
     * @param Mage_Payment_Model_Info $payment
     *
     * @return void
     */
    public function hasBrandAliasInterfaceSupport($payment=null)
    {
        return in_array(
            $this->getOpsBrand($payment),
            $this->getBrandsForAliasInterface()
        );
    }
}


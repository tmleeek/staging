<?php
/**
 * Netresearch_OPS_Model_Payment_DirectEbanking
 *
 * @package
 * @copyright 2011 Netresearch
 * @author    Thomas Kappel <thomas.kappel@netresearch.de>
 * @license   OSL 3.0
 */
class Netresearch_OPS_Model_Payment_DirectEbanking
    extends Netresearch_OPS_Model_Payment_Abstract
{
    /** Check if we can capture directly from the backend */
    protected $_canBackendDirectCapture = true;

    /** info source path */
    protected $_infoBlockType = 'ops/info_redirect';

    /** payment code */
    protected $_code = 'ops_directEbanking';


     protected function getPayment()
    {
        $checkout = Mage::getSingleton('checkout/session');
        $payment = $checkout->getQuote()->getPayment();
        if (!$payment->getId()) {
            $payment = Mage::getModel('sales/order')->loadByIncrementId($checkout->getLastRealOrderId())->getPayment();
        }
        return $payment;
    }

    /** ops payment code */
    public function getOpsCode() {
        return trim($this->getPayment()->getAdditionalInformation('PM'));
    }

    public function getOpsBrand($payment=null) {
        return trim($this->getPayment()->getAdditionalInformation('BRAND'));
    }

    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  Mage_Payment_Model_Info
     */
    public function assignData($data)
    {
        $brand = '';
        if (is_object($data) && $data instanceof Varien_Object) {
            $brand = $data['directEbanking_brand'];
        } elseif (is_array($data) && isset($data['directEbanking_brand'])) {
            $brand = $data['directEbanking_brand'];
        }
        $pm = $this->getPmForBrand($brand);
        $payment = Mage::getSingleton('checkout/session')->getQuote()->getPayment();
        $payment->setAdditionalInformation('PM',    $pm);
        $payment->setAdditionalInformation('BRAND', $brand);

        parent::assignData($data);
        return $this;
    }

    /**
     * get PM value for given brand
     *
     * pm == brand, except for brand "Sofort Uberweisung"
     *
     * @param mixed $pm 
     * @return void
     */
    protected function getPmForBrand($brand)
    {
        if ('Sofort Uberweisung' === $brand) {
            return 'DirectEbanking';
        }
        return $brand;
    }
}


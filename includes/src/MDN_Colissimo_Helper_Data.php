<?php
class MDN_Colissimo_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function redirectReferrer(){
        Mage::app()->getResponse()->setRedirect($_SERVER['HTTP_REFERER']);
         Mage::app()->getResponse()->sendResponse();
         exit;
    }

    public function checkPhone($ret,$phone){

        if(!empty($phone)){
            if(preg_match('/^06|07|\+336|\+337/',$phone)){
                $ret['addressVO']['MobileNumber'] = $this->cleanPhone($phone);
            }else{
                $ret['addressVO']['phone'] = $this->cleanPhone($phone);
            }
        }
        return $ret;
    }

    public function cleanSpaces($phone){
        return str_replace(' ', '',$phone);
    }

    public function cleanPhone($phone){
        $ret = str_replace('+33', '0',$phone);
        return preg_replace("/[^0-9]/", "",$ret);
    }

    public function supervision(){
        $url = Mage::getStoreConfig('colissimo/account_shipment/supervision');
        if(!empty($url)){
            $supervision = file_get_contents($url);
            return preg_match('/\[OK\]/',$supervision) ? true : false;
        }else{
            Mage::throwException('The supervision URL is not set.');
        }
    }

    /**
     * Returns Magento version for compatibility issues
     * @author Arnaud P <arnaud@boostmyshop.com>
     * @version 1.1.0
     */

    public function getMagentoVersion()
    {
        return Mage::getVersion();
    }
}
<?php

/**
 * Currency rate import model (From Google)
 * This is a bit of a hacky solution, but it works (for now)
 * Full credit to http://www.magentocommerce.com/boards/viewthread/335191/ and http://stackoverflow.com/a/19786423
 *
 * @category    Mage
 * @package     Mage_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Tatva_Directory_Model_Currency_Import_Webservicex extends Mage_Directory_Model_Currency_Import_Webservicex {
    //protected $_url = 'https://finance.google.com/finance/converter?a=1&from={{CURRENCY_FROM}}&to={{CURRENCY_TO}}';
    protected $_url = 'http://www.xe.com/currencyconverter/convert/?Amount=1&From={{CURRENCY_FROM}}&To={{CURRENCY_TO}}';
    protected $_messages = array();

    /**
     * HTTP client
     *
     * @var Varien_Http_Client
     */
    protected $_httpClient;

    public function __construct()
    {
        $this->_httpClient = new Varien_Http_Client();
    }

    protected function _convert($currencyFrom, $currencyTo, $retry = 0)
    {
        $url = str_replace('{{CURRENCY_FROM}}', $currencyFrom, $this->_url);
        $url = str_replace('{{CURRENCY_TO}}', $currencyTo, $url);

        try
        {
            $response = $this->_httpClient
                ->setUri($url)
                ->setConfig(array('timeout' => Mage::getStoreConfig('currency/webservicex/timeout')))
                ->request('GET')
                ->getBody();

            $data = explode('uccResultAmount', $response);
            @$data = explode('uccToCurrencyCode', $data[1]);
            $return = preg_replace('/[^0-9,.]/', '', $data[0]);

            /*$get    = explode("<span class=bld>", $response);
            $get    = explode("</span>", $get[1]);
            $return = preg_replace("/[^0-9\.]/", null, $get[0]);*/

            if(!is_numeric($return))
            {
                throw new Exception('Return value is not numeric');
            }

            return $return;
        }
        catch (Exception $e)
        {
            if($retry == 0)
            {
                $this->_convert($currencyFrom, $currencyTo, 1);
            }
            else
            {
                $this->_messages[] = Mage::helper('directory')->__('Cannot retrieve rate from %s.', $url);
            }
        }
    }
}
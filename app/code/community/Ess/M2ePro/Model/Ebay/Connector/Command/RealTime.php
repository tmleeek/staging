<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

abstract class Ess_M2ePro_Model_Ebay_Connector_Command_RealTime extends Ess_M2ePro_Model_Connector_Command_RealTime
{
    /**
     * @var Ess_M2ePro_Model_Marketplace|null
     */
    protected $marketplace = NULL;

    /**
     * @var Ess_M2ePro_Model_Account|null
     */
    protected $account = NULL;

    // ########################################

    public function __construct(array $params = array(),
                                Ess_M2ePro_Model_Marketplace $marketplace = NULL,
                                Ess_M2ePro_Model_Account $account = NULL)
    {
        $this->marketplace = $marketplace;
        $this->account = $account;

        parent::__construct($params);
    }

    // ########################################

    protected function buildRequestInstance()
    {
        $request = parent::buildRequestInstance();

        $requestData = $request->getData();

        if (!is_null($this->marketplace)) {
            $requestData['marketplace'] = $this->marketplace->getNativeId();
        }
        if (!is_null($this->account)) {
            $requestData['account'] = $this->account->getChildObject()->getServerHash();
        }

        $request->setData($requestData);

        return $request;
    }

    // ########################################

    public static function ebayTimeToString($time)
    {
        return (string)self::getEbayDateTimeObject($time)->format('Y-m-d H:i:s');
    }

    public static function ebayTimeToTimeStamp($time)
    {
        return (int)self::getEbayDateTimeObject($time)->format('U');
    }

    // -----------------------------------------

    private static function getEbayDateTimeObject($time)
    {
        $dateTime = NULL;

        if ($time instanceof DateTime) {
            $dateTime = clone $time;
            $dateTime->setTimezone(new DateTimeZone('UTC'));
        } else {
            is_int($time) && $time = '@'.$time;
            $dateTime = new DateTime($time, new DateTimeZone('UTC'));
        }

        if (is_null($dateTime)) {
            throw new Ess_M2ePro_Model_Exception('eBay DateTime object is null');
        }

        return $dateTime;
    }

    // ########################################
}
<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Mysql4_Amazon_Processing_Action_Collection
    extends Ess_M2ePro_Model_Mysql4_Collection_Abstract
{
    // ########################################

    public function _construct()
    {
        parent::_construct();
        $this->_init('M2ePro/Amazon_Processing_Action');
    }

    // ########################################

    /**
     * @param Ess_M2ePro_Model_Account[] $accounts
     * @return $this
     */
    public function setAccountsFilter(array $accounts)
    {
        $accountIds = array();
        foreach ($accounts as $account) {
            $accountIds[] = $account->getId();
        }

        $this->addFieldToFilter('account_id', array('in' => $accountIds));

        return $this;
    }

    public function setRequestPendingSingleIdFilter($requestPendingSingleIds)
    {
        if (!is_array($requestPendingSingleIds)) {
            $requestPendingSingleIds = array($requestPendingSingleIds);
        }

        $this->addFieldToFilter('request_pending_single_id', array('in' => $requestPendingSingleIds));
        return $this;
    }

    public function setNotProcessedFilter()
    {
        $this->addFieldToFilter('request_pending_single_id', array('null' => true));
        return $this;
    }

    public function setInProgressFilter()
    {
        $this->addFieldToFilter('request_pending_single_id', array('notnull' => true));
        return $this;
    }

    public function setStartedBeforeFilter($minutes)
    {
        $dateTime = new DateTime('now', new DateTimeZone('UTC'));
        $dateTime->modify('- '.(int)$minutes.' minutes');

        $this->addFieldToFilter('start_date', array('lt' => $dateTime->format('Y-m-d H:i:s')));

        return $this;
    }

    // ########################################
}
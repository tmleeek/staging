<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Mysql4_Request_Pending_Partial_Collection
    extends Ess_M2ePro_Model_Mysql4_Collection_Abstract
{
    // ########################################

    public function _construct()
    {
        parent::_construct();
        $this->_init('M2ePro/Request_Pending_Partial');
    }

    // ########################################

    public function setOnlyExpiredItemsFilter()
    {
        $this->addFieldToFilter('expiration_date', array('lt' => Mage::helper('M2ePro')->getCurrentGmtDate()));
        return $this;
    }

    public function setOnlyOutdatedItemsFilter()
    {
        $this->getSelect()->where(new Zend_Db_Expr('DATE_ADD(`expiration_date`, INTERVAL 12 HOUR) < NOW()'));
        return $this;
    }

    // ########################################
}
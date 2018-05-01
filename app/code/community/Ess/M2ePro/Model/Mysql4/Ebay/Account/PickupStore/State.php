<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Mysql4_Ebay_Account_PickupStore_State
    extends Ess_M2ePro_Model_Mysql4_Abstract
{
    //########################################

    public function _construct()
    {
        $this->_init('M2ePro/Ebay_Account_PickupStore_State', 'id');
    }

    //########################################

    public function markAsInProcessing(array $itemIds)
    {
        $this->_getWriteAdapter()->update(
            $this->getMainTable(),
            array(
                'is_in_processing' => 1,
            ),
            array('id IN (?)' => $itemIds)
        );
    }

    public function unmarkAsInProcessing(array $itemIds)
    {
        $this->_getWriteAdapter()->update(
            $this->getMainTable(),
            array(
                'is_in_processing' => 0,
            ),
            array('id IN (?)' => $itemIds)
        );
    }

    //########################################
}
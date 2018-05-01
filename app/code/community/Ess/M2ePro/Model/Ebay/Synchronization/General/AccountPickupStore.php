<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

final class Ess_M2ePro_Model_Ebay_Synchronization_General_AccountPickupStore
    extends Ess_M2ePro_Model_Ebay_Synchronization_General_Abstract
{
    //########################################

    /**
     * @return null
     */
    protected function getNick()
    {
        return NULL;
    }

    // ---------------------------------------

    /**
     * @return int
     */
    protected function getPercentsStart()
    {
        return 60;
    }

    /**
     * @return int
     */
    protected function getPercentsEnd()
    {
        return 100;
    }

    //########################################

    public function performActions()
    {
        $result = true;

        $result = !$this->processTask('AccountPickupStore_Process') ? false : $result;
        $result = !$this->processTask('AccountPickupStore_Update') ? false : $result;

        return $result;
    }

    //########################################
}
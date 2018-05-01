<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

abstract class Ess_M2ePro_Model_Ebay_Synchronization_General_Feedbacks_Abstract
    extends Ess_M2ePro_Model_Ebay_Synchronization_General_Abstract
{
    //########################################

    protected function processTask($taskPath)
    {
        return parent::processTask('Feedbacks_'.$taskPath);
    }

    protected function intervalIsLocked()
    {
        if ($this->getInitiator() == Ess_M2ePro_Helper_Data::INITIATOR_USER ||
            $this->getInitiator() == Ess_M2ePro_Helper_Data::INITIATOR_DEVELOPER) {
            return false;
        }

        return parent::intervalIsLocked();
    }

    //########################################
}
<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

final class Ess_M2ePro_Model_Cron_Task_EbayActions extends Ess_M2ePro_Model_Cron_Task_Abstract
{
    const NICK = 'ebay_actions';
    const MAX_MEMORY_LIMIT = 512;

    //####################################

    protected function getNick()
    {
        return self::NICK;
    }

    protected function getMaxMemoryLimit()
    {
        return self::MAX_MEMORY_LIMIT;
    }

    //####################################

    protected function performActions()
    {
        $actionsProcessor = Mage::getModel('M2ePro/Ebay_Actions_Processor');
        $actionsProcessor->setLockItem($this->getLockItem());
        $actionsProcessor->process();
    }

    //####################################
}
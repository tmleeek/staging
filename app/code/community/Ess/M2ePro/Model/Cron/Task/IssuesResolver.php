<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

final class Ess_M2ePro_Model_Cron_Task_IssuesResolver extends Ess_M2ePro_Model_Cron_Task_Abstract
{
    const NICK = 'issues_resolver';
    const MAX_MEMORY_LIMIT = 512;

    //########################################

    protected function getNick()
    {
        return self::NICK;
    }

    protected function getMaxMemoryLimit()
    {
        return self::MAX_MEMORY_LIMIT;
    }

    //########################################

    protected function performActions()
    {
        $this->removeMissedProcessingLocks();
    }

    //########################################

    private function removeMissedProcessingLocks()
    {
        /** @var Ess_M2ePro_Model_Mysql4_Processing_Lock_Collection $collection */
        $collection = Mage::getResourceModel('M2ePro/Processing_Lock_Collection');
        $collection->getSelect()->joinLeft(
            array('p' => Mage::getResourceModel('M2ePro/Processing')->getMainTable()),
            'p.id = main_table.processing_id',
            array()
        );
        $collection->addFieldToFilter('p.id', array('null' => true));

        $logData = array();
        foreach ($collection->getItems() as $item) {
            /**@var Ess_M2ePro_Model_Processing_Lock $item */

            if (!isset($logData[$item->getModelName()][$item->getObjectId()]) ||
                !in_array($item->getTag(), $logData[$item->getModelName()][$item->getObjectId()]))
            {
                $logData[$item->getModelName()][$item->getObjectId()][] = $item->getTag();
            }

            $item->deleteInstance();
        }

        if (!empty($logData)) {
            Mage::helper('M2ePro/Module_Logger')->process(
                $logData, 'Processing Locks Records were broken and removed', false
            );
        }
    }

    //########################################
}
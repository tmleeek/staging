<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

final class Ess_M2ePro_Model_Synchronization_Global_MagentoProducts
    extends Ess_M2ePro_Model_Synchronization_Global_Abstract
{
    //########################################

    /**
     * @return string
     */
    protected function getType()
    {
        return Ess_M2ePro_Model_Synchronization_Task_Global_Abstract::MAGENTO_PRODUCTS;
    }

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
        return 90;
    }

    //########################################

    protected function performActions()
    {
        $result = true;

        $result = !$this->processTask('MagentoProducts_DeletedProducts') ? false : $result;
        $result = !$this->processTask('MagentoProducts_AddedProducts') ? false : $result;
        $result = !$this->processTask('MagentoProducts_Inspector') ? false : $result;

        return $result;
    }

    //########################################
}
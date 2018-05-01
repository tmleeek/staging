<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

final class Ess_M2ePro_Model_Cron_Task_UpdateEbayAccountsPreferences
    extends Ess_M2ePro_Model_Cron_Task_Abstract
{
    const NICK = 'update_ebay_accounts_preferences';
    const MAX_MEMORY_LIMIT = 128;

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

    public function performActions()
    {
        /** @var Ess_M2ePro_Model_Mysql4_Account_Collection $accountCollection */
        $accountCollection = Mage::helper('M2ePro/Component_Ebay')->getCollection('Account');

        /** @var Ess_M2ePro_Model_Account[] $accounts */
        $accounts = $accountCollection->getItems();

        foreach ($accounts as $account) {
            $account->getChildObject()->updateUserPreferences();
        }
    }

    //########################################
}
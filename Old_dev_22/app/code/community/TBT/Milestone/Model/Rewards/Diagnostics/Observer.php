<?php

class TBT_Milestone_Model_Rewards_Diagnostics_Observer extends Varien_Object
{
    const CORE_RESOURCE_CODE = 'tbtmilestone_setup';

    /**
     * Observes 'controller_action_predispatch_rewardsadmin_manage_diagnostics_reinstalldb' which is dispatched when
     * admin clicks 'Re-Install DB' and it will delete 'core_resource' entry for Milestone module, hence forcing
     * re-installation of database scripts for this module.
     * @param  Varien_Event $observer
     * @return this
     */
    public function reinstalldb($observer)
    {
        $code = self::CORE_RESOURCE_CODE;
        echo "<br>Deleting core_resource table entry with code '{$code}'...";
        flush();
        $conn = Mage::getSingleton('core/resource')->getConnection('core_write');
        $conn->beginTransaction();
        $this->_clearDbInstallMemory($conn, $code);
        echo "Done<br>";
        flush();

        $conn->commit();

        return $this;
    }

    public function _clearDbInstallMemory($conn, $code)
    {
        $table_prefix = Mage::getConfig()->getTablePrefix();
        $conn->query("DELETE FROM `{$table_prefix}core_resource` WHERE `code` = '{$code}';");

        return $this;
    }

}

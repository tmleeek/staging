<?php

class TBT_Rewards_Model_Mysql4_Metrics extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {}

    /**
     * Set main table and idField
     *
     * @param string $table
     * @param string $field
     * @return TBT_Rewards_Model_Mysql4_Metrics
     */
    public function init($table, $field = 'id')
    {
        $this->_init($table, $field);
        return $this;
    }
}

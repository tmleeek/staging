<?php

abstract class TBT_Rewards_Model_Mysql4_Abstract extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Now supports loading by two fields (such as any two fields that, together, are unique).
     * @see Mage_Core_Model_Resource_Db_Abstract::_getLoadSelect()
     */
    protected function _getLoadSelect($fields, $values, $object)
    {
        if (!is_array($fields)) {
            $fields = array($fields);
        }

        if (!is_array($values)) {
            $values = array($values);
        }

        $select = $this->_getReadAdapter()->select()
        ->from($this->getMainTable());

        for ($i = 0; $i < count($fields); $i++) {
            $field = $fields[$i];
            $value = $values[$i];

            $field  = $this->_getReadAdapter()->quoteIdentifier(sprintf('%s.%s', $this->getMainTable(), $field));
            $select->where($field . '=?', $value);
        }

        return $select;
    }
}

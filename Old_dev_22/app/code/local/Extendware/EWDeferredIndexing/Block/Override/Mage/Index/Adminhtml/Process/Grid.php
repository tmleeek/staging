<?php
class Extendware_EWDeferredIndexing_Block_Override_Mage_Index_Adminhtml_Process_Grid extends Extendware_EWDeferredIndexing_Block_Override_Mage_Index_Adminhtml_Process_Grid_Bridge
{
	public function decorateStatus($value, $row, $column, $isExport)
    {
        $class = '';
        switch ($row->getStatus()) {
            case Mage_Index_Model_Process::STATUS_PENDING :
                $class = 'grid-severity-notice';
                break;
            case Mage_Index_Model_Process::STATUS_RUNNING :
            case Mage_Index_Model_Process::STATUS_DEFERRED :
                $class = 'grid-severity-major';
                break;
            case Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX :
                $class = 'grid-severity-critical';
                break;
        }
        return '<span class="'.$class.'"><span>'.$value.'</span></span>';
    }
}

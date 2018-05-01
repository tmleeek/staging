<?php

class Extendware_EWCore_Block_Adminhtml_Cache_Additional_Cache extends Extendware_EWCore_Block_Mage_Adminhtml_Template
{
    public function getFlushCacheUrl()
    {
        return $this->getUrl('extendware_ewcore/adminhtml_cache/flush');
    }
}

<?php
class Extendware_EWDeferredIndexing_Model_Override_Mage_Index_Process extends Extendware_EWDeferredIndexing_Model_Override_Mage_Index_Process_Bridge
{
	const STATUS_DEFERRED    = 'deferred';

	public function getStatusesOptions()
    {
        return array(
            self::STATUS_PENDING            => Mage::helper('index')->__('Ready'),
            self::STATUS_RUNNING            => Mage::helper('index')->__('Processing'),
            self::STATUS_REQUIRE_REINDEX    => Mage::helper('index')->__('Reindex Required'),
        	self::STATUS_DEFERRED    		=> Mage::helper('index')->__('Deferred / Queued'),
        );
    }

	public function reindexAll($force = false) {
		if (Mage::helper('ewdeferredindexing/config')->isFullIndexingDeferrable() === true and $force === false) {
			$this->changeStatus(self::STATUS_DEFERRED);
		} else {
			parent::reindexAll();
		}
	}

	public function reindexEverything($force = false) {
		parent::reindexEverything();
		$ignoredIndexes = Mage::helper('ewdeferredindexing/config')->getIgnoredIndexes();
        if (in_array($this->getIndexerCode(), $ignoredIndexes) === true) {
        	$force = true;
        }
		if ($force === true) return $this->reindexAll(true);
	}

	public function updateProcessData(array $data) {
		$resource = Mage::getSingleton('core/resource');
        $adapter  = $resource->getConnection('index_write');

		$adapter->update(
            $this->_getResource()->getMainTable(),
            $data,
            $adapter->quoteInto('process_id=?', $this->getId())
        );
		
		return $this;
	}
    public function processEvent(Mage_Index_Model_Event $event, $force = false)
    {
        if (!$this->matchEvent($event)) {
            return $this;
        }
        
        if ($force === false and $this->getMode() == self::MODE_MANUAL) {
            $this->changeStatus(self::STATUS_REQUIRE_REINDEX);
            return $this;
        }
        
		$this->updateProcessData(array('started_at' => $this->_getResource()->formatDate(time())));
        $this->_setEventNamespace($event);
        $isError = false;

        try {
			$this->getIndexer()->processEvent($event);
        } catch (Exception $e) {
            $isError = true;
        }
        $event->resetData();
        $this->_resetEventNamespace($event);
        $this->updateProcessData(array('ended_at' => $this->_getResource()->formatDate(time())));
        $event->addProcessId($this->getId(), $isError ? self::EVENT_STATUS_ERROR : self::EVENT_STATUS_DONE);

        return $this;
    }
}

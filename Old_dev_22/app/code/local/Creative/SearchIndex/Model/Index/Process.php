<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Index
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Enter description here ...
 *
 * @method Mage_Index_Model_Resource_Process _getResource()
 * @method Mage_Index_Model_Resource_Process getResource()
 * @method string getIndexerCode()
 * @method Mage_Index_Model_Process setIndexerCode(string $value)
 * @method string getStatus()
 * @method Mage_Index_Model_Process setStatus(string $value)
 * @method string getStartedAt()
 * @method Mage_Index_Model_Process setStartedAt(string $value)
 * @method string getEndedAt()
 * @method Mage_Index_Model_Process setEndedAt(string $value)
 * @method string getMode()
 * @method Mage_Index_Model_Process setMode(string $value)
 *
 * @category    Mage
 * @package     Mage_Index
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Creative_SearchIndex_Model_Index_Process extends Mage_Index_Model_Process
{
    /**
     * Reindex all data what this process responsible is
     *
     */
    public function reindexAll()
    {
        if ($this->isLocked()) {
            Mage::throwException(Mage::helper('index')->__('%s Index process is working now. Please try run this process later.', $this->getIndexer()->getName()));
        }
             //echo $this->getIndexerCode();
             //exit;
        if($this->getIndexerCode() != "catalogsearch_fulltext")
        {
          //echo "Adasd";
          //exit;
            $processStatus = $this->getStatus();

            $this->_getResource()->startProcess($this);
            $this->lock();
            try
            {
                $eventsCollection = $this->getUnprocessedEventsCollection();

                /** @var $eventResource Mage_Index_Model_Resource_Event */
                $eventResource = Mage::getResourceSingleton('index/event');

                if ($eventsCollection->count() > 0 && $processStatus == self::STATUS_PENDING || $this->getForcePartialReindex())
                {
                    $this->_getResource()->beginTransaction();
                    try
                    {
                        $this->_processEventsCollection($eventsCollection, false);
                        $this->_getResource()->commit();
                    }
                    catch (Exception $e)
                    {
                        $this->_getResource()->rollBack();
                        throw $e;
                    }
                }
                else
                {
                    //Update existing events since we'll do reindexAll
                    $eventResource->updateProcessEvents($this);
                    $this->getIndexer()->reindexAll();
                }
                $this->unlock();

                $unprocessedEvents = $eventResource->getUnprocessedEvents($this);
                if ($this->getMode() == self::MODE_MANUAL && (count($unprocessedEvents) > 0))
                {
                    $this->_getResource()->updateStatus($this, self::STATUS_REQUIRE_REINDEX);
                }
                else
                {
                    $this->_getResource()->endProcess($this);
                }
            }
            catch (Exception $e)
            {
                $this->unlock();
                $this->_getResource()->failProcess($this);
                throw $e;
            }
            Mage::dispatchEvent('after_reindex_process_' . $this->getIndexerCode());
            return $this;
        }
        //echo "234434";
          //exit;
    }
}

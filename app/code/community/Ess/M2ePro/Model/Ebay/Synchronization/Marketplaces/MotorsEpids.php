<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

final class Ess_M2ePro_Model_Ebay_Synchronization_Marketplaces_MotorsEpids
    extends Ess_M2ePro_Model_Ebay_Synchronization_Marketplaces_Abstract
{
    /** @var Ess_M2ePro_Model_Marketplace */
    protected $marketplace;

    //########################################

    /**
     * @return string
     */
    protected function getNick()
    {
        return '/motors_epids/';
    }

    /**
     * @return string
     */
    protected function getTitle()
    {
        return 'Parts Compatibility [ePIDs]';
    }

    // ---------------------------------------

    /**
     * @return int
     */
    protected function getPercentsStart()
    {
        return 0;
    }

    /**
     * @return int
     */
    protected function getPercentsEnd()
    {
        return 100;
    }

    //########################################

    protected function isPossibleToRun()
    {
        if (!parent::isPossibleToRun()) {
            return false;
        }

        $params = $this->getParams();

        $marketplace = Mage::helper('M2ePro/Component_Ebay')->getCachedObject(
            'Marketplace', $params['marketplace_id']
        );
        $this->marketplace = $marketplace;

        return $marketplace->getChildObject()->isEpidEnabled();
    }

    protected function performActions()
    {
        $partNumber = 1;
        $this->deleteAllSpecifics();

        for ($i = 0; $i < 100; $i++) {

            $this->getActualLockItem()->setPercents($this->getPercentsStart());

            $this->getActualOperationHistory()->addTimePoint(__METHOD__.'get'.$this->marketplace->getId(),
                                                             'Get ePIDs from eBay');
            $response = $this->receiveFromEbay($partNumber);
            $this->getActualOperationHistory()->saveTimePoint(__METHOD__.'get'.$this->marketplace->getId());

            if (empty($response)) {
                break;
            }

            $this->getActualLockItem()->setStatus(
                'Processing ePIDs data ('.(int)$partNumber.'/'.(int)$response['total_parts'].')'
            );
            $this->getActualLockItem()->setPercents($this->getPercentsStart() + $this->getPercentsInterval()/2);
            $this->getActualLockItem()->activate();

            $this->getActualOperationHistory()->addTimePoint(__METHOD__.'save'.$this->marketplace->getId(),
                                                             'Save ePIDs to DB');
            $this->saveSpecificsToDb($response['data']);
            $this->getActualOperationHistory()->saveTimePoint(__METHOD__.'save'.$this->marketplace->getId());

            $this->getActualLockItem()->setPercents($this->getPercentsEnd());
            $this->getActualLockItem()->activate();

            $partNumber = $response['next_part'];

            if (is_null($partNumber)) {
                break;
            }
        }

        $this->logSuccessfulOperation();
    }

    //########################################

    protected function receiveFromEbay($partNumber)
    {
        $dispatcherObj = Mage::getModel('M2ePro/Ebay_Connector_Dispatcher');
        $connectorObj = $dispatcherObj->getVirtualConnector('marketplace','get','motorsEpids',
                                                            array(
                                                                'marketplace' => $this->marketplace->getNativeId(),
                                                                'part_number' => $partNumber
                                                            ));

        $dispatcherObj->process($connectorObj);
        $response = $connectorObj->getResponseData();

        if (is_null($response) || empty($response['data'])) {
            $response = array();
        }

        $dataCount = isset($response['data']) ? count($response['data']) : 0;
        $this->getActualOperationHistory()->addText("Total received parts from eBay: {$dataCount}");

        return $response;
    }

    protected function deleteAllSpecifics()
    {
        /** @var $connWrite Varien_Db_Adapter_Pdo_Mysql */
        $connWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tableMotorsEpids = Mage::getSingleton('core/resource')
                                        ->getTableName('m2epro_ebay_dictionary_motor_epid');

        $helper = Mage::helper('M2ePro/Component_Ebay_Motors');
        $scope = $helper->getEpidsScopeByType($helper->getEpidsTypeByMarketplace(
            $this->marketplace->getId()
        ));

        $connWrite->delete(
            $tableMotorsEpids,
            array(
                'is_custom = ?' => 0,
                'scope = ?'     => $scope
            )
        );
    }

    protected function saveSpecificsToDb(array $data)
    {
        $totalCountItems = count($data['items']);
        if ($totalCountItems <= 0) {
            return;
        }

        /** @var $connWrite Varien_Db_Adapter_Pdo_Mysql */
        $connWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tableMotorsEpids = Mage::getSingleton('core/resource')
                                        ->getTableName('m2epro_ebay_dictionary_motor_epid');

        $iteration            = 0;
        $iterationsForOneStep = 1000;
        $percentsForOneStep   = ($this->getPercentsInterval()/2) / ($totalCountItems/$iterationsForOneStep);

        $temporaryIds   = array();
        $itemsForInsert = array();

        $helper = Mage::helper('M2ePro/Component_Ebay_Motors');
        $scope = $helper->getEpidsScopeByType($helper->getEpidsTypeByMarketplace(
            $this->marketplace->getId()
        ));

        for ($i = 0; $i < $totalCountItems; $i++) {

            $item = $data['items'][$i];
            $temporaryIds[] = $item['ePID'];

            $itemsForInsert[] = array(
                'epid'         => $item['ePID'],
                'product_type' => (int)$item['product_type'],
                'make'         => $item['Make'],
                'model'        => $item['Model'],
                'year'         => $item['Year'],
                'trim'         => (isset($item['Trim']) ? $item['Trim'] : NULL),
                'engine'       => (isset($item['Engine']) ? $item['Engine'] : NULL),
                'submodel'     => (isset($item['Submodel']) ? $item['Submodel'] : NULL),
                'scope'        => $scope
            );

            if (count($itemsForInsert) >= 100 || $i >= ($totalCountItems - 1)) {

                $connWrite->insertMultiple($tableMotorsEpids, $itemsForInsert);
                $connWrite->delete($tableMotorsEpids, array('is_custom = ?' => 1,
                                                                'epid IN (?)'   => $temporaryIds));
                $itemsForInsert = $temporaryIds = array();
            }

            if (++$iteration % $iterationsForOneStep == 0) {
                $percentsShift = ($iteration/$iterationsForOneStep) * $percentsForOneStep;
                $this->getActualLockItem()->setPercents(
                    $this->getPercentsStart() + $this->getPercentsInterval()/2 + $percentsShift
                );
            }
        }
    }

    protected function logSuccessfulOperation()
    {
        // M2ePro_TRANSLATIONS
        // The "Parts Compatibility [ePIDs]" Action for eBay Site: "%mrk%" has been successfully completed.

        $tempString = Mage::getModel('M2ePro/Log_Abstract')->encodeDescription(
            'The "Parts Compatibility [ePIDs]" Action for eBay Site: "%mrk%" has been successfully completed.',
            array('mrk' => $this->marketplace->getTitle())
        );

        $this->getLog()->addMessage($tempString,
                                    Ess_M2ePro_Model_Log_Abstract::TYPE_SUCCESS,
                                    Ess_M2ePro_Model_Log_Abstract::PRIORITY_LOW);
    }

    //########################################
}
<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Amazon_Processing_Action extends Ess_M2ePro_Model_Abstract
{
    const TYPE_PRODUCT_ADD    = 0;
    const TYPE_PRODUCT_UPDATE = 1;
    const TYPE_PRODUCT_DELETE = 2;

    const TYPE_ORDER_UPDATE   = 3;
    const TYPE_ORDER_CANCEL   = 4;
    const TYPE_ORDER_REFUND   = 5;

    //####################################

    /** @var Ess_M2ePro_Model_Processing $processing */
    private $processing = NULL;

    /** @var Ess_M2ePro_Model_Request_Pending_Single $requestPendingSingle */
    private $requestPendingSingle = NULL;

    //####################################

    public function _construct()
    {
        parent::_construct();
        $this->_init('M2ePro/Amazon_Processing_Action');
    }

    //####################################

    public function setProcessing(Ess_M2ePro_Model_Processing $processing)
    {
        $this->processing = $processing;
        return $this;
    }

    /**
     * @return Ess_M2ePro_Model_Processing
     * @throws Ess_M2ePro_Model_Exception_Logic
     */
    public function getProcessing()
    {
        if (!$this->getId()) {
            throw new Ess_M2ePro_Model_Exception_Logic('Instance must be loaded first.');
        }

        if (!is_null($this->processing)) {
            return $this->processing;
        }

        return $this->processing = Mage::helper('M2ePro')->getObject('Processing', $this->getProcessingId());
    }

    //------------------------------------

    public function setRequestPendingSingle(Ess_M2ePro_Model_Request_Pending_Single $requestPendingSingle)
    {
        $this->requestPendingSingle = $requestPendingSingle;
        return $this;
    }

    /**
     * @return Ess_M2ePro_Model_Request_Pending_Single
     * @throws Ess_M2ePro_Model_Exception_Logic
     */
    public function getRequestPendingSingle()
    {
        if (!$this->getId()) {
            throw new Ess_M2ePro_Model_Exception_Logic('Instance must be loaded first.');
        }

        if (!$this->getRequestPendingSingleId()) {
            return null;
        }

        if (!is_null($this->requestPendingSingle)) {
            return $this->requestPendingSingle;
        }

        return $this->requestPendingSingle = Mage::helper('M2ePro')->getObject(
            'Request_Pending_Single', $this->getRequestPendingSingleId()
        );
    }

    //####################################

    public function getAccountId()
    {
        return (int)$this->getData('account_id');
    }

    public function getProcessingId()
    {
        return (int)$this->getData('processing_id');
    }

    public function getRequestPendingSingleId()
    {
        return (int)$this->getData('request_pending_single_id');
    }

    public function getRelatedId()
    {
        return (int)$this->getData('related_id');
    }

    public function getType()
    {
        return (int)$this->getData('type');
    }

    public function getRequestData()
    {
        return $this->getSettings('request_data');
    }

    public function getStartDate()
    {
        return (string)$this->getData('start_date');
    }

    //####################################
}
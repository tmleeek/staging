<?php

class MDN_Mpm_Model_Report extends Mage_Core_Model_Abstract {

    const kStatusNew = 'new';
    const kStatusSubmitted = 'submitted';
    const kStatusAvailable = 'available';
    const kStatusExpired = 'expired';
    const kStatusError = 'error';

    const kTypeAllOffers = 'product_offers_all';
    const kTypeCommissions = 'commissions';

    public function _construct() {
        parent::_construct();
        $this->_init('Mpm/Report');
    }

    /**
     * Request a new report
     *
     * @param $type
     */
    public function request($type, $params = array())
    {
        $reportId = Mage::helper('Mpm/Carl')->requestClientReport($type, $params);

        $report = Mage::getModel('Mpm/Report')
                    ->setreport_id($reportId)
                    ->setstatus(self::kStatusNew)
                    ->setreport_type($type)
                    ->setreport_params(serialize($params))
                    ->save();

        return $report;
    }

    public function getPending($type, $params = array())
    {
        $collection = Mage::getModel('Mpm/Report')
                        ->getCollection()
                        ->addFieldToFilter('report_type', $type)
                        ->addFieldToFilter('status', array('neq' => self::kStatusAvailable))
                        ->addFieldToFilter('status', array('neq' => self::kStatusError))
                        ->addFieldToFilter('status', array('neq' => self::kStatusExpired));

        if (count($params) > 0)
        {
            foreach($collection as $report)
            {
                $canMatch = true;
                foreach($params as $k => $v)
                {
                    if ($report->getParam($k) != $v)
                        $canMatch = false;
                }
                if ($canMatch)
                    return $report;
            }
        }
        else
        {
            $report = $collection->getFirstItem();
            if ($report->getId())
                return $report;
        }

        return false;
    }

    /**
     * Update report status from Carl
     *
     * @return $this
     */
    public function updateStatus()
    {
        $status = Mage::helper('Mpm/Carl')->getReportStatus($this->getreport_id());
        Mage::helper('Mpm')->log('update status for report #'.$this->getreport_id().', new status is '.$status);
        $this->setStatus(strtolower($status))->save();
        return $this;
    }

    public function checkExpire()
    {
        $requestedTimeStamp = strtotime($this->getrequested_at());
        $age = Mage::getModel('core/date')->timestamp(time()) - $requestedTimeStamp;

        if ($age > 7200)    //2 hours
        {
            $this->setStatus(self::kStatusExpired)->save();
            return true;
        }
        return false;
    }

    /**
     *
     */
    protected function _beforeSave() {
        parent::_beforeSave();

        $this->setrequested_at(date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(time())));
    }

    public function getReportResult()
    {
        $content = Mage::helper('Mpm/Carl')->getReportContent($this->getreport_id());
        return $content;
    }

    public function processResult()
    {
        $content = $this->getReportResult();
        $result = '';
        try
        {
            switch($this->getreport_type())
            {
                case self::kTypeAllOffers:
                    $result = Mage::getSingleton('Mpm/Report_ProductOffersAll')->process($this, $content);
                    break;
                case self::kTypeCommissions:
                    $result = Mage::getSingleton('Mpm/Report_Commissions')->process($this, $content);
                    break;
            }
        }
        catch(Exception $ex)
        {
            $result = $ex->getMessage();
        }
        $this->setresult($result)->save();
        return $this;
    }

    public function getParam($code = null)
    {
        $params = unserialize($this->getreport_params());
        if ($code == null)
            return $params;
        if (isset($params[$code]))
            return $params[$code];
        else
            return false;
    }

}
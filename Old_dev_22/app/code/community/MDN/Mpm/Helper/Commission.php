<?php


class MDN_Mpm_Helper_Commission extends Mage_Core_Helper_Abstract {

    public function synchronizeAll()
    {
        $count = 0;
        foreach(Mage::helper('Mpm/Carl')->getChannelsSubscribed() as $channel)
        {
            if ($channel->commission == 'enabled') {
                $this->Synchronize($channel->channelCode);
                $count++;
            }
        }
        return $count;
    }

    public function Synchronize($channelCode)
    {
        $pendingReport = Mage::getModel('Mpm/Report')->getPending(MDN_Mpm_Model_Report::kTypeCommissions, array('channel' => $channelCode));
        if ($pendingReport)
        {
            Mage::helper('Mpm')->log('A pending report exists : #'.$pendingReport->getreport_id());
            $pendingReport->updateStatus();
            if ($pendingReport->getStatus() == MDN_Mpm_Model_Report::kStatusAvailable)
            {
                Mage::helper('Mpm')->log('Process report #'.$pendingReport->getreport_id());
                $pendingReport->processResult();
            }
            else
            {
                if ($pendingReport->checkExpire())
                {
                    Mage::helper('Mpm')->log('Report #'.$pendingReport->getreport_id().' is expired');
                }
                else
                    Mage::helper('Mpm')->log('Report #'.$pendingReport->getreport_id().' is not available yet');
            }
        }
        else
        {
            //request a new report
            Mage::helper('Mpm')->log('Request a new report for '.MDN_Mpm_Model_Report::kTypeCommissions);
            $pendingReport = Mage::getModel('Mpm/Report')->request(MDN_Mpm_Model_Report::kTypeCommissions, array('channel' => $channelCode, 'title' => 'Commissions '.$channelCode));
        }
        return $pendingReport;

    }

}
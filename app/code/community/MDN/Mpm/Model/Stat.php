<?php

class MDN_Mpm_Model_Stat extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('Mpm/Stat');
    }

    public function getSegments()
    {
        $segments = array('Global', 'Category');
        if (Mage::helper('Mpm')->getBrandAttribute())
            $segments[] = 'Brand';
        if (Mage::helper('Mpm')->getSupplierAttribute())
            $segments[] = 'Supplier';
        return $segments;
    }

    public function run()
    {
        $segments = $this->getSegments();
        $channels = Mage::helper('Mpm/Carl')->getChannelsSubscribed();

        foreach($channels as $channel)
        {
            $competitors = Mage::getSingleton('Mpm/Product_Offer')->getAllCompetitors($channel->channelCode);
            foreach($segments as $segment)
            {
                $segmentObj = Mage::getModel('Mpm/Stat_Segment_'.$segment);
                $segmentObj->truncate($channel->channelCode);
                foreach($segmentObj->getOccurrences() as $occurenceKey => $occurenceLabel)
                {
                    foreach($competitors as $competitor)
                    {
                        try
                        {
                            $data = $segmentObj->getStats($channel->channelCode, $occurenceKey, $competitor);
                            if ($data) {
                                $data['channel'] = $channel->channelCode;
                                $data['segment_type'] = $segment;
                                $data['segment_value'] = $occurenceLabel;
                                $data['competitor'] = $competitor['seller_name'];
                                $this->insertData($data);
                            }
                        }
                        catch(Exception $ex)
                        {
                            throw new Exception($ex);
                        }
                    }
                }

            }
        }

    }

    public function insertData($data)
    {
        $model = Mage::getModel('Mpm/Stat');
        $model->setData($data);
        $model->save();
        return $model;
    }

}
<?php

class MDN_Mpm_Block_Dashboard_Matching extends Mage_Adminhtml_Block_Template {

    protected $_channelStats = null;

    public function getMatchingCategories()
    {
        $categories = array();
        foreach($this->getChannelStats() as $k => $v)
        {
            if ($k == 'total')
                continue;
            $categories[] = Mage::helper('Mpm/Carl')->getChannelLabel($k);
        }
        return $categories;
    }

    public function getChannelStats()
    {
        if (!$this->_channelStats)
        {
            $this->_channelStats = Mage::helper('Mpm/Carl')->getChannelStats();
        }
        return $this->_channelStats;
    }

    public function getMatchingSeries()
    {
        $series = array();

        $types = array('associated' => '#a3ce1c', 'not_associated' => '#525252', 'pending' => '#a7a9ac');
        foreach($types as $type => $color)
        {
            $serie = array('name' => $type, 'data' => array(), 'index' => count($series), 'color' => $color);

            foreach($this->getChannelStats() as $k => $v)
            {
                if ($k == 'total')
                    continue;
                $serie['data'][] = (isset($v->details->$type)) ? $v->details->$type->nbr : 0;
            }

            $series[] = $serie;
        }

        return $series;
    }

}
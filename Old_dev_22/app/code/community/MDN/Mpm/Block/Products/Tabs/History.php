<?php

class MDN_Mpm_Block_Products_Tabs_History extends Mage_Adminhtml_Block_Widget  {

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('Mpm/Products/Tabs/History.phtml');
    }

    public function getProduct()
    {
        return Mage::registry('mpm_product');
    }

    public function getChannel()
    {
        return Mage::registry('mpm_channel');
    }

    public function initDatas()
    {
        $from = date('Y-m-d', time() - 3600 * 24 * 31);
        $to = date('Y-m-d');
        $channel = $this->getChannel();
        $sku = $this->getProduct()->getProductId();

        $history = null;
        try
        {
            $history = Mage::helper('Mpm/Carl')->getOffersHistory($sku, $channel, $from, $to);
        }
        catch(Exception $ex)
        {
            return $this;
        }

        $allDates = array();
        $competitors = array();
        $competitorNames = array();
        $values = array();
        foreach((array)$history as $competitor => $items)
        {
            foreach($items as $item)
            {
                $date = strtotime($item->created_at);
                $date = date('Y-m-d', $date);
                if (!in_array($date, $allDates))
                    $allDates[] = $date;
                $values[$competitor.'_'.$date] = $item;
                $competitorNames[$competitor] = empty($item->offer->competitor_name) ? $competitor : $item->offer->competitor_name;
            }
            $competitors[] = $competitor;
        }
        sort($allDates);

        $this->setAllDates($allDates);
        $this->setCompetitors($competitors);
        $this->setCompetitorNames($competitorNames);
        $this->setValues($values);

        return $this;
    }

    public function getValue($key)
    {
        $values = $this->getValues();
        if (isset($values[$key]))
            return $values[$key];
    }

    public function getProgressImage($item, $lastValue)
    {
        $img = '';
        if ($item->offer->shipping + $item->offer->price == $lastValue->offer->shipping + $lastValue->offer->price)
            $img = 'flat';
        if ($item->offer->shipping + $item->offer->price > $lastValue->offer->shipping + $lastValue->offer->price)
            $img = 'increase';
        if ($item->offer->shipping + $item->offer->price < $lastValue->offer->shipping + $lastValue->offer->price)
            $img = 'decrease';
        return Mage::getDesign()->getSkinUrl('Mpm/images/'.$img.'.png');
    }

    public function isMe($competitor)
    {
        return ($competitor == Mage::getStoreConfig('mpm/repricing/seller_id_'.$this->getchannel()));
    }

}

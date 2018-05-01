<?php

class MDN_Mpm_Helper_Product extends Mage_Core_Helper_Abstract {

    private static $inProgress = false;

    /**
     *  Synchronize all offers from report
     */
    public function synchronizeAllOffers()
    {
        $pendingReport = Mage::getModel('Mpm/Report')->getPending(MDN_Mpm_Model_Report::kTypeAllOffers);
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
            Mage::helper('Mpm')->log('Request a new report for '.MDN_Mpm_Model_Report::kTypeAllOffers);
            $pendingReport = Mage::getModel('Mpm/Report')->request(MDN_Mpm_Model_Report::kTypeAllOffers);
        }
        return $pendingReport;
    }

    /**
     * Get offers from Carl and update DB
     *
     * @param $product
     */
    public function synchronizeOffers($product)
    {
        Mage::helper('Mpm')->log('Synchronize offers for '.$product->getSku().' (#'.$product->getId().')');

        $offers = Mage::helper('Mpm/Carl')->getProductOffers($product->getSku());

        //change competitor key to seller_name
        foreach($offers as &$offer)
        {
            $offer['seller_name'] = $offer['competitor'];
        }

        Mage::getSingleton('Mpm/Report_ProductOffersAll')->insertProductOffers($product->getId(), $offers);
    }

    /**
     *
     * @param $product
     * @return mixed
     */
    public function getOffers($product, $onlyMe = false, $channel = false, $filters = array(), $sortBy = array())
    {
        if (is_object($product))
            $productId = $product->getSku();
        else
            $productId = $product;
        $collection = new MDN_Mpm_Model_ProductOffersCollection();
        $collection->setProductId($productId);
        $collection->setAllChannel(true);

        if ($channel){
            $collection->setAllChannel(false);
            $collection->setChannel($channel);
        }
        if(!empty($sortBy)){
            $collection->addAttributeToSort($sortBy['field'],$sortBy['dir']);

        }
        if ($onlyMe)
            $collection->addFieldToFilter('is_me', array("like" => "%'1%'"));

        foreach($filters as $filterKey => $filterValue){
            $collection->addFieldToFilter($filterKey,array("like" => "%'$filterValue%'"));
        }

        $collection = $collection->load();
        return $collection;
    }

    /**
     * Return best offer from all offers, considering best offer mode(best price or bbw)
     *
     * @param $offers
     * @param $channel
     * @param bool $excludeMe : do not consider "is_me" offers
     * @return mixed
     */
    public function getBestOffer($offers, $channel, $excludeMe = false, $method = MDN_Mpm_Model_Pricer::kCompeteWithBestPrice)
    {

        $newOffers = array();
        foreach($offers as $offer)
        {
            if ($channel == $offer->getChannel())
            {
                if (!$excludeMe || ($excludeMe && !$offer->getIsMe()))
                    $newOffers[] = $offer;
            }
        }
        $offers = $newOffers;

        switch($method)
        {
            case MDN_Mpm_Model_Pricer::kCompeteWithBestPrice:
                usort($offers, array('MDN_Mpm_Model_Product_Offer', 'sortOffersPerPrice'));
                break;
            case MDN_Mpm_Model_Pricer::kCompeteWithBestRank:
                usort($offers, array('MDN_Mpm_Model_Product_Offer', 'sortOffersPerRank'));
                break;
        }

        if (isset($offers[0]))
            return $offers[0];
    }

    /**
     * Apply algorithm on ALL products
     */
    public function repriceAll()
    {
        Mage::helper('Mpm')->log('START repriceall');
        $productIds = Mage::getModel('catalog/product')->getCollection()->getAllIds();
        foreach($productIds as $productId)
        {
            $product = Mage::getModel('catalog/product')->load($productId);
            Mage::getSingleton('Mpm/Pricer')->processProduct($product, null);
            $product->clearInstance();  //memory flush
        }
        Mage::helper('Mpm')->log('END repriceall');
    }

    /**
     * Set updated_at = now for product id
     *
     * @param $productId
     */
    public function touchUpdatedAt($productId)
    {
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        $tableName = $resource->getTableName('catalog_product_entity');
        $query = "update  ".$tableName." set updated_at = NOW() WHERE entity_id = ".$productId;
        $writeConnection->query($query);
    }

    public function setProductsGridFilters($statuses, $channels, $behaviours)
    {
        $session = Mage::getSingleton('adminhtml/session');
        $session->setData('mpm_productgrid_channels', $channels);
        $session->setData('mpm_productgrid_statuses', $statuses);
        $session->setData('mpm_productgrid_behaviours', $behaviours);
    }

    public function getProductsGridFilters()
    {
        $obj = array();
        $session = Mage::getSingleton('adminhtml/session');
        $obj['channels'] = $session->getData('mpm_productgrid_channels');
        if (!$obj['channels'])
        {
            $obj['channels'] = array();
            $channels = Mage::helper('Mpm/Carl')->getChannelsSubscribed(true);
            foreach($channels as $key => $label)
                $obj['channels'][$key] = 1;
        }
        $obj['statuses'] = $session->getData('mpm_productgrid_statuses');
        $obj['behaviours'] = $session->getData('mpm_productgrid_behaviours');
        return $obj;
    }

    public function reset()
    {
        $settings = Mage::getModel('Mpm/Product_Setting')->getCollection();
        foreach($settings as $setting)
            $setting->delete();

        $offers = Mage::getModel('Mpm/Product_Offer')->getCollection();
        foreach($offers as $offer)
            $offer->delete();

        $reports = Mage::getModel('Mpm/Report')->getCollection();
        foreach($reports as $report)
            $report->delete();

        $logs = Mage::getModel('Mpm/PricingLog')->getCollection();
        foreach($logs as $log)
            $log->delete();


        $logs = Mage::getModel('Mpm/Commission')->getCollection();
        foreach($logs as $log)
            $log->delete();
    }


    /**
     * Return reached rank with final price based on existing competitors
     *
     * @param $productId
     * @param $channel
     * @param $finalPrice
     */
    public function simulateRank($productId, $channel, $finalPrice)
    {
        $rank = 1;

        $offers = $this->getOffers($productId, false, $channel)->setOrder('total', 'ASC');

        foreach($offers as $offer)
        {
            if (!$offer->getIsMe())
            {
                if ($offer->getTotal() > $finalPrice)
                    return $rank;
                $rank++;
            }
        }

        return $rank;
    }

    public function pricingInProgress()
    {
        $this->hasPricingInProgress();
        $this->hasProductsInQueue();
    }

    public function hasProductsInQueue()
    {
        $tasks = array();
        $products = Mage::helper('Mpm/PricingQueue')->getTasks(50);

        if(count($products) > 0) {
            $message = Mage::helper('Mpm')->__('Products in waiting to update').': ';
            foreach($products as $task) {
                if(!in_array($task->product_id, $tasks)) {
                    $message .= '<a href="' . Mage::getUrl('adminhtml/catalog_product/edit', array('id' => $task->product_id)) . '" target="_blank">'
                        . $task->product_id
                        . '</a>, ';
                    $tasks[] = $task->product_id;
                }
            }
            $message = rtrim($message, ', ');
            Mage::getSingleton('adminhtml/session')->addError($message);
        }
    }

    private function hasPricingInProgress()
    {
        $progress = Mage::Helper('Mpm/Carl')->pricingInProgress();

        if($progress->in_progress === 'yes' && self::$inProgress === false) {
            $message = Mage::helper('Mpm')->__('A recalculation of your prices is in progress due of the configuration modifications')
                . ' '
                . $progress->progress
            ;

            self::$inProgress = true;
            Mage::getSingleton('adminhtml/session')->addError($message);
        }
        else
        {
            if ($progress->to_reprice === 'yes')
            {
                $message = Mage::helper('Mpm')->__('A full catalog repricing is pending, please wait');
                Mage::getSingleton('adminhtml/session')->addError($message);
            }
        }

    }
}
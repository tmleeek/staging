<?php

/**
 *
 *
 */
class MDN_Mpm_Model_Report_ProductOffersAll extends Mage_Core_Model_Abstract {

    /**
     * @param $csv
     */
    public function process($report, $csv)
    {
        Mage::helper('Mpm')->log('Start process CSV');

        $products = $this->csvToArray($csv);
        Mage::helper('Mpm')->log('CSV converted to array ('.count($products).' products)');

        $errorsCount = 0;
        foreach($products as $sku => $offers)
        {
            try
            {
                $productId = Mage::getSingleton('catalog/product')->getIdBySku($sku);
                if (!$productId)
                    throw new Exception('Unable to find product id for sku "'.$sku.'"');
                $count = $this->insertProductOffers($productId, $offers);
                Mage::helper('Mpm')->log('Offers inserted for product '.$sku.' : '.$count);
            }
            catch(Exception $ex)
            {
                Mage::helper('Mpm')->log('Error inserting offers for product '.$sku.' : '.$ex->getMessage());
                $errorsCount++;
            }
        }

        $result = 'End process CSV : '.count($products).' products processed, '.$errorsCount.' errors';
        Mage::helper('Mpm')->log($result);
        return $result;
    }

    protected function csvToArray($csv)
    {
        $tmpPath = Mage::getBaseDir('var').DS.'mpm_report.csv';
        if (file_exists($tmpPath))
            unlink($tmpPath);
        file_put_contents($tmpPath, $csv);

        $products = array();
        $columnIndexes = array();
        $handle = fopen($tmpPath, "r");
        $isFirst = yes;

        while (($values = fgetcsv($handle, 10000, ",", '"', '"')))
        {
            if ($isFirst)
            {
                $columnIndexes = array_flip($values);
                $isFirst = false;

                $requiredColumns = array('id','channel', 'rank', 'competitor', 'price', 'shipping', 'total_price');
                foreach($requiredColumns as $requiredColumn)
                {
                    if (!isset($columnIndexes[$requiredColumn]))
                        throw new Exception('Column '.$requiredColumn.' is missing in CSV file, import is stopped');
                }

                continue;
            }

            $sku = $values[$columnIndexes['id']];
            $channel = $values[$columnIndexes['channel']];
            $rank = $values[$columnIndexes['rank']];
            $sellerName = $values[$columnIndexes['competitor']];
            $price = $values[$columnIndexes['price']];
            $shipping = $values[$columnIndexes['shipping']];
            $total = $values[$columnIndexes['total_price']];
            $date = $values[$columnIndexes['date']];

            if (!isset($products[$sku]))
                $products[$sku] = array();
            $products[$sku][] = array('channel' => $channel, 'rank' => $rank, 'seller_name' => $sellerName, 'price' => $price, 'shipping' => $shipping, 'total' => $total, 'updated_at' => $date);

        }

        fclose($handle);
        if (file_exists($tmpPath))
            unlink($tmpPath);

        return $products;
    }

    public function insertProductOffers($productId, $offers)
    {
        //create an array with current offers
        $currentOffers =  Mage::getModel('Mpm/Product_Offer')->getCollection()->addFieldToFilter('product_id', $productId);
        $currentOfferKeys = array();
        foreach($currentOffers as $item)
            $currentOfferKeys[] = $item->getChannel().' | '.$item->getRank().' | '.number_format($item->getTotal(), 2, '.', '');

        //create an array with new offers
        $newOfferKeys = array();
        foreach($offers as $item)
            $newOfferKeys[] = $item['channel'].' | '.$item['rank'].' | '.number_format($item['total'], 2, '.', '');

        //delete existing
        Mage::getSingleton('Mpm/Product_Offer')->deleteForOneProduct($productId);

        //insert
        $count = 0;
        foreach($offers as $offer)
        {
            $obj = Mage::getModel('Mpm/Product_Offer');
            foreach($offer as $k => $v)
                $obj->setData($k, $v);
            $obj->setproduct_id($productId);
            $obj->save();
            $count++;
        }


        //if changes, launch repricing
        $diff1 = array_diff($currentOfferKeys, $newOfferKeys);
        $diff2 = array_diff($newOfferKeys, $currentOfferKeys);
        if ((count($diff1) + count($diff2) > 0))
        {
            Mage::helper('Mpm')->log('Offers for product id '.$productId.' have changed, launch repricing');
            Mage::getModel('Mpm/Pricer')->processProduct($productId);
        }

        return $count;
    }

}
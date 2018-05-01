<?php

/**
 *
 *
 */
class MDN_Mpm_Model_Report_Commissions extends Mage_Core_Model_Abstract {

    /**
     * @param $csv
     */
    public function process($report, $csv)
    {
        $channel = $report->getParam('channel');
        if (!$channel)
            throw new Exception('No channel available');

        Mage::helper('Mpm')->log('Start process CSV');


        $datas = $this->csvToArray($csv);
        Mage::helper('Mpm')->log('CSV converted to array ('.count($datas).' products)');

        $errorsCount = 0;
        foreach($datas as $sku => $data)
        {
            try
            {
                $productId = Mage::getSingleton('catalog/product')->getIdBySku($sku);
                if (!$productId)
                    throw new Exception('Unable to find product id for sku "'.$sku.'"');
                $percent = number_format($data['fee'] / $data['price'] * 100, 2, '.', '');

                Mage::getModel('Mpm/Commission')->insertOrUpdate($channel, $sku, $percent);
            }
            catch(Exception $ex)
            {
                Mage::helper('Mpm')->log('Error updating commission for '.$sku.' : '.$ex->getMessage());
                $errorsCount++;
            }
        }

        $result = 'End process commissions : '.count($datas).' records processed, '.$errorsCount.' errors';
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
        $isFirst = true;

        while (($values = fgetcsv($handle, 10000, "\t")))
        {
            if ($isFirst)
            {
                $columnIndexes = array_flip($values);
                $isFirst = false;

                $requiredColumns = array('seller-sku', 'price', 'estimated-referral-fee-per-item');
                foreach($requiredColumns as $requiredColumn)
                {
                    if (!isset($columnIndexes[$requiredColumn]))
                        throw new Exception('Column '.$requiredColumn.' is missing in CSV file, import is stopped');
                }

                continue;
            }

            if ((!isset($values[$columnIndexes['seller-sku']])) || (!isset($values[$columnIndexes['price']])) || (!isset($values[$columnIndexes['estimated-referral-fee-per-item']])))
                continue;

            $sku = $values[$columnIndexes['seller-sku']];
            $price = $values[$columnIndexes['price']];
            $fee = $values[$columnIndexes['estimated-referral-fee-per-item']];

            if (!isset($products[$sku]))
                $products[$sku] = array();
            $products[$sku] = array('sku' => $sku, 'price' => $price, 'fee' => $fee);

        }

        fclose($handle);
        if (file_exists($tmpPath))
            unlink($tmpPath);

        return $products;
    }


}

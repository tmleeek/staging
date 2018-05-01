<?php

class MDN_Mpm_Helper_PricingQueue extends Mage_Core_Helper_Abstract
{
    public function addTask($productId)
    {
        Mage::getModel('Mpm/Queue')
            ->setproduct_id($productId)
            ->save();
    }

    public function playTasks()
    {
        $tasks = $this->getTasks();

        foreach($tasks as $task) {
            $product = Mage::getModel('catalog/product')->load($task->product_id);

            try {
                Mage::getModel('Mpm/Export_Product')->updateProduct($product->getId());
            } catch(\Exception $e) {
                $task->delete();
                continue;
            }
            
            $pricingList = Mage::helper('Mpm/Carl')->repriceProduct($product->getSku());
            foreach($pricingList as $pricing) {
                if($pricing->status === 'error') {
                    $task->delete();
                    continue;
                }

                Mage::helper('Mpm/PricingImport')->setPricing(
                    $product->getSku(),
                    $pricing->channel,
                    $pricing->final_price,
                    $pricing->shipping_price
                );
            }

            $task->delete();
        }
    }

    public function getTasks($limit = 20)
    {
        return Mage::getModel('Mpm/Queue')
            ->getCollection()
            ->setPageSize($limit)
            ->load()
        ;
    }
}
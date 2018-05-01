<?php

class TBT_Rewards_Helper_Array extends Mage_Core_Helper_Abstract
{
    /**
     * Merges any number of arrays by numerically adding their elements,
     * based on matching keys.
     * @param array $array1 First array to merge
     * @param array $arrays... Subsequent arrays to merge
     * @return array
     */
    public function mergeByAddition()
    {
        $arrays = func_get_args();
        $merged = array();
        
        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                if (!is_numeric($value)) {
                    continue;
                }
                 
                if (!array_key_exists($key, $merged)) {
                    $merged[$key] = 0;
                }
                 
                $merged[$key] += $value;
            }
        }
        
        return $merged;
    }
}

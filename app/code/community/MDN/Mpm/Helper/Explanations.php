<?php


class MDN_Mpm_Helper_Explanations extends Mage_Core_Helper_Abstract
{

    public function getHumanExplanations($data)
    {

        $txt = array();

        //echo "<pre>"; var_dump($data);

        $txt[] = $this->__('Calculate price for product %s and channel %s', $data['product_id'], $data['channel']);
        if ($data['bbw_name']) {
            $txt[] = $this->__('We compete with vendor %s with a price of %s so we target an ideal price of %s', $data['bbw_name'], $data['best_offer'], $this->getRuleResult($data, 'ADJUSTMENT'));
            $txt[] = $this->__('Product costs (buying price, shipping and additional costs) are %s', $data['base_cost']);
            if ($data['margin_for_bbw'] < $data['final_margin'])
                $txt[] = $this->__('Minimum margin (%s) does not allow to apply the ideal price, then we apply the minimum price', $this->getRuleResult($data, 'MARGIN') . '%');
        } else {
            $txt[] = $this->__('There is no competitor, so we use the rule without competitor');
        }

        if ($minPrice = $this->getRuleResult($data, 'MIN_PRICE'))
            $txt[] = $this->__('We ensure that final price is higher than %s (minimum price)', $minPrice);
        if ($maxPrice = $this->getRuleResult($data, 'MAX_PRICE'))
            $txt[] = $this->__('We ensure that final price is lower than %s (maximum price)', $maxPrice);
        $txt[] = $this->__('Final price is %s (including %s for commission, %s for taxes)', $data['final_price'], $data['commission_amount'], $data['tax_amount']);

        return implode('<br>', $txt);
    }

    protected function getRuleResult($data, $type)
    {
        foreach ($data['rules_result'] as $elt) {
            if ($elt->type == $type)
                return $elt->result;
        }
    }

}
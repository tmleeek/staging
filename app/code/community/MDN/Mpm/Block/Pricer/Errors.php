<?php

class MDN_Mpm_Block_Pricer_Errors extends MDN_Mpm_Block_Products_GridV2 {

    protected function addStatusFilter(&$collection)
    {
        $collection->addFieldToFilter('status', MDN_Mpm_Model_Pricer::kPricingStatusError);
    }

    protected function _prepareColumns() {
        parent::_prepareColumns();

        $this->removeColumn('final_cost');
        $this->removeColumn('final_price');
        $this->removeColumn('margin');
        $this->removeColumn('my_rank');

        $this->removeColumn('bbw_name');
        $this->removeColumn('bbw_price');

        $this->removeColumn('behaviour');
        $this->removeColumn('margin_for_bbw');

        $this->removeColumn('separator_3');

        $this->addColumn('debug',
            array(
                'header'=> Mage::helper('catalog')->__('Message'),
                'index' => 'debug'
            ));

        return $this;
    }

    public function getRowUrl($row)
    {

    }

    protected function _prepareMassaction() {
        return $this;
    }

}

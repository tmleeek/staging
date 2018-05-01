<?php

class MDN_Mpm_Block_Dashboard_Graph extends Mage_Adminhtml_Block_Template {

    public function getBbw()
    {
        return Mage::helper('Mpm/Carl')->getStatisticsBbw();
    }

}
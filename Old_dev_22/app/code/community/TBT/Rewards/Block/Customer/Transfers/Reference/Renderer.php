<?php

class TBT_Rewards_Block_Customer_Transfers_Reference_Renderer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param Varien_Object $row
     * @return mixed
     */
    public function _getValue(Varien_Object $row)
    {
        $block = $this->getLayout()->createBlock('rewards/customer_transfers_reference');
        $block->setTransfer($row);

        return $block->toHtml();
    }
}

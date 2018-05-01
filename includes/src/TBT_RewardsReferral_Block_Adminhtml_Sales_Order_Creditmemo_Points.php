<?php

class TBT_RewardsReferral_Block_Adminhtml_Sales_Order_Creditmemo_Points extends Mage_Adminhtml_Block_Template
{
    protected $_transfers = null;

    protected function _construct()
    {
        parent::_construct();

        $this->setTemplate('rewardsref/sales/order/creditmemo/points.phtml');
        $this->setFieldWrapper('creditmemo');

        return $this;
    }

    /**
     * Retrieves sum of points earned by the affiliate for this order, first order
     * rule points (if any) + any order rule points
     * @return int Sum of points earned by the affiliate on this order
     */
    public function getAffiliatePointsEarned()
    {
        $points = 0;

        if (!$this->getOrder()) {
            return $points;
        }

        if (!$this->_transfers) {
            $orderId = $this->getOrder()->getId();
            $this->_transfers = Mage::getResourceModel( 'rewardsref/referral_order_transfer_reference_collection' )
                ->addTransferInfo()
                ->filterAssociatedWithOrder($orderId);
        }

        foreach ($this->_transfers as $transfer) {
            if ($transfer->getQuantity() > 0) {
                $points += $transfer->getQuantity();
            }
        }

        return $points;
    }

}

<?php

class TBT_Rewards_Block_Adminhtml_Sales_Order_Creditmemo_Points extends Mage_Adminhtml_Block_Template
{
    protected $_transfers = null;
    
    protected function _construct()
    {
        parent::_construct();
        
        $this->setTemplate('rewards/sales/order/creditmemo/points.phtml');
        $this->setFieldWrapper('creditmemo');
        
        return $this;
    }
    
    public function getPointsSpent()
    {
        $points = 0;
        
        if (!$this->getOrder()) {
            return $points;
        }
        
        if (!$this->_transfers) {
            $orderId = $this->getOrder()->getId();
            $this->_transfers = Mage::getModel('rewards/transfer')->getTransfersAssociatedWithOrder($orderId);
        }
        
        foreach ($this->_transfers as $transfer) {
            if ($transfer->getQuantity() < 0) {
                $points += $transfer->getQuantity();
            }
        }
        
        return $points * -1;
    }
    
    public function getPointsEarned()
    {
        $points = 0;
        
        if (!$this->getOrder()) {
            return $points;
        }
        
        if (!$this->_transfers) {
            $orderId = $this->getOrder()->getId();
            $this->_transfers = Mage::getModel('rewards/transfer')->getTransfersAssociatedWithOrder($orderId);
        }
        
        foreach ($this->_transfers as $transfer) {
            if ($transfer->getQuantity() > 0) {
                $points += $transfer->getQuantity();
            }
        }
        
        return $points;
    }
    
    public function getCustomerBalance()
    {
        if (!$this->getOrder()) {
            return 0;
        }
        
        $currencyIds = Mage::getSingleton('rewards/currency')->getAvailCurrencyIds();
        $currencyId = $currencyIds[0];
        
        $customer = Mage::getModel('rewards/customer')->load($this->getOrder()->getCustomerId());
        $pointsBalance = $customer->getUsablePointsBalance($currencyId);
        
        return $pointsBalance;
    }
    
    public function getCustomerBalanceString()
    {
        $currencyIds = Mage::getSingleton('rewards/currency')->getAvailCurrencyIds();
        $currencyId = $currencyIds[0];
        
        $balance = $this->getCustomerBalance();
        return Mage::getModel('rewards/points')->set($currencyId, $balance);
    }
}

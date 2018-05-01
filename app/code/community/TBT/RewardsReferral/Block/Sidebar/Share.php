<?php

class TBT_RewardsReferral_Block_Sidebar_Share extends Mage_Core_Block_Template
{
        protected function _construct() {
                parent::_construct ();
                $this->_controller = 'customer';
                $this->_blockGroup = 'rewards';
                $this->headerText = "My Points & Rewards";
                $this->setTemplate ( 'rewards/customer/sidebar.phtml' );
        }

        protected function _prepareLayout() {
                parent::_prepareLayout ();
        }

        public function getCustomer() {
            return Mage::getSingleton('rewards/session')->getCustomer();
        }

        public function getReferralUrl() {
            return (string)Mage::helper('rewardsref/url')->getUrl($this->getCustomer());
        }

        public function getReferralCode() {
            return (string)Mage::helper('rewardsref/code')->getCode($this->getReferralEmail());
        }

        public function getReferralEmail() {
            return (string)$this->getCustomer()->getEmail();
        }

        public function getReferralEmailUrl()
        {
            return $this->getUrl('rewardsref/customer/index') . "#refer_form";
        }

        /**
         * Fetches a string representing the number of points being spent in the cart
         *
         * @return string
         */
        public function getPointsSpending() {
                $str = $this->_getRewardsHelper ()->emphasizeThePoints ( $this->_getRewardsSess ()->getTotalPointsSpendingAsString () );
                return $str;
        }

        /**
         * Fetches a string of the number of points the customer will earn from the cart.
         *
         * @return string
         */
        public function getPointsEarning() {
                $str = $this->_getRewardsHelper ()->emphasizeThePoints ( $this->_getRewardsSess ()->getTotalPointsEarningAsString () );
                return $str;
        }

        /**
         * Fetches a string with the customer points summary.
         *
         * @return string
         */
        public function getCustomerPoints() {
                $str = $this->_getRewardsHelper ()->emphasizeThePoints ( $this->_getRewardsSess ()->getSessionCustomer ()->getPointsSummary () );
                return $str;
        }

        /**
         * Fetches a string of the number of points remaining in the cart.
         *
         * @return string
         */
        public function getPointsRemaining() {
                $points_remain_str = $this->_getRewardsHelper ()->emphasizeThePoints ( $this->_getRewardsSess ()->getTotalPointsRemainingAsString () );
                return $points_remain_str;
        }

        /**
         * True if the customer is spending any points in their cart.
         * False otherwise
         *
         * @return boolean
         */
        public function isSpendingAnyPoints() {
                return $this->_getRewardsSess ()->hasRedemptions ();
        }

        /**
         * True if the cart has overspending in it.
         *
         * @return boolean
         */
        public function isCartOverspent() {
                $overspent = $this->_getRewardsSess ()->isCartOverspent ();
                return $overspent;
        }

        /**
         * True if the customer is logged in.
         *
         * @return boolean
         */
        public function isCustomerLoggedIn() {
                $logged_in = $this->_getRewardsSess ()->isCustomerLoggedIn ();
                return $logged_in;
        }

        protected function _toHtml() {
                $showSidebarWhenNotLoggedIn = Mage::helper ( 'rewards/config' )->showSidebarIfNotLoggedIn ();
                $showSidebar = Mage::helper ( 'rewards/config' )->showSidebar ();
                if (($this->isCustomerLoggedIn () || $showSidebarWhenNotLoggedIn) && $showSidebar) {
                        return parent::_toHtml ();
                } else {
                        return '';
                }
        }

        /**
         * Fetches the customer rewards session.
         *
         * @return TBT_Rewards_Model_Session
         */
        protected function _getRewardsSess() {
                return Mage::getSingleton ( 'rewards/session' );
        }

        /**
         * Fetches the customer rewards helper singelton class
         *
         * @return TBT_Rewards_Helper_Data
         */
        protected function _getRewardsHelper() {
                return Mage::helper ( 'rewards' );
        }

}

<?php

class TBT_Rewards_Model_Customer_Observer extends Varien_Object
{

    /**
     * @var int
     */
    protected $oldId  = -1;

    /**
     * This is used to know if a customer model is new or not. Works by checking isObjectNew() in customerBeforeSave()
     *
     * @var string
     **/
    protected $_isNew = false;

    /**
     * AfterLoad for customer
     * @param Varien_Event_Observer $observer
     */
    public function customerAfterLoad(Varien_Event_Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $customer = Mage::getModel('rewards/customer')->getRewardsCustomer($customer);
        return $this;
    }

    /**
     * AfterSave for customer
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function customerAfterSave(Varien_Event_Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        Mage::getSingleton('rewards/session')->setCustomer($customer);

        return $this;
    }

    /**
     * CustomerAfterCommit observes 'customer_save_commit_after'
     * @param  Varien_Event_Observer $observer
     * @return $this
     */
    public function customerAfterCommit($observer)
    {
        $customer_obj = $observer->getEvent()->getCustomer();
        $customer = Mage::getModel('rewards/customer')->getRewardsCustomer($customer_obj);

        //If the customer is new (hence not having an id before) get applicable rules,
        //and create a transfer for each one
        if ($this->_isNew && !$this->getCreatedTransferForNewCustomer()) {
            // making sure this will not be fired twice
            $this->setCreatedTransferForNewCustomer(true);
            $customer->createTransferForNewCustomer();

            Mage::getSingleton('rewards/session')->setCustomer($customer);

            $this->setRequiresDispatchAfterOrder(false);
            if ($this->getIsOrderBeingPlaced()) {
                $this->setRequiresDispatchAfterOrder(true);
                return $this;
            }

            $this->_dispatchCustomerCreation($customer);
        }

        return $this;
    }

    /**
     * BeforeSave for customer (customer_save_before)
     * @param Varien_Event_Observer $observer
     */
    public function customerBeforeSave($observer)
    {
        $customer = $observer->getEvent()->getCustomer();

        // because 'customer_save_before' is fired twice, making sure we don't keep this as true
        $this->_isNew = false;
        if ($customer->isObjectNew()) {
            $this->_isNew = true;
        }

        return $this;
    }

    public function orderIsBeingPlaced($observer)
    {
        $this->setIsOrderBeingPlaced(true);
        return $this;
    }

    public function submitOrderSuccess($observer)
    {
        $this->setIsOrderBeingPlaced(false);

        if (!$observer->getEvent()) {
            return $this;
        }

        if (!$observer->getEvent()->getOrder()) {
            return $this;
        }

        $customer = $observer->getEvent()->getOrder()->getCustomer();
        if (!$customer) {
            return $this;
        }

        if ($this->getRequiresDispatchAfterOrder()) {
            $this->_dispatchCustomerCreation($customer);
        }
        return $this;
    }

    protected function _dispatchCustomerCreation($customer)
    {

        if ( Mage::helper('rewards/dispatch')->smartDispatch('rewards_customer_signup', array(
                'customer' => $customer
        )) ) {
            Mage::getSingleton('rewards/session')->triggerNewCustomerCreate($customer);
            Mage::dispatchEvent('rewards_new_customer_create', array(
                    'customer' => &$customer
            ));
        }

        return $this;
    }

    /**
     * True if the id specified is new to this customer model after a SAVE event.
     *
     * @param integer $checkId
     * @return boolean
     */
    public function isNewCustomer($checkId)
    {
        return $this->oldId != $checkId;
    }

    /**
     * Loads the customer wrapper
     * @param Mage_Customer_Model_Customer $customer
     * @return TBT_Rewards_Model_Customer_Wrapper
     */
    private function _loadCustomer(Mage_Customer_Model_Customer $customer)
    {
        return Mage::getModel('rewards/customer')->load($customer->getId());
    }

    /**
     * Sets up save for any rewards specific customer fields
     *
     * @return TBT_Rewards_Model_Customer_Observer
     */
    public function adminhtmlCustomerPrepareSave($observer)
    {
        if (!$observer->getEvent()) {
            return $this;
        }

        $request = $observer->getEvent()->getRequest();
        if (!$request) {
            return $this;
        }

        $customer = $observer->getEvent()->getCustomer();
        if (!$customer) {
            return $this;
        }

        $data = $request->getPost();

        if (isset($data['rewards_points_notification_save'])) {
            $rewardsPointsNotification = 0;
            if (isset($data['rewards_points_notification'])) {
                $rewardsPointsNotification = $data['rewards_points_notification'];
            }
            $customer->setData('rewards_points_notification', $rewardsPointsNotification);
        }

        return $this;
    }

}

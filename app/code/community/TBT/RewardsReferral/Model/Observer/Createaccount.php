<?php

class TBT_RewardsReferral_Model_Observer_Createaccount extends Varien_Object
{

    /**
     * Observer called when an account is being created the standard way
     * @param unknown_type $o
     */
    public function beforeCreate($o)
    {
        $this->attemptReferralCheck($o);
        return $this;
    }

    /**
     * Observer called when an account is being created through the checkout
     * @param unknown_type $o
     */
    public function beforeSaveBilling($o)
    {
        $this->attemptReferralCheck($o, 'billing');
        return $this;
    }

    /**
     * Set the checkout method to session
     *
     * @param unknown_type $o
     * @return TBT_RewardsReferral_Model_Observer_Createaccount
     */
    public function onepageSaveMethod($o)
    {
         $method = Mage::app()->getRequest()->getPost("method");

         if($method == "register" ) {
              Mage::getSingleton('core/session')->setRegisterCheckoutMethod(true);
         } else {
              Mage::getSingleton('core/session')->setRegisterCheckoutMethod(false);
         }

         return $this;
    }

    /**
     * Base observer method that gets called by above observer methods whenever an account is being created in the frontend.
     * @param unknown_type $o
     * @param unknown_type $subfield
     * @throws Exception
     */
    public function attemptReferralCheck($o, $subfield=null, $data = array())
    {
        try {
            //@nelkaake The customer is already logged in so there is no need to create referral links
            if (Mage::getSingleton('rewards/session')->isCustomerLoggedIn()) {
                return $this;
            }

            $code_field = 'rewards_referral';
            $email_field = 'email';
            $firstname_field = 'firstname';
            $lastname_field = 'lastname';

            if (empty($data)) {
                $action = $o->getControllerAction();
                $request = $action->getRequest();
                $this->setRequest($request);
                $data = $request->getPost();
            }

            //@nelkaake Added on Thursday July 8, 2010: If a subfield (like billing) is needed, use it.
            if ($subfield) {
                if (isset($data[$subfield])) {
                    $data = $data[$subfield];
                }
            }

            //@nelkaake Added on Tuesday July 5, 2010: First some failsafe checks...
            if (empty($data)) {
                throw new Exception(
                        "Dispatched an event after the customer account creation method, " .
                        'but no data was found in app\code\community\TBT\RewardsReferral\Model\Observer\Createaccount.php ' .
                        "in TBT_RewardsReferral_Model_Observer_Createaccount::attemptReferralCheck."
                        , 1);
                return $this;
            }

            //@nelkaake Added on Thursday July 8, 2010: Was a code and e-mail passed?
            //@nelkaake (add) on 1/11/10: By default, use the textbox code/email.
            $use_field = true;
            if (!isset($data[$code_field])) {
                $use_field = false;
            } else {
                if (empty($data[$code_field])) {
                    $use_field = false;
                }
            }

            // If it's not set or if it's empty using the field, use the session
            if (!$use_field) {
                //@nelkaake Changed on Wednesday October 6, 2010: Change the code if the customer does
                if (Mage::helper('rewardsref/code')->getReferral()) {
                    $data[$code_field] = Mage::helper('rewardsref/code')->getReferral();
                } else {
                    $data[$code_field] = '';
                    //throw new Exception("Customer signup was detected with data, but the '{$code_field}' field was not detected.", 1);
                }
            }

            // If all the possible referral code options are empty or not set, exit the registration system.
            if (empty($data[$code_field])) {
                return $this;
            }

            if (!isset($data[$email_field])) {
                throw new Exception("Customer signup was detected with data and the 'rewards_referral', but the '{$email_field}' field was not detected.", 1);
            }
            if (!isset($data[$firstname_field])) {
                Mage::helper('rewardsref')->log("Customer '{$firstname_field}' was not detected, but other data was detected.");
                $data[$firstname_field] = ' ';
            }
            if (!isset($data[$lastname_field])) {
                Mage::helper('rewardsref')->log("Customer '{$lastname_field}' was not detected, but other data was detected.");
                $data[$lastname_field] = ' ';
            }

            //@nelkaake Added on Thursday July 8, 2010: Fetchthe required data and load the customer
            $referral_code_or_email = $data[$code_field];
            $new_customer_email = $data[$email_field];
            //@nelkaake Added on Thursday July 8, 2010: We use this method of getting the full name becuase Magento has it's own getName() logic.
            $new_customer_name = Mage::getModel('customer/customer')
                    ->setFirstname($data[$firstname_field])
                    ->setLastname($data[$lastname_field])
                    ->getName();


            // Let's make sure the referral entry is valid.
            $referral_email     = Mage::helper('rewardsref/code')->parseEmailFromReferralString($referral_code_or_email);
            $new_customer_email = Mage::helper('rewardsref/code')->parseEmailFromReferralString($new_customer_email);
            if ($referral_email == $new_customer_email) {
                throw new Exception("Customer with e-mail {$new_customer_email} tried to refer his/her self {$referral_email}.", 1);
            }

            Mage::helper('rewardsref/code')->setReferral($referral_code_or_email);
            Mage::helper('rewardsref')->initateSessionReferral2($new_customer_email, $new_customer_name);
        } catch (Exception $e) {
            Mage::helper('rewardsref')->log($e->getMessage());
            if ($e->getCode() != 1) {
                Mage::logException($e);
            }
        }
    }

    /**
     * Retrieve Customer Group for referred users from Sweet Tooth Referral configuration.
     * If Referral Configuration Customer Group is Blank then Default Magento Customer Group will be used.
     * @param int $store_id
     * @return int Customer Group Id
    */
    public function getCustomerGroupId($storeId)
    {
        $groupId = Mage::getStoreConfig('customer/create_account/default_group', $storeId);
        if (Mage::getStoreConfig('rewards/referral/customer_group', $storeId)) {
            $groupId = Mage::getStoreConfig('rewards/referral/customer_group', $storeId);
        }

        return $groupId;
    }

    /**
     * Observer method that gets called before saving customer model to set the Customer Group for referred customers
     * @param $observer
     * @return TBT_RewardsReferral_Model_Observer_Createaccount
     */
    public function setReferredCustomerGroup($observer)
    {
        $customer =  $observer->getEvent()->getCustomer();

        //Referred Customer Group will set only for new customers
        if ($customer->getId()) {
            return $this;
        }

        // check if this is a referred customer else return
        if (!Mage::getSingleton('rewardsref/referral')->referralExists($customer->getEmail())) {
            return $this;
        }

        //this method is executed 2x in 1.4.0.1 set registry var to indicate already ran
        if (Mage::registry('rewards_customer_before_save_observer_executed')) {
            return $this;
        }

        Mage::register('rewards_customer_before_save_observer_executed',true);

        // Retrieve Customer Group for referred users from Sweet Tooth Referral configuration
        $storeId = $this->getStoreId() ? $this->getStoreId() : Mage::app()->getStore()->getId();
        $groupId = $this->getCustomerGroupId($storeId);

        $customer->setData('group_id', $groupId);

        $rewardsCustomer = Mage::getModel('rewards/customer')->getRewardsCustomer( $customer );

        // if the referred customer is registering an account we used the below to retrieve customer in rewards Session
        // without this the customer will be null in session and validation will fail when registering account normally
        if (Mage::registry('rewards_referral_customer')) {
            Mage::unregister('rewards_referral_customer');
        }

        Mage::register('rewards_referral_customer',$rewardsCustomer);

        return $this;
    }

    /**
     * Set referral defult customer group for quote
     * Event: sales_quote_save_before
     *
     * @param $observer
     * @return TBT_RewardsReferral_Model_Observer_Createaccount
     */
    public function setQuoteRefCustomerGroup($observer)
    {
         $quote = $observer->getQuote();
         $this->setCheckoutRefCustomerGroup($quote);

         return $this;
    }

    /**
     * Set referral defult customer group for order
     * Event: sales_order_save_before
     *
     * @param $observer
     * @return TBT_RewardsReferral_Model_Observer_Createaccount
     */
    public function setOrderRefCustomerGroup($observer)
    {
         $order = $observer->getOrder();
         $this->setCheckoutRefCustomerGroup($order);
         Mage::getSingleton('core/session')->unsRegisterCheckoutMethod();

         return $this;
    }

    /**
     * Set referral defult customer group for order or quote
     * Event: sales_order_save_before
     *
     * @param $obj TBT_Rewards_Model_Sales_Quote || TBT_Rewards_Model_Sales_Order
     * @return TBT_RewardsReferral_Model_Observer_Createaccount
     */
    public function setCheckoutRefCustomerGroup($obj)
    {
         try {
              if (!Mage::getSingleton('core/session')->getRegisterCheckoutMethod()
                   || !($obj instanceof TBT_Rewards_Model_Sales_Quote || $obj instanceof TBT_Rewards_Model_Sales_Order)
                   || !Mage::getSingleton('rewardsref/referral')->referralExists($obj->getCustomerEmail())
                 ) {
                  return $this;
              }

              $storeId = $obj->getStoreId() ? $obj->getStoreId() : Mage::app()->getStore()->getId();
              $groupId = $this->getCustomerGroupId($storeId);

              $obj->setCustomerGroupId($groupId);

         } catch(Exception $e) {
              Mage::helper('rewards')->logException($e);
         }

         return $this;
    }

    /**
     * Observer validate referal email / code
     * @param $observer
     * @return TBT_RewardsReferral_Model_Observer_Createaccount
     */
    public function validateReferral($observer)
    {
        $storeId = $this->getStoreId() ? $this->getStoreId() : Mage::app()->getStore()->getId();

        if (!Mage::getStoreConfigFlag('rewards/referral/warning', $storeId)) {
            return $this;
        }

        try {
            $customer = $observer->getEvent()->getCustomer();
            $session = $this->_getSession();
            $error = "";

            $rewardsReferral = Mage::app()->getRequest()->getParam('rewards_referral');
            $rewardsReferral = trim($rewardsReferral);

            if (empty($rewardsReferral)) {
                return $this;
            }

            $customerEmail = Mage::app()->getRequest()->getParam('email');
            $codeHelper = Mage::helper('rewardsref/code');
            $customerModel = Mage::getModel('customer/customer');
            $customerModel->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
            $refDetails = null;

            if ($codeHelper->check_email_address($rewardsReferral)) {
                if ($rewardsReferral == $customerEmail) {
                    $error = Mage::helper('rewardsref')->__("Referral email same with customer account.");
                } else {
                    $refDetails = $customerModel->loadByEmail($rewardsReferral);
                }
            } else if (Mage::helper('rewardsref/shortcode')->isValid($rewardsReferral)) {
                $refEmail = Mage::helper('rewardsref/shortcode')->getEmail($rewardsReferral);
                $refDetails = $customerModel->loadByEmail($refEmail);
            } else {
                $refEmail = $codeHelper->getEmail($rewardsReferral);

                if ($codeHelper->check_email_address($refEmail)) {
                    $refDetails = $customerModel->loadByEmail($refEmail);
                } else {
                    $error = Mage::helper('rewardsref')->__("Please enter valid referral code or email.");
                }
            }

            if (empty($error) && !$refDetails->getId()) {
                 $error = Mage::helper('rewardsref')->__("Referral email not yet registered on the store.");
            }

            if (!empty($error)) {
                throw new Exception($error);
            }

        } catch (Exception $e) {
            Mage::throwException($e->getMessage());
        }

        return $this;
    }

    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

}

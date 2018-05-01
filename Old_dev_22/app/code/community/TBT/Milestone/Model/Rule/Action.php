<?php

/**
 * @method int getCustomerId()
 */
class TBT_Milestone_Model_Rule_Action extends Varien_Object
{
    /**
     *
     * @var null|TBT_Milestone_Model_Rule
     * @see TBT_Milestone_Model_Rule_Action::setRule()
     */
    protected $_rule = null;

    /**
     * Based on available data will generate a description for the conditions of this milestone.
     *
     * @example "milestone for placing 2 orders"
     * @return string appropriate to comeplete the following sentence 'Points received by reaching a ...';
     *
     */
    public function getMilestoneDescription()
    {
        $milestoneDescription = $this->getData('milestone_description');
        $milestoneDescription = !empty($milestoneDescription) ? $milestoneDescription : $this->getRuleCondition()->getMilestoneDescription();
        $milestoneDescription = !empty($milestoneDescription) ? $milestoneDescription : $this->getRuleCondition()->getMilestoneName();
        $milestoneDescription = !empty($milestoneDescription) ? $milestoneDescription : "milestone";

        return $milestoneDescription;
    }

    /**
     * Based on what's supplied in the class and the notification settings passed through by the conditions,
     * posts the success message for this action.
     *
     * @return self
     */
    public function notifySuccess()
    {
        $notificationSettings = $this->getRuleCondition()->getNotificationSettings();
        if (empty($notificationSettings)){
            return $this;
        }

        if ($notificationSettings['frontend'] && !$this->_getHelper()->isInAdminMode()){
            $successMessage = $this->_getFrontendSuccessMessage();
            if (!empty($successMessage)){
                Mage::getSingleton ('core/session')->addSuccess($successMessage);
            }
        }

        if ($notificationSettings['backend'] && $this->_getHelper()->isInAdminMode()){
            $successMessage = $this->_getBackendSuccessMessage();
            if (!empty($successMessage)){
                Mage::getSingleton ('core/session')->addSuccess($successMessage);
            }
        }

        if ($notificationSettings['email']
            || ($notificationSettings['email_not_logged'] && Mage::helper('rewards')->getIsAdmin())
        ) {
            $successMessage = $this->_getEmailSuccessMessage();
            if (!empty($successMessage)){
                $customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
                $template = $this->_getHelper('config')->getEmailTemplate($this->getRule()->getConditionType(), $customer->getStoreId());
                if ($template != "none"){
                    $emailSent = $this->_sendEmail($customer, $template, $successMessage);
                    if (!$emailSent){
                        throw new Exception("Was not able to send milestone email to customer #{$this->getCustomerId()}");
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Uses Magento Transactional System to send an email with milestone details and message
     * @param Mage_Customer_Model_Customer $customer
     * @param int|string $template. Name or Id of the template to use
     * @param string $message. Optional message to send
     *
     * @return boolean. Whether sending email failed or not.
     */
    protected function _sendEmail($customer, $template, $message = null)
    {
        /* @var $translate Mage_Core_Model_Translate */
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        /* @var $email Mage_Core_Model_Email_Template */
        $email = Mage::getModel('core/email_template');
        $sender = array(
                'name' => strip_tags($this->_getHelper('config')->getMailSenderName($customer->getStoreId())),
                'email' => strip_tags($this->_getHelper('config')->getMailSenderEmail($customer->getStoreId()))
        );

        $email->setDesignConfig(array(
                'area' => 'frontend',
                'store' => $customer->getStoreId())
        );
        $rewardsCustomer = Mage::getModel('rewards/customer')->getRewardsCustomer($customer);

        $vars = array(
                'logo_url'                            => Mage::getDesign()->getSkinUrl(Mage::getStoreConfig('design/header/logo_src')),
                'logo_alt'                            => Mage::getStoreConfig('design/header/logo_alt'),
                'customer_name'                       => $customer->getName(),
                'customer_firstname'                  => $customer->getFirstname(),
                'customer_email'                      => $customer->getEmail(),
                'customer_points_balance'             => (string) $rewardsCustomer->getPointsSummary(),
                'customer_pending_points'             => (string) $rewardsCustomer->getPendingPointsSummary(),
                'customer_has_pending_points'         => $rewardsCustomer->hasPendingPoints(),
                'customer_affiliate_url'              => (string) Mage::helper('rewardsref/url')->getUrl($rewardsCustomer),
                'customer_referral_code'              => (string) Mage::helper('rewardsref/code')->getCode($rewardsCustomer->getEmail()),
                'customer_referral_shortcode'         => (string) Mage::helper('rewardsref/shortcode')->getCode($rewardsCustomer->getId()),
                'store_name'                          => $customer->getStore()->getName(),
                'milestone_description'               => $this->getRuleCondition()->getMilestoneDescription(),
                'milestone_message'                   => !empty($message) ? $message : "",
                'milestone_target'                    => $this->getRule()->getCondition()->getThreshold(),
                'milestone_details_condition'         => $this->getRule()->getConditionType(),
                'milestone_details_action'            => $this->getRule()->getActionType(),
                'milestone_details_points_amount'     => $this->getPointsAmount(),
                'milestone_details_points_string'     => $this->getPointsObject(),
                'milestone_details_customer_group_id' => $this->getCustomerGroupId(),
        );

        $email->sendTransactional($template, $sender, $customer->getEmail(), $customer->getName(), $vars);
        $translate->setTranslateInline(true);
        return $email->getSentSuccess();
    }

    /**
     * @return TBT_Milestone_Model_Rule_Condition
     */
    public function getRuleCondition()
    {
        return $this->getRule()->getCondition();
    }

    /**
     * @return string. Message to display to the customer on the front-end upon success of the action.
     */
    protected function _getFrontendSuccessMessage()
    {
        return "";
    }

    /**
     * @return string. Message to display to the customer on the front-end upon success of the action.
     */
    protected function _getBackendSuccessMessage()
    {
        return "";
    }

    /**
     * @return string. Message to display to the customer on the front-end upon success of the action.
     */
    protected function _getEmailSuccessMessage()
    {
        return "";
    }

    /**
     * @return TBT_Milestone_Helper_Data
     */
    protected function _getHelper($type = null)
    {
        $helper = is_null($type) ? "data" : $type;
        return Mage::helper("tbtmilestone/{$helper}");
    }

    /**
     * Pass in the rule object by refrence.
     * @param TBT_Milestone_Model_Rule $rule
     * @return self
     */
    public function setRule(&$rule)
    {
        $this->_rule = $rule;

        return $this;
    }

    /**
     * Once this class is insatiated by the Rule object, it's reference becomes available.
     * @return TBT_Milestone_Model_Rule
     */
    public function getRule()
    {
        return $this->_rule;
    }

}

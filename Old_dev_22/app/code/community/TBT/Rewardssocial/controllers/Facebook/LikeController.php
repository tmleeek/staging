<?php
/**
 * WDCA - Sweet Tooth
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the WDCA SWEET TOOTH POINTS AND REWARDS
 * License, which extends the Open Software License (OSL 3.0).
 * The Sweet Tooth License is available at this URL:
 *      https://www.sweettoothrewards.com/terms-of-service
 * The Open Software License is available at this URL:
 *      http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * By adding to, editing, or in any way modifying this code, WDCA is
 * not held liable for any inconsistencies or abnormalities in the
 * behaviour of this code.
 * By adding to, editing, or in any way modifying this code, the Licensee
 * terminates any agreement of support offered by WDCA, outlined in the
 * provided Sweet Tooth License.
 * Upon discovery of modified code in the process of support, the Licensee
 * is still held accountable for any and all billable time WDCA spent
 * during the support process.
 * WDCA does not guarantee compatibility with any other framework extension.
 * WDCA is not responsbile for any inconsistencies or abnormalities in the
 * behaviour of this code if caused by other framework extension.
 * If you did not receive a copy of the license, please send an email to
 * support@sweettoothrewards.com or call 1.855.699.9322, so we can send you a copy
 * immediately.
 *
 * @category   [TBT]
 * @package    [TBT_Rewards]
 * @copyright  Copyright (c) 2014 Sweet Tooth Inc. (http://www.sweettoothrewards.com
 */

/**
 * Customer Controller
 *
 * @category   TBT
 * @package    TBT_Rewards
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */
class TBT_Rewardssocial_Facebook_LikeController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {

        if (Mage::getConfig()->getModuleConfig('TBT_Rewards')->is('active', 'false')) {
            throw new Exception(Mage::helper('rewardssocial')->__("Sweet Tooth must be installed on the server in order to use the Sweet Tooth Social system."));
        }
        die(Mage::helper('rewardssocial')->__("If you're seeing this page it confirms that Sweet Tooth is installed and the Sweet Tooth Social system is ready for use."));

        return $this;
    }

    public function encryptAction()
    {
        $input = $this->getRequest()->getParam('input');
        echo Mage::helper('rewardssocial/crypt')->encrypt($input);
        exit;
    }

    /**
     * For liking and unliking products
     */
    public function onLikeAction()
    {
        try {
            $action = $this->getRequest()->getParam('action', 'unlike');
            $page_key = $this->getRequest()->getParam('page_key');

            // Check if customer is not logged in display a message and don't do any liking actions
            if ( ! $this->_rs()->isCustomerLoggedIn() ) {
                if ( $action == 'like' ) {
                    throw new Exception($this->__("You must be logged in for us to reward you for Facebook-Liking a page!"), 110);
                }
            }

            // Pull variables from the request
            $liked_url = Mage::helper('rewardssocial/crypt')->decrypt($page_key); //encryption allows us to protect against programatic LIKING
            $facebook_account_id = - 1; // until we can get the facebook ID let's use -1
            $customer = $this->_rs()->getCustomer();

            if ( $action == 'like' ) {
                $this->_getFacebookLikeValidator()->initReward($facebook_account_id, $liked_url, $customer);
            } elseif ( $action == 'unlike' ) {
                $this->_getFacebookLikeValidator()->cancelLikeRewards($facebook_account_id, $liked_url, $customer);
            } else {
                // Do nothing because the action specified was invalid.
                Mage::helper('rewards/debug')->log( "Invalid Facebook LIKE action specified '{$action}' in TBT_Rewardssocial_Facebook_LikeController::onLikeAction(). Customer was not rewarded and facebook LIKE was not acknowledge by rewards system.");
            }

            $message = "";
            if ($action == 'like') {
                $message = $this->__("Thanks for Liking this page!");
                $predictedPoints = $this->_getFacebookLikeValidator()->getPredictedFacebookLikePoints();
                if (count($predictedPoints) > 0) {
                    $pointsString = (string) Mage::getModel('rewards/points')->set($predictedPoints);
                    $message = $this->__("You've earned <b>%s</b> for Liking this page!", $pointsString);
                }
            }

            $this->_jsonSuccess(array(
                'success' => true,
                'message' => $message
            ));
        } catch ( Exception $ex ) {
            // if error code > 100, it's user one and can be displayed
            if ($ex->getCode() > 100) {
                $message = $ex->getMessage();
            } else {
                $message = $this->__('There was a problem trying to reward you for liking this page on Facebook.<br/>Try again and contact us if you still encounter this issue.');
                // log the exception
                Mage::helper('rewards')->logException("There was a problem (un)liking a page ({$liked_url}) on Facebook for customer {$customer->getEmail()} (ID: {$customer->getId()}): ".
                    $ex->getMessage());
            }

            $this->_jsonError(array(
                'success' => false,
                'message' => $message
            ));
        }

        return $this;
    }

    protected function _getSimpleMsgResponseHtml() {

         $result = array();
         $result [Mage_Core_Model_Message::ERROR] = array();
         $result [Mage_Core_Model_Message::NOTICE] = array();
         $result [Mage_Core_Model_Message::SUCCESS] = array();
         $result [Mage_Core_Model_Message::WARNING] = array();

         $all_msgs = $this->_cs()->getMessages(true)->getItems();

         foreach( $all_msgs  as $msg ) {
             $response_block = Mage::getBlockSingleton('rewardssocial/facebook_like_notificationblock_response');
             $response_block->setMsg($msg);
             $response_html = $response_block->toHtml();

             return $response_html;
         }

         return "";
    }

    protected function _ajaxRedirectResponse()
    {
        $this->getResponse()
            ->setHeader('HTTP/1.1', '403 Session Expired')
            ->setHeader('Login-Required', 'true')
            ->sendResponse();
        return $this;
    }



    /**
     * @return TBT_Rewardssocial_Model_Facebook_Like_Validator
     */
    protected function _getFacebookLikeValidator() {
        return Mage::getSingleton('rewardssocial/facebook_like_validator');
    }
    /**
     * @return TBT_Rewards_Model_Session
     */
    protected function _rs() {
        return Mage::getSingleton('rewards/session');
    }


    /**
     * @return Mage_Core_Model_Session
     */
    protected function _cs() {
        return Mage::getSingleton('core/session');
    }

    protected function _jsonSuccess($response)
    {
        $this->getResponse()->setBody(json_encode($response));
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        return $this;
    }

    protected function _jsonError($response)
    {
        return $this->_jsonSuccess($response);
    }
}
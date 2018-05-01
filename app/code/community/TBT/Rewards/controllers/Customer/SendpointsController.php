<?php

/**
 * WDCA - Sweet Tooth
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the WDCA SWEET TOOTH POINTS AND REWARDS 
 * License, which extends the Open Software License (OSL 3.0).

 * The Open Software License is available at this URL: 
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright  Copyright (c) 2014 Sweet Tooth Inc. (http://www.sweettoothrewards.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer Send Points Controller
 *
 * @category   TBT
 * @package    TBT_Rewards
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */
class TBT_Rewards_Customer_SendpointsController extends Mage_Core_Controller_Front_Action {
	
	public function preDispatch() {
		parent::preDispatch ();
	}
	
	public function sendAction() {
		try {
			$friend_email = $this->getRequest ()->get ( 'friend_email' );
			$points_amt = $this->getRequest ()->get ( 'points_amt' );
			$currency_id = $this->getRequest ()->get ( 'currency_id' );
			$personal_comment = strip_tags ( $this->getRequest ()->get ( 'personal_comment' ) );
			
			if (! $friend_email || $friend_email === '') {
				throw new Exception ( 'You must enter a friend\'s email address to send them points!' );
			}
			
			if (! $this->isValidEmail ( $friend_email )) {
				throw new Exception ( 'The e-mail addres you entered is invalid.' );
			}
			
			if (! $points_amt || $points_amt <= 0) {
				throw new Exception ( 'You must enter a valid number of points to send your friend.' );
			}										
			
			if ($currency_id == null) {
				if (Mage::getSingleton ( 'rewards/session' )->getCustomer ()->getNumCurrencies () == 1) {
					$currency_ids = Mage::getSingleton ( 'rewards/currency' )->getAvailCurrencyIds ();
					$currency_id = $currency_ids [0];
				} else {
					throw new Exception ( 'You must enter a valid currency!' );
				}
			}
			
			$friend = Mage::getModel ( 'rewards/customer' )->setWebsiteId ( Mage::app ()->getWebsite ()->getId () )->loadByEmail ( $friend_email );
			$sender = Mage::getSingleton ( 'rewards/session' )->getSessionCustomer ();
			$points_sent_string = Mage::getModel ( 'rewards/points' )->set ( $currency_id, $points_amt )->__toString();
			
			//Verify the sender has enough points of given type.
			if (! $sender->getId ()) {
				Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You must log in or sign up before sending points to a friend!' ) );
				$this->_redirect ( 'customer/login' );
				return $this;
			}
			$balance = $sender->getUsablePointsBalance ( $currency_id );
			if (! $balance) {
				$balance = 0;
			}
			if ($points_amt > $balance) {
				Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You cannot send %s to your friend when you only have %s!', Mage::getModel ( 'rewards/points' )->set ( $currency_id, $points_amt ), Mage::getModel ( 'rewards/points' )->set ( $currency_id, $balance ) ) );
				$this->_redirect ( 'rewards/customer/' );
				return $this;
			}
			
			//Verify if friend exists. If not and option enabled in Sweet Tooth configuration an invitation email to the store is sent to the user			
			$friend_id = $friend->getId ();
			if (! $friend_id) {			
			 	if (Mage::helper('rewards/config')->getSendInvitationEmailToUnregisteredFriend($sender->getStoreId())) {
			 		if (!$this->notifyFriendEmail($sender, $friend_email, $points_sent_string, $personal_comment, true)) {
			 			Mage::helper('rewards')->log($this->__("Error sending invitation email to %s to register an account and never miss points again (%s from %s (%s)).", $friend_email, $points_sent_string, $sender->getName(), $sender->getEmail()));
			 			Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( "There is no customer with that email address (%s).", $friend_email ) );
			 		} else {
				 		Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( "There is no customer with that email address (%s). An invitation to the store was sent, but no points.", $friend_email ) );
			 		}					
			 	} else {
			 		Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( "There is no customer with that email address (%s).", $friend_email ) );
			 	}
				$this->_redirect ( 'rewards/customer/' );
				return $this;
			}				
			
			//Verify that the friend can receive points of that type
			/* TODO WDCA - point currencies are not yet specific to customer groups */
			if (! $friend->hasCurrencyId ( $currency_id )) {
				$currency_caption = Mage::getSingleton ( 'rewards/currency' )->getCurrencyCaption ( $currency_id );
				Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( '%s is not allowed to use the %s points currency.', $friend->getName (), $currency_caption ) );
				$this->_redirect ( 'rewards/customer/' );
				return $this;
			}						
						
			if ($friend_email == $sender->getEmail ()) {
				Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( "You cannot send points to yourself!!" ) );
				$this->_redirect ( 'rewards/customer/' );
				return $this;
			}
			
			if (! $personal_comment || empty ( $personal_comment )) {
				$personal_comment = '';
			}
			
			$is_transfer_successful = Mage::helper ( 'rewards/transfer' )->transferPointsToFriend ( $points_amt, $currency_id, $friend_id, $personal_comment );
			if ($is_transfer_successful) {
				Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'You have successfully sent %s to %s!', $points_sent_string, $friend->getName () ) );
				
				if (!$this->notifyFriendEmail($sender, $friend, $points_sent_string, $personal_comment)) {
					Mage::helper('rewards')->log(Mage::helper('rewards')->__("Error sending email notification to %s (%s) for receiving %s points from %s (%s).", $friend->getName(), $friend->getEmail(), $points_sent_string, $sender->getName(), $sender->getEmail()));
				}
			} else {
				throw new Exception ( 'Points could not be sent to your friend.' );
			}
		} catch ( Exception $ex ) {
			Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( $ex->getMessage () ) );
			$this->_redirect ( 'rewards/customer/' );
			return $this;
		}
		
		$this->_redirect ( 'rewards/customer/' );
		return;
	}
	
	private function isValidEmail($email) {
		$validator = new Zend_Validate_EmailAddress ();
		return $validator->isValid ( $email );
	}
	
	/**
	* Notification email sent to customer receiving points through 'Send Points to a Friend'
	* 
	* @param TBT_Rewards_Model_Customer $customer 	Customer sending points
	* @param TBT_Rewards_Model_Customer $friend 	Customer receiving points or user's email (if not registered yet)
	* @param type $pointsString 					Amount of points transferred
	* @param type $personal_comment 				Customer's sending points personal message to his friend
	* @param type $invitation						True if user receiving points not registered yet, so sending invitation instead of notification
	*/
	public function notifyFriendEmail($customer, $friend, $pointsString, $personal_comment='', $invitation = false) {
		/* @var $translate Mage_Core_Model_Translate */
		$translate = Mage::getSingleton('core/translate');
		$translate->setTranslateInline(false);
		/* @var $email Mage_Core_Model_Email_Template */
		$email = Mage::getModel('core/email_template');
	
		$sender = array(
				            'name' => strip_tags(Mage::helper('rewards/config')->getSenderName($customer->getStoreId())),
				            'email' => strip_tags(Mage::helper('rewards/config')->getSenderEmail($customer->getStoreId()))
		);
	
		$email->setDesignConfig(array(
				            'area' => 'frontend',
				            'store' => $customer->getStoreId())
		);
	
		if ($invitation) {
			$vars = array(
							            'customer_name' => $customer->getName(),
							            'customer_email' => $customer->getEmail(),							            
							            'friend_email' => $friend,
							            'store_name' => $customer->getStore()->getName(),
							            'points_transferred' => $pointsString,
							            'comment' => $personal_comment		         
			);
			$template = Mage::helper('rewards/config')->getFriendInvitationEmailTemplate($customer->getStoreId());
			$email->sendTransactional($template, $sender, $friend, '', $vars);
			
		} else { 
			$vars = array(		
				            'customer_name' => $customer->getName(),
				            'customer_email' => $customer->getEmail(),
				            'friend_name' => $friend->getName(),
				            'friend_email' => $friend->getEmail(),
				            'store_name' => $customer->getStore()->getName(),
				            'points_transferred' => $pointsString,
				            'comment' => $personal_comment		         
			);
			$template = Mage::helper('rewards/config')->getFriendNotificationEmailTemplate($customer->getStoreId());			
			$email->sendTransactional($template, $sender, $friend->getEmail(), $friend->getName(), $vars);
		}
	
		$translate->setTranslateInline(true);
		return $email->getSentSuccess();
	}
}
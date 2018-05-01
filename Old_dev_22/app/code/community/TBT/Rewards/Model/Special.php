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
 * Special Rule Model
 *
 * @category   TBT
 * @package    TBT_Rewards
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */
class TBT_Rewards_Model_Special extends Mage_Rule_Model_Rule implements TBT_Rewards_Model_Migration_Importable {
	
	public function _construct() {
		parent::_construct ();
		$this->_init ( 'rewards/special' );
	}
	
	public function _afterLoad() {
		
		// TODO: Mage_Rule_Model_Rule _afterLoad isn't compatible with how we are handling conditions... find a way to fix it
		//parent::_afterLoad ();
		$this->setPointsConditions ( Mage::helper ( 'rewards' )->unhashIt ( $this->getConditionsSerialized () ) );
	}
	
	public function _beforeSave() {
		
		// TODO: Mage_Rule_Model_Rule _beforeSave isn't compatible with how we are handling conditions... find a way to fix it
		//parent::_beforeSave ();
		$this->setConditionsSerialized ( Mage::helper ( 'rewards' )->hashIt ( $this->getPointsConditions () ) );
	}
	
	/**
	 * Checks to see if the website id is applicable to this rule
	 * @param integer $website_id
	 * @return boolean      : true if the website is applicable to this rule, false otherwise
	 */
	public function isApplicableToWebsite($website_id) {
		return array_search ( $website_id, explode ( ',', $this->getWebsiteIds () ) ) !== false;
	}
	
	/**
	 * Use this function so that dates are correcly read in from post.
	 *
	 * @param array $rule
	 * @return unknown
	 */
	public function loadPost(array $rule) {
		foreach ( $rule as $key => $value ) {
			/**
			 * convert dates into Zend_Date
			 */
			if (in_array ( $key, array ('from_date', 'to_date' ) ) && $value) {
				$value = Mage::app ()->getLocale ()->date ( $value, Mage::app ()->getLocale ()->getDateFormat ( Mage_Core_Model_Locale::FORMAT_TYPE_SHORT ), null, false );
			}
			$this->setData ( $key, $value );
		}
		return $this;
	}
	
	/**
	 * Forcefully Save object data even if ID does not exist
	 * Used for migrating data and ST campaigns.     
	 *
	 * @return Mage_Core_Model_Abstract
	 */
	public function saveWithId() {
		$real_id = $this->getId ();
		$exists = Mage::getModel ( $this->_resourceName )->setId ( null )->load ( $real_id )->getId ();
		
		if (! $exists) {
			$this->setId ( null );
		}
		
		$this->save ();
		
		if (! $exists) {
			$write = Mage::getSingleton ( 'core/resource' )->getConnection ( 'core_write' );
			$write->update ( $this->_getResource ()->getMainTable (), array ($this->_getResource ()->getIdFieldName () => $real_id ), array ("`{$this->_getResource()->getIdFieldName()}` = {$this->getId()}" ) );
			$write->commit ();
		}
		
		return $this;
	}

}
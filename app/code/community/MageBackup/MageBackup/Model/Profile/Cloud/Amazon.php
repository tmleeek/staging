<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup profile cloud s3 model.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Model_Profile_Cloud_Amazon {

	public static function getRegionsArray() {
		return array(
			'us-east-1'			=> Mage::helper('magebackup')->__('US East (N. Virginia)'),
			'us-west-1'			=> Mage::helper('magebackup')->__('US West (N. California)'),
			'us-west-2'			=> Mage::helper('magebackup')->__('US West (Oregon)'),
			'eu-west-1'			=> Mage::helper('magebackup')->__('EU (Ireland)'),
			'eu-central-1'		=> Mage::helper('magebackup')->__('EU (Frankfurt)'),
			'ap-northeast-1'	=> Mage::helper('magebackup')->__('Asia Pacific (Tokyo)'),
			'ap-northeast-2'	=> Mage::helper('magebackup')->__('Asia Pacific (Seoul)'),
			'ap-southeast-1'	=> Mage::helper('magebackup')->__('Asia Pacific (Singapore)'),
			'ap-southeast-2'	=> Mage::helper('magebackup')->__('Asia Pacific (Sydney)'),
			'sa-east-1'			=> Mage::helper('magebackup')->__('South America (São Paulo)'),
		);
	}

	public static function getStorageClassesArray() {
		return array(
			'STANDARD'				=> Mage::helper('magebackup')->__('Standard Storage'),
			'REDUCED_REDUNDANCY'	=> Mage::helper('magebackup')->__('Reduced Redundancy Storage (RRS)'),
			'STANDARD_IA'			=> Mage::helper('magebackup')->__('Standard - Infrequent Access Storage (Standard_IA)'),
		);
	}

	public function getGlacierRegionsArray() {
		return array(
			'us-east-1'			=> Mage::helper('magebackup')->__('US East (N. Virginia)'),
			'us-west-1'			=> Mage::helper('magebackup')->__('US West (N. California)'),
			'us-west-2'			=> Mage::helper('magebackup')->__('US West (Oregon)'),
			'eu-west-1'			=> Mage::helper('magebackup')->__('EU (Ireland)'),
			'eu-central-1'		=> Mage::helper('magebackup')->__('EU (Frankfurt)'),
			'ap-northeast-1'	=> Mage::helper('magebackup')->__('Asia Pacific (Tokyo)'),
			'ap-northeast-2'	=> Mage::helper('magebackup')->__('Asia Pacific (Seoul)'),
			'ap-southeast-2'	=> Mage::helper('magebackup')->__('Asia Pacific (Sydney)'),
		);
	}
}
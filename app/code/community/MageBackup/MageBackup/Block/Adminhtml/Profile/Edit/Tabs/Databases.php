<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup adminhtml profile edit tab databases block.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Block_Adminhtml_Profile_Edit_Tabs_Databases extends Mage_Adminhtml_Block_Widget_Tabs {

	public function __construct() {
		parent::__construct();

		$this->setTemplate('magebackup/profile/edit/databases.phtml');
	}

	/**
	 * Retrieve currently edited profile
	 *
	 * @return MageZone_Pack_Model_Profile
	 */
	public function getProfile() {
		return Mage::registry('magebackup/profile');
	}
}
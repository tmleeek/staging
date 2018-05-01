<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup adminhtml profile backup type renderer.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Block_Adminhtml_Profile_Renderer_Backuptype extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

	/**
	 * Render.
	 */
	public function render(Varien_Object $item) {
		$profile	= Mage::getSingleton('magebackup/profile')->load($item->getId());
		$type		= (int) $profile->getValue('backup_type');
		$types		= Mage::getSingleton('magebackup/profile_backuptype')->getBackupTypesArray();

		return isset($types[$type]) ? $types[$type] : $types[0];
	}
}
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
class MageBackup_MageBackup_Block_Adminhtml_Profile_Renderer_Cronenable extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

	/**
	 * Render.
	 */
	public function render(Varien_Object $item) {
		$profile	= Mage::getSingleton('magebackup/profile')->load($item->getId());
		$enable		= $profile->getValue('cron_enable');

		if ($enable) {
			$frequencies	= Mage::getSingleton('magebackup/profile_cron')->getCronFrequenciesArray();
			$frequency		= $profile->getValue('cron_frequency');

			if (isset($frequencies[$frequency])) {
				return $frequencies[$frequency];
			}
		}

		return Mage::helper('magebackup')->__('No');
	}
}
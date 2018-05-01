<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup backup duration renderer.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Block_Adminhtml_Backup_Renderer_Duration extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

	/**
	 * Render.
	 */
	public function render(Varien_Object $item) {
		if (!$item->getStartTime() || !$item->getEndTime()) {
			return '-';
		}

		$runtime		= strtotime($item->getEndTime()) - strtotime($item->getStartTime());
		$runtimeSec		= $runtime % 60;
		$runtimeMin		= (int) ($runtime / 60) % 60;
		$runtimeHour	= (int) $runtime / 60 / 60;

		return Mage::helper('magebackup')->__('%dh %dm %ds', $runtimeHour, $runtimeMin, $runtimeSec);
	}
}
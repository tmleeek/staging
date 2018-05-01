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
class MageBackup_MageBackup_Block_Adminhtml_Backup_Renderer_Namedescription extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

	/**
	 * Render.
	 */
	public function render(Varien_Object $item) {
		$html	= array();
		$html[]	= '<div class="mb-backup-name">';
		$html[]	= $item->getName();
		$html[]	= '</div>';

		if ($item->getDescription()) {
			$html[]	= '<div class="mb-backup-description">';
			$html[]	= $item->getDescription();
			$html[]	= '</div>';
		}

		return implode("\n", $html);
	}
}
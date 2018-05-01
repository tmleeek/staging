<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup backup download renderer.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Block_Adminhtml_Backup_Renderer_Download extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

	/**
	 * Render.
	 */
	public function render(Varien_Object $item) {
		$filePath	= $item->getFilePath();
		$multipart	= $item->getMultipart();
		$html		= array();
		$html[]		= $item->getFileName();
		$html[]		= '<div class="mb-download-container">';

		if ($multipart <= 1 && is_file($filePath)) {
			$html[]	= '<a href="' . $this->getUrl('*/*/download', array('id' => $item->getId())) . '">' . Mage::helper('magebackup')->__('Download') . '</a>';
		} else {
			for ($i = 0; $i < $multipart; $i++) {
				$file	= Mage::helper('magebackup')->getZipPart($filePath, $i);

				if (is_file($file)) {
					$html[]	= '<a href="' . $this->getUrl('*/*/download', array('id' => $item->getId(), 'part' => $i)) . '">' . Mage::helper('magebackup')->__('Part %d', $i) . '</a>';
				}
			}
		}


		$profile	= Mage::getModel('magebackup/profile')->load($item->getProfileId());
		$logFile	= $profile->getLogDir() . '/magebackup.id.' . $item->getId() . '.log';

		if (file_exists($logFile)) {
			$html[]	= '<a href="' . $this->getUrl('*/*/downloadLog', array('id' => $item->getId())) . '">' . Mage::helper('magebackup')->__('Download Log') . '</a>';
		}

		$html[]	= '</div>';

		return implode("\n", $html);
	}
}
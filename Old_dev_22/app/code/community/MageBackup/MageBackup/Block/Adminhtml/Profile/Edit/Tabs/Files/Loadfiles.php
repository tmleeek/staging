<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup adminhtml profile edit load files block.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Block_Adminhtml_Profile_Edit_Tabs_Files_LoadFiles extends Mage_Adminhtml_Block_Template {

	public function _toHtml() {
		$root	= Mage::getBaseDir() . DS;
		$dir	= $this->getRequest()->getPost('dir');

		if (file_exists($root . $dir)) {
			$files = scandir($root . $dir);
			natcasesort($files);

			if (count($files) > 2) { /* The 2 accounts for . and .. */
				echo '<ul class="jqueryFileTree" style="display: none;">';

				// All dirs
				foreach ($files as $file) {
					if (file_exists($root . $dir . $file) && $file != '.' && $file != '..' && is_dir($root . $dir . $file)) {
						echo '<li class="directory collapsed">'
							. '<button type="button" class="mb-button mb-folder-exclude" data-value="' . htmlentities($dir . $file) . '" title="' . $this->__('Exclude Directory') . '"><i></i></button>'
							. ' <button type="button" class="mb-button mb-subfolders-skip" data-value="' . htmlentities($dir . $file) . '" title="' . $this->__('Skip Subdirectories') . '"><i></i></button>'
							. ' <button type="button" class="mb-button mb-files-skip" data-value="' . htmlentities($dir . $file) . '" title="' . $this->__('Skip Files') . '"><i></i></button>'
							. ' <a href="#" rel="' . htmlentities($dir . $file) . '/">' . htmlentities($file) . '</a>'
							. ' </li>'
						;
					}
				}

				// All files
				foreach ($files as $file) {
					if (file_exists($root . $dir . $file) && $file != '.' && $file != '..' && !is_dir($root . $dir . $file)) {
						$ext = preg_replace('/^.*\./', '', $file);

						echo '<li class="file ext_' . $ext . '">'
							. ' <button type="button" class="mb-button mb-file-exclude" data-value="' . htmlentities($dir . $file) . '" title="' . $this->__('Exclude File') . '"><i></i></button>'
							. ' <a href="#" rel="' . htmlentities($dir . $file) . '">' . htmlentities($file) . '</a>'
							. ' </li>'
						;
					}
				}

				echo '</ul>';
			}

		}
	}
}
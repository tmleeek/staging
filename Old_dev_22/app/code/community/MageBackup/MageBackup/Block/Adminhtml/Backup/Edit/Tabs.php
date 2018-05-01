<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup adminhtml backup edit tabs block.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Block_Adminhtml_Backup_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::_construct();

		$this->setId('profile_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('magebackup')->__('Backup Information'));
	}

	protected function _beforeToHtml() {
		$this->addTab('general_information', array(
			'label'		=> Mage::helper('magebackup')->__('General Information'),
			'title'		=> Mage::helper('magebackup')->__('General Information'),
			'content'	=> $this->getLayout()->createBlock('magebackup/adminhtml_backup_edit_tabs_general')->toHtml(),
		));

		return parent::_beforeToHtml();
	}
}
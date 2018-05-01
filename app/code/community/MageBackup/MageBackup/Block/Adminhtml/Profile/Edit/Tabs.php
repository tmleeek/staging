<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup adminhtml profile edit tabs block.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Block_Adminhtml_Profile_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::_construct();

		$this->setId('profile_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('magebackup')->__('Profile Information'));
	}

	protected function _beforeToHtml() {
		$this->addTab('general_configuration', array(
			'label'		=> Mage::helper('magebackup')->__('General Configuration'),
			'title'		=> Mage::helper('magebackup')->__('General Configuration'),
			'content'	=> $this->getLayout()->createBlock('magebackup/adminhtml_profile_edit_tabs_general')->toHtml(),
		));

		$this->addTab('cloud_configuration', array(
			'label'		=> Mage::helper('magebackup')->__('Cloud Configuration'),
			'title'		=> Mage::helper('magebackup')->__('Cloud Configuration'),
			'content'	=> $this->getLayout()->createBlock('magebackup/adminhtml_profile_edit_tabs_cloud')->toHtml(),
		));

		$this->addTab('files_configuration', array(
			'label'		=> Mage::helper('magebackup')->__('File Configuration'),
			'title'		=> Mage::helper('magebackup')->__('File Configuration'),
			'content'	=> $this->getLayout()->createBlock('magebackup/adminhtml_profile_edit_tabs_files')->toHtml(),
		));

		$this->addTab('databases_configuration', array(
			'label'		=> Mage::helper('magebackup')->__('Database Configuration'),
			'title'		=> Mage::helper('magebackup')->__('Database Configuration'),
			'content'	=> $this->getLayout()->createBlock('magebackup/adminhtml_profile_edit_tabs_databases')->toHtml(),
		));

		return parent::_beforeToHtml();
	}
}
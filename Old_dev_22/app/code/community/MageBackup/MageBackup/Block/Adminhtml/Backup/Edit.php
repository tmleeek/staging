<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup adminhtml backup edit block.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Block_Adminhtml_Backup_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->_objectId	= 'id';
		$this->_blockGroup	= 'magebackup';
		$this->_controller	= 'adminhtml_backup';
		$backup				= Mage::registry('magebackup/backup');

		if ($backup && $backup->getId()) {
			$this->_updateButton('save', 'label', Mage::helper('magebackup')->__('Save'));

			$this->_addButton('save_and_continue', array(
				'label'		=> Mage::helper('magebackup')->__('Apply'),
				'onclick'	=> 'saveAndContinueEdit()',
				'class'		=> 'save',
			), -100);

			$this->_formScripts[] = "
				function saveAndContinueEdit() {
					editForm.submit($('edit_form').action + 'back/edit/');
				}
			";
		} else {
			$this->_updateButton('save', 'label', Mage::helper('magebackup')->__('Backup Now'));
		}

		$this->_updateButton('delete', 'label', Mage::helper('magebackup')->__('Delete Backup'));
	}

	public function getHeaderText() {
		$backup	= Mage::registry('magebackup/backup');

		if ($backup && $backup->getId()) {
			return Mage::helper('magebackup')->__("Edit Backup '%s'", $this->escapeHtml($backup->getName()));
		} else {
			return Mage::helper('magebackup')->__('Add New Backup');
		}
	}
}
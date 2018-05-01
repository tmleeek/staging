<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup adminhtml profile edit block.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Block_Adminhtml_Profile_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
	
	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();
		
		$this->_objectId	= 'id';
		$this->_blockGroup	= 'magebackup';
		$this->_controller	= 'adminhtml_profile';
		
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
	}
	
	public function getHeaderText() {
		$profile	= Mage::registry('magebackup/profile');

		if ($profile && $profile->getId()) {
			return Mage::helper('magebackup')->__("Edit Profile '%s'", $this->escapeHtml($profile->getName()));
		} else {
			return Mage::helper('magebackup')->__('Add New Profile');
		}
	}
}
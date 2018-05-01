<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup adminhtml backup edit tab general block.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Block_Adminhtml_Backup_Edit_Tabs_General extends Mage_Adminhtml_Block_Widget_Form {

	protected function _prepareForm() {
		$model		= Mage::registry('magebackup/backup');
		$form		= new Varien_Data_Form();

		$fieldset	= $form->addFieldset('backup_form', array(
			'legend'	=> Mage::helper('magebackup')->__('Backup Information'),
			'class'		=> 'fieldset-wide',
		));

		$profiles	= Mage::getSingleton('magebackup/profile')->getProfilesArray();

		if (!$model->getId()) {
			$fieldset->addField('profile_id', 'select', array(
				'name'		=> 'profile_id',
				'label'		=> Mage::helper('magebackup')->__('Profile'),
				'values'	=> $profiles,
				'required'	=> true
			));
		} else {
			$fieldset->addField('profile_id_select', 'select', array(
				'name'		=> 'profile_id_select',
				'label'		=> Mage::helper('magebackup')->__('Profile'),
				'values'	=> $profiles,
				'required'	=> false,
				'disabled'	=> true,
			));

			$fieldset->addField('profile_id', 'hidden', array(
				'name'		=> 'profile_id',
			));
		}

		$fieldset->addField('name', 'text', array(
			'name'		=> 'name',
			'label'		=> Mage::helper('magebackup')->__('Backup Description'),
			'class'		=> 'required-entry',
			'required'	=> true,

		));

		$fieldset->addField('description', 'editor', array(
			'name'		=> 'description',
			'label'		=> Mage::helper('magebackup')->__('Backup Comment'),
			'title'		=> Mage::helper('magebackup')->__('Backup Comment'),
			'style'		=> 'width: 98%; height: 200px;',
			'wysiwyg'	=> false,
			'required'	=> false,
		));

		if (!$model->getId()) {
			$fieldset->addField('disable_ajax', 'select', array(
				'name'		=> 'disable_ajax',
				'label'		=> Mage::helper('magebackup')->__('Disable AJAX'),
				'values'	=> MageBackup_MageBackup_Model_Status::getYesNoArray()
			));

			$model->setName(Mage::helper('magebackup')->__('Backup taken on %s', date('j F Y G:i:s')));
		}

		$form->setValues($model->getData());
		$this->setForm($form);

		return parent::_prepareForm();
	}
}
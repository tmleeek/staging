<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup adminhtml profile edit tab general block.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Block_Adminhtml_Profile_Edit_Tabs_General extends Mage_Adminhtml_Block_Widget_Form {

	protected function _prepareForm() {
		$model		= Mage::registry('magebackup/profile');
		$form		= new Varien_Data_Form();

		// profile information
		$info		= $form->addFieldset('profile-info', array(
			'legend'	=> Mage::helper('magebackup')->__('Profile Information'),
			'class'		=> 'fieldset-wide',
		));

		$info->addField('name', 'text', array(
			'name'		=> 'name',
			'label'		=> Mage::helper('magebackup')->__('Name'),
			'class'		=> 'required-entry',
			'required'	=> true,
		));

		$info->addField('description', 'editor', array(
			'name'		=> 'description',
			'label'		=> Mage::helper('magebackup')->__('Description'),
			'style'		=> 'height: 60px',
			'wysiwyg'	=> false,
		));

		// basic configuration
		$basic		= $form->addFieldset('profile-basic', array(
			'legend'	=> Mage::helper('magebackup')->__('Basic Configuration'),
			'class'		=> 'fieldset-wide',
		));

		$basic->addField('output_directory', 'text', array(
			'name'		=> 'data[output_directory]',
			'label'		=> Mage::helper('magebackup')->__('Output Directory'),
			'required'	=> true,
		));

		$basic->addField('backup_type', 'select', array(
			'name'		=> 'data[backup_type]',
			'label'		=> Mage::helper('magebackup')->__('Backup Type'),
			'values'	=> Mage::getSingleton('magebackup/profile_backuptype')->getBackupTypesArray(),
			'required'	=> true,
		));

		$basic->addField('log_level', 'select', array(
			'name'		=> 'data[log_level]',
			'label'		=> Mage::helper('magebackup')->__('Log Level'),
			'values'	=> array(
				'0'			=> Mage::helper('magebackup')->__('None'),
				'1'			=> Mage::helper('magebackup')->__('Errors only'),
				'2'			=> Mage::helper('magebackup')->__('Errors and Warnings'),
				'3'			=> Mage::helper('magebackup')->__('All Information'),
				'4'			=> Mage::helper('magebackup')->__('All Information and Debug'),
			),
		));

		$basic->addField('server_name', 'hidden', array(
			'name'		=> 'data[server_name]',
		));

		// backup deletion
		$backupDel	= $form->addFieldset('backup-deletion-config', array(
			'legend'	=> Mage::helper('magebackup')->__('Backup Deletion Configuration'),
		));

		$backupDel->addField('num_keep', 'text', array(
			'name'				=> 'data[num_keep]',
			'label'				=> Mage::helper('magebackup')->__('Number of backup files to keep in local storage'),
		));

		// zip configuration
		$zip		= $form->addFieldset('zip-config', array(
			'legend'	=> Mage::helper('magebackup')->__('Zip Archiver Configuration'),
		));

		$zip->addField('zip_fragment_size', 'text', array(
			'name'					=> 'data[zip_fragment_size]',
			'label'					=> Mage::helper('magebackup')->__('Part size for split archives (MB)'),
			'after_element_html'	=> '<span class="mb-help" title="' . Mage::helper('magebackup')->__('MageBackup can create split (multi-part) archives in order to work around size restrictions under various circumstances. This option defines the maximum size of each archive part. If you reduce it to 0, the multi-part feature is disabled.') . '"></span>',
		));

		$zip->addField('zip_read_chunk', 'text', array(
			'name'					=> 'data[zip_read_chunk]',
			'label'					=> Mage::helper('magebackup')->__('Chunk size for large files processing (MB)'),
			'after_element_html'	=> '<span class="mb-help" title="' . Mage::helper('magebackup')->__('MageBackup processes large file in small chunks, in order to avoid timeouts. This parameter defines the maximum chunk size for this kind of processing.') . '"></span>',
		));

		$zip->addField('zip_threshold', 'text', array(
			'name'					=> 'data[zip_threshold]',
			'label'					=> Mage::helper('magebackup')->__('Big file threshold (MB)'),
			'after_element_html'	=> '<span class="mb-help" title="' . Mage::helper('magebackup')->__('Files over this size will be stored uncompressed, or their processing will span multiple steps (depending on the archiver engine) in order to avoid timeouts. We suggest increasing this value only on fast and reliable servers.') . '"></span>',
		));

		// ajax configuration
		$ajax		= $form->addFieldset('ajax-config', array(
			'legend'	=> Mage::helper('magebackup')->__('AJAX Configuration'),
		));

		$ajax->addField('ajax_max_time', 'text', array(
			'name'		=> 'data[ajax_max_time]',
			'label'		=> Mage::helper('magebackup')->__('Time limit per request (second)'),
		));

		$ajax->addField('ajax_num_files', 'text', array(
			'name'		=> 'data[ajax_num_files]',
			'label'		=> Mage::helper('magebackup')->__('Files to process per request'),
		));

		$ajax->addField('ajax_num_queries', 'text', array(
			'name'		=> 'data[ajax_num_queries]',
			'label'		=> Mage::helper('magebackup')->__('Database queries to process per request'),
		));

		// cron configuration
		$cron		= $form->addFieldset('cron-config', array(
			'legend'	=> Mage::helper('magebackup')->__('Scheduled Backup Configuration')
		));

		$cron->addField('cron_enable', 'checkbox', array(
			'name'		=> 'data[cron_enable]',
			'label'		=> Mage::helper('magebackup')->__('Enable Scheduled Backup'),
			'onclick'	=> 'this.value = this.checked ? 1 : 0;',
			'checked'	=> $model->getValue('cron_enable') ? true : false,
		));

		$cron->addField('cron_frequency', 'select', array(
			'name'		=> 'data[cron_frequency]',
			'label'		=> Mage::helper('magebackup')->__('Frequency'),
			'values'	=> Mage::getSingleton('magebackup/profile_cron')->getCronFrequenciesArray(),
		));

		$cron->addField('cron_hour', 'select', array(
			'name'		=> 'data[cron_hour]',
			'label'		=> Mage::helper('magebackup')->__('Start Hour'),
			'values'	=> Mage::getSingleton('magebackup/profile_cron')->getCronHoursArray(),
		));


		$form->setValues($model->getData());
		$form->addValues($model->getValues());

		$form->addValues(array(
			'server_name'	=> $_SERVER['SERVER_NAME']
		));

		$this->setForm($form);

		return parent::_prepareForm();
	}
}
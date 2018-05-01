<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup adminhtml profile edit tab cloud block.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Block_Adminhtml_Profile_Edit_Tabs_Cloud extends Mage_Adminhtml_Block_Widget_Form {

	protected function _prepareForm() {
		$model		= Mage::registry('magebackup/profile');
		$form		= new Varien_Data_Form();

		// basic configuration
		$basic		= $form->addFieldset('cloud-basic', array(
			'legend'	=> Mage::helper('magebackup')->__('Basic Configuration'),
			'class'		=> 'fieldset-wide',
		));

		$basic->addField('cloud_engine', 'select', array(
			'name'		=> 'data[cloud_engine]',
			'label'		=> Mage::helper('magebackup')->__('Cloud Storage'),
			'values'	=> Mage::getSingleton('magebackup/profile_cloud')->getEnginesArray(),
		));
		
		$basic->addField('cloud_delete_local', 'checkbox', array(
			'name'		=> 'data[cloud_delete_local]',
			'label'		=> Mage::helper('magebackup')->__('Delete Local Backup'),
			'onclick'	=> 'this.value = this.checked ? 1 : 0;',
			'checked'	=> $model->getValue('cloud_delete_local') ? true : false,
		));

		// Amazon S3
		$s3			= $form->addFieldset('s3-config', array('legend' => Mage::helper('magebackup')->__('Amazon S3')));

		$s3->addField('s3_accesskey', 'text', array(
			'name'		=> 'data[s3_accesskey]',
			'label'		=> Mage::helper('magebackup')->__('Access Key'),
		));

		$s3->addField('s3_secretkey', 'text', array(
			'name'		=> 'data[s3_secretkey]',
			'label'		=> Mage::helper('magebackup')->__('Secret Key'),
		));

		$s3->addField('s3_bucket', 'text', array(
			'name'		=> 'data[s3_bucket]',
			'label'		=> Mage::helper('magebackup')->__('Bucket'),
		));

		$s3->addField('s3_directory', 'text', array(
			'name'		=> 'data[s3_directory]',
			'label'		=> Mage::helper('magebackup')->__('Directory'),
		));

		$s3->addField('s3_region', 'select', array(
			'name'		=> 'data[s3_region]',
			'label'		=> Mage::helper('magebackup')->__('Region'),
			'values'	=> Mage::getSingleton('magebackup/profile_cloud_amazon')->getRegionsArray(),
		));

		$s3->addField('s3_use_ssl', 'checkbox', array(
			'name'		=> 'data[s3_use_ssl]',
			'label'		=> Mage::helper('magebackup')->__('Use SSL Connection'),
			'onclick'	=> 'this.value = this.checked ? 1 : 0;',
			'checked'	=> $model->getValue('s3_use_ssl') ? true : false,
		));

		$s3->addField('s3_storage_class', 'select', array(
			'name'		=> 'data[s3_storage_class]',
			'label'		=> Mage::helper('magebackup')->__('Storage Class'),
			'values'	=> Mage::getSingleton('magebackup/profile_cloud_amazon')->getStorageClassesArray(),
		));

		$s3->addField('s3_use_aws2', 'checkbox', array(
			'name'		=> 'data[s3_use_aws2]',
			'label'		=> Mage::helper('magebackup')->__('Use AWS SDK Version 2 (For PHP 5.4 and older)'),
			'onclick'	=> 'this.value = this.checked ? 1 : 0;',
			'checked'	=> $model->getValue('s3_use_aws2') ? true : false,
		));

		// Amazon Glacier
		$glacier			= $form->addFieldset('glacier-config', array('legend' => Mage::helper('magebackup')->__('Amazon Glacier')));

		$glacier->addField('glacier_accesskey', 'text', array(
			'name'		=> 'data[glacier_accesskey]',
			'label'		=> Mage::helper('magebackup')->__('Access Key'),
		));

		$glacier->addField('glacier_secretkey', 'text', array(
			'name'		=> 'data[glacier_secretkey]',
			'label'		=> Mage::helper('magebackup')->__('Secret Key'),
		));

		$glacier->addField('glacier_vault', 'text', array(
			'name'		=> 'data[glacier_vault]',
			'label'		=> Mage::helper('magebackup')->__('Vault Name'),
		));

		$glacier->addField('glacier_region', 'select', array(
			'name'		=> 'data[glacier_region]',
			'label'		=> Mage::helper('magebackup')->__('Region'),
			'values'	=> Mage::getSingleton('magebackup/profile_cloud_amazon')->getGlacierRegionsArray(),
		));

		$glacier->addField('glacier_use_ssl', 'checkbox', array(
			'name'		=> 'data[glacier_use_ssl]',
			'label'		=> Mage::helper('magebackup')->__('Use SSL Connection'),
			'onclick'	=> 'this.value = this.checked ? 1 : 0;',
			'checked'	=> $model->getValue('glacier_use_ssl') ? true : false,
		));

		$glacier->addField('glacier_use_aws2', 'checkbox', array(
			'name'		=> 'data[glacier_use_aws2]',
			'label'		=> Mage::helper('magebackup')->__('Use AWS SDK Version 2 (For PHP 5.4 and older)'),
			'onclick'	=> 'this.value = this.checked ? 1 : 0;',
			'checked'	=> $model->getValue('glacier_use_aws2') ? true : false,
		));
		
		// Google Storage
		$googlestorage	= $form->addFieldset('googlestorage-config', array('legend' => Mage::helper('magebackup')->__('Google Cloud Storage')));

		$googlestorage->addField('googlestorage_accesskey', 'text', array(
			'name'		=> 'data[googlestorage_accesskey]',
			'label'		=> Mage::helper('magebackup')->__('Access Key'),
		));

		$googlestorage->addField('googlestorage_secretkey', 'text', array(
			'name'		=> 'data[googlestorage_secretkey]',
			'label'		=> Mage::helper('magebackup')->__('Secret Key'),
		));

		$googlestorage->addField('googlestorage_bucket', 'text', array(
			'name'		=> 'data[googlestorage_bucket]',
			'label'		=> Mage::helper('magebackup')->__('Bucket'),
		));

		$googlestorage->addField('googlestorage_directory', 'text', array(
			'name'		=> 'data[googlestorage_directory]',
			'label'		=> Mage::helper('magebackup')->__('Directory'),
		));

		// google drive
		$googledrive	= $form->addFieldset('googledrive-config', array('legend' => Mage::helper('magebackup')->__('Google Drive')));

		$googledrive->addField('googledrive_access_token', 'text', array(
			'name'		=> 'data[googledrive_access_token]',
			'label'		=> Mage::helper('magebackup')->__('Access Token'),
			'class'		=> 'disabled',
			'readonly'	=> true,
		));

		$googledrive->addField('googledrive_refresh_token', 'text', array(
			'name'		=> 'data[googledrive_refresh_token]',
			'label'		=> Mage::helper('magebackup')->__('Refresh Token'),
			'class'		=> 'disabled',
			'readonly'	=> true,
		));

		$googledrive->addField('googledrive_as1', 'button', array(
			'name'					=> 'googledrive_as1',
			'label'					=> Mage::helper('magebackup')->__('Authentication - Step 1'),
			'value'					=> Mage::helper('magebackup')->__('Authentication - Step 1'),
			'class'					=> 'form-button',
			'onclick'				=> 'MageBackup.googledriveAuth1(\'' . Mage::helper('adminhtml')->getUrl('adminhtml/magebackup_profile/googleDriveAuth1') . '\')',
			'after_element_html'	=> '<p class="note">' . Mage::helper('magebackup')->__('Click on this button to open a new window where you can log in to your account with the storage provider.') . '</p>',
		));

		$googledrive->addField('googledrive_directory', 'text', array(
			'name'		=> 'data[googledrive_directory]',
			'label'		=> Mage::helper('magebackup')->__('Directory'),
		));
		
		// OneDrive
		$onedrive	= $form->addFieldset('onedrive-config', array('legend' => Mage::helper('magebackup')->__('Microsoft OneDrive')));

		$onedrive->addField('onedrive_access_token', 'text', array(
			'name'		=> 'data[onedrive_access_token]',
			'label'		=> Mage::helper('magebackup')->__('Access Token'),
			'class'		=> 'disabled',
			'readonly'	=> true,
		));

		$onedrive->addField('onedrive_refresh_token', 'text', array(
			'name'		=> 'data[onedrive_refresh_token]',
			'label'		=> Mage::helper('magebackup')->__('Refresh Token'),
			'class'		=> 'disabled',
			'readonly'	=> true,
		));

		$onedrive->addField('onedrive_redirect_uri', 'hidden', array(
			'name'		=> 'data[onedrive_redirect_uri]',
		));

		$onedrive->addField('onedrive_as1', 'button', array(
			'name'					=> 'onedrive_as1',
			'label'					=> Mage::helper('magebackup')->__('Authentication '),
			'value'					=> Mage::helper('magebackup')->__('Authentication'),
			'class'					=> 'form-button',
			'onclick'				=> 'MageBackup.onedriveAuth1(\'' . Mage::helper('adminhtml')->getUrl('adminhtml/magebackup_profile/onedriveAuth1') . '\')',
			'after_element_html'	=> '<p class="note">' . Mage::helper('magebackup')->__('Click on this button to open a new window where you can log in to your account with the storage provider.') . '</p>',
		));

		$onedrive->addField('onedrive_directory', 'text', array(
			'name'		=> 'data[onedrive_directory]',
			'label'		=> Mage::helper('magebackup')->__('Directory'),
		));

		// Dropbox
		$dropbox	= $form->addFieldset('dropbox-config', array('legend' => Mage::helper('magebackup')->__('Dropbox')));

		$dropbox->addField('dropbox_access_token', 'text', array(
			'name'		=> 'data[dropbox_access_token]',
			'label'		=> Mage::helper('magebackup')->__('Access Token'),
			'class'		=> 'disabled',
			'readonly'	=> true,
		));

		$urlStep1	= Mage::helper('adminhtml')->getUrl('adminhtml/magebackup_profile/dropboxAuth1/');
		$urlStep2	= Mage::helper('adminhtml')->getUrl('adminhtml/magebackup_profile/dropboxAuth2/');

		$dropbox->addField('dropbox_as1', 'button', array(
			'name'					=> 'dropbox_as1',
			'label'					=> Mage::helper('magebackup')->__('Authentication - Step 1'),
			'value'					=> Mage::helper('magebackup')->__('Authentication - Step 1'),
			'class'					=> 'form-button',
			'onclick'				=> 'MageBackup.dropboxAuth1(\'' . $urlStep1 . '\')',
			'after_element_html'	=> '<p class="note">' . Mage::helper('magebackup')->__('Click on this button to open a new window where you can log in to your account with the storage provider. Then, please copy the code, close the popup window and click on the Step 2 button below.') . '</p>',
		));

		$dropbox->addField('dropbox_as2', 'button', array(
			'name'					=> 'dropbox_as2',
			'label'					=> Mage::helper('magebackup')->__('Authentication - Step 2'),
			'value'					=> Mage::helper('magebackup')->__('Authentication - Step 2'),
			'class'					=> 'form-button',
			'onclick'				=> 'MageBackup.dropboxAuth2(\'' . $urlStep2 . '\')',
			'after_element_html'	=> '<p class="note">' . Mage::helper('magebackup')->__('Click on this button after having clicked on the Step 1 button and logged in to your storage provider account.') . '</p>',
		));

		$dropbox->addField('dropbox_directory', 'text', array(
			'name'		=> 'data[dropbox_directory]',
			'label'		=> Mage::helper('magebackup')->__('Directory'),
		));

		// FTP server
		$ftp		= $form->addFieldset('ftp-config', array('legend' => Mage::helper('magebackup')->__('FTP Server')));

		$ftp->addField('ftp_server', 'text', array(
			'name'		=> 'data[ftp_server]',
			'label'		=> Mage::helper('magebackup')->__('Host'),
		));

		$ftp->addField('ftp_port', 'text', array(
			'name'		=> 'data[ftp_port]',
			'label'		=> Mage::helper('magebackup')->__('Port'),
		));

		$ftp->addField('ftp_username', 'text', array(
			'name'		=> 'data[ftp_username]',
			'label'		=> Mage::helper('magebackup')->__('Username'),
		));

		$ftp->addField('ftp_password', 'password', array(
			'name'		=> 'data[ftp_password]',
			'label'		=> Mage::helper('magebackup')->__('Password'),
		));

		$ftp->addField('ftp_path', 'text', array(
			'name'		=> 'data[ftp_path]',
			'label'		=> Mage::helper('magebackup')->__('Path'),
		));

		$ftp->addField('ftp_passive', 'checkbox', array(
			'name'		=> 'data[ftp_passive]',
			'label'		=> Mage::helper('magebackup')->__('Use Passive Mode'),
			'onclick'	=> 'this.value = this.checked ? 1 : 0;',
			'checked'	=> $model->getValue('ftp_passive') ? true : false,
		));

		$ftp->addField('ftp_ftps', 'checkbox', array(
			'name'		=> 'data[ftp_ftps]',
			'label'		=> Mage::helper('magebackup')->__('Use FTP over SSL (FTPS)'),
			'onclick'	=> 'this.value = this.checked ? 1 : 0;',
			'checked'	=> $model->getValue('ftp_ftps') ? true : false,
		));

		// SFTP server
		$sftp		= $form->addFieldset('sftp-config', array('legend' => Mage::helper('magebackup')->__('SFTP (SSH) Server')));

		$sftp->addField('sftp_server', 'text', array(
			'name'		=> 'data[sftp_server]',
			'label'		=> Mage::helper('magebackup')->__('Host'),
		));

		$sftp->addField('sftp_port', 'text', array(
			'name'		=> 'data[sftp_port]',
			'label'		=> Mage::helper('magebackup')->__('Port'),
		));

		$sftp->addField('sftp_username', 'text', array(
			'name'		=> 'data[sftp_username]',
			'label'		=> Mage::helper('magebackup')->__('Username'),
		));

		$sftp->addField('sftp_password', 'password', array(
			'name'		=> 'data[sftp_password]',
			'label'		=> Mage::helper('magebackup')->__('Password'),
		));

		$sftp->addField('sftp_path', 'text', array(
			'name'		=> 'data[sftp_path]',
			'label'		=> Mage::helper('magebackup')->__('Path'),
		));

		$sftp->addField('sftp_public_key', 'text', array(
			'name'		=> 'data[sftp_pubkey]',
			'label'		=> Mage::helper('magebackup')->__('Public Key'),
			'after_element_html'	=> '<span class="mb-help" title="' . Mage::helper('magebackup')->__('The absolute filesystem path to an RSA / DSA public key file used to connect to the remote server.') . '"></span>',
		));

		$sftp->addField('sftp_private_key', 'text', array(
			'name'		=> 'data[sftp_privkey]',
			'label'		=> Mage::helper('magebackup')->__('Private Key'),
			'after_element_html'	=> '<span class="mb-help" title="' . Mage::helper('magebackup')->__('The absolute filesystem path to an RSA / DSA private key file used to connect to the remote server. If it\'s encrypted, enter the passphrase in the password field above.') . '"></span>',
		));
		
		$form->addValues($model->getValues());
		$this->setForm($form);

		return parent::_prepareForm();
	}
}
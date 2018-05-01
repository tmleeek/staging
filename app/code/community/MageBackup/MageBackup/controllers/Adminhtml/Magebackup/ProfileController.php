<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup profile controller.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Adminhtml_MageBackup_ProfileController extends Mage_Adminhtml_Controller_Action {

	/**
	 * Acl check for admin.
	 *
	 * @return bool
	 */
	protected function _isAllowed() {
		$action	= strtolower($this->getRequest()->getActionName());

		switch ($action) {
			case 'new':
				$aclResource = 'magebackup/profile/create';
				break;
			case 'edit':
				$aclResource = 'magebackup/profile/edit';
				break;
			case 'save':
				$aclResource = 'magebackup/profile/save';
				break;
			case 'delete':
				$aclResource = 'magebackup/profile/delete';
				break;
			default:
				$aclResource = 'magebackup/profile';
		}

		return Mage::getSingleton('admin/session')->isAllowed($aclResource);
	}

	public function indexAction() {
		$this->_title($this->__('MageBackup'))
			->_title($this->__('Manage Profiles'))
		;

		$this->loadLayout()
			->_setActiveMenu('magebackup/magebackup/profile')
		;

		$this->renderLayout();
	}

	public function gridAction() {
		$this->getResponse()->setBody(
			$this->getLayout()
				->createBlock('magebackup/adminhtml_profile_grid')
				->toHtml()
		);
	}

	public function editAction() {
		$id		= (int) $this->getRequest()->getParam('id');
		$model	= Mage::getModel('magebackup/profile')->load($id);

		if ($id && !$model->getId()) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('magebackup')->__('Profile no longer exists.'));
			$this->_redirect('*/*/');

			return;
		}

		Mage::register('magebackup/profile', $model);

		if ($id) {
			$title	= $model->getName();
		} else {
			$title	= $this->__('New Profile');
			$model->setDefaultValues();
		}

		$this->_title($this->__('MageBackup'))
			->_title($title)
		;

		$this->loadLayout()
			->_setActiveMenu('magebackup/magebackup/profile')
		;

		$this->_addContent($this->getLayout()->createBlock('magebackup/adminhtml_profile_edit'))
			->_addLeft($this->getLayout()->createBlock('magebackup/adminhtml_profile_edit_tabs'))
			->_addJs($this->getLayout()->createBlock('core/template')->setTemplate('magebackup/profile/edit/js.phtml'))
		;

		$this->renderLayout();
	}

	public function newAction() {
		$this->_forward('edit');
	}

	public function saveAction() {
		if ($this->getRequest()->getPost()) {
			try {
				$postData	= $this->getRequest()->getPost();
				$model		= Mage::getModel('magebackup/profile');

				if ($this->getRequest()->getParam('id') <= 0) {
					$model->setCreatedTime(Mage::getSingleton('core/date')->gmtDate());
				}

				$model->addData($postData)
					->setUpdateTime(Mage::getSingleton('core/date')->gmtDate())
					->setId($this->getRequest()->getParam('id'))
					->save()
				;

				// save profile data
				$profileId		= $model->getId();
				$profileData	= $this->getRequest()->getPost('data', array());

				$fields			= array(
					'cron_enable',
					'cloud_delete_local',
					'included_databases',
					'ftp_passive',
					'ftp_ftps',
					's3_use_ssl',
					's3_use_aws2',
					'glacier_use_ssl',
					'glacier_use_aws2'
				);

				foreach ($fields as $field) {
					if (!isset($profileData[$field])) {
						$profileData[$field]	= '';
					}
				}

				foreach ($profileData as $key => $value) {
					$dataModel	= Mage::getModel('magebackup/data');
					$dataModel->loadByFields(array(
						'profile_id'	=> $profileId,
						'name'			=> $key
					));

					if (is_array($value)) {
						$value	= Mage::helper('core')->jsonEncode($value);
					}

					$dataModel->setProfileId($profileId)
						->setName($key)
						->setValue($value)
						->save()
					;

				}

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('magebackup')->__('Profile was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setProfileData(false);

				// check if 'Apply'
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}

				// go to grid
				$this->_redirect('*/*/');

				return;

			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setProfileData($this->getRequest()->getPost());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));

				return;
			}
		}

		$this->_redirect('*/*/');
	}

	public function deleteAction() {
		if ($this->getRequest()->getParam('id') > 0) {
			try {
				$model	= Mage::getModel('magebackup/profile');

				$model->setId($this->getRequest()->getParam('id'))
					->delete()
				;

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('magebackup')->__('Profile was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminihtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}

		$this->_redirect('*/*/');
	}

	public function massDeleteAction() {
		$ids	= $this->getRequest()->getParam('profile');

		if (!is_array($ids)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('magebackup')->__('Please select Profile(s)'));
		} else {
			try {
				foreach ($ids as $id) {
					$profile	= Mage::getModel('magebackup/profile')->load($id);
					$profile->delete();
				}

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('magebackup')->__('Total of %d Profile(s) were successfully deleted', count($ids)));
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}

		$this->_redirect('*/*/');
	}

	public function loadFilesAction() {
		$this->loadLayout()
			->renderLayout()
		;
	}

	public function dropboxAuth1Action() {
		$dropbox	= Mage::getSingleton('magebackup/profile_cloud_dropbox');
		$url		= $dropbox->oauthOpen();

		$this->_redirectUrl($url);
	}

	public function dropboxAuth2Action() {
		$authCode		= $this->getRequest()->getParam('code');

		$dropbox	= Mage::getSingleton('magebackup/profile_cloud_dropbox');

		echo json_encode($dropbox->getAuth($authCode));
		die();
	}

	public function googleDriveAuth1Action() {
		$googleDrive	= Mage::getSingleton('magebackup/profile_cloud_googledrive');
		$url			= $googleDrive->oauthOpen();

		$this->_redirectUrl($url);
	}

	public function googleDriveAuth2Action() {
		$authCode		= $this->getRequest()->getParam('code');

		$googleDrive	= Mage::getSingleton('magebackup/profile_cloud_googledrive');
		$token			= json_decode($googleDrive->getAuth($authCode));

		$script			= <<<SCRIPT
<script>
	if (window.opener) {
		window.opener.MageBackup.googledriveAuth2('{$token->access_token}', '{$token->refresh_token}');
	}
</script>
SCRIPT;

		echo $script;
		die();
	}

	public function oneDriveAuth1Action() {
		$oneDrive	= Mage::getSingleton('magebackup/profile_cloud_onedrive');
		$url		= $oneDrive->oauthOpen();

		$this->_redirectUrl($url);
	}

	public function oneDriveAuth2Action() {
		$authCode		= $this->getRequest()->getParam('code');

		$oneDrive	= Mage::getSingleton('magebackup/profile_cloud_onedrive');
		$token		= $oneDrive->getAuth($authCode);

		$script			= <<<SCRIPT
<script>
	if (window.opener) {
		window.opener.MageBackup.onedriveAuth2('{$token->access_token}', '{$token->refresh_token}', '{$token->redirect_uri}');
	}
</script>
SCRIPT;

		echo $script;
		die();
	}
}
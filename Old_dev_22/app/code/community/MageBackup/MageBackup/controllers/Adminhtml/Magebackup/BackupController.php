<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup backup controller.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Adminhtml_MageBackup_BackupController extends Mage_Adminhtml_Controller_Action {

	/**
	 * Acl check for admin.
	 *
	 * @return bool
	 */
	protected function _isAllowed() {
		$action = strtolower($this->getRequest()->getActionName());

		switch ($action) {
			case 'new':
				$aclResource = 'magebackup/backup/create';
				break;
			case 'edit':
				$aclResource = 'magebackup/backup/edit';
				break;
			case 'save':
				$aclResource = 'magebackup/backup/save';
				break;
			case 'download':
				$aclResource = 'magebackup/backup/download';
				break;
			case 'delete':
				$aclResource = 'magebackup/backup/delete';
				break;
			case 'deleteFiles':
				$aclResource = 'magebackup/backup/deleteFiles';
				break;
			default:
				$aclResource = 'magebackup/backup';
		}

		return Mage::getSingleton('admin/session')->isAllowed($aclResource);
	}

	/**
	 * Check if at least one profile was created.
	 */
	protected function _checkProfile() {
		$profiles	= Mage::getSingleton('magebackup/profile')->getProfilesArray();

		if (!count($profiles)) {
			Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('magebackup')->__('You need to create a profile before make a backup. Click <a href="%s">here</a> to create profile now.', $this->getUrl('/magebackup_profile/new')));
			return false;
		}

		return true;
	}

	public function indexAction() {
		$this->_checkProfile();

		$this->_title($this->__('MageBackup'))
			->_title($this->__('Manage Backups'))
		;

		$this->loadLayout()
			->_setActiveMenu('magebackup/magebackup/backup')
		;

		$this->renderLayout();
	}

	public function gridAction() {
		$this->getResponse()->setBody(
			$this->getLayout()
				->createBlock('magebackup/adminhtml_backup_grid')
				->toHtml()
		);
	}

	public function editAction() {
		$this->_checkProfile();

		$backupId		= (int) $this->getRequest()->getParam('id');
		$backupModel	= Mage::getModel('magebackup/backup')->load($backupId);

		if ($backupId && !$backupModel->getId()) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('magebackup')->__('Backup no longer exists.'));
			$this->_redirect('*/*/');

			return;
		}

		Mage::register('magebackup/backup', $backupModel);

		if ($backupId) {
			
		} else {
			$title	= $this->__('New Backup');
			$backupModel->setDefaultValues();
		}

		$this->_title($this->__('MageBackup'))
			->_title($title)
		;

		$this->loadLayout()
			->_setActiveMenu('magebackup/magebackup/backup')
		;

		$this->_addContent($this->getLayout()->createBlock('magebackup/adminhtml_backup_edit'))
			->_addLeft($this->getLayout()->createBlock('magebackup/adminhtml_backup_edit_tabs'))
		;

		$this->renderLayout();
	}

	public function newAction() {
		if (!$this->_checkProfile()) {
			$this->_redirect('*/*/');
		}

		$this->_forward('edit');
	}

	public function deleteAction() {
		if ($this->getRequest()->getParam('id') > 0) {
			try {
				$backup	= Mage::getModel('magebackup/backup')->load($this->getRequest()->getParam('id'));
				$backup->delete();

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('magebackup')->__('Backup was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}

		$this->_redirect('*/*/');
	}

	public function deleteFilesAction() {
		if ($this->getRequest()->getParam('id') > 0) {
			try {
				$backup	= Mage::getModel('magebackup/backup')->load($this->getRequest()->getParam('id'));
				$backup->deleteFiles();

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('magebackup')->__('Backup files were successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}

		$this->_redirect('*/*/');
	}

	public function deleteLogAction() {
		if ($this->getRequest()->getParam('id') > 0) {
			try {
				$backup	= Mage::getModel('magebackup/backup')->load($this->getRequest()->getParam('id'));
				$backup->deleteLog();

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('magebackup')->__('Backup log were successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}

		$this->_redirect('*/*/');
	}

	public function massDeleteAction() {
		$ids	= $this->getRequest()->getParam('backup');

		if (!is_array($ids)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('magebackup')->__('Please select Backup(s)'));
		} else {
			try {
				foreach ($ids as $id) {
					$backup	= Mage::getModel('magebackup/backup')->load($id);
					$backup->delete();
				}

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('magebackup')->__('Total of %d Backup(s) were successfully deleted', count($ids)));
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}

		$this->_redirect('*/*/');
	}

	public function massDeleteFilesAction() {
		$ids	= $this->getRequest()->getParam('backup');

		if (!is_array($ids)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('magebackup')->__('Please select Backup(s)'));
		} else {
			try {
				foreach ($ids as $id) {
					$backup	= Mage::getModel('magebackup/backup')->load($id);
					$backup->deleteFiles();
				}

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('magebackup')->__('Total of %d Backup(s) files were successfully deleted', count($ids)));
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}

		$this->_redirect('*/*/');
	}

	public function massDeleteLogAction() {
		$ids	= $this->getRequest()->getParam('backup');

		if (!is_array($ids)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('magebackup')->__('Please select Backup(s)'));
		} else {
			try {
				foreach ($ids as $id) {
					$backup	= Mage::getModel('magebackup/backup')->load($id);
					$backup->deleteLog();
				}

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('magebackup')->__('Total of %d Backup(s) log were successfully deleted', count($ids)));
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}

		$this->_redirect('*/*/');
	}

	public function saveAction() {
		if ($this->getRequest()->getPost()) {
			try {
				$postData = $this->getRequest()->getPost();
				$backup		= Mage::getModel('magebackup/backup');
				$profile	= Mage::getModel('magebackup/profile')->load($this->getRequest()->getParam('profile_id'));

				if (!$profile->getId()) {
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('magebackup')->__('Profile not found.'));

					$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
					return;
				}

				if ($this->getRequest()->getParam('id') <= 0) {
					$backup->setStartTime(Mage::getSingleton('core/date')->gmtDate());
				}

				if ($this->getRequest()->getParam('id') <= 0) {
					$fileName	= $profile->genArchiveName();

					$backup->addData($postData)
						->setId($this->getRequest()->getParam('id'))
						->setProfileId($this->getRequest()->getParam('profile_id'))
						->setFileName($fileName)
						->setFilePath($profile->getOutputDir() . '/' . $fileName)
						->setStatus('fail')
						->save()
					;

					if ($this->getRequest()->getParam('disable_ajax') > 0) {
						Mage::helper('magebackup')->unlimit();
						
						if ($backup->backup()) {
							Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('magebackup')->__('Backup was successfully saved'));
						} else {
							Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('magebackup')->__('Backup failed!'));
						}

						$this->_redirect('*/*/');
						return;
					} else {
						$backupAjax	= Mage::getModel('magebackup/backup_ajax')
							->load($backup->getId())
						;

						$session	= Mage::getSingleton('magebackup/session');
						$session->clear();
						$session->setBackup($backupAjax);

						$this->_redirect('*/*/ajax');
						return;
					}
				} else {
					$backup->addData($postData)
						->setId($this->getRequest()->getParam('id'))
						->save()
					;

					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('magebackup')->__('Backup was successfully saved'));

					// check if 'Save and Continue'
					if ($this->getRequest()->getParam('back')) {
						$this->_redirect('*/*/edit', array('id' => $backup->getId()));
						return;
					}

					// go to grid
					$this->_redirect('*/*/');
					return;
				}


			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());

				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
	}

	public function ajaxAction() {
		$session	= Mage::getSingleton('magebackup/session');
		$backup		= $session->getBackup();

		if (!$backup) {
			$this->_redirect('*/*/new');
			return;
		}

		Mage::register('magebackup/backup', $backup);

		// if it is not ajax, then display screen
		if (!$this->getRequest()->isXmlHttpRequest()) {
			$this->_title($this->__('Mage Backup'))
				->_title($this->__('Backup'))
			;

			$this->loadLayout();
			$this->_setActiveMenu('magebackup/magebackup/backup');
			$this->renderLayout();
		} else {
			Mage::helper('magebackup')->unlimit();

			$backup->backup();

			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($backup->getResponse()));
		}
	}

	public function downloadAction() {
		if ($this->getRequest()->getParam('id') <= 0) {
			$this->_redirect('*/*/');
		}

		Mage::helper('magebackup')->unlimit();

		try {
			$backup		= Mage::getSingleton('magebackup/backup')->load($this->getRequest()->getParam('id'));
			$filePath	= $backup->getFilePath();
			$part		= (int) $this->getRequest()->getParam('part');

			$filePath	= Mage::helper('magebackup')->getZipPart($filePath, $part);

			if (!is_file($filePath)) {
				$this->_redirect('*/*/');
			}

			$content = array(
				'type' => 'filename',
				'value' => $filePath,
			);

			$this->_prepareDownloadResponse(basename($filePath), $content);

			exit();
		} catch (Exception $e) {
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());

			$this->_redirect('*/*/');
			return;
		}
	}

	public function downloadLogAction() {
		if ($this->getRequest()->getParam('id') <= 0) {
			$this->_redirect('*/*/');
		}

		Mage::helper('magebackup')->unlimit();

		try {
			$backup		= Mage::getSingleton('magebackup/backup')->load($this->getRequest()->getParam('id'));
			$profile	= $backup->getProfile();

			$logFile	= $profile->getLogDir() . '/magebackup.id.' . $backup->getId() . '.log';

			if (!is_file($logFile)) {
				$this->_redirect('*/*/');
			}

			$content = array(
				'type' => 'filename',
				'value' => $logFile,
			);

			$this->_prepareDownloadResponse(basename($logFile), $content);

			exit();
		} catch (Exception $e) {
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());

			$this->_redirect('*/*/');
			return;
		}
	}
}
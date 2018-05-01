<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup adminhtml backup edit form block.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Block_Adminhtml_Backup_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

	protected function _prepareForm() {
		$form	= new Varien_Data_Form(array(
			'id'		=> 'edit_form',
			'action'	=> $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
			'method'	=> 'post',
		));

		$form->setUseContainer(true);
		$this->setForm($form);

		return parent::_prepareForm();
	}
}
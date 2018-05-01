<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup adminhtml backup block.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Block_Adminhtml_Backup extends Mage_Adminhtml_Block_Widget_Grid_Container {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->_controller			= 'adminhtml_backup';
		$this->_blockGroup			= 'magebackup';
		$this->_headerText			= $this->__('Manage Backups');
		$this->_addButtonLabel		= $this->__('Create New Backup');

		parent::__construct();
	}
}
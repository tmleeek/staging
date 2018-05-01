<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup backup ajax response model.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Model_Backup_Ajax_Response {
	public $step		= '';
	public $nextstep	= 'initialize';
	public $error		= '';
	public $archive		= '';
	public $progress	= 0;
	public $info		= '';
	public $done		= 0;
}
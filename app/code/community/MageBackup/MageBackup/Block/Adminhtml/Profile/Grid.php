<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup adminhtml profile grid block.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Block_Adminhtml_Profile_Grid extends Mage_Adminhtml_Block_Widget_Grid {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->setId('profileGrid');
		$this->setDefaultSort('profile_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}

	protected function _prepareCollection() {
		$collection	= Mage::getModel('magebackup/profile')->getCollection();

		$this->setCollection($collection);

		return parent::_prepareCollection();
	}

	protected function _prepareColumns() {
		$this->addColumn('profile_id', array(
			'header'	=> Mage::helper('magebackup')->__('ID'),
			'align'		=> 'right',
			'width'		=> '50px',
			'index'		=> 'profile_id',
			'type'		=> 'number',
		));

		$this->addColumn('name', array(
			'header'	=> Mage::helper('magebackup')->__('Name'),
			'align'		=> 'left',
			'index'		=> 'name',
		));

		$this->addColumn('backup_type', array(
			'header'	=> Mage::helper('magebackup')->__('Backup Type'),
			'align'		=> 'left',
			'index'		=> 'backup_type',
			'filter'	=> false,
			'sortable'	=> false,
			'renderer'	=> 'magebackup/adminhtml_profile_renderer_backuptype',
			'width'		=> '200px',
		));

		$this->addColumn('cron_enable', array(
			'header'	=> Mage::helper('magebackup')->__('Scheduled Backup'),
			'align'		=> 'left',
			'index'		=> 'cron_enable',
			'filter'	=> false,
			'sortable'	=> false,
			'renderer'	=> 'magebackup/adminhtml_profile_renderer_cronenable',
			'width'		=> '200px',
		));

		$this->addColumn('cloud_upload', array(
			'header'	=> Mage::helper('magebackup')->__('Cloud Upload'),
			'align'		=> 'left',
			'index'		=> 'cloud_upload',
			'filter'	=> false,
			'sortable'	=> false,
			'renderer'	=> 'magebackup/adminhtml_profile_renderer_cloudupload',
			'width'		=> '200px',
		));

		$this->addColumn('action', array(
			'header'	=> Mage::helper('magebackup')->__('Action'),
			'width'		=> '100px',
			'type'		=> 'action',
			'getter'	=> 'getId',
			'actions'	=> array(
				array(
					'caption'	=> Mage::helper('magebackup')->__('Edit'),
					'url'		=> array('base' => '*/*/edit'),
					'field'		=> 'id',
				),
			),
			'filter'	=> false,
			'sortable'	=> false,
			'index'		=> 'stores',
			'is_system'	=> true,
		));

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction() {
		$this->setMassactionIdField('profile_id');
		$this->getMassactionBlock()->setFormFieldName('profile');
		$this->getMassactionBlock()->addItem('delete', array(
			'label'		=> Mage::helper('magebackup')->__('Delete'),
			'url'		=> $this->getUrl('*/*/massDelete'),
			'confirm'	=> Mage::helper('magebackup')->__('Are you sure?'),
		));

		return parent::_prepareMassaction();
	}

	public function getRowUrl($item) {
		return $this->getUrl('*/*/edit', array('id' => $item->getId()));
	}

	public function getGridUrl() {
		return $this->getUrl('*/*/grid', array('_current' => true));
	}
}
<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup adminhtml backup grid block.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Block_Adminhtml_Backup_Grid extends Mage_Adminhtml_Block_Widget_Grid {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->setId('backupGrid');
		$this->setDefaultSort('backup_id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}

	protected function _prepareCollection() {
		$collection	= Mage::getModel('magebackup/backup')->getCollection();

		$this->setCollection($collection);

		return parent::_prepareCollection();
	}

	protected function _prepareColumns() {
		$this->addColumn('backup_id', array(
			'header'	=> Mage::helper('magebackup')->__('ID'),
			'align'		=> 'right',
			'width'		=> '50px',
			'index'		=> 'backup_id',
			'type'		=> 'number',
		));

		$this->addColumn('name', array(
			'header'	=> Mage::helper('magebackup')->__('Name'),
			'align'		=> 'left',
			'index'		=> 'name',
			'renderer'	=> 'magebackup/adminhtml_backup_renderer_namedescription',
		));

		$profiles	= Mage::getSingleton('magebackup/profile')->getProfilesArray();
		$this->addColumn('profile_id', array(
			'header'	=> Mage::helper('magebackup')->__('Profile'),
			'align'		=> 'left',
			'width'		=> '15%',
			'index'		=> 'profile_id',
			'type'		=> 'options',
			'options'	=> $profiles,
		));

		$this->addColumn('status', array(
			'header'	=> Mage::helper('magebackup')->__('Status'),
			'align'		=> 'left',
			'index'		=> 'status',
			'width'		=> '70px',
		));
		
		$this->addColumn('duration', array(
			'header'	=> Mage::helper('magebackup')->__('Duration'),
			'align'		=> 'left',
			'renderer'	=> 'magebackup/adminhtml_backup_renderer_duration',
			'filter'	=> false,
			'sortable'	=> false,
			'width'		=> '90px',
		));

		$this->addColumn('file_name', array(
			'header'	=> Mage::helper('magebackup')->__('Download'),
			'align'		=> 'left',
			'index'		=> 'file_name',
			'renderer'	=> 'magebackup/adminhtml_backup_renderer_download',
			'class'		=> 'nowrap',
			'width'		=> '270px',
		));

		$this->addColumn('file_size', array(
			'header'	=> Mage::helper('magebackup')->__('File Size'),
			'align'		=> 'left',
			'index'		=> 'file_size',
			'filter'	=> false,
			'sortable'	=> false,
			'renderer'	=> 'magebackup/adminhtml_backup_renderer_size',
			'width'		=> '100px',
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
				array(
					'caption'	=> Mage::helper('magebackup')->__('Delete'),
					'url'		=> array('base' => '*/*/delete'),
					'field'		=> 'id',
					'confirm'   => Mage::helper('adminhtml')->__('Are you sure you want to do this?'),
				),
				array(
					'caption'	=> Mage::helper('magebackup')->__('Delete Files'),
					'url'		=> array('base' => '*/*/deleteFiles'),
					'field'		=> 'id',
					'confirm'   => Mage::helper('adminhtml')->__('Are you sure you want to do this?'),
				),
				array(
					'caption'	=> Mage::helper('magebackup')->__('Delete Log'),
					'url'		=> array('base' => '*/*/deleteLog'),
					'field'		=> 'id',
					'confirm'   => Mage::helper('adminhtml')->__('Are you sure you want to do this?'),
				)
			),
			'filter'	=> false,
			'sortable'	=> false,
			'index'		=> 'stores',
			'is_system'	=> true,
		));

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction() {
		$this->setMassactionIdField('backup_id');
		$this->getMassactionBlock()->setFormFieldName('backup');

		$this->getMassactionBlock()->addItem('delete', array(
			'label'		=> Mage::helper('magebackup')->__('Delete'),
			'url'		=> $this->getUrl('*/*/massDelete'),
			'confirm'	=> Mage::helper('magebackup')->__('Are you sure you want to do this?'),
		));

		$this->getMassactionBlock()->addItem('deleteFiles', array(
			'label'		=> Mage::helper('magebackup')->__('Delete Files'),
			'url'		=> $this->getUrl('*/*/massDeleteFiles'),
			'confirm'	=> Mage::helper('magebackup')->__('Are you sure you want to do this?'),
		));

		$this->getMassactionBlock()->addItem('deleteLog', array(
			'label'		=> Mage::helper('magebackup')->__('Delete Log'),
			'url'		=> $this->getUrl('*/*/massDeleteLog'),
			'confirm'	=> Mage::helper('magebackup')->__('Are you sure you want to do this?'),
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
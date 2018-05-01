<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Trigger Email Suite
 * @version   1.0.1
 * @revision  168
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_EmailReport_Block_Adminhtml_Recipient_Single_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('emailreport_recipient_single_grid');
        $this->setDefaultSort('email');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('emailreport/recipient_collection')->getSingleRecipientCollection();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('recipient_email', array(
            'header'    => Mage::helper('emailreport')->__('Email'),
            'index'     => 'recipient_email',
            'width'     => '100px',
        ));

        $this->addColumn('emails_num', array(
            'header'    => Mage::helper('emailreport')->__('Total Emails'),
            'align'     => 'left',
            'type'      => 'number',
            'index'     => 'emails_num',
        ));

        $this->addColumn('emails_num_delivered', array(
            'header'    => Mage::helper('emailreport')->__('Total Emails (Delivered)'),
            'align'     => 'left',
            'type'      => 'number',
            'index'     => 'emails_num_delivered',
        ));

        $this->addColumn('emails_num_pending', array(
            'header'    => Mage::helper('emailreport')->__('Total Emails (Pending)'),
            'align'     => 'left',
            'type'      => 'number',
            'index'     => 'emails_num_pending',
        ));

        $this->addColumn('open_num', array(
            'header'    => Mage::helper('emailreport')->__('Number of Opens'),
            'align'     => 'left',
            'type'      => 'number',
            'index'     => 'open_num',
        ));

        $this->addColumn('click_num', array(
            'header'    => Mage::helper('emailreport')->__('Number of Clicks'),
            'align'     => 'left',
            'type'      => 'number',
            'index'     => 'click_num',
        ));

        $this->setFilterVisibility(false);

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}
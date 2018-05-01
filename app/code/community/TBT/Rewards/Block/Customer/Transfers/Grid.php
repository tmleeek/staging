<?php

/**
 * WDCA - Sweet Tooth
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the WDCA SWEET TOOTH POINTS AND REWARDS 
 * License, which extends the Open Software License (OSL 3.0).

 * The Open Software License is available at this URL: 
 * http://opensource.org/licenses/osl-3.0.php
 * 
 * DISCLAIMER
 * 
 * By adding to, editing, or in any way modifying this code, WDCA is 
 * not held liable for any inconsistencies or abnormalities in the 
 * behaviour of this code. 
 * By adding to, editing, or in any way modifying this code, the Licensee
 * terminates any agreement of support offered by WDCA, outlined in the 
 * provided Sweet Tooth License. 
 * Upon discovery of modified code in the process of support, the Licensee 
 * is still held accountable for any and all billable time WDCA spent 
 * during the support process.
 * WDCA does not guarantee compatibility with any other framework extension. 
 * WDCA is not responsbile for any inconsistencies or abnormalities in the
 * behaviour of this code if caused by other framework extension.
 * If you did not receive a copy of the license, please send an email to 
 * support@sweettoothrewards.com or call 1.855.699.9322, so we can send you a copy 
 * immediately.
 * 
 * @category   [TBT]
 * @package    [TBT_Rewards]
 * @copyright  Copyright (c) 2014 Sweet Tooth Inc. (http://www.sweettoothrewards.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer Transfers
 *
 * @category   TBT
 * @package    TBT_Rewards
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */
class TBT_Rewards_Block_Customer_Transfers_Grid extends TBT_Rewards_Block_Widget_Grid
{
    protected $_defaultCellRenderer = 'rewards/customer_transfers_reference_renderer';

    protected $_pointsColumnHeader = null;

    protected $_defaultSort = 'creation_ts';
    protected $_defaultDir = 'desc';

    protected function _construct()
    {
        parent::_construct();

        $this->_pointsColumnHeader = $this->__("Points");
        $this->_emptyText = $this->__("You have no transfers.");

        $this->setId('transfers');
        $this->setUseAjax(true);

        return $this;
    }

    protected function _prepareCollection()
    {
        $customer = $this->getCustomer();
        $collection = Mage::getResourceModel('rewards/transfer_collection')
            ->selectPointsCaption('points_caption')
            ->addFieldToFilter('customer_id', $customer->getId());

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('points', array(
            'header'    => $this->_pointsColumnHeader,
            'index'     => 'points_caption',
            'width'     => '125px',
            'sortable'  => false  // TODO: it would be cool to sort by this
        ));

        $this->addColumn('creation_ts', array(
            'header'       => $this->__("Date"),
            'index'        => 'creation_ts',
            'filter_index' => 'rewards_transfer_id',
            'type'         => 'date',
            'width'        => '90px',
            'sortable'     => false  // TODO: it would be cool to sort by this
        ));

        $this->addColumn('comments', array(
            'header'    => $this->__("Comment"),
            'index'     => 'comments',
            'type'      => 'text',
            'nl2br'     => true,
            'escape'    => true,
            'sortable'  => false
        ));

        $this->addColumn('status', array(
            'header'    => $this->__("Status"),
            'index'     => 'status',
            'type'      => 'options',
            'options'   => Mage::getSingleton('rewards/transfer_status')->getOptionArray(),
            'width'     => '120px',
            'sortable'  => false  // TODO: it would be cool to sort by this
        ));

        $this->addColumn('reference', array(
            'header'    => "&#160;",
            'index'     => 'reference',
            'renderer'  => $this->_defaultCellRenderer,
            'width'     => '90px',
            'sortable'  => false
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('rewards/customer_transfers/grid');
    }

    /**
     * Returns a rewards customer
     * @return TBT_Rewards_Model_Customer
     */
    public function getCustomer()
    {
        $customer = Mage::registry( 'customer' );
        $customer = Mage::getModel('rewards/customer')->getRewardsCustomer($customer);
        return $customer;
    }
}

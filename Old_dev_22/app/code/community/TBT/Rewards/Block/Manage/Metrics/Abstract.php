<?php

/**
*
*/
class TBT_Rewards_Block_Manage_Metrics_Abstract extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'rewards';
        $this->setTemplate('rewards/metrics/grid/container.phtml');
        $this->_removeButton('add');
        $this->addButton('filter_form_submit', array(
          'label'   => Mage::helper('rewards')->__('Show Report'),
          'onclick' => 'filterFormSubmit()'
        ));

        return $this;
    }
}
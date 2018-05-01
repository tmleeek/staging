<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_AutoCancelOrder_Block_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('AutoCancelOrderShowLog');
        $this->_parentTemplate = $this->getTemplate();
        //$this->setTemplate('Shipping/List.phtml');	
        $this->setEmptyText($this->__('No items'));
        $this->setDefaultSort('aco_id');
        $this->setDefaultDir('DESC');
    }

    /**
     * Load collection of model
     *
     * @return unknown
     */
    protected function _prepareCollection()
    {		         
        $collection = Mage::getModel('AutoCancelOrder/Log')
        	->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
   /**
     * define columns that will be displayed 
     *
     * @return unknown
     */
    protected function _prepareColumns()
    {
        $this->addColumn('aco_id', array(
            'header'=> Mage::helper('AutoCancelOrder')->__('Id'),
            'index' => 'aco_id'
        ));

        $this->addColumn('aco_date', array(
            'header'=> Mage::helper('AutoCancelOrder')->__('Date'),
            'index' => 'aco_date',
            'type' => 'datetime'
        ));
        
        $this->addColumn('aco_message', array(
            'header'=> Mage::helper('AutoCancelOrder')->__('Message'),
            'index' => 'aco_message',
            'renderer' => 'MDN_AutoCancelOrder_Block_Widget_Grid_Column_Renderer_Message'
        ));


        return parent::_prepareColumns();
    }


    /**
     * return the template of Block_Widget_Grid to generat html
     * 
     * @return type 
     */
    public function getGridParentHtml()
    {
        $templateName = Mage::getDesign()->getTemplateFilename($this->_parentTemplate, array('_relative'=>true));
        return $this->fetchView($templateName);
    }
    
    /**
     * Call Edit action from controller for each row (database entry)
     */
    public function getRowUrl($row) {
        return $this->getUrl('AutoCancelOrder/Admin/viewOrder', array('log_id' => $row->getId()));
    }

    
    /**
     * return url to come back to the config page
     */
    public function getBackUrl(){
        return $this->getUrl('AutoCancelOrder/Admin/backToConfig');
    }
    
    /**
     * return admin controller action : clearLogs() to erase database entries
     */
    public function getDeleteUrl(){
         return $this->getUrl('AutoCancelOrder/Admin/clearLogs');
    }
 
}

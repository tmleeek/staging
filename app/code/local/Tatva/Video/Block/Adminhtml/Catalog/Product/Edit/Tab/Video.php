<?php
/**
 * created : 17 septembre 2009
 * 
 * EXIG FOU-001 FOU-002
 * REG BO-601
 * 
 * @category SQLI
 * @package Sqli_Video
 * @author alay
 * @copyright SQLI - 2009 - http://www.sqli.com
 */

/**
 * 
 * @package Sqli_Video
 */
class Tatva_Video_Block_Adminhtml_Catalog_Product_Edit_Tab_Video extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('video');
        $this->setDefaultSort('video_item_id');
        $this->setUseAjax(true);
        $this->setTemplate('catalog/product/video/widget/grid.phtml');
    }
    
    protected function _prepareLayout()
    {
    	$productId = $this->getRequest()->getParam('id');
    	
    	if($productId){
        	$this->setChild('add_button',
            	$this->getLayout()->createBlock('tatvavideo/adminhtml_catalog_product_edit_tab_video_create'));
    	}
        return parent::_prepareLayout();
    }
        
    /**
     * Retrieve currently edited product model
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        return Mage::registry('current_product');
    }

    protected function _prepareCollection()
    {
    	$productId = $this->getRequest()->getParam('id');
    	if(!$productId){
    		$productId = -1;
    	}
    	$collection = Mage::getModel('tatvavideo/item')
    		->getCollection()
    		->addProductIdFilter($productId)
			->addOrderFilter();
        
    	$this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
    
    protected function _prepareColumns()
    {
        
		$this->addColumn('video_item_id', array(
            'header'    => Mage::helper('tatvavideo')->__('Video ID'),
            'index'     => 'video_item_id',
			'width' => '200px'
            
        ));
		
	   $this->addColumn('video_url', array(
            'header'    => Mage::helper('tatvavideo')->__('Video URL'),
            'index'     => 'video_url'
        ));
        
        
       

  	    $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('tatvavideo')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
        return parent::_prepareColumns();
    }
    
	public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    
 	public function getMainButtonsHtml()
    {
        $html = $this->getAddButtonHtml();
        return $html.parent::getMainButtonsHtml();
    }
    
    public function getGridUrl()
    {
        return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('*/*/gridOnlyVideoAndSupplier', array('_current'=>true));
    }
    
	public function getVideoEditUrl(){
		return $this->getUrl(
                '*/*/videoEdit',
                array(
                    'popup'     => 1
                )
            );
	}
	
    public function getUpdateJsObjectName(){
    	return 'update'.$this->getJsObjectName();
    }
}

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
class Tatva_Video_Block_Adminhtml_Catalog_Product_Edit_Tab_Video_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'video_item_id';
        $this->_blockGroup = 'tatvavideo';
        $this->_controller = 'adminhtml_catalog_product_edit_tab_video';

        parent::__construct();

        if($this->getRequest()->getParam('popup')) {
            $this->_removeButton('back');
            $this->_addButton(
                'close',
                array(
                    'label'     => Mage::helper('catalog')->__('Close Window'),
                    'class'     => 'cancel',
                    'onclick'   => 'window.close()',
                    'level'     => -1
                )
            );
        }

        $this->_updateButton('save', 'label', Mage::helper('tatvavideo')->__('Save Video'));
		

        if (! Mage::registry('video_item_data')->getId()) {
            $this->_removeButton('delete');
        } else {
            $this->_updateButton('delete', 'label', Mage::helper('tatvavideo')->__('Delete Video'));
        }
    }

    public function getHeaderText()
    {   
    	
	    if (Mage::registry('video_item_data')->getId()) {
            return Mage::helper('tatvavideo')->__('Edit Video');
        }
        else {
            return Mage::helper('tatvavideo')->__('New Vieo');
        }
    }

    public function getDeleteUrl()
    {
        return 
        	$this->getUrl('tatvavideo/adminhtml_video/delete',
	   			array(
	   				'popup' => true,
	   				'video_item_id' => $this->getRequest()->getParam('video_item_id'),
	   			));
	}
	
    /**
     * URL Validate action
     */
    public function getValidationUrl()
    {
        return $this->getUrl('tatvavideo/adminhtml_video/validate', array('_current'=>true));
    }

}
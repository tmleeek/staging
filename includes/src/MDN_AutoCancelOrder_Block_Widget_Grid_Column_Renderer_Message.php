<?php

class MDN_AutoCancelOrder_Block_Widget_Grid_Column_Renderer_Message
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        // get current message
        $message = $row->getaco_message();
        
        // detect if message containt errors
        if( strpos($message, "canceled successfully") ){
            // good
            $html = '<p id="aco_'.$row->getaco_id().'" class="aco-message-cancel-success">'.$message.'</p>';
        } 
        if( strpos($message, "successfully unhold") ){
            // good
            $html = '<p id="aco_'.$row->getaco_id().'" class="aco-message-unhold-success">'.$message.'</p>';
        }
        if( strpos($message, "can not be canceled") ){
            $html = '<p id="aco_'.$row->getaco_id().'" class="aco-message-error">'.$message.'</p>';
        }
        
    	
    	return $html;
    }

}
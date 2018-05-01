<?php
class Tatva_Advice_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/advice?id=15 
    	 *  or
    	 * http://site.com/advice/id/15 	
    	 */
    	/* 
		$advice_id = $this->getRequest()->getParam('id');

  		if($advice_id != null && $advice_id != '')	{
			$advice = Mage::getModel('advice/advice')->load($advice_id)->getData();
		} else {
			$advice = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($advice == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$adviceTable = $resource->getTableName('advice');
			
			$select = $read->select()
			   ->from($adviceTable,array('advice_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$advice = $read->fetchRow($select);
		}
		Mage::register('advice', $advice);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}
<?php
class Tatva_Material_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/material?id=15 
    	 *  or
    	 * http://site.com/material/id/15 	
    	 */
    	/* 
		$material_id = $this->getRequest()->getParam('id');

  		if($material_id != null && $material_id != '')	{
			$material = Mage::getModel('material/material')->load($material_id)->getData();
		} else {
			$material = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($material == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$materialTable = $resource->getTableName('material');
			
			$select = $read->select()
			   ->from($materialTable,array('material_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$material = $read->fetchRow($select);
		}
		Mage::register('material', $material);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}
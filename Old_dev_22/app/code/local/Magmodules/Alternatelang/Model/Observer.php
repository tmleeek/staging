<?php
/**
 * Magmodules.eu - http://www.magmodules.eu - info@magmodules.eu
 * =============================================================
 * NOTICE OF LICENSE [Single domain license]
 * This source file is subject to the EULA that is
 * available through the world-wide-web at:
 * http://www.magmodules.eu/license-agreement/
 * =============================================================
 * @category    Magmodules
 * @package     Magmodules_Alternatelang
 * @author      Magmodules <info@magmodules.eu>
 * @copyright   Copyright (c) 2016 (http://www.magmodules.eu)
 * @license     http://www.magmodules.eu/license-agreement/  
 * =============================================================
 */
 
class Magmodules_Alternatelang_Model_Observer {

	public function addExtraField($observer) 
	{			
		if(Mage::getStoreConfig('alternatelang/config/cms_categories')) {		
			$model = Mage::registry('cms_page');
			$form = $observer->getForm();
		
			$collection = Mage::getModel('cms/page')->getCollection()->distinct(true)->addFieldToSelect('alternate_category')->setOrder('title','ASC');
			$collection->setFirstStoreFlag(true);
		
			$options[] = array('value'=> '', 'label'=> '');				
			$pages= '';
		
			foreach($collection as $option) {
				if($option->getAlternateCategory()) {
					$options[] = array('value'=> $option->getAlternateCategory(), 'label'=> $option->getAlternateCategory());				
					$pages .= '- ' . $option->getTitle() . '<br/>';
				}
			}
		
			$options[] = array('value'=> '-1', 'label'=> '-- ' . Mage::helper('alternatelang')->__('Add new'));				

			$fieldset = $form->addFieldset('alternatelang_category_fieldset', array('legend'=>Mage::helper('cms')->__('Alternate Language Settings'), 'class'=>'fieldset-wide'));

			$fieldset->addField('alternate_category', 'select', array(
				'name'      => 'alternate_category',
				'label'     => Mage::helper('alternatelang')->__('Alternate Language Category'),
				'title'     => Mage::helper('alternatelang')->__('Alternate Language Category'),
				'disabled'  => false,
				'value'     => $model->getAlternateCategory(),
				'values'	=> $options,
				'onchange' 	=> 'category_new()',
			));

			$fieldset->addField('alternate_category_new', 'text', array(
				'name'      => 'alternate_category_new',
				'label'     => Mage::helper('alternatelang')->__('New Category'),
				'title'     => Mage::helper('alternatelang')->__('New Category'),
				'disabled'  => false,
				'value'     => '',
			));
		}	
	}

	public function cms_page_prepare_save(Varien_Event_Observer $observer) 
	{
		if(Mage::getStoreConfig('alternatelang/config/cms_categories')) {		       
			$params = $observer->getRequest()->getParams(); 
			$page = $observer->getPage();
		
			if(($params['alternate_category'] == '-1') && (isset($params['alternate_category_new']))) {
				$page->setData('alternate_category', $params['alternate_category_new']);
			}
			return $this;
		}
    }

	public function addMessage($observer) 
	{			
		Mage::getSingleton('core/session')->addNotice(Mage::helper('alternatelang')->__('Please flush your cache now, otherwise the changes will not be visible on the frontend and source.'));
	}    
	    
}
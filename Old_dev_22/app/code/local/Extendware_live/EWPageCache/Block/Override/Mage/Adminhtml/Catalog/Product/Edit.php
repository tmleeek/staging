<?php
class Extendware_EWPageCache_Block_Override_Mage_Adminhtml_Catalog_Product_Edit extends Extendware_EWPageCache_Block_Override_Mage_Adminhtml_Catalog_Product_Edit_Bridge
{
	protected function _prepareLayout()
    {
    	$this->setChild('ewpagecache_flush_fpc',
			$this->getLayout()->createBlock('adminhtml/widget_button')
				->setData(array(
					'label'     => Mage::helper('ewpagecache')->__('Flush FPC'),
					'onclick'   => "flushByTag('" . $this->getFlushFpcUrl() . "');",
					'class' => 'delete'
				))
		);
        return parent::_prepareLayout();
    }
    
	public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('ewpagecache_flush_fpc') . $this->getChildHtml('delete_button');
    }
    
	public function getFlushFpcUrl() {
		$params = array(
			'type' => 'product', 
			'id' => Mage::app()->getRequest()->getParam('id'),
		);
    	return $this->getUrl('extendware_ewpagecache/adminhtml_cache/flushByTagAjax', $params);
    }
    
	protected function _toHtml()
    {
    	$script = '<script>';
    	$script .= "
    	function flushByTag(url) {
	    	new Ajax.Request(url, {
    			method: 'get',
				onSuccess: function(response) {
    				alert(response.responseText);
				},
    			onFailure: function(response) {
    				alert('" . Mage::helper('ewpagecache')->__('Error encountered when trying to flush cache') . "');
    			}
			});
    	};
    			";
    	$script .= '</script>';
        return $script . parent::_toHtml();
    }
}

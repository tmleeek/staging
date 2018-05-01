<?php
class Extendware_EWPageCache_Block_Override_Mage_Adminhtml_Cms_Page_Edit extends Extendware_EWPageCache_Block_Override_Mage_Adminhtml_Cms_Page_Edit_Bridge
{
	public function __construct()
    {
        $this->_addButton('ewpagecache_flush_fpc', array(
			'label' => Mage::helper('ewpagecache')->__('Flush FPC'),
			'onclick'   => "flushByTag('" . $this->getFlushFpcUrl() . "');",
			'class' => 'delete',
        ), 0);
        return parent::__construct();
    }
    
	public function getFlushFpcUrl() {
		$params = array(
			'type' => 'page', 
			'id' => Mage::app()->getRequest()->getParam('page_id'),
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

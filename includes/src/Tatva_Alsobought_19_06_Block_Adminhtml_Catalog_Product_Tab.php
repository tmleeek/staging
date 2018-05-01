<?php

class Tatva_Alsobought_Block_Adminhtml_Catalog_Product_Tab extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface {
 

	protected $productId;
    /**
     * Set the template for the block
     *
     */
    public function _construct()
    {
        parent::_construct();
 		$this->productId = $this->getRequest()->getParam('id');
        $this->setTemplate('alsobought/catalog/product/tab.phtml');
    }

    /**
     * Retrieve the label used for the tab relating to this block
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Also Bought');
    }
 
    /**
     * Retrieve the title used by this tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Also Bought');
    }
 
    /**
     * Determines whether to display the tab
     * Add logic here to decide whether you want the tab to display
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }
 
    /**
     * Stops the tab being hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
 
    /**
     * AJAX TAB's
     * If you want to use an AJAX tab, uncomment the following functions
     * Please note that you will need to setup a controller to recieve
     * the tab content request
     *
     */
    /**
     * Retrieve the class name of the tab
     * Return 'ajax' here if you want the tab to be loaded via Ajax
     *
     * return string
     */
   public function getTabClass()
   {
       return 'ajax';
   }
 
    /**
     * Determine whether to generate content on load or via AJAX
     * If true, the tab's content won't be loaded until the tab is clicked
     * You will need to setup a controller to handle the tab request
     *
     * @return bool
     */
   public function getSkipGenerateContent()
   {
       return true;
   }

    /**
     * Retrieve the URL used to load the tab content
     * Return the URL here used to load the content by Ajax
     * see self::getSkipGenerateContent & self::getTabClass
     *
     * @return string
     */
   public function getTabUrl()
   {
   		$store = ($this->getRequest()->getParam('store'))?$this->getRequest()->getParam('store'):'';
		$url = 'adminhtml/catalog_product/alsobought/id/'.$this->productId;
		if($store != '')
			$url .= '/store/'.$store;
       	return Mage::getUrl($url);
   }
 
}
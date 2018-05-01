<?php
class Pektsekye_OptionImages_Block_Adminhtml_Catalog_Product_Edit_Js extends Mage_Adminhtml_Block_Widget
{

	protected $_product;   

	protected function _prepareLayout()
    {

        $this->setChild(
            'upload_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'   => Mage::helper('optionimages')->__('Upload Images'),
					'onclick' => 'instOptionImages.massUploadByType(\\\'links\\\')'
               
                ))
        );

    }


    /**
     * Retrieve Upload button HTML
     *
     * @return string
     */
    public function getUploadButtonHtml()
    {
        return $this->getChildHtml('upload_button');
    }	
	
    /**
     * Retrive config json
     *
     * @return string
     */
    public function getConfigJson()
    {
		$this->getConfig()->setUrl(Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/catalog_product_gallery/upload'));		
        $this->getConfig()->setParams(array('form_key' => $this->getFormKey()));

		$this->getConfig()->setFileField('image');
        $this->getConfig()->setFilters(array(
            'images'    => array(
                'label' => Mage::helper('optionimages')->__('Images (.gif, .jpg, .png)'),
                'files' => array('*.gif','*.jpg','*.jpeg','*.png')
            )
        ));
        $this->getConfig()->setReplaceBrowseWithRemove(true);
        $this->getConfig()->setWidth('32');
        $this->getConfig()->setHideUploadButton(true);
        return Zend_Json::encode($this->getConfig()->getData());
    }

    /**
     * Retrive config object
     *
     * @return Varien_Config
     */
    public function getConfig()
    {
        if(is_null($this->_config)) {
            $this->_config = new Varien_Object();
        }

        return $this->_config;
    }	
	
	
    public function getOptionImages()
    { 
		$config = array();
		$mediaconfig = Mage::getSingleton('catalog/product_media_config');
		$helper = Mage::helper('optionimages');
		
		foreach ($helper->getOptionImages() as $value) {
			$config[$value->getOption_id()][$value->getId()] = array(array('url' => $mediaconfig->getMediaUrl($value->getImage())));
		}	
		
        return Zend_Json::encode($config);
    }
	
}
<?php

class MDN_Mpm_Block_Products_Tabs_Information extends Mage_Adminhtml_Block_Widget  {

    protected $_offerInformation = null;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('Mpm/Products/Tabs/Information.phtml');
    }
    public function getProduct()
    {
        return Mage::registry('mpm_product');
    }

    public function getChannel()
    {
        return Mage::registry('mpm_channel');
    }

    public function getChannelLabel()
    {
        return Mage::helper('Mpm/Carl')->getChannelLabel(Mage::registry('mpm_channel'));
    }

    public function getOfferInformation()
    {
        if($this->getChannel() === 'custom_nc_default') {
            return null;
        }

        if ($this->_offerInformation == null)
        {
            try
            {

                $this->_offerInformation = Mage::helper('Mpm/Carl')
                    ->getOfferInformation($this->getProduct()->getProductId(), $this->getChannel());
            }
            catch(Exception $ex)
            {
                $this->_offerInformation = false;
            }

        }
        return $this->_offerInformation;
    }

    public  function getIdMagento($sku)
    {
        return Mage::getModel('catalog/product')->loadByAttribute('sku', $sku)->getId();
    }

    public function getImageMagento($sku)
    {
        $product = Mage::getModel('catalog/product')->load($this->getIdMagento($sku));
        return Mage::getModel('catalog/product_media_config')
            ->getMediaUrl( $product->getImage() );
    }

    public function getImage($data)
    {
        if (isset($data->_source->image_url[0]))
            return $data->_source->image_url[0];
        else
            return false;
    }

    public function getQty()
    {
        $_product = Mage::getModel('catalog/product')->load($this->getIdMagento($this->getProduct()->getProductId()));
        $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product);
        return (int)$stockItem->getQty();
    }

    public function getStatus()
    {
        $_product = Mage::getModel('catalog/product')->load($this->getIdMagento($this->getProduct()->getProductId()));

        switch($_product->getStatus())
        {
            case 1: return $this->__('Enabled');
            case 2: return $this->__('Disabled');
        }
    }

}
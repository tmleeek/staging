<?php

class TBT_Rewardssocial_Block_Pinterest_Pin_Button extends TBT_Rewardssocial_Block_Widget_Abstract implements TBT_Rewardssocial_Block_Pinterest_Pin_Button_Interface
{
    protected $_predictedPoints = null;
    protected $_customer = null;

    public function _prepareLayout()
    {
        if (!Mage::helper('rewardssocial/pinterest_config')->isPinningEnabled()) {
            $this->setIsHidden(true);
        }

        return parent::_prepareLayout();
    }

    public function getHasPinned()
    {
        $customer = $this->_getRS()->getSessionCustomer();
        if (!$customer->getId()) {
            return false;
        }

        $url = Mage::helper('core/url')->getCurrentUrl();
        return $this->_getValidator()->hasPinned($customer->getId(), $url);
    }

    public function getHasPredictedPoints()
    {
        $predictedPoints = $this->getPredictedPoints();
        return !empty($predictedPoints) && !$this->getHasPinned();
    }

    public function getNotificationBlock()
    {
        return $this->getLayout()->createBlock('core/template')
            ->setTemplate('rewardssocial/pinterest/pin/points.phtml')
            ->setPredictedPointsString($this->getPredictedPointsString());
    }

    public function getPredictedPoints()
    {
        if ($this->_predictedPoints === null ) {
            $this->_predictedPoints = $this->_getValidator()->getPredictedPinterestPinPoints();
        }

        return $this->_predictedPoints;
    }

    public function isCounterEnabled()
    {
        $countEnabled = Mage::helper('rewardssocial/pinterest_config')->isPinningCounterEnabled();
        return $countEnabled;
    }

    public function getCustomerPinterestUsername()
    {
        return $this->getCustomer()->getPinterestUsername();
    }

    public function getRequestUri()
    {
        // We must use original request because $this->getRequest() contains the controller/action it mapped to.
        $request = $this->getRequest()->getOriginalRequest();

        $scheme = $request->getScheme();
        $domain = $request->getHttpHost();
        $requestPath = $request->getRequestUri();

        return $scheme . "://" . $domain . $requestPath;
    }

    public function getRequestUriEncoded()
    {
        return urlencode($this->getRequestUri());
    }

    public function getPinnableMediaUri()
    {
        $product = Mage::registry('product');
        return $this->helper('catalog/image')->init($product, 'image');
    }

    /**
     * This return the link to the product image that will be pinned.
     * This actually point to a controller that will redirect to the image needed for Pinterest. We do this to be able
     * to observe when a user actually pins a product or not and reward them after that.
     *
     * @return string
     */
    public function getPinnableMediaUriEncoded()
    {
        $uri = $this->getUrl('rewardssocial/index/observePinning');
        // remove '___SID =U' from url
        $uri = Mage::getModel('core/url')->sessionUrlVar($uri);

        $data              = array();
        $data['productId'] = $this->getProductId();

        $customerId    = $this->getCustomer()->getId();
        if ($customerId) {
            $data['customerId'] = $customerId;
        }

        $productId  = $this->getProductId();
        if ($productId) {
            $data['productId'] = $productId;
        }

        $url = Mage::helper('core/url')->getCurrentUrl();
        // remove '___SID =U' from url
        $url = Mage::getModel('core/url')->sessionUrlVar($url);
        if ($url) {
            $data['url'] = $url;
        }

        $data = urlencode(Mage::helper('rewardssocial/crypt')->encrypt(json_encode($data)));
        $uri .= "?data={$data}";

        return urlencode($uri);
    }

    public function getProduct()
    {
        if ($this->_product == null) {
            $this->_product = $this->hasData('product') ? $this->getData('product') : Mage::registry('product');
        }

        return $this->_product;
    }

    /**
     * Returns product main image URL.
     * @return string|null Product image URL, or null
     */
    public function getProductImageUrl()
    {
        $product = $this->getProduct();
        if ($product->getImage() != 'no-selection' && $product->getImage()) {
            return Mage::helper('catalog/image')->init($product, 'image');
        }

        return null;
    }

    /**
     * Returns product's URL as configured in Magento admin.
     * @return string Product URL
     */
    public function getProductUrl()
    {
        $product = $this->getProduct();
        return Mage::helper('rewardssocial')->getProductUrl($product);
    }

    /**
     * Returns product ID
     * @return int Product ID
     */
    public function getProductId()
    {
        return $this->getProduct()->getId();
    }

    public function getCustomer()
    {
        if ($this->_customer === null) {
            $customerId = $this->_getSession()->getCustomer()->getId();
            $this->_customer = Mage::getModel('rewardssocial/customer')->load($customerId);
            $this->_customer->setId($customerId);
        }

        return $this->_customer;
    }

    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    protected function _getValidator()
    {
        return Mage::getSingleton('rewardssocial/pinterest_pin_validator');
    }
}

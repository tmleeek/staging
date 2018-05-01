<?php
/**
 * Facebook Share button
 */
class TBT_Rewardssocial_Block_Facebook_Share_Button extends TBT_Rewardssocial_Block_Widget_Abstract
    implements TBT_Rewardssocial_Block_Facebook_Share_Button_Interface
{
    var $_product = null;

    public function _prepareLayout()
    {
        if (!Mage::helper('rewardssocial/facebook_config')->isFbProductShareEnabled()) {
            $this->setIsHidden(true);
        }

        return parent::_prepareLayout();
    }

    /**
     * Creates notfication block in the layout. Check TBT_Rewardssocial_Block_Widgets::getPointsNotificationBlock()
     * @return $this
     */
    public function getNotificationBlock()
    {
        return $this->getLayout()->createBlock('core/template')
            ->setTemplate('rewardssocial/facebook/share/points.phtml')
            ->setPredictedPointsString($this->getPredictedPointsString());
    }

    public function getPredictedPointsString()
    {
        $predictedPoints = $this->getPredictedPoints();
        return (string) Mage::getModel('rewards/points')->set($predictedPoints);
    }

    /**
     * Checks if customer will earn any points for sharing a product on Facebook.
     * @return True, if customer will earn points for this action.
     */
    public function getHasPredictedPoints()
    {
        $predictedPoints = $this->getPredictedPoints();
        $hasPredictedPoints = !empty($predictedPoints) && !$this->getHasAlreadySharedProduct();

        return $hasPredictedPoints;
    }

    public function getHasAlreadySharedProduct()
    {
        $customer = $this->_getRs()->getSessionCustomer();
        if (!$customer->getId()) {
            return false;
        }
        $hasSharedProduct = $this->_getValidator()->hasAlreadySharedProduct($customer->getId(), $this->getProductId());

        return $hasSharedProduct;
    }

    public function getPredictedPoints()
    {
        if ($this->_predictedPoints === null ) {
            $this->_predictedPoints = $this->_getValidator()->getPredictedPoints();
        }

        return $this->_predictedPoints;
    }

    /**
     * Checks if counter is enabled. Facebook Share has no counter.
     * @return boolean False, as Facebook share has no counter.
     */
    public function isCounterEnabled()
    {
        return false;
    }

    /**
     * Returns product name
     * @return string|null Product name or null
     */
    public function getProductName()
    {
        if ($productName = $this->getProduct()->getName()) {
            return $productName;
        }

        return null;
    }

    /**
     * Returns product description. If available, short description is used otherwise normal description.
     * @return string|null Product short description or description, if first not available or null.
     */
    public function getProductDescription()
    {
        $product = $this->getProduct();
        if (($productDescription = $product->getShortDescription()) || ($productDescription = $product->getDescription())) {
            return $productDescription;
        }

        return null;
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

    public function getProduct()
    {
        if ($this->_product == null) {
            $this->_product = $this->hasData('product') ? $this->getData('product') : Mage::registry('product');
        }

        return $this->_product;
    }

    public function getOnClickAction()
    {
        $action = "fbShareAction(this, {url: '{$this->getProductUrl()}', eventName: 'facebook_product_share:response'}); return false;";
        return $action;
    }

    protected function _getValidator()
    {
        return Mage::getSingleton('rewardssocial/facebook_share_validator');
    }
}

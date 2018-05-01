<?php
/**
 * created : 12 oct. 2009
 * Catalog product model
 *
 * updated by <user> : <date>
 * Description of the update
 *
 * @category SQLI
 * @package Sqli_Catalog
 * @author ysanchez
 * @copyright SQLI - 2009 - http://www.tatva.com
 */

/**
 * Catalog product model
 *
 * @package Sqli_Catalog
 */
class Tatva_Catalog_Model_Product extends Mage_Catalog_Model_Product
{


   public function getLinkInstance()
    {
        if (!$this->_linkInstance) {
            $this->_linkInstance = Mage::getSingleton('catalog/product_link');
        }
        return $this->_linkInstance;
    }



    public function getAlsoBoughtProducts()
    {
        if (!$this->hasAlsoBoughtProducts()) {
            $products = array();
            foreach ($this->getAlsoBoughtProductCollection() as $product) {
                $products[] = $product;
            }
            $this->setAlsoBoughtProducts($products);
        }
        return $this->getData('also_bought_products');
    }

    /**
     * Retrieve also bought products identifiers
     *
     * @return array
     */
    public function getAlsoBoughtProductIds()
    {
        if (!$this->hasAlsoBoughtProductIds()) {
            $ids = array();
            foreach ($this->getAlsoBoughtProducts() as $product) {
                $ids[] = $product->getId();
            }
            $this->setAlsoBoughtProductIds($ids);
        }
        return $this->getData('also_bought_product_ids');
    }

    /**
     * Retrieve collection also bought product
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Link_Product_Collection
     */
    public function getAlsoBoughtProductCollection()
    {
        $collection = $this->getLinkInstance()
        	->useAlsoBoughtLinks()
            ->getProductCollection()
            ->setIsStrongMode()
            ->setProduct($this);
        return $collection;
    }


    public function getAlsoboughtProductCollectionProductPage($product)
    {
          $collection = $this->getLinkInstance()
        	->useAlsoBoughtLinks()
            ->getProductCollection()
            ->setIsStrongMode()
            ->setProduct($product);
        return $collection;
    }
    /**
     * Retrieve collection also bought link
     */
    public function getAlsoBoughtLinkCollection()
    {
        $collection = $this->getLinkInstance()->useAlsoBoughtLinks()
            ->getLinkCollection();

        $collection->setProduct($this);
        $collection->addLinkTypeIdFilter();
        $collection->addProductIdFilter();
        $collection->joinAttributes();
        return $collection;
    }


    public function getDiscountPercent($product) {
		$customerSession = Mage::getSingleton('customer/session');
		if($customer = $customerSession->getCustomer()) {
			$customerGroupId = $customer->getGroupId();
		} else {
			$customerGroupId = 0;
		}
		$websiteId = Mage::app()->getWebsite()->getId();
         //$todayDate = $product->getResource()->formatDate(time(), false);

        // $start =  $todayDate.' 23:59:59';
         //$end =    $todayDate.' 00:00:00';
        //$todayDate = date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time()));
        $todayDate = date("Y-m-d H:i", Mage::getModel('core/date')->timestamp(time())).':00';
		$catalogRuleProducts = Mage::getResourceModel('catalogrule/rule_product_price_collection')
								->addFieldToFilter('website_id',$websiteId)
								->addFieldToFilter('customer_group_id',$customerGroupId)
                                ->addFieldToFilter('latest_start_date', array('lteq'=>$todayDate))
								->addFieldToFilter('earliest_end_date', array('gteq'=>$todayDate));


		$catalogRuleProducts = $catalogRuleProducts->getProductIds();

		if (in_array($product->getId(),$catalogRuleProducts) || $product->getSqliProductpushSpecialsDisplay()) {
		   $price = $product->getPrice();

			if($price){
				$finalPrice = $product->getFinalPrice();
				$percent = round(($price-$finalPrice)*100/$price,0);
				return $percent;
			}
		}



    			$_taxHelper = Mage::helper('tax');
    			$_simplePricesTax = ($_taxHelper->displayPriceIncludingTax() || $_taxHelper->displayBothPrices());

    			$_regularPrice = $_taxHelper->getPrice($product, $product->getPrice(), $_simplePricesTax);
    			if($_regularPrice){
					$finalPrice = $_taxHelper->getPrice($product, $product->getFinalPrice(), $_simplePricesTax);

					$percent = round(100 - ($finalPrice * 100 / $_regularPrice));
					return $percent;
			   
    		}

		return 0;
	}



    }
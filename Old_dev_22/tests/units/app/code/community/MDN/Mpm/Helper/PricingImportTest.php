<?php namespace units\app\code\community\MDN\Mpm\Helper;

class PricingImportTest extends \units\BaseHelper {

    protected $_testClassName = 'MDN_Mpm_Helper_PricingImport';

    public function testSetPricingWithBadSku(){

        $sku = 'sku';
        $channel = 'amazon_fr_default';
        $finalPrice = 55.98;
        $shippingPrice = 2.5;
        $productId = null;

        $productMock = $this->buildMock('Mage_Catalog_Model_Product');
        $productMock->expects($this->once())
            ->method('getIdBySku')
            ->with($sku)
            ->will($this->returnValue($productId));

        $this->addMageModel('catalog/product', $productMock);

        $helperDataMock = $this->buildMock('MDN_Mpm_Helper_Data');
        $helperDataMock->expects($this->once())
            ->method('log')
            ->with('Unable to load sku '.$sku);

        $this->addMageHelper('Mpm', $helperDataMock);

        $result = $this->_instance->setPricing($sku, $channel, $finalPrice, $shippingPrice);

        $this->assertFalse($result);
    }

    public function testSetPricingWithEmptyPriceAttribute(){

        $sku = 'sku';
        $channel = 'amazon_fr_default';
        $finalPrice = 55.98;
        $shippingPrice = 2.5;
        $productId = 100;

        $productMock = $this->buildMock('Mage_Catalog_Model_Product');
        $productMock->expects($this->once())
            ->method('getIdBySku')
            ->with($sku)
            ->will($this->returnValue($productId));

        $this->addMageModel('catalog/product', $productMock);

        $this->addMageStoreConfig('mpm/repricing/price_attributes_'.$channel, null);
        $this->addMageStoreConfig('mpm/repricing/shipping_attributes_'.$channel, null);

        $result = $this->_instance->setPricing($sku, $channel, $finalPrice, $shippingPrice);

        $this->assertEmpty($result);

    }

    public function testSetPricingWithoutSavingProduct(){

        $sku = 'sku';
        $channel = 'amazon_fr_default';
        $finalPrice = 55.98;
        $shippingPrice = 2.5;
        $productId = 100;
        $mpmPriceAttribute = 'mpm_price';
        $mpmShippingPriceAttribute = 'mpm_shipping_price';

        $productMock = $this->buildMock('Mage_Catalog_Model_Product');
        $productMock->expects($this->once())
            ->method('getIdBySku')
            ->with($sku)
            ->will($this->returnValue($productId));

        $this->addMageModel('catalog/product', $productMock);

        $this->addMageStoreConfig('mpm/repricing/price_attributes_'.$channel, $mpmPriceAttribute);
        $this->addMageStoreConfig('mpm/repricing/shipping_attributes_'.$channel, $mpmShippingPriceAttribute);

        $catalogResourceProductActionMock = $this->buildMock('Mage_Catalog_Model_Resource_Product_Action');
        $catalogResourceProductActionMock->expects($this->once())
            ->method('updateAttributes')
            ->with(array($productId), array($mpmPriceAttribute => $finalPrice, $mpmShippingPriceAttribute => $shippingPrice), 0);

        $this->addMageModel('catalog/resource_product_action', $catalogResourceProductActionMock);

        $this->addMageStoreConfig('mpm/repricing/save_product_after_update', null);
        $this->addMageStoreConfig('mpm/repricing/change_product_updated_at', null);

        $result = $this->_instance->setPricing($sku, $channel, $finalPrice, $shippingPrice);

        $this->assertEmpty($result);

    }

    public function testSetPricingWithoutProductSaveWithTouchAfterUpdate(){

        $sku = 'sku';
        $channel = 'amazon_fr_default';
        $finalPrice = 55.98;
        $shippingPrice = 2.5;
        $productId = 100;
        $mpmPriceAttribute = 'mpm_price';
        $mpmShippingPriceAttribute = 'mpm_shipping_price';

        $productMock = $this->buildMock('Mage_Catalog_Model_Product');
        $productMock->expects($this->once())
            ->method('getIdBySku')
            ->with($sku)
            ->will($this->returnValue($productId));

        $this->addMageModel('catalog/product', $productMock);

        $this->addMageStoreConfig('mpm/repricing/price_attributes_'.$channel, $mpmPriceAttribute);
        $this->addMageStoreConfig('mpm/repricing/shipping_attributes_'.$channel, $mpmShippingPriceAttribute);

        $catalogResourceProductActionMock = $this->buildMock('Mage_Catalog_Model_Resource_Product_Action');
        $catalogResourceProductActionMock->expects($this->once())
            ->method('updateAttributes')
            ->with(array($productId), array($mpmPriceAttribute => $finalPrice, $mpmShippingPriceAttribute => $shippingPrice), 0);

        $this->addMageModel('catalog/resource_product_action', $catalogResourceProductActionMock);

        $this->addMageStoreConfig('mpm/repricing/save_product_after_update', null);
        $this->addMageStoreConfig('mpm/repricing/change_product_updated_at', 1);

        $mpmProductMock = $this->buildMock('MDN_Mpm_Helper_Product');
        $mpmProductMock->expects($this->once())
            ->method('touchUpdatedAt')
            ->with($productId);

        $this->addMageHelper('Mpm/Product', $mpmProductMock);

        $result = $this->_instance->setPricing($sku, $channel, $finalPrice, $shippingPrice);

        $this->assertEmpty($result);

    }

    public function testSetPricingWithProductSave(){

        $sku = 'sku';
        $channel = 'amazon_fr_default';
        $finalPrice = 55.98;
        $shippingPrice = 2.5;
        $productId = 100;
        $mpmPriceAttribute = 'mpm_price';
        $mpmShippingPriceAttribute = 'mpm_shipping_price';

        $productMock = $this->buildMock('Mage_Catalog_Model_Product');
        $productMock->expects($this->once())
            ->method('getIdBySku')
            ->with($sku)
            ->will($this->returnValue($productId));
        $productMock->expects($this->once())
            ->method('load')
            ->with($productId)
            ->will($this->returnValue($productMock));

        $this->addMageModel('catalog/product', $productMock);

        $this->addMageStoreConfig('mpm/repricing/price_attributes_'.$channel, $mpmPriceAttribute);
        $this->addMageStoreConfig('mpm/repricing/shipping_attributes_'.$channel, $mpmShippingPriceAttribute);

        $catalogResourceProductActionMock = $this->buildMock('Mage_Catalog_Model_Resource_Product_Action');
        $catalogResourceProductActionMock->expects($this->once())
            ->method('updateAttributes')
            ->with(array($productId), array($mpmPriceAttribute => $finalPrice, $mpmShippingPriceAttribute => $shippingPrice), 0);

        $this->addMageModel('catalog/resource_product_action', $catalogResourceProductActionMock);

        $this->addMageStoreConfig('mpm/repricing/save_product_after_update', 1);

        $result = $this->_instance->setPricing($sku, $channel, $finalPrice, $shippingPrice);

        $this->assertEmpty($result);

    }

}
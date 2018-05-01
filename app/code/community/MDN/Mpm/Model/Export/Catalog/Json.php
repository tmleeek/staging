<?php

class MDN_Mpm_Model_Export_Catalog_Json extends MDN_Mpm_Model_Export_Catalog
{
    const FILE_FORMAT = 'json';

    public $json = array();
    public $jsonProduct = array();

    public function parseCatalog($canArchive = false)
    {
        $this->addHeader();
        $this->json['products']['product'] = array();

        if(!$canArchive) {
            $fp = fopen($this->filepath,'a+');
            //write json header, delete ]}} last chars
            fwrite($fp,substr(json_encode($this->json),0, -3));
        }

        $part = 1;
        $startProductId = 0;
        while (($products = $this->getProductsCollection($startProductId))) {
            if ($part > static::PROTECTION || $products->count() == 0) {
                break;
            }

            if(($startProductId = $this->parseCollection($products)) === null) {
                break;
            };

            if($canArchive) {
                file_put_contents($this->filepath . DS . 'part' . $part . '.'.static::FILE_FORMAT, json_encode($this->json));
                Mage::helper('Mpm')->log("Save part file #$part into " . $this->filepath . DS . 'part' . $part . '.'.static::FILE_FORMAT);
            } else {
                fwrite($fp,($part > 1 ? ',' : '').substr(json_encode($this->json['products']['product']), 1, -1));
                Mage::helper('Mpm')->log("Append part #$part into " . $this->filepath);
            }

            $this->json['products']['product'] = array();
            $part++;
        }

        if(!$canArchive) {
            fwrite($fp,']}}');
            fclose($fp);
        }
    }

    protected function addHeader()
    {
        $this->json['header'] = array(
            'client' => null,
            'time' => time(),
            'type' => 'catalog'
        );
    }

    protected function addProduct($product)
    {
        $this->jsonProduct = array();
        $this->addAttributes($product);
        $this->addStocks($product);
        $this->json['products']['product'][] = $this->jsonProduct;
    }

    protected function addStocks($product)
    {
        $this->jsonProduct['stock']['qty'] = $product->getData('qty');
    }

    protected function addAttributes($product)
    {
        $this->jsonProduct['attributes']['global'] = array();

        $this->addAttributeSetName($product);
        $requiredAttributes = $this->addRequiredAttributes($product);

        if ($this->isMarketplaceInstalled())
            $this->addChannelReferences($product);

        foreach ($this->getAdditionnalAttributes($product, $requiredAttributes) as $attributeName => $attributeValue) {
            $this->jsonProduct['attributes']['global'][$attributeName] = $attributeValue;
        }

        if ($this->isErpInstalled())
            $this->addErpDatas($product);
    }

    protected function addChannelReferences($product)
    {
        $this->jsonProduct['carl'] = array();
        foreach($this->getChannelReferences($product) as $channelCode => $references) {
            $this->jsonProduct['carl'][$channelCode] = $references;
        }
    }

    protected function addErpDatas($product)
    {
        $this->jsonProduct['erp'] = array();

        if(($barcode = $this->getBarcode($product)) !== null) {
            $this->jsonProduct['erp']['barcode'] = $barcode;
        }

        if(($productAvailability = $this->getErpProductAvailability($product)) !== null) {
            $this->jsonProduct['erp']['availability'] = array();
            foreach($productAvailability as $attributeName => $attributeValue) {
                $this->jsonProduct['erp']['availability'][$attributeName] = $attributeValue;
            }
        }

        if(($warehouses = $this->getErpWarehouses($product)) !== null) {
            $this->jsonProduct['erp']['warehouses'] = array();
            foreach($warehouses as $warehouseCode => $warehouseAttributes) {
                foreach($warehouseAttributes as $attributeName => $attributeValue) {
                    $this->jsonProduct['erp']['warehouses'][$warehouseCode][$attributeName] = $attributeValue;
                }
            }
        }

        if(($bestSupplier = $this->getErpBestSupplier($product)) !== null) {
            $this->jsonProduct['erp']['best_supplier'] = array();
            foreach($productAvailability as $attributeName => $attributeValue) {
                $this->jsonProduct['erp']['best_supplier'][$attributeName] = $attributeValue;
            }
        }
    }

    protected function addId($product)
    {
        $this->jsonProduct['attributes']['global']['id'] = $this->encodeToUtf8($product->getId());
    }

    protected function addName($product)
    {
        $this->jsonProduct['attributes']['global']['name'] = $this->encodeToUtf8($product->getname());
    }

    protected function addSku($product)
    {
        $this->jsonProduct['attributes']['global']['sku'] = $product->getsku();
    }

    protected function addCategory($product)
    {
        $category = $this->getCategoryPath($product);
        if ($category) {
            $this->jsonProduct['attributes']['global']['category'] = $this->encodeToUtf8($category);
        }
    }

    protected function addPrice($product)
    {
        $this->jsonProduct['attributes']['global']['price'] = $product->getprice();

        if (!is_null($product->getSpecialPrice()) && $product->getSpecialPrice() != false) {
            if (Mage::app()->getLocale()->isStoreDateInInterval(null, $product->getSpecialFromDate(), $product->getSpecialToDate())) {
                $this->jsonProduct['attributes']['global']['special_price'] = $product->getSpecialPrice();
            }
        }
    }

    protected function addCost($product)
    {
        $this->jsonProduct['attributes']['global']['cost'] = $product->getcost();
    }

    protected function addManufacturer($product)
    {
        $this->jsonProduct['attributes']['global']['manufacturer'] = $this->encodeToUtf8($product->getAttributeText('manufacturer'));
    }

    protected function addWeight($product)
    {
        $this->jsonProduct['attributes']['global']['weight'] = $product->getweight();
    }

    protected function addAttributeSetName($product)
    {
        $this->jsonProduct['attributes']['global']['attribute_set_name'] = $this->encodeToUtf8($this->getAttributeSetName($product));
    }
}
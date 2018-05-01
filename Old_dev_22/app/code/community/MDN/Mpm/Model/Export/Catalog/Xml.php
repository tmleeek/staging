<?php

class MDN_Mpm_Model_Export_Catalog_Xml extends MDN_Mpm_Model_Export_Catalog
{
    const FILE_FORMAT = 'xml';

    public function parseCatalog($canArchive = false)
    {
        $this->xml = Mage::helper('Mpm/XmlWriter');

        if(!$canArchive) {
            $this->initXml();
            $this->addHeader();
            $this->xml->push('products');
            $this->xml->pop();  //products
            $this->xml->pop();  //stream
            $this->writePart($this->filepath, str_replace("</products>\n</stream>", "", $this->xml->flush()), false);
        }

        $part = 1;
        $startProductId = 0;
        while (($products = $this->getProductsCollection($startProductId))) {
            if ($part > static::PROTECTION || $products->count() == 0) {
                break;
            }

            if($canArchive) {
                $this->initXml();
                $this->addHeader();
                $this->xml->push('products');
            }

            if(($startProductId = $this->parseCollection($products)) === null) {
                break;
            };

            if(!$canArchive) {
                $this->writePart($this->filepath, $this->xml->flush(), false);
                Mage::helper('Mpm')->log("Append part #$part into " . $this->filepath);
            } else {
                $this->xml->pop(); //products
                $this->xml->pop(); //stream
                $this->writePart($this->filepath . DS . 'part' . $part . '.'.static::FILE_FORMAT, $this->xml->flush(), true);
                Mage::helper('Mpm')->log("Save part file #$part into " . $this->filepath . DS . 'part' . $part . '.'.static::FILE_FORMAT);
            }

            $part++;
        }

        if(!$canArchive) {
            $this->writePart($this->filepath, "  </products>\n</stream>", true);
        }
    }

    protected function writePart($filepath, $xml, $close = false)
    {
        $fp = fopen($filepath, 'a+');
        fwrite($fp, $xml);
        if ($close) {
            fclose($fp);
        }
    }

    protected function initXml()
    {
        $this->xml->init();
        $this->xml->push('stream');
    }

    protected function addHeader()
    {
        $this->xml->push('header');
        $this->xml->element('client', '');
        $this->xml->element('time', time());
        $this->xml->element('type', 'catalog');
        $this->xml->pop();
    }

    protected function addProduct($product)
    {
        $this->xml->push('product');

        $this->addAttributes($product);
        $this->addStocks($product);

        $this->xml->pop();
    }

    protected function addStocks($product)
    {
        $this->xml->push('stock');
        $this->xml->element('qty', $this->xml->encloseCData($product->getData('qty')));
        $this->xml->pop();
    }

    protected function addAttributes($product)
    {
        $this->xml->push('attributes');
        $this->xml->push('global');

        $this->addAttributeSetName($product);
        $requiredAttributes = $this->addRequiredAttributes($product);

        if($this->isMarketplaceInstalled())
            $this->addChannelReferences($product);

        foreach ($this->getAdditionnalAttributes($product, $requiredAttributes) as $attributeName => $attributeValue) {
            $this->xml->element($attributeName, $attributeValue);
        }

        $this->xml->pop();  //global
        $this->xml->pop();  //attributes

        if ($this->isErpInstalled())
            $this->addErpDatas($product);
    }

    protected function addChannelReferences($product)
    {
        $this->xml->push('carl');
        foreach($this->getChannelReferences($product) as $channelCode => $references) {
            $this->xml->element($channelCode, $references);
        }
        $this->xml->pop();  //carl
    }

    protected function addErpDatas($product)
    {
        $this->xml->push('erp');

        if(($barcode = $this->getBarcode($product)) !== null) {
            $this->xml->element('barcode', $this->xml->encloseCData($barcode));
        }

        if(($productAvailability = $this->getErpProductAvailability($product)) !== null) {
            $this->xml->push('availability');
            foreach($productAvailability as $attributeName => $attributeValue) {
                $this->xml->element($attributeName, $this->xml->encloseCData($attributeValue));
            }
            $this->xml->pop();  //availability
        }

        if(($warehouses = $this->getErpWarehouses($product)) !== null) {
            $this->xml->push('warehouses');
            foreach($warehouses as $warehouseCode => $warehouseAttributes) {
                $this->xml->push($warehouseCode);
                foreach($warehouseAttributes as $attributeName => $attributeValue) {
                    $this->xml->element($attributeName, $this->xml->encloseCData($attributeValue));
                }
                $this->xml->pop();  //warehouseCode
            }
            $this->xml->pop();  //warehouses
        }

        if(($bestSupplier = $this->getErpBestSupplier($product)) !== null) {
            $this->xml->push('best_supplier');
            foreach($bestSupplier as $attributeName => $attributeValue) {
                $this->xml->element($attributeName, $this->xml->encloseCData($attributeValue));
            }
            $this->xml->pop();  //best_supplier
        }

        $this->xml->pop();  //erp
    }

    protected function addId($product)
    {
        $this->xml->element('id', $this->xml->encloseCData($product->getId()));
    }

    protected function addName($product)
    {
        $this->xml->element('name', $this->xml->encloseCData($this->encodeToUtf8($product->getname())));
    }

    protected function addSku($product)
    {
        $this->xml->element('sku', $this->xml->encloseCData($product->getsku()));
    }

    protected function addCategory($product)
    {
        $category = $this->getCategoryPath($product);
        if ($category)
            $this->xml->element('category', $this->xml->encloseCData($this->encodeToUtf8($category)));

    }

    protected function addPrice($product)
    {
        $this->xml->element('price', $this->xml->encloseCData($product->getprice()));

        if (!is_null($product->getSpecialPrice()) && $product->getSpecialPrice() != false) {
            if (Mage::app()->getLocale()->isStoreDateInInterval(null, $product->getSpecialFromDate(), $product->getSpecialToDate())) {
                $this->xml->element('special_price', $this->xml->encloseCData($product->getSpecialPrice()));
            }
        }
    }

    protected function addCost($product)
    {
        $this->xml->element('cost', $this->xml->encloseCData($product->getcost()));
    }

    protected function addManufacturer($product)
    {
        $this->xml->element('manufacturer', $this->xml->encloseCData($this->encodeToUtf8($product->getAttributeText('manufacturer'))));
    }

    protected function addWeight($product)
    {
        $this->xml->element('weight', $this->xml->encloseCData($product->getweight()));
    }

    protected function addAttributeSetName($product)
    {
        $this->xml->element('attribute_set_name', $this->xml->encloseCData($this->encodeToUtf8($this->getAttributeSetName($product))));
    }
}
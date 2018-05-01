<?php
require_once 'app/Mage.php';
umask(0);
$app = Mage::app('admin');
$store = Mage::app()->getStore('admin');
$collection = Mage::getModel('catalog/product')->getCollection()
->addAttributeToSelect('*')
->joinField('qty',
                 'cataloginventory/stock_item',
                 'qty',
                 'product_id=entity_id',
                 '{{table}}.stock_id=1',
                 'left')
->addAttributeToFilter('qty', array("gt" => 0))        
->addAttributeToFilter('status', array('gt' => 0))        ;
$productavaiblity_model=Mage::getModel('SalesOrderPlanning/ProductAvailabilityStatus');
$productinformation = array();
$i = 1;
foreach($collection as $product)
{
    foreach($product->getCategoryIds() as $category_id) 
    {
        $category = Mage::getModel('catalog/category')->load($category_id);
        $productinformation[$i]["category"] = $category->getParentCategory()->getName();
        $productinformation[$i]["unique_id"] = $product->getId(); 
        $productinformation[$i]["title"] = $product->getName(); 
        $productinformation[$i]["description"] = $product->getDescription(); 
        $productinformation[$i]["prix"] = (float) $product->getPrice();
        $productinformation[$i]["product_URL"] = $product->getProductUrl();
        $productinformation[$i]["image_URL"] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog/product'.$product->getImage();
        $taxCalculation = Mage::getModel('tax/calculation');
        $request = $taxCalculation->getRateRequest(null, null, null, $store);
        $taxClassId = $product->getTaxClassId();
        $percent = $taxCalculation->getRate($request->setProductClassId($taxClassId));
        $productinformation[$i]["shipping_costs"] = $product->getPrice()*$percent/100;
        $productinformation[$i]["available_stock"] = "In stock";
        $productinformation[$i]["stock_quantity"] = intval(Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty());
        $productAvailabilityStatus = $productavaiblity_model->load($product->getId(), 'pa_product_id');
        $str = explode(",",$productAvailabilityStatus->getMessage());
        $productinformation[$i]["delivery_description"] = str_replace(" **","",$str[1]);
        $productinformation[$i]["model_refrence"] = $product->getSku();
        if(count($category->getName())>1)
        {
            $productinformation[$i]["catagories"] = $category->getName()." > ";
        }
        else
        {
            $productinformation[$i]["catagories"] = $category->getName();
        }
        $attribute = $product->getResource()->getAttribute('ean13');
        $productinformation[$i]["ean"] = $attribute ->getFrontend()->getValue($product);
        $productinformation[$i]["currency"] = Mage::app()->getStore(Mage::app()->getStore()->getId())->getCurrentCurrencyCode();
        $productinformation[$i]["weight"] = intval($product->getWeight());
        $productinformation[$i]["color"] = $product->getResource()->getAttribute('colour')->getFrontend()->getValue($product);
        $productinformation[$i]["type"] = $product->getTypeID();
    }
    $i++;
}
$dom = new DOMDocument("1.0");
header("Content-Type: text/xml");
$root = $dom->createElement('products');
$dom->appendChild($root);
foreach($productinformation as $key=>$value)
{
    $pro =  $dom->createElement("product");
    $root->appendChild($pro);
      foreach($value as $k=>$v)
            {
               $toElem  = $dom->createElement($k, $v);
               $pro->appendChild($toElem);
            }
      
}
echo $dom->saveXML();
?>
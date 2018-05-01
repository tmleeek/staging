<?php
chdir(dirname(__FILE__));
require 'app/Mage.php';
umask(0);
Mage::app('admin');
$vPath = 'pouring-cork-nickel-plated-inox-pouring-cork-paderno-4262.html';
    $oRewrite = Mage::getModel('core/url_rewrite')->loadByRequestPath($vPath);
    $iProductId = $oRewrite->getProductId();
     $oProduct = Mage::getModel('catalog/product')->addAttributeToSelect("url_key",$vPath)->load();
     echo $oProduct->getId();
     echo "test";
?>
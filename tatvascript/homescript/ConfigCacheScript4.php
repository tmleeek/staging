<?php
require 'app/Mage.php';
Mage::app(0);

 # refresh magento configuration cache
  Mage::app()->getCacheInstance()->cleanType('config');

?>
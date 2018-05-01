<?php
require 'app/Mage.php';
Mage::app('admin')->setUseSessionInUrl(false);
error_reporting(E_ALL | E_STRICT);
Mage::app()->cleanCache();

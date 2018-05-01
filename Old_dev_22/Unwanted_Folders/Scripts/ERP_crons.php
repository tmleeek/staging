<?php

require_once '../app/Mage.php';

Mage::app();


// cron: update_stocks // AdvancedStock/Observer::UpdateStocksForOrders
//app\code\community\MDN\AdvancedStock\etc\config.xml
Mage::getModel("AdvancedStock/Observer")->UpdateStocksForOrders();

//auto_cancel_order_cron //AutoCancelOrder/Observer::ExecuteTasks
//app\code\community\MDN\AutoCancelOrder\etc\config.xml
Mage::getModel("AutoCancelOrder/Observer")->ExecuteTasks();

//execute_tasks // BackgroundTask/Observer::ExecuteTasks
//app\code\community\MDN\BackgroundTask\etc\config.xml
Mage::getModel("BackgroundTask/Observer")->ExecuteTasks();









?>
<?php

//########################################

/** @var $installer Ess_M2ePro_Model_Upgrade_MySqlSetup */
$installer = $this;
$installer->startSetup();

$connection = $installer->getConnection();

// New Processing System
//########################################

/*
    ALTER TABLE `m2epro_processing_lock`
        DROP COLUMN `related_hash`,
        DROP COLUMN `description`,
        ADD COLUMN `processing_id` INT(11) UNSIGNED NOT NULL AFTER `id`,
        ADD INDEX `processing_id` (`processing_id`);
*/

// ---------------------------------------

if (!$installer->getTablesObject()->isExists('processing')) {
    $installer->run(<<<SQL

    DROP TABLE IF EXISTS `m2epro_processing`;
    CREATE TABLE `m2epro_processing`
    (
      `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `model` VARCHAR(255) NOT NULL,
      `params` LONGTEXT DEFAULT NULL,
      `result_data` LONGTEXT DEFAULT NULL,
      `result_messages` LONGTEXT DEFAULT NULL,
      `is_completed` TINYINT(2) NOT NULL DEFAULT 0,
      `expiration_date` DATETIME NOT NULL,
      `update_date` DATETIME DEFAULT NULL,
      `create_date` DATETIME DEFAULT NULL,
      PRIMARY KEY (`id`),
      INDEX `model` (`model`),
      INDEX `is_completed` (`is_completed`),
      INDEX `expiration_date` (`expiration_date`)
    )
    ENGINE = MYISAM
    CHARACTER SET utf8
    COLLATE utf8_general_ci;

SQL
    );
}

if (!$installer->getTablesObject()->isExists('request_pending_single')) {
    $installer->run(<<<SQL

    DROP TABLE IF EXISTS `m2epro_request_pending_single`;
    CREATE TABLE `m2epro_request_pending_single` (
      `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `component` VARCHAR(12) NOT NULL,
      `server_hash` VARCHAR(255) NOT NULL,
      `result_data` LONGTEXT DEFAULT NULL,
      `result_messages` LONGTEXT DEFAULT NULL,
      `expiration_date` DATETIME NOT NULL,
      `is_completed` TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
      `update_date` DATETIME DEFAULT NULL,
      `create_date` DATETIME DEFAULT NULL,
      PRIMARY KEY (`id`),
      INDEX `component` (`component`),
      INDEX `server_hash` (`server_hash`),
      INDEX `is_completed` (`is_completed`)
    )
    ENGINE = MYISAM
    CHARACTER SET utf8
    COLLATE utf8_general_ci;

SQL
    );
}

if (!$installer->getTablesObject()->isExists('request_pending_partial')) {
    $installer->run(<<<SQL

    DROP TABLE IF EXISTS `m2epro_request_pending_partial`;
    CREATE TABLE `m2epro_request_pending_partial` (
      `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `component` VARCHAR(12) NOT NULL,
      `server_hash` VARCHAR(255) NOT NULL,
      `next_part` INT(11) UNSIGNED DEFAULT NULL,
      `result_messages` LONGTEXT DEFAULT NULL,
      `expiration_date` DATETIME NOT NULL,
      `is_completed` TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
      `update_date` DATETIME DEFAULT NULL,
      `create_date` DATETIME DEFAULT NULL,
      PRIMARY KEY (`id`),
      INDEX `component` (`component`),
      INDEX `server_hash` (`server_hash`),
      INDEX `next_part` (`next_part`),
      INDEX `is_completed` (`is_completed`)
    )
    ENGINE = MYISAM
    CHARACTER SET utf8
    COLLATE utf8_general_ci;

SQL
    );
}

if (!$installer->getTablesObject()->isExists('request_pending_partial_data')) {
    $installer->run(<<<SQL

    DROP TABLE IF EXISTS `m2epro_request_pending_partial_data`;
    CREATE TABLE `m2epro_request_pending_partial_data` (
      `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `request_pending_partial_id` INT(11) UNSIGNED NOT NULL,
      `part_number` INT(11) UNSIGNED NOT NULL,
      `data` LONGTEXT DEFAULT NULL,
      PRIMARY KEY (`id`),
      INDEX `part_number` (`part_number`),
      INDEX `request_pending_partial_id` (`request_pending_partial_id`)
    )
    ENGINE = MYISAM
    CHARACTER SET utf8
    COLLATE utf8_general_ci;

SQL
    );
}

if (!$installer->getTablesObject()->isExists('connector_pending_requester_single')) {
    $installer->run(<<<SQL

    DROP TABLE IF EXISTS `m2epro_connector_pending_requester_single`;
    CREATE TABLE `m2epro_connector_pending_requester_single` (
      `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `processing_id` INT(11) UNSIGNED NOT NULL,
      `request_pending_single_id` INT(11) UNSIGNED NOT NULL NULL,
      `update_date` DATETIME DEFAULT NULL,
      `create_date` DATETIME DEFAULT NULL,
      PRIMARY KEY (`id`),
      INDEX `processing_id` (`processing_id`),
      INDEX `request_pending_single_id` (`request_pending_single_id`)
    )
    ENGINE = INNODB
    CHARACTER SET utf8
    COLLATE utf8_general_ci;

SQL
    );
}

if (!$installer->getTablesObject()->isExists('connector_pending_requester_partial')) {
    $installer->run(<<<SQL

    DROP TABLE IF EXISTS `m2epro_connector_pending_requester_partial`;
    CREATE TABLE `m2epro_connector_pending_requester_partial` (
      `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `processing_id` INT(11) UNSIGNED NOT NULL,
      `request_pending_partial_id` INT(11) UNSIGNED NOT NULL,
      `update_date` DATETIME DEFAULT NULL,
      `create_date` DATETIME DEFAULT NULL,
      PRIMARY KEY (`id`),
      INDEX `request_pending_partial_id` (`request_pending_partial_id`)
    )
    ENGINE = INNODB
    CHARACTER SET utf8
    COLLATE utf8_general_ci;

SQL
    );
}

if (!$installer->getTablesObject()->isExists('amazon_processing_action')) {
    $installer->run(<<<SQL

    DROP TABLE IF EXISTS `m2epro_amazon_processing_action`;
    CREATE TABLE `m2epro_amazon_processing_action` (
      `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `account_id` INT(11) UNSIGNED NOT NULL,
      `processing_id` INT(11) UNSIGNED NOT NULL,
      `request_pending_single_id` INT(11) UNSIGNED DEFAULT NULL,
      `related_id` INT(11) UNSIGNED DEFAULT NULL,
      `type` VARCHAR(12) NOT NULL,
      `request_data` LONGTEXT NOT NULL,
      `start_date` DATETIME DEFAULT NULL,
      `update_date` DATETIME DEFAULT NULL,
      `create_date` DATETIME DEFAULT NULL,
      PRIMARY KEY (`id`),
      INDEX `account_id` (`account_id`),
      INDEX `processing_id` (`processing_id`),
      INDEX `request_pending_single_id` (`request_pending_single_id`),
      INDEX `related_id` (`related_id`),
      INDEX `type` (`type`),
      INDEX `start_date` (`start_date`)
    )
    ENGINE = INNODB
    CHARACTER SET utf8
    COLLATE utf8_general_ci;

SQL
    );
}

// ---------------------------------------

if ($installer->getTablesObject()->isExists('amazon_processed_inventory')) {
    $installer->getTableModifier('amazon_processed_inventory')->truncate();
}

if ($installer->getTablesObject()->isExists('locked_object') &&
    !$installer->getTablesObject()->isExists('processing_lock')
) {
    $installer->run(<<<SQL
    RENAME TABLE `m2epro_locked_object` TO `m2epro_processing_lock`;
SQL
    );
}

$processingLockTable = $installer->getTablesObject()->getFullName('processing_lock');

$installer->getTableModifier('processing_lock')
    ->addColumn('processing_id', 'INT(11) UNSIGNED NOT NULL', NULL, 'id', true, false)
    ->dropColumn('description', true, false)
    ->commit();

$installer->getTableModifier('listing_product')->addColumn(
    'need_synch_rules_check', 'TINYINT(2) UNSIGNED NOT NULL', '0', 'tried_to_list', true
);

$processingTable = $installer->getTablesObject()->getFullName('processing');
$requestPendingSingleTable = $installer->getTablesObject()->getFullName('request_pending_single');
$connectorPendingRequesterSingleTable = $installer->getTablesObject()->getFullName(
    'connector_pending_requester_single'
);

$amazonProcessingActionTable = $installer->getTablesObject()->getFullName('amazon_processing_action');
$processingRequestTable = $installer->getTablesObject()->getFullName('processing_request');

$processingRequests = $installer->getConnection()->query("
  SELECT * FROM {$processingRequestTable};
")->fetchAll(PDO::FETCH_ASSOC);

$performTypeSingle = 1;

if (!empty($processingRequests)) {

    $processingRequestHashesForDelete = array();

    foreach ($processingRequests as $processingRequest) {
        if ($processingRequest['perform_type'] != $performTypeSingle) {
            $processingRequestHashesForDelete[] = $processingRequest['hash'];
            continue;
        }

        $responserModel = $processingRequest['responser_model'];
        $responserModel = str_replace('Connector_Ebay', 'Ebay_Connector', $responserModel);
        $responserModel = str_replace('Connector_Amazon', 'Amazon_Connector', $responserModel);
        $responserModel = str_replace(
            'Amazon_Connector_Product_MultipleResponser', 'Amazon_Connector_Product_Responser', $responserModel
        );
        $responserModel = str_replace(
            'Amazon_Connector_Product_List_MultipleResponser',
            'Amazon_Connector_Product_List_Responser',
            $responserModel
        );
		$responserModel = str_replace(
            'Amazon_Connector_Product_Relist_MultipleResponser',
            'Amazon_Connector_Product_Relist_Responser',
            $responserModel
        );
        $responserModel = str_replace(
            'Amazon_Connector_Product_Revise_MultipleResponser',
            'Amazon_Connector_Product_Revise_Responser',
            $responserModel
        );
        $responserModel = str_replace(
            'Amazon_Connector_Product_Stop_MultipleResponser',
            'Amazon_Connector_Product_Stop_Responser',
            $responserModel
        );
        $responserModel = str_replace(
            'Amazon_Connector_Product_Delete_MultipleResponser',
            'Amazon_Connector_Product_Delete_Responser',
            $responserModel
        );
        $responserModel = str_replace('Connector_Buy', 'Buy_Connector', $responserModel);
        $responserModel = str_replace('Connector_Translation', 'Translation_Connector', $responserModel);

        $processingRunnerModelName = preg_replace(
            '/Responser$/', 'ProcessingRunner', $responserModel
        );

        if (preg_match('/^M2ePro\/(Amazon|Buy)_Connector_Orders_(Update|Cancel|Refund)/', $processingRunnerModelName)) {
            $processingRequestHashesForDelete[] = $processingRequest['hash'];
            continue;
        }

        $responserParams = json_decode($processingRequest['responser_params'], true);

        $requestPendingSingleData = array(
            'component'       => $processingRequest['component'],
            'server_hash'     => $processingRequest['processing_hash'],
            'expiration_date' => $processingRequest['expiration_date'],
            'update_date'     => $processingRequest['update_date'],
            'create_date'     => $processingRequest['create_date'],
        );

        $connection->insert($requestPendingSingleTable, $requestPendingSingleData);
        $requestPendingSingleId = $connection->lastInsertId($requestPendingSingleTable);

        if (preg_match('/^M2ePro\/Amazon_Connector_Product_/', $responserModel)) {

            $listingsProductsIds = array();
            $requestData = array();

            foreach ($responserParams['products'] as $listingProductId => $productData) {
                $productData['id'] = $listingProductId;
                $requestData[$listingProductId] = $productData;

                $listingsProductsIds[] = $listingProductId;
            }

            if (strpos($responserModel, 'List') !== false) {
                $processingRunnerModelName = 'M2ePro/Amazon_Connector_Product_List_ProcessingRunner';
            } else {
                $processingRunnerModelName = 'M2ePro/Amazon_Connector_Product_ProcessingRunner';
            }

            $actionType = NULL;

            switch ($responserParams['action_type']) {
                case 1:
                    $actionType = 0;
                    break;

                case 2:
                case 3:
                case 4:
                    $actionType = 1;
                    break;

                case 5:
                    $actionType = 2;
                    break;
            }

            foreach ($requestData as $listingProductId => $productData) {

                $responserProductData = array_merge(
                    array('id' => $listingProductId),
                    $responserParams['products'][$listingProductId]
                );

                if (isset($responserProductData['configurator']['mode'])) {
                    if ($responserProductData['configurator']['mode'] == 'full') {
                        $responserProductData['configurator']['is_default_mode'] = true;
                    } else {
                        $responserProductData['configurator']['is_default_mode'] = false;
                    }
                    unset($responserProductData['configurator']['mode']);
                }

                if (isset($responserProductData['configurator']['allowed_data_types'])) {
                    $allowedDataTypes = $responserProductData['configurator']['allowed_data_types'];

                    $priceDataTypeIndex = array_search('price', $allowedDataTypes);

                    if ($priceDataTypeIndex !== false) {
                        unset($allowedDataTypes[$priceDataTypeIndex]);
                        $allowedDataTypes[] = 'regular_price';

                        $responserProductData['configurator']['allowed_data_types'] = $allowedDataTypes;
                    }
                }

                $newResponserParams = array(
                    'account_id'      => $responserParams['account_id'],
                    'action_type'     => $responserParams['action_type'],
                    'lock_identifier' => $responserParams['lock_identifier'],
                    'logs_action'     => $responserParams['logs_action'],
                    'logs_action_id'  => $responserParams['logs_action_id'],
                    'status_changer'  => $responserParams['status_changer'],
                    'params'          => $responserParams['params'],
                    'product'         => $responserProductData,
                );

                $processingData = array(
                    'model'  => $processingRunnerModelName,
                    'params' => json_encode(array(
                        'component'             => $processingRequest['component'],
                        'server_hash'           => NULL,
                        'account_id'            => $responserParams['account_id'],
                        'responser_model_name'  => $responserModel,
                        'responser_params'      => $newResponserParams,
                        'request_data'          => $productData['request'],
                        'listing_product_id'    => $listingProductId,
                        'lock_identifier'       => $responserParams['lock_identifier'],
                        'action_type'           => $responserParams['action_type'],
                        'start_date'            => $processingRequest['create_date'],
                    )),
                    'expiration_date' => date('Y-m-d H:i:s', strtotime($processingRequest['create_date'])+86400),
                    'update_date' => $processingRequest['update_date'],
                    'create_date' => $processingRequest['create_date'],
                );

                $connection->insert($processingTable, $processingData);
                $processingId = $connection->lastInsertId($processingTable);

                $connection->update(
                    $processingLockTable,
                    array(
                        'processing_id' => $processingId,
                        'related_hash'  => '',
                    ),
                    array(
                        'related_hash = ?' => $processingRequest['hash'],
                        'model_name = ?'   => 'M2ePro/Listing_Product',
                        'object_id = ?'    => $listingProductId,
                    )
                );

                $amazonProcessingActionData = array(
                    'account_id'                => $responserParams['account_id'],
                    'processing_id'             => $processingId,
                    'request_pending_single_id' => $requestPendingSingleId,
                    'type'                      => $actionType,
                    'related_id'                => $listingProductId,
                    'request_data'              => json_encode($productData['request']),
                    'start_date'                => $processingRequest['create_date'],
                    'update_date'               => $processingRequest['update_date'],
                    'create_date'               => $processingRequest['create_date'],
                );

                $connection->insert($amazonProcessingActionTable, $amazonProcessingActionData);
            }

            $connection->delete(
                $processingLockTable,
                array('related_hash = ?' => $processingRequest['hash'])
            );

            continue;
        }

        if (preg_match('/^M2ePro\/Amazon_Search_/', $processingRunnerModelName)) {
            $processingRunnerModelName = 'M2ePro/Amazon_Search_Settings_ProcessingRunner';
        }

        if (preg_match('/^M2ePro\/Buy_Connector_Product_/', $responserModel)) {
            if (strpos($responserModel, 'List') !== false) {
                $processingRunnerModelName = 'M2ePro/Buy_Connector_Product_List_ProcessingRunner';
            } else {
                $processingRunnerModelName = 'M2ePro/Buy_Connector_Product_ProcessingRunner';
            }
        }

        $processingData = array(
            'model'  => $processingRunnerModelName,
            'params' => json_encode(array(
                'component'             => $processingRequest['component'],
                'server_hash'           => $processingRequest['processing_hash'],
                'account_id'            => $responserParams['account_id'],
                'responser_model_name'  => $responserModel,
                'responser_params'      => $responserParams,
            )),
            'expiration_date' => date('Y-m-d H:i:s', strtotime($processingRequest['create_date'])+86400),
            'update_date' => $processingRequest['update_date'],
            'create_date' => $processingRequest['create_date'],
        );

        $connection->insert($processingTable, $processingData);
        $processingId = $connection->lastInsertId($processingTable);

        $connection->update(
            $processingLockTable,
            array('processing_id' => $processingId),
            array('related_hash = ?' => $processingRequest['hash'])
        );

        $requesterPendingSingleData = array(
            'processing_id' => $processingId,
            'request_pending_single_id' => $requestPendingSingleId,
            'update_date' => $processingRequest['update_date'],
            'create_date' => $processingRequest['create_date'],
        );

        $connection->insert($connectorPendingRequesterSingleTable, $requesterPendingSingleData);
    }

    if (!empty($processingRequestHashesForDelete)) {
        $connection->delete($processingLockTable, array('related_hash IN (?)' => $processingRequestHashesForDelete));
    }
}

$installer->getTableModifier('processing_lock')->dropColumn('related_hash', true, true);

if ($installer->getTablesObject()->isExists('processing_request')) {
    $installer->run(<<<SQL
        DROP TABLE `m2epro_processing_request`;
SQL
    );
}

//########################################

/*
  INSERT INTO `m2epro_config` (`group`,`key`,`value`,`notice`,`update_date`,`create_date`) VALUES
  ('/cron/task/request_pending_single/', 'mode', '1', '0 - disable, \r\n1 - enable', '2013-05-08 00:00:00',
   '2013-05-08 00:00:00'),
  ('/cron/task/request_pending_single/', 'interval', '60', 'in seconds', '2013-05-08 00:00:00',
   '2013-05-08 00:00:00'),
  ('/cron/task/request_pending_single/', 'last_access', NULL, 'date of last access', '2013-05-08 00:00:00',
   '2013-05-08 00:00:00'),
  ('/cron/task/request_pending_single/', 'last_run', NULL, 'date of last run', '2014-01-01 00:00:00',
   '2014-01-01 00:00:00'),
  ('/cron/task/request_pending_partial/', 'mode', '1', '0 - disable, \r\n1 - enable', '2013-05-08 00:00:00',
   '2013-05-08 00:00:00'),
  ('/cron/task/request_pending_partial/', 'interval', '60', 'in seconds', '2013-05-08 00:00:00', '2013-05-08 00:00:00'),
  ('/cron/task/request_pending_partial/', 'last_access', NULL, 'date of last access', '2013-05-08 00:00:00',
   '2013-05-08 00:00:00'),
  ('/cron/task/request_pending_partial/', 'last_run', NULL, 'date of last run', '2014-01-01 00:00:00',
   '2014-01-01 00:00:00'),
  ('/cron/task/connector_requester_pending_single/', 'mode', '1', '0 - disable, \r\n1 - enable', '2013-05-08 00:00:00',
   '2013-05-08 00:00:00'),
  ('/cron/task/connector_requester_pending_single/', 'interval', '60', 'in seconds', '2013-05-08 00:00:00',
   '2013-05-08 00:00:00'),
  ('/cron/task/connector_requester_pending_single/', 'last_access', NULL, 'date of last access', '2013-05-08 00:00:00',
   '2013-05-08 00:00:00'),
  ('/cron/task/connector_requester_pending_single/', 'last_run', NULL, 'date of last run', '2014-01-01 00:00:00',
   '2014-01-01 00:00:00'),
  ('/cron/task/connector_requester_pending_partial/', 'mode', '1', '0 - disable, \r\n1 - enable', '2013-05-08 00:00:00',
   '2013-05-08 00:00:00'),
  ('/cron/task/connector_requester_pending_partial/', 'interval', '60', 'in seconds', '2013-05-08 00:00:00',
   '2013-05-08 00:00:00'),
  ('/cron/task/connector_requester_pending_partial/', 'last_access', NULL, 'date of last access', '2013-05-08 00:00:00',
   '2013-05-08 00:00:00'),
  ('/cron/task/connector_requester_pending_partial/', 'last_run', NULL, 'date of last run', '2014-01-01 00:00:00',
   '2014-01-01 00:00:00'),
  ('/cron/task/amazon_actions/', 'mode', '1', '0 - disable, \r\n1 - enable', '2013-05-08 00:00:00',
   '2013-05-08 00:00:00'),
  ('/cron/task/amazon_actions/', 'interval', '60', 'in seconds', '2013-05-08 00:00:00', '2013-05-08 00:00:00'),
  ('/cron/task/amazon_actions/', 'last_access', NULL, 'date of last access', '2013-05-08 00:00:00',
   '2013-05-08 00:00:00'),
  ('/cron/task/amazon_actions/', 'last_run', NULL, 'date of last run', '2014-01-01 00:00:00', '2014-01-01 00:00:00');
*/

$installer->getMainConfigModifier()
    ->insert('/cron/task/request_pending_single/', 'mode', '1', '0 - disable, \r\n1 - enable');
$installer->getMainConfigModifier()
    ->insert('/cron/task/request_pending_single/', 'interval', '60', 'in seconds');
$installer->getMainConfigModifier()
    ->insert('/cron/task/request_pending_single/', 'last_access', NULL, 'date of last access');
$installer->getMainConfigModifier()
    ->insert('/cron/task/request_pending_single/', 'last_run', NULL, 'date of last run');

$installer->getMainConfigModifier()
    ->insert('/cron/task/request_pending_partial/', 'mode', '1', '0 - disable, \r\n1 - enable');
$installer->getMainConfigModifier()
    ->insert('/cron/task/request_pending_partial/', 'interval', '60', 'in seconds');
$installer->getMainConfigModifier()
    ->insert('/cron/task/request_pending_partial/', 'last_access', NULL, 'date of last access');
$installer->getMainConfigModifier()
    ->insert('/cron/task/request_pending_partial/', 'last_run', NULL, 'date of last run');

$installer->getMainConfigModifier()
    ->insert('/cron/task/connector_requester_pending_single/', 'mode', '1', '0 - disable, \r\n1 - enable');
$installer->getMainConfigModifier()
    ->insert('/cron/task/connector_requester_pending_single/', 'interval', '60', 'in seconds');
$installer->getMainConfigModifier()
    ->insert('/cron/task/connector_requester_pending_single/', 'last_access', NULL, 'date of last access');
$installer->getMainConfigModifier()
    ->insert('/cron/task/connector_requester_pending_single/', 'last_run', NULL, 'date of last run');

$installer->getMainConfigModifier()
    ->insert('/cron/task/connector_requester_pending_partial/', 'mode', '1', '0 - disable, \r\n1 - enable');
$installer->getMainConfigModifier()
    ->insert('/cron/task/connector_requester_pending_partial/', 'interval', '60', 'in seconds');
$installer->getMainConfigModifier()
    ->insert('/cron/task/connector_requester_pending_partial/', 'last_access', NULL, 'date of last access');
$installer->getMainConfigModifier()
    ->insert('/cron/task/connector_requester_pending_partial/', 'last_run', NULL, 'date of last run');

$installer->getMainConfigModifier()
    ->insert('/cron/task/amazon_actions/', 'mode', '1', '0 - disable, \r\n1 - enable');
$installer->getMainConfigModifier()
    ->insert('/cron/task/amazon_actions/', 'interval', '60', 'in seconds');
$installer->getMainConfigModifier()
    ->insert('/cron/task/amazon_actions/', 'last_access', NULL, 'date of last access');
$installer->getMainConfigModifier()
    ->insert('/cron/task/amazon_actions/', 'last_run', NULL, 'date of last run');

//########################################

if (!$installer->getTablesObject()->isExists('ebay_processing_action')) {

    $installer->run(<<<SQL

    DROP TABLE IF EXISTS `m2epro_ebay_processing_action`;
    CREATE TABLE `m2epro_ebay_processing_action` (
      `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `account_id` INT(11) UNSIGNED NOT NULL,
      `marketplace_id` INT(11) UNSIGNED NOT NULL,
      `processing_id` INT(11) UNSIGNED NOT NULL,
      `related_id` INT(11) UNSIGNED DEFAULT NULL,
      `type` VARCHAR(12) NOT NULL,
      `priority` INT(11) UNSIGNED NOT NULL DEFAULT '0',
      `request_timeout` INT(11) UNSIGNED DEFAULT NULL,
      `request_data` LONGTEXT NOT NULL,
      `start_date` DATETIME DEFAULT NULL,
      `update_date` DATETIME DEFAULT NULL,
      `create_date` DATETIME DEFAULT NULL,
      PRIMARY KEY (`id`),
      INDEX `account_id` (`account_id`),
      INDEX `marketplace_id` (`marketplace_id`),
      INDEX `processing_id` (`processing_id`),
      INDEX `related_id` (`related_id`),
      INDEX `type` (`type`),
      INDEX `priority` (`priority`),
      INDEX `start_date` (`start_date`)
    )
    ENGINE = INNODB
    CHARACTER SET utf8
    COLLATE utf8_general_ci;

SQL
    );
}

// --------------------------------------------

/*
    INSERT INTO `m2epro_config` (`group`,`key`,`value`,`notice`,`update_date`,`create_date`) VALUES
    ('/cron/task/ebay_actions/', 'mode', '1', '0 - disable, \r\n1 - enable', '2013-05-08 00:00:00',
    '2013-05-08 00:00:00'),
    ('/cron/task/ebay_actions/', 'interval', '60', 'in seconds', '2013-05-08 00:00:00',
    '2013-05-08 00:00:00'),
    ('/cron/task/ebay_actions/', 'last_access', NULL, 'date of last access', '2013-05-08 00:00:00',
    '2013-05-08 00:00:00'),
    ('/cron/task/ebay_actions/', 'last_run', NULL, 'date of last run', '2014-01-01 00:00:00',
    '2014-01-01 00:00:00');
*/

$installer->getMainConfigModifier()->insert('/cron/task/ebay_actions/', 'mode', 1);
$installer->getMainConfigModifier()->insert('/cron/task/ebay_actions/', 'interval', 60);
$installer->getMainConfigModifier()->insert('/cron/task/ebay_actions/', 'last_access', NULL);
$installer->getMainConfigModifier()->insert('/cron/task/ebay_actions/', 'last_run', NULL);

//########################################

// BOPIS
//########################################

if (!$installer->getTablesObject()->isExists('ebay_account_pickup_store')) {
    $installer->run(<<<SQL

    DROP TABLE IF EXISTS `m2epro_ebay_account_pickup_store`;
    CREATE TABLE `m2epro_ebay_account_pickup_store` (
      `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `name` VARCHAR(255) NOT NULL,
      `location_id` VARCHAR(255) DEFAULT NULL,
      `account_id` INT(11) UNSIGNED NOT NULL,
      `marketplace_id` INT(11) UNSIGNED NOT NULL,
      `phone` VARCHAR(255) NOT NULL,
      `postal_code` VARCHAR(50) NOT NULL,
      `url` VARCHAR(255) NOT NULL,
      `utc_offset` VARCHAR(50) NOT NULL ,
      `country` VARCHAR(255) NOT NULL,
      `region` VARCHAR(255) NOT NULL,
      `city` VARCHAR(255) NOT NULL,
      `address_1` VARCHAR(255) NOT NULL,
      `address_2` VARCHAR(255) NOT NULL,
      `latitude` FLOAT,
      `longitude` FLOAT,
      `business_hours` TEXT NOT NULL,
      `special_hours` TEXT NOT NULL,
      `pickup_instruction` TEXT NOT NULL,
      `qty_mode` TINYINT(2) UNSIGNED NOT NULL,
      `qty_custom_value` INT(11) UNSIGNED NOT NULL,
      `qty_custom_attribute` VARCHAR(255) NOT NULL,
      `qty_percentage` INT(11) UNSIGNED NOT NULL DEFAULT 100,
      `qty_modification_mode` TINYINT(2) UNSIGNED NOT NULL,
      `qty_min_posted_value` int(11) UNSIGNED DEFAULT NULL,
      `qty_max_posted_value` int(11) UNSIGNED DEFAULT NULL,
      `update_date` DATETIME DEFAULT NULL,
      `create_date` DATETIME DEFAULT NULL,
      PRIMARY KEY (`id`),
      INDEX `name` (`name`),
      INDEX `location_id` (`location_id`),
      INDEX `account_id` (`account_id`),
      INDEX `marketplace_id` (`marketplace_id`)
    )
    ENGINE = INNODB
    CHARACTER SET utf8
    COLLATE utf8_general_ci;

SQL
    );
}

if (!$installer->getTablesObject()->isExists('ebay_account_pickup_store_state')) {
    $installer->run(<<<SQL

    DROP TABLE IF EXISTS `m2epro_ebay_account_pickup_store_state`;
    CREATE TABLE `m2epro_ebay_account_pickup_store_state` (
      `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `account_pickup_store_id` INT(11) UNSIGNED NOT NULL,
      `is_in_processing` TINYINT(2) UNSIGNED DEFAULT 0,
      `sku` VARCHAR(255) NOT NULL,
      `online_qty` INT(11) NOT NULL,
      `target_qty` INT(11) NOT NULL,
      `is_added` TINYINT(2) NOT NULL DEFAULT 0,
      `is_deleted` TINYINT(2) NOT NULL DEFAULT 0,
      `update_date` DATETIME DEFAULT NULL,
      `create_date` DATETIME DEFAULT NULL,
      PRIMARY KEY (`id`),
      INDEX `account_pickup_store_id` (`account_pickup_store_id`),
      INDEX `is_in_processing` (`is_in_processing`),
      INDEX `sku` (`sku`)
    )
    ENGINE = INNODB
    CHARACTER SET utf8
    COLLATE utf8_general_ci;

SQL
    );
}

if (!$installer->getTablesObject()->isExists('ebay_listing_product_pickup_store')) {
    $installer->run(<<<SQL

    DROP TABLE IF EXISTS `m2epro_ebay_listing_product_pickup_store`;
    CREATE TABLE `m2epro_ebay_listing_product_pickup_store` (
      `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `listing_product_id` INT(11) UNSIGNED,
      `account_pickup_store_id` INT(11) UNSIGNED,
      `is_process_required` TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
      PRIMARY KEY (`id`),
      INDEX `listing_product_id` (`listing_product_id`),
      INDEX `account_pickup_store_id` (`account_pickup_store_id`),
      INDEX `is_process_required` (`is_process_required`)
    )
    ENGINE = INNODB
    CHARACTER SET utf8
    COLLATE utf8_general_ci;

SQL
    );
}

if (!$installer->getTablesObject()->isExists('ebay_account_pickup_store_log')) {
    $installer->run(<<<SQL

    DROP TABLE IF EXISTS `m2epro_ebay_account_pickup_store_log`;
    CREATE TABLE `m2epro_ebay_account_pickup_store_log` (
      `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `account_pickup_store_state_id` INT(11) UNSIGNED DEFAULT NULL,
      `location_id` VARCHAR(255) NOT NULL,
      `location_title` VARCHAR(255) DEFAULT NULL,
      `action_id` INT(11) UNSIGNED DEFAULT NULL,
      `action` TINYINT(2) UNSIGNED NOT NULL DEFAULT 1,
      `type` TINYINT(2) UNSIGNED NOT NULL DEFAULT 1,
      `priority` TINYINT(2) UNSIGNED NOT NULL DEFAULT 3,
      `description` TEXT DEFAULT NULL,
      `update_date` DATETIME DEFAULT NULL,
      `create_date` DATETIME DEFAULT NULL,
      PRIMARY KEY (`id`),
      INDEX `account_pickup_store_state_id` (`account_pickup_store_state_id`),
      INDEX `location_id` (`location_id`),
      INDEX `location_title` (`location_title`),
      INDEX `action` (`action`),
      INDEX `action_id` (`action_id`),
      INDEX `priority` (`priority`),
      INDEX `type` (`type`)
    )
    ENGINE = MYISAM
    CHARACTER SET utf8
    COLLATE utf8_general_ci;

SQL
    );
}

// --------------------------------------------

/*
    ALTER TABLE `m2epro_ebay_marketplace`
        ADD COLUMN `is_in_store_pickup` TINYINT(2) UNSIGNED NOT NULL DEFAULT 0 AFTER `is_click_and_collect`,
        ADD INDEX `is_in_store_pickup` (`is_in_store_pickup`);
*/
$installer->getTableModifier('ebay_marketplace')
    ->addColumn('is_in_store_pickup', 'TINYINT(2) UNSIGNED NOT NULL', 0, 'is_click_and_collect', true);

/*
    INSERT INTO `m2epro_synchronization_config`(`group`, `key`, `value`, `notice`) VALUES
        ('/ebay/defaults/account_pickup_store/', 'mode', '1', '0 - disable, \r\n1 - enable');
*/

$installer->getSynchConfigModifier()
    ->insert('/ebay/defaults/account_pickup_store/', 'mode', '1', '0 - disable, \r\n1 - enable');

/*
    INSERT INTO `m2epro_config`(`group`, `key`, `value`, `notice`) VALUES
        ('/logs/clearing/ebay_pickup_store/', 'mode', '1', '0 - disable, \r\n1 - enable'),
        ('/logs/clearing/ebay_pickup_store/', 'days', '1', 'in days'),
        ('/logs/ebay_pickup_store/', 'last_action_id', '0', NULL);
*/

$installer->getMainConfigModifier()
    ->insert('/logs/clearing/ebay_pickup_store/', 'mode', '1', '0 - disable, \r\n1 - enable');
$installer->getMainConfigModifier()
    ->insert('/logs/clearing/ebay_pickup_store/', 'days', '30', 'in days');
$installer->getMainConfigModifier()
    ->insert('/logs/ebay_pickup_store/', 'last_action_id', 0);

$installer->run(<<<SQL

  UPDATE `m2epro_ebay_marketplace`
  SET `is_in_store_pickup` = 1
  WHERE `marketplace_id` = 1 AND `origin_country` = 'us'
  OR    `marketplace_id` = 4 AND `origin_country` = 'au'
  OR    `marketplace_id` = 3 AND `origin_country` = 'gb';

SQL
);

//########################################

/*
    DELETE FROM `m2epro_primary_config`
    WHERE `group` = '/M2ePro/license/' AND `key` = 'directory';

    DELETE FROM `m2epro_primary_config`
    WHERE `group` = '/M2ePro/license/valid/' AND `key` = 'directory';

    DELETE FROM `m2epro_primary_config`
    WHERE `group` = '/M2ePro/server/' AND `key` = 'lock';

    DELETE FROM `m2epro_primary_config`
    WHERE `group` = '/M2ePro/license/ebay/'
    AND `key` IN ('mode' , 'expiration_date', 'status', 'is_free');

    DELETE FROM `m2epro_primary_config`
    WHERE `group` = '/M2ePro/license/amazon/'
    AND `key` IN ('mode' , 'expiration_date', 'status', 'is_free');

    DELETE FROM `m2epro_primary_config`
    WHERE `group` = '/M2ePro/license/buy/'
    AND `key` IN ('mode' , 'expiration_date', 'status', 'is_free');

    INSERT INTO `m2epro_primary_config` (`group`,`key`,`value`,`notice`,`update_date`,`create_date`) VALUES
    ('/M2ePro/license/', 'status', 1, NULL, '2013-05-08 00:00:00', '2013-05-08 00:00:00');
*/

$primaryConfig = $installer->getPrimaryConfigModifier();

$primaryConfig->getEntity('/M2ePro/license/', 'directory')->delete();
$primaryConfig->getEntity('/M2ePro/license/valid/', 'directory')->delete();
$primaryConfig->getEntity('/M2ePro/server/', 'lock')->delete();

foreach (array('ebay', 'amazon', 'buy') as $component) {

    $tempGroup = '/M2ePro/license/' . $component . '/';

    $primaryConfig->getEntity($tempGroup, 'mode')->delete();
    $primaryConfig->getEntity($tempGroup, 'expiration_date')->delete();
    $primaryConfig->getEntity($tempGroup, 'status')->delete();
    $primaryConfig->getEntity($tempGroup, 'is_free')->delete();
}

$installer->getPrimaryConfigModifier()->insert('/M2ePro/license/', 'status', 1);

//########################################

/*
    ### Global
    ### -------------------------------

    INSERT INTO m2epro_synchronization_config (`group`, `key`, `value`, `notice`)
    VALUES ('/global/', 'mode', 1, '0 - disable,\r\n1 - enable');

    UPDATE m2epro_synchronization_config
    SET `group` = '/global/magento_products/'
    WHERE `group` = '/defaults/' AND `key` = 'mode';

    UPDATE m2epro_synchronization_config
    SET `group` = '/global/magento_products/deleted_products/'
    WHERE `group` = '/defaults/deleted_products/'
    AND (`key` = 'mode' OR `key` = 'interval' OR `key` = 'last_time');

    UPDATE m2epro_synchronization_config
    SET `group` = '/global/magento_products/added_products/'
    WHERE `group` = '/defaults/added_products/' AND `key` = 'last_magento_product_id';

    UPDATE m2epro_synchronization_config
    SET `group` = '/global/magento_products/inspector/'
    WHERE `group` = '/defaults/inspector/' AND `key` = 'mode';

    DELETE FROM m2epro_synchronization_config
    WHERE `group` = '/defaults/inspector/product_changes/' AND `key` = 'type';

    UPDATE m2epro_synchronization_config
    SET `group` = '/global/magento_products/inspector/'
    WHERE `group` = '/defaults/inspector/product_changes/circle/'
    AND (`key` = 'last_listing_product_id'
        OR `key` = 'min_interval_between_circles'
        OR `key` = 'max_count_times_for_full_circle'
        OR `key` = 'min_count_items_per_one_time'
        OR `key` = 'max_count_items_per_one_time'
        OR `key` = 'last_time_start_circle');

    UPDATE m2epro_synchronization_config
    SET `group` = '/global/processing/'
    WHERE `group` = '/defaults/processing/' AND `key` = 'mode';

    UPDATE m2epro_synchronization_config
    SET `group` = '/global/stop_queue/'
    WHERE `group` = '/defaults/stop_queue/'
    AND (`key` = 'mode' OR `key` = 'interval' OR `key` = 'last_time');

    ### -------------------------------

    ### Ebay
    ### -------------------------------

    INSERT INTO m2epro_synchronization_config (`group`, `key`, `value`, `notice`)
    VALUES ('/ebay/general/', 'mode', 1, '0 - disable,\r\n1 - enable');

    UPDATE m2epro_synchronization_config
    SET `group` = '/ebay/general/account_pickup_store/'
    WHERE `group` = '/ebay/defaults/account_pickup_store/' AND `key` = 'mode';

    UPDATE m2epro_synchronization_config
    SET `group` = '/ebay/general/feedbacks/'
    WHERE `group` = '/ebay/feedbacks/' AND `key` = 'mode';

    UPDATE m2epro_synchronization_config
    SET `group` = '/ebay/general/feedbacks/receive/'
    WHERE `group` = '/ebay/feedbacks/receive/'
    AND (`key` = 'mode' OR `key` = 'interval' OR `key` = 'last_time');

    UPDATE m2epro_synchronization_config
    SET `group` = '/ebay/general/feedbacks/response/'
    WHERE `group` = '/ebay/feedbacks/response/'
    AND (`key` = 'mode' OR `key` = 'interval' OR `key` = 'last_time' OR `key` = 'attempt_interval');

    UPDATE m2epro_synchronization_config
    SET `group` = '/ebay/listings_products/'
    WHERE `group` = '/ebay/defaults/' AND `key` = 'mode';

    UPDATE m2epro_synchronization_config
    SET `group` = '/ebay/listings_products/remove_duplicates/'
    WHERE `group` = '/ebay/defaults/remove_duplicates/' AND `key` = 'mode';

    UPDATE m2epro_synchronization_config
    SET `group` = '/ebay/listings_products/update/'
    WHERE `group` = '/ebay/defaults/update_listings_products/' AND `key` = 'mode';

    UPDATE m2epro_synchronization_config
    SET `group` = '/ebay/other_listings/synchronization/'
    WHERE `group` = '/ebay/other_listings/templates/' AND `key` = 'mode';

    UPDATE m2epro_synchronization_config
    SET `group` = '/ebay/other_listings/synchronization/relist/'
    WHERE `group` = '/ebay/other_listings/templates/relist/' AND `key` = 'mode';

    UPDATE m2epro_synchronization_config
    SET `group` = '/ebay/other_listings/synchronization/revise/'
    WHERE `group` = '/ebay/other_listings/templates/revise/' AND `key` = 'mode';

    UPDATE m2epro_synchronization_config
    SET `group` = '/ebay/other_listings/synchronization/stop/'
    WHERE `group` = '/ebay/other_listings/templates/stop/' AND `key` = 'mode';

    INSERT INTO m2epro_synchronization_config (`group`, `key`, `value`, `notice`)
    VALUES ('/ebay/templates/synchronization/', 'mode', 1, '0 - disable,\r\n1 - enable');

    UPDATE m2epro_synchronization_config
    SET `group` = '/ebay/templates/synchronization/list/'
    WHERE `group` = '/ebay/templates/list/' AND `key` = 'mode';

    UPDATE m2epro_synchronization_config
    SET `group` = '/ebay/templates/synchronization/relist/'
    WHERE `group` = '/ebay/templates/relist/' AND `key` = 'mode';

    UPDATE m2epro_synchronization_config
    SET `group` = '/ebay/templates/synchronization/revise/'
    WHERE `group` = '/ebay/templates/revise/' AND `key` = 'mode';

    UPDATE m2epro_synchronization_config
    SET `group` = '/ebay/templates/synchronization/revise/total/'
    WHERE `group` = '/ebay/templates/revise/total/'
    AND (`key` = 'last_listing_product_id' OR `key` = 'start_date' OR `key` = 'end_date');

    UPDATE m2epro_synchronization_config
    SET `group` = '/ebay/templates/synchronization/stop/'
    WHERE `group` = '/ebay/templates/stop/' AND `key` = 'mode';

    UPDATE m2epro_synchronization_config
    SET `group` = '/ebay/templates/remove_unused/'
    WHERE `group` = '/ebay/defaults/remove_unused_templates/'
    AND (`key` = 'mode' OR `key` = 'interval' OR `key` = 'last_time');

    ### -------------------------------

    ### Amazon
    ### -------------------------------

    INSERT INTO `m2epro_synchronization_config` (`group`,`key`,`value`,`notice`,`update_date`,`create_date`) VALUES
    ('/amazon/general/', 'mode', '1', '0 - disable, \r\n1 - enable', '2013-05-08 00:00:00', '2013-05-08 00:00:00')

    UPDATE m2epro_synchronization_config
    SET `group` = '/amazon/general/run_parent_processors/'
    WHERE `group` = '/amazon/defaults/run_parent_processors/'
    AND (`key` = 'mode' OR `key` = 'interval' OR `key` = 'last_time');

    UPDATE m2epro_synchronization_config
    SET `group` = '/amazon/listings_products/'
    WHERE `group` = '/amazon/defaults/' AND `key` = 'mode';

    UPDATE m2epro_synchronization_config
    SET `group` = '/amazon/listings_products/update/'
    WHERE `group` = '/amazon/defaults/update_listings_products/'
    AND (`key` = 'mode' OR `key` = 'interval' OR `key` = 'last_time');

    UPDATE m2epro_synchronization_config
    SET `group` = '/amazon/listings_products/update/defected/'
    WHERE `group` = '/amazon/defaults/update_defected_listings_products/'
    AND (`key` = 'mode' OR `key` = 'interval' OR `key` = 'last_time');

    UPDATE m2epro_synchronization_config
    SET `group` = '/amazon/templates/synchronization/list/'
    WHERE `group` = '/amazon/templates/list/' AND `key` = 'mode';

    UPDATE m2epro_synchronization_config
    SET `group` = '/amazon/templates/synchronization/relist/'
    WHERE `group` = '/amazon/templates/relist/' AND `key` = 'mode';

    UPDATE m2epro_synchronization_config
    SET `group` = '/amazon/templates/synchronization/revise/'
    WHERE `group` = '/amazon/templates/revise/' AND `key` = 'mode';

    UPDATE m2epro_synchronization_config
    SET `group` = '/amazon/templates/synchronization/revise/total/'
    WHERE `group` = '/amazon/templates/revise/total/'
    AND (`key` = 'last_listing_product_id' OR `key` = 'start_date' OR `key` = 'end_date');

    UPDATE m2epro_synchronization_config
    SET `group` = '/amazon/templates/synchronization/stop/'
    WHERE `group` = '/amazon/templates/stop/' AND `key` = 'mode';

    ### -------------------------------

    ### Buy
    ### -------------------------------

    UPDATE m2epro_synchronization_config
    SET `group` = '/buy/listings_products/'
    WHERE `group` = '/buy/defaults/' AND `key` = 'mode';

    UPDATE m2epro_synchronization_config
    SET `group` = '/buy/listings_products/update/'
    WHERE `group` = '/buy/defaults/update_listings_products/'
    AND (`key` = 'mode' OR `key` = 'interval' OR `key` = 'last_time');

    INSERT INTO m2epro_synchronization_config (`group`, `key`, `value`, `notice`)
    VALUES ('/buy/templates/synchronization/', 'mode', 1, '0 - disable,\r\n1 - enable');

    UPDATE m2epro_synchronization_config
    SET `group` = '/buy/templates/synchronization/list/'
    WHERE `group` = '/buy/templates/list/' AND `key` = 'mode';

    UPDATE m2epro_synchronization_config
    SET `group` = '/buy/templates/synchronization/relist/'
    WHERE `group` = '/buy/templates/relist/' AND `key` = 'mode';

    UPDATE m2epro_synchronization_config
    SET `group` = '/buy/templates/synchronization/revise/'
    WHERE `group` = '/buy/templates/revise/' AND `key` = 'mode';

    UPDATE m2epro_synchronization_config
    SET `group` = '/buy/templates/synchronization/revise/total/'
    WHERE `group` = '/buy/templates/revise/total/'
    AND (`key` = 'last_listing_product_id' OR `key` = 'start_date' OR `key` = 'end_date');

    UPDATE m2epro_synchronization_config
    SET `group` = '/buy/templates/synchronization/stop/'
    WHERE `group` = '/buy/templates/stop/' AND `key` = 'mode';

    ### -------------------------------
*/

$installer->getTableModifier('synchronization_log')->truncate();

// Global
// --------------------------------------------

$synchConfig = $installer->getSynchConfigModifier();

$synchConfig->insert("/global/", "mode", 1, "0 - disable,\r\n1 - enable");

$synchConfig->getEntity("/defaults/", "mode")
    ->updateGroup("/global/magento_products/");
$synchConfig->getEntity("/defaults/deleted_products/", "mode")
    ->updateGroup("/global/magento_products/deleted_products/");
$synchConfig->getEntity("/defaults/deleted_products/", "interval")
    ->updateGroup("/global/magento_products/deleted_products/");
$synchConfig->getEntity("/defaults/deleted_products/", "last_time")
    ->updateGroup("/global/magento_products/deleted_products/");
$synchConfig->getEntity("/defaults/added_products/", "last_magento_product_id")
    ->updateGroup("/global/magento_products/added_products/");

$synchConfig->getEntity("/defaults/inspector/", "mode")
    ->updateGroup("/global/magento_products/inspector/");
$synchConfig->getEntity("/defaults/inspector/product_changes/", "type")->delete();

$synchConfig->getEntity("/defaults/inspector/product_changes/circle/", "last_listing_product_id")
    ->updateGroup("/global/magento_products/inspector/");
$synchConfig->getEntity("/defaults/inspector/product_changes/circle/", "min_interval_between_circles")
    ->updateGroup("/global/magento_products/inspector/");
$synchConfig->getEntity("/defaults/inspector/product_changes/circle/", "max_count_times_for_full_circle")
    ->updateGroup("/global/magento_products/inspector/");
$synchConfig->getEntity("/defaults/inspector/product_changes/circle/", "min_count_items_per_one_time")
    ->updateGroup("/global/magento_products/inspector/");
$synchConfig->getEntity("/defaults/inspector/product_changes/circle/", "max_count_items_per_one_time")
    ->updateGroup("/global/magento_products/inspector/");
$synchConfig->getEntity("/defaults/inspector/product_changes/circle/", "last_time_start_circle")
    ->updateGroup("/global/magento_products/inspector/");

$synchConfig->getEntity("/defaults/processing/", "mode")
    ->updateGroup("/global/processing/");

$synchConfig->getEntity("/defaults/stop_queue/", "mode")
    ->updateGroup("/global/stop_queue/");
$synchConfig->getEntity("/defaults/stop_queue/", "interval")
    ->updateGroup("/global/stop_queue/");
$synchConfig->getEntity("/defaults/stop_queue/", "last_time")
    ->updateGroup("/global/stop_queue/");

// eBay
// --------------------------------------------

$synchConfig->insert("/ebay/general/", "mode", 1, "0 - disable,\r\n1 - enable");
$synchConfig->getEntity("/ebay/defaults/account_pickup_store/", "mode")
    ->updateGroup("/ebay/general/account_pickup_store/");

$synchConfig->getEntity("/ebay/feedbacks/", "mode")
    ->updateGroup("/ebay/general/feedbacks/");
$synchConfig->getEntity("/ebay/feedbacks/receive/", "mode")
    ->updateGroup("/ebay/general/feedbacks/receive/");
$synchConfig->getEntity("/ebay/feedbacks/receive/", "interval")
    ->updateGroup("/ebay/general/feedbacks/receive/");
$synchConfig->getEntity("/ebay/feedbacks/receive/", "last_time")
    ->updateGroup("/ebay/general/feedbacks/receive/");

$synchConfig->getEntity("/ebay/feedbacks/response/", "mode")
    ->updateGroup("/ebay/general/feedbacks/response/");
$synchConfig->getEntity("/ebay/feedbacks/response/", "interval")
    ->updateGroup("/ebay/general/feedbacks/response/");
$synchConfig->getEntity("/ebay/feedbacks/response/", "last_time")
    ->updateGroup("/ebay/general/feedbacks/response/");
$synchConfig->getEntity("/ebay/feedbacks/response/", "attempt_interval")
    ->updateGroup("/ebay/general/feedbacks/response/");

$synchConfig->getEntity("/ebay/defaults/", "mode")
    ->updateGroup("/ebay/listings_products/");
$synchConfig->getEntity("/ebay/defaults/remove_duplicates/", "mode")
    ->updateGroup("/ebay/listings_products/remove_duplicates/");
$synchConfig->getEntity("/ebay/defaults/update_listings_products/", "mode")
    ->updateGroup("/ebay/listings_products/update/");

$synchConfig->getEntity("/ebay/other_listings/templates/", "mode")
    ->updateGroup("/ebay/other_listings/synchronization/");
$synchConfig->getEntity("/ebay/other_listings/templates/relist/", "mode")
    ->updateGroup("/ebay/other_listings/synchronization/relist/");
$synchConfig->getEntity("/ebay/other_listings/templates/revise/", "mode")
    ->updateGroup("/ebay/other_listings/synchronization/revise/");
$synchConfig->getEntity("/ebay/other_listings/templates/stop/", "mode")
    ->updateGroup("/ebay/other_listings/synchronization/stop/");

$synchConfig->insert("/ebay/templates/synchronization/", "mode", 1, "0 - disable,\r\n1 - enable");
$synchConfig->getEntity("/ebay/templates/list/", "mode")
    ->updateGroup("/ebay/templates/synchronization/list/");
$synchConfig->getEntity("/ebay/templates/relist/", "mode")
    ->updateGroup("/ebay/templates/synchronization/relist/");
$synchConfig->getEntity("/ebay/templates/revise/", "mode")
    ->updateGroup("/ebay/templates/synchronization/revise/");
$synchConfig->getEntity("/ebay/templates/revise/total/", "last_listing_product_id")
    ->updateGroup("/ebay/templates/synchronization/revise/total/");
$synchConfig->getEntity("/ebay/templates/revise/total/", "start_date")
    ->updateGroup("/ebay/templates/synchronization/revise/total/");
$synchConfig->getEntity("/ebay/templates/revise/total/", "end_date")
    ->updateGroup("/ebay/templates/synchronization/revise/total/");
$synchConfig->getEntity("/ebay/templates/stop/", "mode")
    ->updateGroup("/ebay/templates/synchronization/stop/");

$synchConfig->getEntity("/ebay/defaults/remove_unused_templates/", "mode")
    ->updateGroup("/ebay/templates/remove_unused/");
$synchConfig->getEntity("/ebay/defaults/remove_unused_templates/", "interval")
    ->updateGroup("/ebay/templates/remove_unused/");
$synchConfig->getEntity("/ebay/defaults/remove_unused_templates/", "last_time")
    ->updateGroup("/ebay/templates/remove_unused/");

// Amazon
// --------------------------------------------

$synchConfig->insert('/amazon/general/', 'mode', 1, '0 - disable, \r\n1 - enable');

$synchConfig->getEntity("/amazon/defaults/run_parent_processors/", "mode")
    ->updateGroup("/amazon/general/run_parent_processors/");
$synchConfig->getEntity("/amazon/defaults/run_parent_processors/", "interval")
    ->updateGroup("/amazon/general/run_parent_processors/");
$synchConfig->getEntity("/amazon/defaults/run_parent_processors/", "last_time")
    ->updateGroup("/amazon/general/run_parent_processors/");

$synchConfig->getEntity("/amazon/defaults/", "mode")
    ->updateGroup("/amazon/listings_products/");

$synchConfig->getEntity("/amazon/defaults/update_listings_products/", "mode")
    ->updateGroup("/amazon/listings_products/update/");
$synchConfig->getEntity("/amazon/defaults/update_listings_products/", "interval")
    ->updateGroup("/amazon/listings_products/update/");
$synchConfig->getEntity("/amazon/defaults/update_listings_products/", "last_time")
    ->updateGroup("/amazon/listings_products/update/");

$synchConfig->getEntity("/amazon/defaults/update_defected_listings_products/", "mode")
    ->updateGroup("/amazon/listings_products/update/defected/");
$synchConfig->getEntity("/amazon/defaults/update_defected_listings_products/", "interval")
    ->updateGroup("/amazon/listings_products/update/defected/");
$synchConfig->getEntity("/amazon/defaults/update_defected_listings_products/", "last_time")
    ->updateGroup("/amazon/listings_products/update/defected/");

$synchConfig->insert("/amazon/templates/synchronization/", "mode", 1, "0 - disable,\r\n1 - enable");
$synchConfig->getEntity("/amazon/templates/list/", "mode")
    ->updateGroup("/amazon/templates/synchronization/list/");
$synchConfig->getEntity("/amazon/templates/relist/", "mode")
    ->updateGroup("/amazon/templates/synchronization/relist/");
$synchConfig->getEntity("/amazon/templates/revise/", "mode")
    ->updateGroup("/amazon/templates/synchronization/revise/");
$synchConfig->getEntity("/amazon/templates/revise/total/", "last_listing_product_id")
    ->updateGroup("/amazon/templates/synchronization/revise/total/");
$synchConfig->getEntity("/amazon/templates/revise/total/", "start_date")
    ->updateGroup("/amazon/templates/synchronization/revise/total/");
$synchConfig->getEntity("/amazon/templates/revise/total/", "end_date")
    ->updateGroup("/amazon/templates/synchronization/revise/total/");
$synchConfig->getEntity("/amazon/templates/stop/", "mode")
    ->updateGroup("/amazon/templates/synchronization/stop/");

// Buy
// --------------------------------------------

$synchConfig->getEntity("/buy/defaults/", "mode")
    ->updateGroup("/buy/listings_products/");
$synchConfig->getEntity("/buy/defaults/update_listings_products/", "mode")
    ->updateGroup("/buy/listings_products/update/");
$synchConfig->getEntity("/buy/defaults/update_listings_products/", "interval")
    ->updateGroup("/buy/listings_products/update/");
$synchConfig->getEntity("/buy/defaults/update_listings_products/", "last_time")
    ->updateGroup("/buy/listings_products/update/");

$synchConfig->insert("/buy/templates/synchronization/", "mode", 1, "0 - disable,\r\n1 - enable");
$synchConfig->getEntity("/buy/templates/list/", "mode")
    ->updateGroup("/buy/templates/synchronization/list/");
$synchConfig->getEntity("/buy/templates/relist/", "mode")
    ->updateGroup("/buy/templates/synchronization/relist/");
$synchConfig->getEntity("/buy/templates/revise/", "mode")
    ->updateGroup("/buy/templates/synchronization/revise/");
$synchConfig->getEntity("/buy/templates/revise/total/", "last_listing_product_id")
    ->updateGroup("/buy/templates/synchronization/revise/total/");
$synchConfig->getEntity("/buy/templates/revise/total/", "start_date")
    ->updateGroup("/buy/templates/synchronization/revise/total/");
$synchConfig->getEntity("/buy/templates/revise/total/", "end_date")
    ->updateGroup("/buy/templates/synchronization/revise/total/");
$synchConfig->getEntity("/buy/templates/stop/", "mode")
    ->updateGroup("/buy/templates/synchronization/stop/");

//########################################

if ($installer->getTablesObject()->isExists('amazon_processed_inventory')) {
    $installer->run(<<<SQL

    DROP TABLE IF EXISTS m2epro_amazon_processed_inventory;

SQL
    );
}

// --------------------------------------------

/*
    INSERT INTO m2epro_synchronization_config (`group`, `key`, `value`, `notice`)
    VALUES ('/amazon/listings_products/update/blocked/', 'mode', 1, '0 - disable,\r\n1 - enable'),
           ('/amazon/listings_products/update/blocked/', 'interval', 3600, 'in seconds'),
           ('/amazon/listings_products/update/blocked/', 'last_time', NULL, 'in seconds');

    INSERT INTO m2epro_synchronization_config (`group`, `key`, `value`, `notice`)
    VALUES ('/amazon/other_listings/update/blocked/', 'mode', 1, '0 - disable,\r\n1 - enable'),
           ('/amazon/other_listings/update/blocked/', 'interval', 3600, 'in seconds'),
           ('/amazon/other_listings/update/blocked/', 'last_time', NULL, 'in seconds');
*/

$synchConfig = $installer->getSynchConfigModifier();

$synchConfig->insert('/amazon/listings_products/update/blocked/', 'mode', 1, '0 - disable,\r\n1 - enable');
$synchConfig->insert('/amazon/listings_products/update/blocked/', 'interval', 3600, 'in seconds');
$synchConfig->insert('/amazon/listings_products/update/blocked/', 'last_time', NULL, 'in seconds');

$synchConfig->insert('/amazon/other_listings/update/blocked/', 'mode', 1, '0 - disable,\r\n1 - enable');
$synchConfig->insert('/amazon/other_listings/update/blocked/', 'interval', 3600, 'in seconds');
$synchConfig->insert('/amazon/other_listings/update/blocked/', 'last_time', NULL, 'in seconds');

//########################################

/*
    UPDATE `m2epro_synchronization_config`
    SET `group` = '/ebay/general/account_pickup_store/update/'
    WHERE `group` = '/ebay/general/account_pickup_store/';

    INSERT INTO `m2epro_synchronization_config` (`group`,`key`,`value`,`notice`,`update_date`,`create_date`)
    VALUES ('/ebay/general/account_pickup_store/', 'mode', '1', '0 - disable, \r\n1 - enable',
             '2013-05-08 00:00:00', '2013-05-08 00:00:00'),
           ('/ebay/general/account_pickup_store/process/', 'mode', '1', '0 - disable, \r\n1 - enable',
             '2013-05-08 00:00:00', '2013-05-08 00:00:00');
*/

$installer->getSynchConfigModifier()
    ->getEntity('/ebay/general/account_pickup_store/', 'mode')
    ->updateGroup('/ebay/general/account_pickup_store/update/');

$installer->getSynchConfigModifier()
    ->insert('/ebay/general/account_pickup_store/', 'mode', 1, '0 - disable, \r\n1 - enable');
$installer->getSynchConfigModifier()
    ->insert('/ebay/general/account_pickup_store/process/', 'mode', 1, '0 - disable, \r\n1 - enable');

//########################################

// eBay Out of Stock Control
//########################################
/*
    ALTER TABLE m2epro_ebay_account
    ADD COLUMN user_preferences TEXT DEFAULT NULL AFTER info;
*/

$installer->getTableModifier('ebay_account')
    ->addColumn('user_preferences', 'TEXT', 'NULL', 'info');

//########################################

/*
    INSERT INTO `m2epro_config` (`group`,`key`,`value`,`notice`,`update_date`,`create_date`) VALUES
    ('/cron/task/update_ebay_accounts_preferences/', 'mode', '1', '0 - disable, \r\n1 - enable',
    '2016-01-01 00:00:00', '2016-01-01 00:00:00'),
    ('/cron/task/update_ebay_accounts_preferences/', 'interval', '86400', 'in seconds',
    '2016-01-01 00:00:00', '2016-01-01 00:00:00'),
    ('/cron/task/update_ebay_accounts_preferences/', 'last_run', NULL, 'date of last run',
    '2016-01-01 00:00:00', '2016-01-01 00:00:00');

    INSERT INTO `m2epro_config` (`group`,`key`,`value`,`notice`,`update_date`,`create_date`) VALUES
    ('/view/products_grid/', 'use_alternative_mysql_select', '0', '0 - disable, \r\n1 - enable',
    '2016-01-01 00:00:00', '2016-01-01 00:00:00');
*/

$installer->getMainConfigModifier()
    ->insert("/cron/task/update_ebay_accounts_preferences/", "mode", 1, "0 - disable,\r\n1 - enable");
$installer->getMainConfigModifier()
    ->insert("/cron/task/update_ebay_accounts_preferences/", "interval", 86400, "in seconds");
$installer->getMainConfigModifier()
    ->insert("/cron/task/update_ebay_accounts_preferences/", "last_run", NULL, "date of last access");

$installer->getMainConfigModifier()
    ->insert('/view/products_grid/', 'use_alternative_mysql_select', 0, "0 - disable, \r\n1 - enable");

//########################################

$configTable = $installer->getTable('m2epro_config');
$cacheConfigTable = $installer->getTable('m2epro_cache_config');

$oldData = $connection->query("

SELECT * FROM `{$configTable}` WHERE
    `group` = '/view/ebay/advanced/autoaction_popup/' AND `key` = 'shown' OR
    `group` = '/view/ebay/motors_epids_attribute/' AND `key` = 'listing_notification_shown' OR
    `group` = '/view/ebay/multi_currency_marketplace_2/' AND `key` = 'notification_shown' OR
    `group` = '/view/ebay/multi_currency_marketplace_19/' AND `key` = 'notification_shown' OR
    `group` = '/view/requirements/popup/' AND `key` = 'closed'

")->fetchAll();

$insertParts = array();
$ids = array();
foreach ($oldData as $tempRow) {

    $insertParts[] = "(
        '{$tempRow['group']}',
        '{$tempRow['key']}',
        '{$tempRow['value']}',
        '{$tempRow['notice']}',
        '{$tempRow['update_date']}',
        '{$tempRow['create_date']}'
    )";

    $ids[] = $tempRow['id'];
}

if (!empty($insertParts)) {

    $insertString = implode(',', $insertParts);
    $insertSql = 'INSERT INTO `'.$cacheConfigTable.'` (`group`,`key`,`value`,`notice`,`update_date`,`create_date`)
                  VALUES' . $insertString;

    $connection->query($insertSql);

    $idsString = implode(',', $ids);

    $connection->query(<<<SQL

        DELETE FROM `{$configTable}` WHERE `id` IN ({$idsString});

SQL
    );
}

$installer->run(<<<SQL

    UPDATE `m2epro_cache_config`
    SET `group` = '/view/ebay/listing/advanced/autoaction_popup/',
        `key`   = 'shown'
    WHERE `group` = '/view/ebay/advanced/autoaction_popup/'
      AND `key`   = 'shown';

    UPDATE `m2epro_cache_config`
    SET `group` = '/view/ebay/listing/motors_epids_attribute/',
        `key`   = 'notification_shown'
    WHERE `group` = '/view/ebay/motors_epids_attribute/'
      AND `key`   = 'listing_notification_shown';

    UPDATE `m2epro_cache_config`
    SET `group` = '/view/ebay/template/selling_format/multi_currency_marketplace_2/',
        `key`   = 'notification_shown'
    WHERE `group` = '/view/ebay/multi_currency_marketplace_2/'
      AND `key`   = 'notification_shown';

    UPDATE `m2epro_cache_config`
    SET `group` = '/view/ebay/template/selling_format/multi_currency_marketplace_19/',
        `key`   = 'notification_shown'
    WHERE `group` = '/view/ebay/multi_currency_marketplace_19/'
      AND `key`   = 'notification_shown';

SQL
);

//########################################

/*
    INSERT INTO `m2epro_config` (`group`,`key`,`value`,`notice`,`update_date`,`create_date`) VALUES
        ('/cron/checker/task/repair_crashed_tables/', 'interval', '3600', 'in seconds',
        '2016-02-18 00:00:00', '2016-02-18 00:00:00');
*/

$installer->getMainConfigModifier()
    ->insert('/cron/checker/task/repair_crashed_tables/', 'interval', '3600', 'in seconds');

//########################################

$tempTable = $installer->getTablesObject()->getFullName('wizard');
$tempQuery = <<<SQL
    SELECT * FROM {$tempTable}
    WHERE `nick` = 'removedEbay3rdParty';
SQL;
$tempRow = $connection->query($tempQuery)->fetch();

if ($tempRow === false) {

    $tempTable = $installer->getTablesObject()->getFullName('synchronization_config');
    $queryStmt = $connection->query(<<<SQL

SELECT `value` FROM {$tempTable} WHERE
    (`group` = '/ebay/other_listing/synchronization/' AND `key` = 'mode')
OR
    (`group` = '/ebay/other_listing/source/');

SQL
    );

    $wizardStatus = 3;
    while ($mode = $queryStmt->fetchColumn()) {

        if ($mode == 1) {
            $wizardStatus = 0;
            break;
        }
    }

    $installer->run(<<<SQL

INSERT INTO `m2epro_wizard` (`nick`, `view`, `status`, `step`, `type`, `priority`)
SELECT 'removedEbay3rdParty', 'ebay', {$wizardStatus}, NULL, 0, MAX( `priority` )+1 FROM `m2epro_wizard`;

SQL
    );
}

// ---------------------------------------

$installer->run(<<<SQL

DELETE FROM `m2epro_synchronization_config`
WHERE `group` LIKE '%/ebay/other_listings/synchronization/%' OR
      `group` LIKE '%/ebay/other_listing/%';

SQL
);

//########################################

/*
   ALTER TABLE `m2epro_ebay_listing_product`
       ADD COLUMN `online_duration` INT(11) UNSIGNED DEFAULT NULL AFTER `online_title`;

   ALTER TABLE `m2epro_ebay_listing_other`
       ADD COLUMN `online_duration` INT(11) UNSIGNED DEFAULT NULL AFTER `currency`;
*/

$installer->getTableModifier('ebay_listing_product')
    ->addColumn('online_duration', 'INT(11) UNSIGNED', 'NULL', 'online_title');

$installer->getTableModifier('ebay_listing_other')
    ->addColumn('online_duration', 'INT(11) UNSIGNED', 'NULL', 'currency');

//########################################

$installer->run(<<<SQL

    UPDATE `m2epro_listing_other`
    SET `status` = 3
    WHERE `component_mode` = 'ebay' AND `status` = 6;

SQL
);

//########################################

/*
    DELETE FROM `m2epro_config` WHERE `group` = '/support/uservoice/';
*/

$installer->getMainConfigModifier()
    ->getEntity('/support/uservoice/', 'api_url')->delete();
$installer->getMainConfigModifier()
    ->getEntity('/support/uservoice/', 'api_client_key')->delete();

//########################################

$installer->run(<<<SQL

UPDATE `m2epro_amazon_listing_other`
SET `title` = '--'
WHERE `title` = ''
OR `title` = 'Unknown (can\'t be received)'
OR `title` IS NULL;

SQL
);

//########################################

// eBay Item UUID
//########################################

/*
    ALTER TABLE `m2epro_ebay_listing_product`
        ADD COLUMN `item_uuid` VARCHAR(32) DEFAULT NULL AFTER `ebay_item_id`,
        ADD COLUMN `is_duplicate` TINYINT(2) UNSIGNED NOT NULL DEFAULT 0 AFTER `item_uuid`,
        ADD INDEX `item_uuid`(`item_uuid`),
        ADD INDEX `is_duplicate`(`is_duplicate`);
*/

$installer->getTableModifier('ebay_listing_product')
    ->addColumn('item_uuid', 'VARCHAR(32)', 'NULL', 'ebay_item_id', true)
    ->addColumn('is_duplicate', 'TINYINT(2) UNSIGNED NOT NULL', '0', 'item_uuid', true);

//########################################

// Amazon orders fulfilment details
//########################################

$installer->getSynchConfigModifier()->insert(
    '/amazon/orders/receive_details/', 'mode', 0, '0 - disable, \r\n1 - enable'
);
$installer->getSynchConfigModifier()->insert(
    '/amazon/orders/receive_details/', 'interval', 3600, 'in seconds'
);
$installer->getSynchConfigModifier()->insert(
    '/amazon/orders/receive_details/', 'last_time', NULL, 'Last check time'
);

//########################################

// Grids Performance
//########################################

// SUPPORT URLS CHANGES
//########################################

/*
    UPDATE `m2epro_config`
    SET `value` = 'https://support.m2epro.com/knowledgebase'
    WHERE `group` = '/support/' AND `key` = 'knowledge_base_url';

    UPDATE `m2epro_config`
    SET `value` = 'https://docs.m2epro.com'
    WHERE `group` = '/support/' AND `key` = 'documentation_url';

    UPDATE `m2epro_config`
    SET `value` = 'https://m2epro.com/'
    WHERE `group` = '/support/' AND `key` = 'main_website_url';

    UPDATE `m2epro_config`
    SET `value` = 'https://support.m2epro.com/'
    WHERE `group` = '/support/' AND `key` = 'main_support_url';

    UPDATE `m2epro_config`
    SET `value` = 'https://www.magentocommerce.com/magento-connect/
                   ebay-amazon-rakuten-magento-integration-order-import-and-stock-level-synchronization.html'
    WHERE `group` = '/support/' AND `key` = 'magento_connect_url'
*/

$installer->getMainConfigModifier()
    ->getEntity('/support/', 'knowledge_base_url')->updateValue('https://support.m2epro.com/knowledgebase');

$installer->getMainConfigModifier()
    ->getEntity('/support/', 'documentation_url')->updateValue('https://docs.m2epro.com');

$installer->getMainConfigModifier()
    ->getEntity('/support/', 'main_website_url')->updateValue('https://m2epro.com/');

$installer->getMainConfigModifier()
    ->getEntity('/support/', 'main_support_url')->updateValue('https://support.m2epro.com/');

$magentoConnectUrl = 'https://www.magentocommerce.com/'
    . 'magento-connect/ebay-amazon-rakuten-magento-integration-order-import-and-stock-level-synchronization.html';
$installer->getMainConfigModifier()
    ->getEntity('/support/', 'magento_connect_url')->updateValue($magentoConnectUrl);

//########################################

// AMAZON SHIPPING TEMPLATES
//########################################

if (!$installer->getTablesObject()->isExists('amazon_template_shipping_template')) {

    $installer->run(<<<SQL

DROP TABLE IF EXISTS `m2epro_amazon_template_shipping_template`;
CREATE TABLE `m2epro_amazon_template_shipping_template` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `template_name_mode` TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
    `template_name_value` VARCHAR(255) NOT NULL,
    `template_name_attribute` VARCHAR(255) NOT NULL,
    `update_date` datetime DEFAULT NULL,
    `create_date` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `title` (`title`)
)
ENGINE = INNODB
CHARACTER SET utf8
COLLATE utf8_general_ci;

SQL
    );
}

//----------------------------------------

/*
    ALTER TABLE `m2epro_amazon_listing_product`
    ADD COLUMN `template_shipping_template_id` INT(11) UNSIGNED DEFAULT NULL AFTER `template_description_id`,
    ADD INDEX `template_shipping_template_id`(`template_shipping_template_id`);

    ALTER TABLE `m2epro_amazon_account`
    ADD COLUMN `shipping_mode` INT(11) UNSIGNED DEFAULT 1 AFTER `related_store_id`;

    ALTER TABLE `m2epro_amazon_template_synchronization`
    CHANGE COLUMN `revise_change_shipping_override_template`
                  `revise_change_shipping_template` tinyint(2) UNSIGNED NOT NULL;
*/

$installer->getTableModifier('amazon_listing_product')
    ->addColumn('template_shipping_template_id', 'INT(11) UNSIGNED', 'NULL', 'template_description_id', true);

$installer->getTableModifier('amazon_account')
    ->addColumn('shipping_mode', 'INT(11) UNSIGNED', '1', 'related_store_id');

$installer->getTableModifier('amazon_template_synchronization')
    ->renameColumn('revise_change_shipping_override_template', 'revise_change_shipping_template', false);

//----------------------------------------

$installer->run(<<<SQL

    UPDATE `m2epro_amazon_account`
    SET `shipping_mode` = 0;

SQL
);

//----------------------------------------

$tempTable = $installer->getTablesObject()->getFullName('listing_product');
$queryStmt = $connection->query("
    SELECT `id`,
           `synch_reasons`
    FROM {$tempTable}
    WHERE `synch_reasons` LIKE '%shippingOverrideTemplate%';
");

while ($row = $queryStmt->fetch()) {

    $reasons = explode(',', $row['synch_reasons']);
    $reasons =  array_unique(array_filter($reasons));

    array_walk($reasons, function (&$el){
        $el = str_replace('shippingOverrideTemplate', 'shippingTemplate', $el);
    });
    $reasons = implode(',', $reasons);

    $connection->query("
        UPDATE {$tempTable}
        SET `synch_reasons` = '{$reasons}'
        WHERE `id` = {$row['id']}
    ");
}

//########################################

// OTHER SET OF CHANGES
//########################################

// ability to disable module
// ---------------------------------------

/*
    INSERT INTO `m2epro_config` (`group`,`key`,`value`,`notice`,`update_date`,`create_date`) VALUES
    (NULL, 'is_disabled', '0', '0 - disable, \r\n1 - enable', '2016-01-01 00:00:00', '2016-01-01 00:00:00');
*/

$installer->getMainConfigModifier()->insert(NULL, 'is_disabled', '0', '0 - disable, \r\n1 - enable');

// cron service can connect from several hostnames
// ---------------------------------------

$installer->getMainConfigModifier()
    ->getEntity('/cron/service/', 'hostname')->updateKey('hostname_1');

// clear garbage from other log table
// ---------------------------------------

$installer->run(<<<SQL
    DELETE FROM `m2epro_listing_other_log` WHERE `action` IN (2, 3, 9, 10, 11, 12, 13, 14, 15, 16, 17);
SQL
);

// fix eBay item URLs for Motors
// ---------------------------------------

$installer->run(<<<SQL
    UPDATE `m2epro_marketplace`
    SET `url` = 'ebay.com/motors'
    WHERE `id` = 9;
SQL
);

// fix for AFN default value
// ---------------------------------------

/*
    ALTER TABLE `m2epro_amazon_listing_product`
       CHANGE COLUMN `is_afn_channel` `is_afn_channel` TINYINT(2) UNSIGNED NOT NULL DEFAULT 0;
*/

$installer->getTableModifier('amazon_listing_product')
    ->changeColumn('is_afn_channel', 'TINYINT(2) UNSIGNED NOT NULL', 0);

// fix for mode_same_category_data
// ---------------------------------------

$listingTable = $installer->getTablesObject()->getFullName('listing');
$listings = $installer->getConnection()->query("
  SELECT * FROM {$listingTable} WHERE `additional_data` LIKE '%mode_same_category_data%';
")->fetchAll(PDO::FETCH_ASSOC);

foreach ($listings as $listing) {

    $listingId = $listing['id'];
    $additionalData = (array)@json_decode($listing['additional_data'], true);

    if (!empty($additionalData['mode_same_category_data']['specifics'])) {
        foreach ($additionalData['mode_same_category_data']['specifics'] as &$specific) {
            unset($specific['attribute_id'], $specific['mode_relation_id']);

            if (!empty($specific['value_ebay_recommended'])) {

                $recommendedValues = (array)@json_decode($specific['value_ebay_recommended'], true);

                if (empty($recommendedValues)) {
                    continue;
                }

                foreach ($recommendedValues as &$recommendedValue) {
                    if (!empty($recommendedValue['value'])) {
                        $recommendedValue = $recommendedValue['value'];
                        $hasOldStructure = true;
                    }
                }
                unset($recommendedValue);

                $specific['value_ebay_recommended'] = json_encode($recommendedValues);
            }
        }
        unset($specific);
    }

    $connection->update(
        $listingTable,
        array('additional_data' => json_encode($additionalData)),
        array('id = ?' => $listingId)
    );
}

// MarketplacesFeatures
// ---------------------------------------

/*
    ALTER TABLE `m2epro_ebay_marketplace`
        ADD COLUMN `is_epid` TINYINT(2) UNSIGNED NOT NULL DEFAULT 0 AFTER `is_holiday_return`;
    ALTER TABLE `m2epro_ebay_marketplace`
        ADD COLUMN `is_ktype` TINYINT(2) UNSIGNED NOT NULL DEFAULT 0 AFTER `is_epid`;
*/

// ---------------------------------------

$installer->getTableModifier('ebay_marketplace')->addColumn(
    'is_epid', 'TINYINT(2) UNSIGNED NOT NULL', 0, 'is_holiday_return', true
);

$installer->getTableModifier('ebay_marketplace')->addColumn(
    'is_ktype', 'TINYINT(2) UNSIGNED NOT NULL', 0, 'is_epid', true
);

// ---------------------------------------

$installer->run(<<<SQL
    UPDATE `{$installer->getTable('m2epro_ebay_marketplace')}`
    SET `is_epid` = 1
    WHERE `marketplace_id` IN (3, 8, 9);
SQL
);

$installer->run(<<<SQL
    UPDATE `{$installer->getTable('m2epro_ebay_marketplace')}`
    SET `is_ktype` = 1
    WHERE `marketplace_id` IN (3, 4, 7, 8, 10, 13);
SQL
);

// SearchSettingsDataCapacity
// ---------------------------------------

/*
    ALTER TABLE `m2epro_amazon_listing_product`
        CHANGE COLUMN `search_settings_data` `search_settings_data` LONGTEXT DEFAULT NULL;
*/

// ---------------------------------------

$installer->getTableModifier('amazon_listing_product')
    ->changeColumn('search_settings_data', 'LONGTEXT', 'NULL');

// AfnAndRepricingFiltersImprovements
// ---------------------------------------

/*
    ALTER TABLE `m2epro_amazon_listing_product`
    ADD COLUMN `variation_parent_afn_state` SMALLINT(4) UNSIGNED DEFAULT NULL AFTER `is_general_id_owner`,
    ADD COLUMN `variation_parent_repricing_state` SMALLINT(4) UNSIGNED DEFAULT NULL AFTER `variation_parent_afn_state`,
    ADD INDEX `variation_parent_afn_state` (`variation_parent_afn_state`),
    ADD INDEX `variation_parent_repricing_state` (`variation_parent_repricing_state`);
*/

// ---------------------------------------

$installer->getTableModifier('amazon_listing_product')
    ->addColumn(
        'variation_parent_afn_state', 'SMALLINT(4) UNSIGNED', 'NULL', 'is_general_id_owner', true, false
    )
    ->addColumn(
        'variation_parent_repricing_state', 'SMALLINT(4) UNSIGNED', 'NULL', 'variation_parent_afn_state', true, false
    )
    ->commit();

// Templates synchronization settings
// ---------------------------------------

/*
    DELETE FROM `m2epro_synchronization_config`
    WHERE `group` = '/settings/product_change/' AND `key` = 'max_count';

    UPDATE `m2epro_synchronization_config`
    SET `value` = '172800'
    WHERE `group` = '/settings/product_change/' AND `key` = 'max_lifetime';
 */

$installer->getSynchConfigModifier()->getEntity('/settings/product_change/', 'max_count')->delete();
$installer->getSynchConfigModifier()->getEntity('/settings/product_change/', 'max_lifetime')->updateValue('172800');

//########################################

// TransactionalLocks
//########################################

$installer->run(<<<SQL

CREATE TABLE IF NOT EXISTS `m2epro_lock_transactional` (
   `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
   `nick` VARCHAR(255) NOT NULL,
   `create_date` DATETIME DEFAULT NULL,
   PRIMARY KEY (`id`),
   INDEX `nick` (`nick`)
)
ENGINE = INNODB
CHARACTER SET utf8
COLLATE utf8_general_ci;

SQL
);

// ArchivedEntity
//########################################

$installer->run(<<<SQL

CREATE TABLE IF NOT EXISTS `m2epro_archived_entity` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `origin_id` INT(11) UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `data` LONGTEXT NOT NULL,
  `create_date` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `origin_id__name` (`origin_id`, `name`)
)
ENGINE = INNODB
CHARACTER SET utf8
COLLATE utf8_general_ci;

SQL
);

//----------------------------------------

$installer->getMainConfigModifier()->insert(
    '/cron/task/archive_orders_entities/', 'mode', '1', '0 - disable, \r\n1 - enable'
);
$installer->getMainConfigModifier()->insert(
    '/cron/task/archive_orders_entities/', 'interval', '3600', 'in seconds'
);
$installer->getMainConfigModifier()->insert(
    '/cron/task/archive_orders_entities/', 'last_access', NULL, 'date of last access'
);
$installer->getMainConfigModifier()->insert(
    '/cron/task/archive_orders_entities/', 'last_run', NULL, 'date of last run'
);

// OrdersGridIndexes
//########################################

$installer->getTableModifier('amazon_order')->addIndex('purchase_create_date');
$installer->getTableModifier('ebay_order')->addIndex('purchase_create_date');
$installer->getTableModifier('buy_order')->addIndex('purchase_create_date');

// Price Convert
//########################################

$installer->getMainConfigModifier()->insert(
    '/magento/attribute/', 'price_type_converting', '0', '0 - disable, \r\n1 - enable'
);

// Amazon Business
//########################################

/*
    INSERT INTO `m2epro_config` (`group`,`key`,`value`,`notice`,`update_date`,`create_date`) VALUES
    ('/amazon/business/', 'mode', '0', '0 - disable, \r\n1 - enable',
     '2013-05-08 00:00:00', '2013-05-08 00:00:00');
 */

$installer->getMainConfigModifier()->insert('/amazon/business/', 'mode', '0', '0 - disable, \r\n1 - enable');

//########################################

$installer->getTableModifier('amazon_marketplace')
    ->addColumn(
        'is_business_available', 'tinyint(2) UNSIGNED NOT NULL', 0, 'is_merchant_fulfillment_available', true, false
    )
    ->addColumn(
        'is_vat_calculation_service_available', 'tinyint(2) UNSIGNED NOT NULL', 0, 'is_business_available', true, false
    )
    ->addColumn(
        'is_product_tax_code_policy_available', 'tinyint(2) UNSIGNED NOT NULL', 0,
        'is_vat_calculation_service_available', true, false
    )
    ->commit();

$connection->update(
    $installer->getTablesObject()->getFullName('amazon_marketplace'),
    array('is_business_available' => 1),
    array('marketplace_id IN (?)' => array(25, 28, 29)) // DE, UK, US
);

$connection->update(
    $installer->getTablesObject()->getFullName('amazon_marketplace'),
    array('is_vat_calculation_service_available' => 1),
    array('marketplace_id IN (?)' => array(25, 26, 28, 30, 31)) // Europe
);

$connection->update(
    $installer->getTablesObject()->getFullName('amazon_marketplace'),
    array('is_product_tax_code_policy_available' => 1),
    array('marketplace_id IN (?)' => array(25, 28)) // DE, UK
);

$installer->getTableModifier('amazon_account')
    ->addColumn(
        'is_vat_calculation_service_enabled', 'TINYINT(2) UNSIGNED NOT NULL', 0, 'magento_orders_settings', false, false
    )
    ->addColumn(
        'is_magento_invoice_creation_disabled', 'TINYINT(2) UNSIGNED NOT NULL', 0,
        'is_vat_calculation_service_enabled', false, false
    )
    ->commit();

//########################################

$installer->run(<<<SQL

CREATE TABLE IF NOT EXISTS `m2epro_amazon_template_product_tax_code` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `product_tax_code_mode` TINYINT(2) NOT NULL,
    `product_tax_code_value` VARCHAR(255) DEFAULT NULL,
    `product_tax_code_attribute` VARCHAR(255) DEFAULT NULL,
    `update_date` DATETIME DEFAULT NULL,
    `create_date` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `title` (`title`)
)
ENGINE = INNODB
CHARACTER SET utf8
COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `m2epro_amazon_template_selling_format_business_discount` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `template_selling_format_id` INT(11) UNSIGNED NOT NULL,
    `qty` INT(11) UNSIGNED NOT NULL,
    `mode` TINYINT(2) UNSIGNED NOT NULL,
    `attribute` VARCHAR(255) DEFAULT NULL,
    `coefficient` VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `template_selling_format_id` (`template_selling_format_id`)
)
ENGINE = INNODB
CHARACTER SET utf8
COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `m2epro_ebay_indexer_listing_product_parent` (
    `listing_product_id` INT(11) UNSIGNED NOT NULL,
    `listing_id` INT(11) UNSIGNED NOT NULL,
    `component_mode` VARCHAR(10) DEFAULT NULL,
    `min_price` DECIMAL(12, 4) UNSIGNED NOT NULL DEFAULT 0.0000,
    `max_price` DECIMAL(12, 4) UNSIGNED NOT NULL DEFAULT 0.0000,
    `create_date` DATETIME NOT NULL,
    PRIMARY KEY (`listing_product_id`),
    INDEX `listing_id` (`listing_id`),
    INDEX `component_mode` (`component_mode`)
)
ENGINE = INNODB
CHARACTER SET utf8
COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `m2epro_amazon_indexer_listing_product_parent` (
    `listing_product_id` INT(11) UNSIGNED NOT NULL,
    `listing_id` INT(11) UNSIGNED NOT NULL,
    `component_mode` VARCHAR(10) DEFAULT NULL,
    `min_regular_price` DECIMAL(12, 4) UNSIGNED DEFAULT NULL,
    `max_regular_price` DECIMAL(12, 4) UNSIGNED DEFAULT NULL,
    `min_business_price` DECIMAL(12, 4) UNSIGNED DEFAULT NULL,
    `max_business_price` DECIMAL(12, 4) UNSIGNED DEFAULT NULL,
    `create_date` DATETIME NOT NULL,
    PRIMARY KEY (`listing_product_id`),
    INDEX `listing_id` (`listing_id`),
    INDEX `component_mode` (`component_mode`)
)
ENGINE = INNODB
CHARACTER SET utf8
COLLATE utf8_general_ci;

SQL
);

//########################################

/*
    ALTER TABLE `m2epro_amazon_listing_product`
    CHANGE COLUMN `online_price` `online_regular_price` DECIMAL(12, 4) UNSIGNED DEFAULT NULL,
    CHANGE COLUMN `online_sale_price` `online_regular_sale_price` DECIMAL(12, 4) UNSIGNED DEFAULT NULL,
    CHANGE COLUMN `online_sale_price_start_date` `online_regular_sale_price_start_date` DATETIME DEFAULT NULL,
    CHANGE COLUMN `online_sale_price_end_date` `online_regular_sale_price_end_date` DATETIME DEFAULT NULL,
    ADD COLUMN `template_product_tax_code_id` INT(11) UNSIGNED DEFAULT NULL AFTER `template_shipping_override_id`,
    ADD COLUMN `online_business_price` DECIMAL(12, 4) UNSIGNED DEFAULT NULL AFTER `online_regular_sale_price_end_date`,
    ADD COLUMN `online_business_discounts` TEXT DEFAULT NULL AFTER `online_business_price`,
    DROP INDEX `online_price`,
    DROP INDEX `online_sale_price`,
    ADD INDEX `online_regular_price` (`online_regular_price`),
    ADD INDEX `online_regular_sale_price` (`online_regular_sale_price`),
    ADD INDEX `template_product_tax_code_id` (`template_product_tax_code_id`),
    ADD INDEX `online_business_price` (`online_business_price`);
 */

$installer->getTableModifier('amazon_listing_product')
    ->renameColumn(
        'online_price', 'online_regular_price', true, false
    )
    ->renameColumn(
        'online_sale_price', 'online_regular_sale_price', true, false
    )
    ->renameColumn(
        'online_sale_price_start_date', 'online_regular_sale_price_start_date', true, false
    )
    ->renameColumn(
        'online_sale_price_end_date', 'online_regular_sale_price_end_date', true, false
    )
    ->addColumn(
        'template_product_tax_code_id', 'INT(11) UNSIGNED', 'NULL', 'template_shipping_override_id', true, false
    )
    ->addColumn(
        'online_business_price', 'DECIMAL(12, 4) UNSIGNED', 'NULL', 'online_regular_sale_price_end_date', true, false
    )
    ->addColumn(
        'online_business_discounts', 'TEXT', 'NULL', 'online_business_price', false, false
    )
    ->commit();

//########################################

/*
    ALTER TABLE `m2epro_amazon_template_selling_format`
    CHANGE COLUMN `price_mode` `regular_price_mode` TINYINT(2) UNSIGNED NOT NULL,
    CHANGE COLUMN `price_custom_attribute` `regular_price_custom_attribute` VARCHAR(255) NOT NULL,
    CHANGE COLUMN `price_coefficient` `regular_price_coefficient` VARCHAR(255) NOT NULL,
    CHANGE COLUMN `map_price_mode` `regular_map_price_mode` TINYINT(2) UNSIGNED NOT NULL,
    CHANGE COLUMN `map_price_custom_attribute` `regular_map_price_custom_attribute` VARCHAR(255) NOT NULL,
    CHANGE COLUMN `sale_price_mode` `regular_sale_price_mode` TINYINT(2) UNSIGNED NOT NULL,
    CHANGE COLUMN `sale_price_custom_attribute` `regular_sale_price_custom_attribute` VARCHAR(255) NOT NULL,
    CHANGE COLUMN `sale_price_coefficient` `regular_sale_price_coefficient` VARCHAR(255) NOT NULL,
    CHANGE COLUMN `price_variation_mode` `regular_price_variation_mode` TINYINT(2) UNSIGNED NOT NULL,
    CHANGE COLUMN `sale_price_start_date_mode` `regular_sale_price_start_date_mode` TINYINT(2) UNSIGNED NOT NULL,
    CHANGE COLUMN `sale_price_start_date_value` `regular_sale_price_start_date_value` DATETIME NOT NULL,
    CHANGE COLUMN `sale_price_start_date_custom_attribute`
                  `regular_sale_price_start_date_custom_attribute` VARCHAR(255) NOT NULL,
    CHANGE COLUMN `sale_price_end_date_mode` `regular_sale_price_end_date_mode` TINYINT(2) UNSIGNED NOT NULL,
    CHANGE COLUMN `sale_price_end_date_value` `regular_sale_price_end_date_value` DATETIME NOT NULL,
    CHANGE COLUMN `sale_price_end_date_custom_attribute`
                  `regular_sale_price_end_date_custom_attribute` VARCHAR(255) NOT NULL,
    CHANGE COLUMN `price_vat_percent` `regular_price_vat_percent` FLOAT UNSIGNED,
    ADD COLUMN `is_regular_customer_allowed` TINYINT(2) UNSIGNED NOT NULL DEFAULT 1 AFTER `qty_max_posted_value`,
    ADD COLUMN `is_business_customer_allowed`
               TINYINT(2) UNSIGNED NOT NULL DEFAULT 0 AFTER `is_regular_customer_allowed`,
    ADD COLUMN `business_price_mode` TINYINT(2) UNSIGNED NOT NULL AFTER `regular_price_vat_percent`,
    ADD COLUMN `business_price_custom_attribute` VARCHAR(255) NOT NULL AFTER `business_price_mode`,
    ADD COLUMN `business_price_coefficient` VARCHAR(255) NOT NULL AFTER `business_price_custom_attribute`,
    ADD COLUMN `business_price_variation_mode` TINYINT(2) UNSIGNED NOT NULL AFTER `business_price_coefficient`,
    ADD COLUMN `business_price_vat_percent` FLOAT UNSIGNED NOT NULL DEFAULT 0 AFTER `business_price_variation_mode`,
    ADD COLUMN `business_discounts_mode` TINYINT(2) UNSIGNED NOT NULL AFTER `business_price_vat_percent`,
    ADD COLUMN `business_discounts_tier_coefficient` VARCHAR(255) NOT NULL AFTER `business_discounts_mode`,
    ADD COLUMN `business_discounts_tier_customer_group_id`
               INT(11) UNSIGNED DEFAULT NULL AFTER `business_discounts_tier_coefficient`,
    DROP INDEX `price_variation_mode`;
 */

$installer->getTableModifier('amazon_template_selling_format')
    ->renameColumn(
        'price_mode', 'regular_price_mode', false, false
    )
    ->renameColumn(
        'price_custom_attribute', 'regular_price_custom_attribute', false, false
    )
    ->renameColumn(
        'price_coefficient', 'regular_price_coefficient', false, false
    )
    ->renameColumn(
        'map_price_mode', 'regular_map_price_mode', false, false
    )
    ->renameColumn(
        'map_price_custom_attribute', 'regular_map_price_custom_attribute', false, false
    )
    ->renameColumn(
        'sale_price_mode', 'regular_sale_price_mode', false, false
    )
    ->renameColumn(
        'sale_price_custom_attribute', 'regular_sale_price_custom_attribute', false, false
    )
    ->renameColumn(
        'sale_price_coefficient', 'regular_sale_price_coefficient', false, false
    )
    ->renameColumn(
        'price_variation_mode', 'regular_price_variation_mode', false, false
    )
    ->renameColumn(
        'sale_price_start_date_mode', 'regular_sale_price_start_date_mode', false, false
    )
    ->renameColumn(
        'sale_price_start_date_value', 'regular_sale_price_start_date_value', false, false
    )
    ->renameColumn(
        'sale_price_start_date_custom_attribute', 'regular_sale_price_start_date_custom_attribute', false, false
    )
    ->renameColumn(
        'sale_price_end_date_mode', 'regular_sale_price_end_date_mode', false, false
    )
    ->renameColumn(
        'sale_price_end_date_value', 'regular_sale_price_end_date_value', false, false
    )
    ->renameColumn(
        'sale_price_end_date_custom_attribute', 'regular_sale_price_end_date_custom_attribute', false, false
    )
    ->renameColumn(
        'price_vat_percent', 'regular_price_vat_percent', false, false
    )
    ->addColumn(
        'is_regular_customer_allowed', 'TINYINT(2) UNSIGNED NOT NULL', 1, 'qty_max_posted_value', false, false
    )
    ->addColumn(
        'is_business_customer_allowed', 'TINYINT(2) UNSIGNED NOT NULL', 0, 'is_regular_customer_allowed', false, false
    )
    ->addColumn(
        'business_price_mode', 'TINYINT(2) UNSIGNED NOT NULL', NULL, 'regular_price_vat_percent', false, false
    )
    ->addColumn(
        'business_price_custom_attribute', 'VARCHAR(255) NOT NULL', NULL, 'business_price_mode', false, false
    )
    ->addColumn(
        'business_price_coefficient', 'VARCHAR(255) NOT NULL', NULL, 'business_price_custom_attribute', false, false
    )
    ->addColumn(
        'business_price_variation_mode', 'TINYINT(2) UNSIGNED NOT NULL',
        NULL, 'business_price_coefficient', false, false
    )
    ->addColumn(
        'business_price_vat_percent', 'FLOAT UNSIGNED', 'NULL', 'business_price_variation_mode', false, false
    )
    ->addColumn(
        'business_discounts_mode', 'TINYINT(2) UNSIGNED NOT NULL',
        NULL, 'business_price_vat_percent', false, false
    )
    ->addColumn(
        'business_discounts_tier_coefficient', 'VARCHAR(255) NOT NULL',
        NULL, 'business_discounts_mode', false, false
    )
    ->addColumn(
        'business_discounts_tier_customer_group_id', 'INT(11) UNSIGNED',
        'NULL', 'business_discounts_tier_coefficient', false, false
    )
    ->dropIndex('price_variation_mode', false)
    ->commit();

$installer->getTableModifier('amazon_template_selling_format')
    ->changeColumn('regular_price_vat_percent', 'FLOAT UNSIGNED', 'NULL');

//########################################

/*
    ALTER TABLE `m2epro_amazon_order`
    ADD COLUMN `is_business` TINYINT(2) UNSIGNED NOT NULL DEFAULT 0 AFTER `is_prime`,
    ADD INDEX `is_business` (`is_business`);
 */

$installer->getTableModifier('amazon_order')
    ->addColumn('is_business', 'TINYINT(2) UNSIGNED NOT NULL', '0', 'is_prime', true);

//########################################

/*
    ALTER TABLE `m2epro_amazon_template_synchronization`
    ADD COLUMN `revise_change_product_tax_code_template`
               tinyint(2) UNSIGNED NOT NULL AFTER `revise_change_shipping_template`;
 */

$installer->getTableModifier('amazon_template_synchronization')->addColumn(
    'revise_change_product_tax_code_template', 'tinyint(2) UNSIGNED NOT NULL', NULL, 'revise_change_shipping_template'
);

//########################################

$installer->getSynchConfigModifier()->getEntity('/ebay/other_listings/update/', 'interval')->insert(3600);

$installer->getSynchConfigModifier()->delete('/amazon/defaults/update_repricing/');
$installer->getSynchConfigModifier()->delete('/amazon/general/update_repricing/');

//########################################

/*
    ALTER TABLE `m2epro_ebay_dictionary_motor_epid`
    ADD COLUMN `scope` TINYINT(2) UNSIGNED NOT NULL AFTER `is_custom`,
    ADD INDEX `scope` (`scope`);

    ALTER TABLE `m2epro_ebay_listing`
    ADD COLUMN `parts_compatibility_mode` VARCHAR(10) DEFAULT NULL AFTER `product_add_ids`;
*/

// ---------------------------------------

$installer->getTableModifier('ebay_dictionary_motor_epid')
    ->addColumn('scope', 'TINYINT(2) UNSIGNED NOT NULL', '0', 'is_custom', true);

$installer->run(<<<SQL
    UPDATE `{$installer->getTable('m2epro_ebay_dictionary_motor_epid')}`
    SET `scope` = 1;
SQL
);

// ---------------------------------------

$installer->getMainConfigModifier()->getEntity('/ebay/motors/', 'epids_attribute')->updateKey('epids_motor_attribute');
$installer->getMainConfigModifier()->getEntity('/ebay/motors/', 'epids_uk_attribute')->insert(NULL);
$installer->getMainConfigModifier()->getEntity('/ebay/motors/', 'epids_de_attribute')->insert(NULL);

// ---------------------------------------

$installer->getTableModifier('ebay_listing')
    ->addColumn('parts_compatibility_mode', 'VARCHAR(10)', 'NULL', 'product_add_ids');

$installer->run(<<<SQL
    UPDATE `{$installer->getTable('m2epro_ebay_listing')}` mel
    INNER JOIN `{$installer->getTable('m2epro_listing')}` ml ON ml.id = mel.listing_id
    SET `parts_compatibility_mode` = 'ktypes'
    WHERE ml.marketplace_id IN (3, 8);
SQL
);

//########################################

$installer->getMainConfigModifier()->insert(
    '/cron/task/issues_resolver/', 'mode', '1', '0 - disable, \r\n1 - enable'
);
$installer->getMainConfigModifier()->insert(
    '/cron/task/issues_resolver/', 'interval', '3600', 'in seconds'
);
$installer->getMainConfigModifier()->insert(
    '/cron/task/issues_resolver/', 'last_access', NULL, 'date of last access'
);
$installer->getMainConfigModifier()->insert(
    '/cron/task/issues_resolver/', 'last_run', NULL, 'date of last run'
);

//########################################

$installer->getMainConfigModifier()->delete('/view/ebay/terapeak/');

//########################################

/*
    ALTER TABLE `m2epro_ebay_order_item`
    ADD COLUMN `waste_recycling_fee` DECIMAL(12, 4) NOT NULL DEFAULT 0.0000 AFTER `final_fee`;
 */
$installer->getTableModifier('ebay_order_item')
    ->addColumn('waste_recycling_fee', 'DECIMAL(12, 4) NOT NULL', '0.0000', 'final_fee');
	
//########################################

$installer->run(<<<SQL

DELETE mp.*, mpl.* FROM `m2epro_processing` mp
LEFT JOIN `m2epro_processing_lock` mpl ON mpl.processing_id = mp.id
WHERE mp.model LIKE '%Amazon_Synchronization_Orders_Receive_ProcessingRunner%'

SQL
);

// REMOVE SOME RAKUTEN FEATURES
//########################################

/*
    ALTER TABLE `m2epro_buy_account`
        DROP COLUMN `ftp_new_sku_access`,
        DROP COLUMN `ftp_inventory_access`,
        DROP COLUMN `ftp_orders_access`;
*/

$installer->getTableModifier('buy_account')
    ->dropColumn('ftp_new_sku_access')
    ->dropColumn('ftp_inventory_access')
    ->dropColumn('ftp_orders_access');

// ---------------------------------------

/*
    ALTER TABLE `m2epro_buy_listing_product`
        DROP COLUMN `template_new_product_id`;
*/

$installer->getTableModifier('buy_listing_product')
    ->dropColumn('template_new_product_id');

// ---------------------------------------

$installer->run(<<<SQL

    DROP TABLE IF EXISTS `m2epro_buy_dictionary_category`;
    DROP TABLE IF EXISTS `m2epro_buy_template_new_product`;
    DROP TABLE IF EXISTS `m2epro_buy_template_new_product_core`;
    DROP TABLE IF EXISTS `m2epro_buy_template_new_product_attribute`;

SQL
);

// ---------------------------------------

$installer->getMainConfigModifier()
    ->getEntity('/buy/template/new_sku/', 'upc_exemption')->delete();

// ---------------------------------------

$tempTable = $installer->getTablesObject()->getFullName('wizard');
$tempQuery = <<<SQL
    SELECT * FROM {$tempTable}
    WHERE `nick` = 'removedBuyNewSku';
SQL;
$tempRow = $connection->query($tempQuery)->fetch();

if ($tempRow === false) {

    $installer->run(<<<SQL

INSERT INTO `m2epro_wizard` (`nick`, `view`, `status`, `step`, `type`, `priority`)
SELECT 'removedBuyNewSku', 'common', 0, NULL, 0, MAX( `priority` )+1 FROM `m2epro_wizard`;

SQL
    );
}

// ---------------------------------------

$installer->run(<<<SQL

DELETE
  `mp`, `mpl`, `mcprs`, `mrps`
  FROM `m2epro_processing` `mp`
  LEFT JOIN `m2epro_processing_lock` `mpl` ON `mp`.`id` = `mpl`.`processing_id`
  LEFT JOIN `m2epro_connector_pending_requester_single` mcprs ON `mp`.`id` = `mcprs`.`processing_id`
  LEFT JOIN `m2epro_request_pending_single` `mrps` ON `mcprs`.`request_pending_single_id` = `mrps`.`id`
  WHERE `params` LIKE '%action_type":"new_sku"%'

SQL
);

// ---------------------------------------

/*
    UPDATE `m2epro_synchronization_config`
    SET `value` = '0'
    WHERE `group` = '/buy/listings_products/update/' AND `key` = 'mode';

    UPDATE `m2epro_synchronization_config`
    SET `value` = '0'
    WHERE `group` = '/buy/other_listings/update/' AND `key` = 'mode';
*/

$installer->getSynchConfigModifier()->getEntity('/buy/listings_products/update/', 'mode')->updateValue('0');
$installer->getSynchConfigModifier()->getEntity('/buy/other_listings/update/', 'mode')->updateValue('0');

// ---------------------------------------

$installer->run(<<<SQL

    UPDATE `m2epro_buy_listing_other`
    SET `title` = '--'
    WHERE `title` IS NULL;

    UPDATE `m2epro_listing_product` `mlp`
    INNER JOIN `m2epro_buy_listing_product` `mblp` ON `mlp`.`id` = `mblp`.`listing_product_id`
    SET `mlp`.`status` = 0
    WHERE `mlp`.`status` != 0 AND `mblp`.`general_id` is NULL;

SQL
);

//########################################

$installer->endSetup();

//########################################
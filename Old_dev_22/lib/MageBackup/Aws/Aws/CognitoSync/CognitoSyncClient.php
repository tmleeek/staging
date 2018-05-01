<?php
namespace MageBackup\Aws\CognitoSync;

use MageBackup\Aws\AwsClient;

/**
 * This client is used to interact with the **Amazon Cognito Sync** service.
 *
 * @method \MageBackup\Aws\Result bulkPublish(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise bulkPublishAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteDataset(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteDatasetAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeDataset(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeDatasetAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeIdentityPoolUsage(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeIdentityPoolUsageAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeIdentityUsage(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeIdentityUsageAsync(array $args = [])
 * @method \MageBackup\Aws\Result getBulkPublishDetails(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getBulkPublishDetailsAsync(array $args = [])
 * @method \MageBackup\Aws\Result getCognitoEvents(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getCognitoEventsAsync(array $args = [])
 * @method \MageBackup\Aws\Result getIdentityPoolConfiguration(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getIdentityPoolConfigurationAsync(array $args = [])
 * @method \MageBackup\Aws\Result listDatasets(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listDatasetsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listIdentityPoolUsage(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listIdentityPoolUsageAsync(array $args = [])
 * @method \MageBackup\Aws\Result listRecords(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listRecordsAsync(array $args = [])
 * @method \MageBackup\Aws\Result registerDevice(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise registerDeviceAsync(array $args = [])
 * @method \MageBackup\Aws\Result setCognitoEvents(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise setCognitoEventsAsync(array $args = [])
 * @method \MageBackup\Aws\Result setIdentityPoolConfiguration(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise setIdentityPoolConfigurationAsync(array $args = [])
 * @method \MageBackup\Aws\Result subscribeToDataset(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise subscribeToDatasetAsync(array $args = [])
 * @method \MageBackup\Aws\Result unsubscribeFromDataset(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise unsubscribeFromDatasetAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateRecords(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateRecordsAsync(array $args = [])
 */
class CognitoSyncClient extends AwsClient {}

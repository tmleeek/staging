<?php
namespace MageBackup\Aws\Kinesis;

use MageBackup\Aws\AwsClient;

/**
 * This client is used to interact with the **Amazon Kinesis** service.
 *
 * @method \MageBackup\Aws\Result addTagsToStream(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise addTagsToStreamAsync(array $args = [])
 * @method \MageBackup\Aws\Result createStream(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createStreamAsync(array $args = [])
 * @method \MageBackup\Aws\Result decreaseStreamRetentionPeriod(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise decreaseStreamRetentionPeriodAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteStream(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteStreamAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeStream(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeStreamAsync(array $args = [])
 * @method \MageBackup\Aws\Result disableEnhancedMonitoring(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise disableEnhancedMonitoringAsync(array $args = [])
 * @method \MageBackup\Aws\Result enableEnhancedMonitoring(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise enableEnhancedMonitoringAsync(array $args = [])
 * @method \MageBackup\Aws\Result getRecords(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getRecordsAsync(array $args = [])
 * @method \MageBackup\Aws\Result getShardIterator(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getShardIteratorAsync(array $args = [])
 * @method \MageBackup\Aws\Result increaseStreamRetentionPeriod(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise increaseStreamRetentionPeriodAsync(array $args = [])
 * @method \MageBackup\Aws\Result listStreams(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listStreamsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listTagsForStream(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listTagsForStreamAsync(array $args = [])
 * @method \MageBackup\Aws\Result mergeShards(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise mergeShardsAsync(array $args = [])
 * @method \MageBackup\Aws\Result putRecord(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putRecordAsync(array $args = [])
 * @method \MageBackup\Aws\Result putRecords(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putRecordsAsync(array $args = [])
 * @method \MageBackup\Aws\Result removeTagsFromStream(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise removeTagsFromStreamAsync(array $args = [])
 * @method \MageBackup\Aws\Result splitShard(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise splitShardAsync(array $args = [])
 */
class KinesisClient extends AwsClient {}

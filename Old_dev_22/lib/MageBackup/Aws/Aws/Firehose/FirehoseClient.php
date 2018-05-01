<?php
namespace MageBackup\Aws\Firehose;

use MageBackup\Aws\AwsClient;

/**
 * This client is used to interact with the **Amazon Kinesis Firehose** service.
 *
 * @method \MageBackup\Aws\Result createDeliveryStream(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createDeliveryStreamAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteDeliveryStream(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteDeliveryStreamAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeDeliveryStream(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeDeliveryStreamAsync(array $args = [])
 * @method \MageBackup\Aws\Result listDeliveryStreams(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listDeliveryStreamsAsync(array $args = [])
 * @method \MageBackup\Aws\Result putRecord(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putRecordAsync(array $args = [])
 * @method \MageBackup\Aws\Result putRecordBatch(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putRecordBatchAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateDestination(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateDestinationAsync(array $args = [])
 */
class FirehoseClient extends AwsClient {}

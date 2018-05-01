<?php
namespace MageBackup\Aws\DynamoDbStreams;

use MageBackup\Aws\AwsClient;
use MageBackup\Aws\DynamoDb\DynamoDbClient;

/**
 * This client is used to interact with the **Amazon DynamoDb Streams** service.
 *
 * @method \MageBackup\Aws\Result describeStream(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeStreamAsync(array $args = [])
 * @method \MageBackup\Aws\Result getRecords(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getRecordsAsync(array $args = [])
 * @method \MageBackup\Aws\Result getShardIterator(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getShardIteratorAsync(array $args = [])
 * @method \MageBackup\Aws\Result listStreams(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listStreamsAsync(array $args = [])
 */
class DynamoDbStreamsClient extends AwsClient
{
    public static function getArguments()
    {
        $args = parent::getArguments();
        $args['retries']['default'] = 11;
        $args['retries']['fn'] = [DynamoDbClient::class, '_applyRetryConfig'];

        return $args;
    }
}
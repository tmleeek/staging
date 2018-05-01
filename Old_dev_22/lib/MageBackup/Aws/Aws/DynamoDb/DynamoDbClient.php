<?php
namespace MageBackup\Aws\DynamoDb;

use MageBackup\Aws\Api\Parser\Crc32ValidatingParser;
use MageBackup\Aws\AwsClient;
use MageBackup\Aws\ClientResolver;
use MageBackup\Aws\HandlerList;
use MageBackup\Aws\Middleware;
use MageBackup\Aws\RetryMiddleware;

/**
 * This client is used to interact with the **Amazon DynamoDB** service.
 *
 * @method \MageBackup\Aws\Result batchGetItem(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise batchGetItemAsync(array $args = [])
 * @method \MageBackup\Aws\Result batchWriteItem(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise batchWriteItemAsync(array $args = [])
 * @method \MageBackup\Aws\Result createTable(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createTableAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteItem(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteItemAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteTable(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteTableAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeLimits(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeLimitsAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeTable(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeTableAsync(array $args = [])
 * @method \MageBackup\Aws\Result getItem(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getItemAsync(array $args = [])
 * @method \MageBackup\Aws\Result listTables(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listTablesAsync(array $args = [])
 * @method \MageBackup\Aws\Result putItem(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putItemAsync(array $args = [])
 * @method \MageBackup\Aws\Result query(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise queryAsync(array $args = [])
 * @method \MageBackup\Aws\Result scan(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise scanAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateItem(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateItemAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateTable(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateTableAsync(array $args = [])
 */
class DynamoDbClient extends AwsClient
{
    public static function getArguments()
    {
        $args = parent::getArguments();
        $args['retries']['default'] = 10;
        $args['retries']['fn'] = [__CLASS__, '_applyRetryConfig'];
        $args['api_provider']['fn'] = [__CLASS__, '_applyApiProvider'];

        return $args;
    }

    /**
     * Convenience method for instantiating and registering the DynamoDB
     * Session handler with this DynamoDB client object.
     *
     * @param array $config Array of options for the session handler factory
     *
     * @return SessionHandler
     */
    public function registerSessionHandler(array $config = [])
    {
        $handler = SessionHandler::fromClient($this, $config);
        $handler->register();

        return $handler;
    }

    /** @internal */
    public static function _applyRetryConfig($value, array &$args, HandlerList $list)
    {
        if (!$value) {
            return;
        }

        $list->appendSign(
            Middleware::retry(
                RetryMiddleware::createDefaultDecider($value),
                function ($retries) {
                    return $retries
                        ? RetryMiddleware::exponentialDelay($retries) / 2
                        : 0;
                },
                isset($args['stats']['retries'])
                    ? (bool) $args['stats']['retries']
                    : false
            ),
            'retry'
        );
    }

    /** @internal */
    public static function _applyApiProvider($value, array &$args, HandlerList $list)
    {
        ClientResolver::_apply_api_provider($value, $args, $list);
        $args['parser'] = new Crc32ValidatingParser($args['parser']);
    }
}

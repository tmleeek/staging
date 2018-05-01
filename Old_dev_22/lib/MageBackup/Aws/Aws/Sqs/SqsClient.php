<?php
namespace MageBackup\Aws\Sqs;

use MageBackup\Aws\AwsClient;
use MageBackup\Aws\CommandInterface;
use MageBackup\Aws\Sqs\Exception\SqsException;
use MageBackup\GuzzleHttp\Psr7\Uri;
use MageBackup\Psr\Http\Message\RequestInterface;

/**
 * Client used to interact Amazon Simple Queue Service (Amazon SQS)
 *
 * @method \MageBackup\Aws\Result addPermission(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise addPermissionAsync(array $args = [])
 * @method \MageBackup\Aws\Result changeMessageVisibility(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise changeMessageVisibilityAsync(array $args = [])
 * @method \MageBackup\Aws\Result changeMessageVisibilityBatch(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise changeMessageVisibilityBatchAsync(array $args = [])
 * @method \MageBackup\Aws\Result createQueue(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createQueueAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteMessage(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteMessageAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteMessageBatch(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteMessageBatchAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteQueue(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteQueueAsync(array $args = [])
 * @method \MageBackup\Aws\Result getQueueAttributes(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getQueueAttributesAsync(array $args = [])
 * @method \MageBackup\Aws\Result getQueueUrl(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getQueueUrlAsync(array $args = [])
 * @method \MageBackup\Aws\Result listDeadLetterSourceQueues(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listDeadLetterSourceQueuesAsync(array $args = [])
 * @method \MageBackup\Aws\Result listQueues(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listQueuesAsync(array $args = [])
 * @method \MageBackup\Aws\Result purgeQueue(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise purgeQueueAsync(array $args = [])
 * @method \MageBackup\Aws\Result receiveMessage(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise receiveMessageAsync(array $args = [])
 * @method \MageBackup\Aws\Result removePermission(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise removePermissionAsync(array $args = [])
 * @method \MageBackup\Aws\Result sendMessage(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise sendMessageAsync(array $args = [])
 * @method \MageBackup\Aws\Result sendMessageBatch(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise sendMessageBatchAsync(array $args = [])
 * @method \MageBackup\Aws\Result setQueueAttributes(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise setQueueAttributesAsync(array $args = [])
 */
class SqsClient extends AwsClient
{
    public function __construct(array $config)
    {
        parent::__construct($config);
        $list = $this->getHandlerList();
        $list->appendBuild($this->queueUrl(), 'sqs.queue_url');
        $list->appendSign($this->validateMd5(), 'sqs.md5');
    }

    /**
     * Converts a queue URL into a queue ARN.
     *
     * @param string $queueUrl The queue URL to perform the action on.
     *                         Retrieved when the queue is first created.
     *
     * @return string An ARN representation of the queue URL.
     */
    public function getQueueArn($queueUrl)
    {
        return strtr($queueUrl, array(
            'http://'        => 'arn:aws:',
            'https://'       => 'arn:aws:',
            '.amazonaws.com' => '',
            '/'              => ':',
            '.'              => ':',
        ));
    }

    /**
     * Moves the URI of the queue to the URI in the input parameter.
     *
     * @return callable
     */
    private function queueUrl()
    {
        return static function (callable $handler) {
            return function (
                CommandInterface $c,
                RequestInterface $r = null
            ) use ($handler) {
                if ($c->hasParam('QueueUrl')) {
                    $uri = Uri::resolve($r->getUri(), $c['QueueUrl']);
                    $r = $r->withUri($uri);
                }
                return $handler($c, $r);
            };
        };
    }

    /**
     * Validates ReceiveMessage body MD5s
     *
     * @return callable
     */
    private function validateMd5()
    {
        return static function (callable $handler) {
            return function (
                CommandInterface $c,
                RequestInterface $r = null
            ) use ($handler) {
                if ($c->getName() !== 'ReceiveMessage') {
                    return $handler($c, $r);
                }

                return $handler($c, $r)
                    ->then(
                        function ($result) use ($c, $r) {
                            foreach ((array) $result['Messages'] as $msg) {
                                if (isset($msg['MD5OfBody'])
                                    && md5($msg['Body']) !== $msg['MD5OfBody']
                                ) {
                                    throw new SqsException(
                                        sprintf(
                                            'MD5 mismatch. Expected %s, found %s',
                                            $msg['MD5OfBody'],
                                            md5($msg['Body'])
                                        ),
                                        $c,
                                        [
                                            'code' => 'ClientChecksumMismatch',
                                            'request' => $r
                                        ]
                                    );
                                }
                            }
                            return $result;
                        }
                    );
            };
        };
    }
}

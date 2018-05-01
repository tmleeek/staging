<?php
namespace MageBackup\Aws\S3;

use MageBackup\Aws\Api\ApiProvider;
use MageBackup\Aws\Api\DocModel;
use MageBackup\Aws\Api\Service;
use MageBackup\Aws\AwsClient;
use MageBackup\Aws\ClientResolver;
use MageBackup\Aws\Command;
use MageBackup\Aws\Exception\AwsException;
use MageBackup\Aws\HandlerList;
use MageBackup\Aws\Middleware;
use MageBackup\Aws\RetryMiddleware;
use MageBackup\Aws\ResultInterface;
use MageBackup\Aws\CommandInterface;
use MageBackup\GuzzleHttp\Exception\RequestException;
use MageBackup\GuzzleHttp\Promise;
use MageBackup\GuzzleHttp\Psr7;
use MageBackup\Psr\Http\Message\RequestInterface;

/**
 * Client used to interact with **Amazon Simple Storage Service (Amazon S3)**.
 *
 * @method \MageBackup\Aws\Result abortMultipartUpload(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise abortMultipartUploadAsync(array $args = [])
 * @method \MageBackup\Aws\Result completeMultipartUpload(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise completeMultipartUploadAsync(array $args = [])
 * @method \MageBackup\Aws\Result copyObject(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise copyObjectAsync(array $args = [])
 * @method \MageBackup\Aws\Result createBucket(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createBucketAsync(array $args = [])
 * @method \MageBackup\Aws\Result createMultipartUpload(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createMultipartUploadAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteBucket(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteBucketAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteBucketCors(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteBucketCorsAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteBucketLifecycle(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteBucketLifecycleAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteBucketPolicy(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteBucketPolicyAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteBucketReplication(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteBucketReplicationAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteBucketTagging(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteBucketTaggingAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteBucketWebsite(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteBucketWebsiteAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteObject(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteObjectAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteObjects(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteObjectsAsync(array $args = [])
 * @method \MageBackup\Aws\Result getBucketAccelerateConfiguration(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getBucketAccelerateConfigurationAsync(array $args = [])
 * @method \MageBackup\Aws\Result getBucketAcl(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getBucketAclAsync(array $args = [])
 * @method \MageBackup\Aws\Result getBucketCors(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getBucketCorsAsync(array $args = [])
 * @method \MageBackup\Aws\Result getBucketLifecycle(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getBucketLifecycleAsync(array $args = [])
 * @method \MageBackup\Aws\Result getBucketLifecycleConfiguration(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getBucketLifecycleConfigurationAsync(array $args = [])
 * @method \MageBackup\Aws\Result getBucketLocation(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getBucketLocationAsync(array $args = [])
 * @method \MageBackup\Aws\Result getBucketLogging(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getBucketLoggingAsync(array $args = [])
 * @method \MageBackup\Aws\Result getBucketNotification(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getBucketNotificationAsync(array $args = [])
 * @method \MageBackup\Aws\Result getBucketNotificationConfiguration(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getBucketNotificationConfigurationAsync(array $args = [])
 * @method \MageBackup\Aws\Result getBucketPolicy(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getBucketPolicyAsync(array $args = [])
 * @method \MageBackup\Aws\Result getBucketReplication(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getBucketReplicationAsync(array $args = [])
 * @method \MageBackup\Aws\Result getBucketRequestPayment(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getBucketRequestPaymentAsync(array $args = [])
 * @method \MageBackup\Aws\Result getBucketTagging(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getBucketTaggingAsync(array $args = [])
 * @method \MageBackup\Aws\Result getBucketVersioning(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getBucketVersioningAsync(array $args = [])
 * @method \MageBackup\Aws\Result getBucketWebsite(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getBucketWebsiteAsync(array $args = [])
 * @method \MageBackup\Aws\Result getObject(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getObjectAsync(array $args = [])
 * @method \MageBackup\Aws\Result getObjectAcl(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getObjectAclAsync(array $args = [])
 * @method \MageBackup\Aws\Result getObjectTorrent(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getObjectTorrentAsync(array $args = [])
 * @method \MageBackup\Aws\Result headBucket(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise headBucketAsync(array $args = [])
 * @method \MageBackup\Aws\Result headObject(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise headObjectAsync(array $args = [])
 * @method \MageBackup\Aws\Result listBuckets(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listBucketsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listMultipartUploads(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listMultipartUploadsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listObjectVersions(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listObjectVersionsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listObjects(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listObjectsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listObjectsV2(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listObjectsV2Async(array $args = [])
 * @method \MageBackup\Aws\Result listParts(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listPartsAsync(array $args = [])
 * @method \MageBackup\Aws\Result putBucketAccelerateConfiguration(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putBucketAccelerateConfigurationAsync(array $args = [])
 * @method \MageBackup\Aws\Result putBucketAcl(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putBucketAclAsync(array $args = [])
 * @method \MageBackup\Aws\Result putBucketCors(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putBucketCorsAsync(array $args = [])
 * @method \MageBackup\Aws\Result putBucketLifecycle(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putBucketLifecycleAsync(array $args = [])
 * @method \MageBackup\Aws\Result putBucketLifecycleConfiguration(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putBucketLifecycleConfigurationAsync(array $args = [])
 * @method \MageBackup\Aws\Result putBucketLogging(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putBucketLoggingAsync(array $args = [])
 * @method \MageBackup\Aws\Result putBucketNotification(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putBucketNotificationAsync(array $args = [])
 * @method \MageBackup\Aws\Result putBucketNotificationConfiguration(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putBucketNotificationConfigurationAsync(array $args = [])
 * @method \MageBackup\Aws\Result putBucketPolicy(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putBucketPolicyAsync(array $args = [])
 * @method \MageBackup\Aws\Result putBucketReplication(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putBucketReplicationAsync(array $args = [])
 * @method \MageBackup\Aws\Result putBucketRequestPayment(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putBucketRequestPaymentAsync(array $args = [])
 * @method \MageBackup\Aws\Result putBucketTagging(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putBucketTaggingAsync(array $args = [])
 * @method \MageBackup\Aws\Result putBucketVersioning(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putBucketVersioningAsync(array $args = [])
 * @method \MageBackup\Aws\Result putBucketWebsite(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putBucketWebsiteAsync(array $args = [])
 * @method \MageBackup\Aws\Result putObject(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putObjectAsync(array $args = [])
 * @method \MageBackup\Aws\Result putObjectAcl(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putObjectAclAsync(array $args = [])
 * @method \MageBackup\Aws\Result restoreObject(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise restoreObjectAsync(array $args = [])
 * @method \MageBackup\Aws\Result uploadPart(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise uploadPartAsync(array $args = [])
 * @method \MageBackup\Aws\Result uploadPartCopy(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise uploadPartCopyAsync(array $args = [])
 */
class S3Client extends AwsClient implements S3ClientInterface
{
    use S3ClientTrait;

    public static function getArguments()
    {
        $args = parent::getArguments();
        $args['retries']['fn'] = [__CLASS__, '_applyRetryConfig'];
        $args['api_provider']['fn'] = [__CLASS__, '_applyApiProvider'];

        return $args + [
            'bucket_endpoint' => [
                'type'    => 'config',
                'valid'   => ['bool'],
                'doc'     => 'Set to true to send requests to a hardcoded '
                    . 'bucket endpoint rather than create an endpoint as a '
                    . 'result of injecting the bucket into the URL. This '
                    . 'option is useful for interacting with CNAME endpoints.',
            ],
            'use_accelerate_endpoint' => [
                'type' => 'config',
                'valid' => ['bool'],
                'doc' => 'Set to true to send requests to an S3 Accelerate'
                    . ' endpoint by default. Can be enabled or disabled on'
                    . ' individual operations by setting'
                    . ' \'@use_accelerate_endpoint\' to true or false. Note:'
                    . ' you must enable S3 Accelerate on a bucket before it can'
                    . ' be accessed via an Accelerate endpoint.',
                'default' => false,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * In addition to the options available to
     * {@see MageBackup\Aws\AwsClient::__construct}, S3Client accepts the following
     * options:
     *
     * - bucket_endpoint: (bool) Set to true to send requests to a
     *   hardcoded bucket endpoint rather than create an endpoint as a result
     *   of injecting the bucket into the URL. This option is useful for
     *   interacting with CNAME endpoints.
     * - calculate_md5: (bool) Set to false to disable calculating an MD5
     *   for all Amazon S3 signed uploads.
     * - use_accelerate_endpoint: (bool) Set to true to send requests to an S3
     *   Accelerate endpoint by default. Can be enabled or disabled on
     *   individual operations by setting '@use_accelerate_endpoint' to true or
     *   false. Note: you must enable S3 Accelerate on a bucket before it can be
     *   accessed via an Accelerate endpoint.
     *
     * @param array $args
     */
    public function __construct(array $args)
    {
        parent::__construct($args);
        $stack = $this->getHandlerList();
        $stack->appendInit(SSECMiddleware::wrap($this->getEndpoint()->getScheme()), 's3.ssec');
        $stack->appendBuild(ApplyChecksumMiddleware::wrap(), 's3.checksum');
        $stack->appendBuild(
            Middleware::contentType(['PutObject', 'UploadPart']),
            's3.content_type'
        );
        $stack->appendBuild(
            AccelerateMiddleware::wrap($this->getConfig('use_accelerate_endpoint')),
            's3.use_accelerate_endpoint'
        );

        // Use the bucket style middleware when using a "bucket_endpoint" (for cnames)
        if ($this->getConfig('bucket_endpoint')) {
            $stack->appendBuild(BucketEndpointMiddleware::wrap(), 's3.bucket_endpoint');
        }

        $stack->appendSign(PutObjectUrlMiddleware::wrap(), 's3.put_object_url');
        $stack->appendSign(PermanentRedirectMiddleware::wrap(), 's3.permanent_redirect');
        $stack->appendInit(Middleware::sourceFile($this->getApi()), 's3.source_file');
        $stack->appendInit($this->getSaveAsParameter(), 's3.save_as');
        $stack->appendInit($this->getLocationConstraintMiddleware(), 's3.location');
        $stack->appendInit($this->getEncodingTypeMiddleware(), 's3.auto_encode');
        $stack->appendInit($this->getHeadObjectMiddleware(), 's3.head_object');
    }

    /**
     * Determine if a string is a valid name for a DNS compatible Amazon S3
     * bucket.
     *
     * DNS compatible bucket names can be used as a subdomain in a URL (e.g.,
     * "<bucket>.s3.amazonaws.com").
     *
     * @param string $bucket Bucket name to check.
     *
     * @return bool
     */
    public static function isBucketDnsCompatible($bucket)
    {
        $bucketLen = strlen($bucket);

        return ($bucketLen >= 3 && $bucketLen <= 63) &&
            // Cannot look like an IP address
            !filter_var($bucket, FILTER_VALIDATE_IP) &&
            preg_match('/^[a-z0-9]([a-z0-9\-\.]*[a-z0-9])?$/', $bucket);
    }

    public function createPresignedRequest(CommandInterface $command, $expires)
    {
        $command = clone $command;
        $command->getHandlerList()->remove('signer');

        /** @var \MageBackup\Aws\Signature\SignatureInterface $signer */
        $signer = call_user_func(
            $this->getSignatureProvider(),
            $this->getConfig('signature_version'),
            $this->getConfig('signing_name'),
            $this->getConfig('signing_region')
        );

        return $signer->presign(
            \MageBackup\Aws\serialize($command),
            $this->getCredentials()->wait(),
            $expires
        );
    }

    public function getObjectUrl($bucket, $key)
    {
        $command = $this->getCommand('GetObject', [
            'Bucket' => $bucket,
            'Key'    => $key
        ]);

        return (string) \MageBackup\Aws\serialize($command)->getUri();
    }

    /**
     * Raw URL encode a key and allow for '/' characters
     *
     * @param string $key Key to encode
     *
     * @return string Returns the encoded key
     */
    public static function encodeKey($key)
    {
        return str_replace('%2F', '/', rawurlencode($key));
    }

    /**
     * Provides a middleware that removes the need to specify LocationConstraint on CreateBucket.
     *
     * @return \Closure
     */
    private function getLocationConstraintMiddleware()
    {
        $region = $this->getRegion();
        return static function (callable $handler) use ($region) {
            return function (Command $command, $request = null) use ($handler, $region) {
                if ($command->getName() === 'CreateBucket') {
                    $locationConstraint = isset($command['CreateBucketConfiguration']['LocationConstraint'])
                        ? $command['CreateBucketConfiguration']['LocationConstraint']
                        : null;

                    if ($locationConstraint === 'us-east-1') {
                        unset($command['CreateBucketConfiguration']);
                    } elseif ('us-east-1' !== $region && empty($locationConstraint)) {
                        $command['CreateBucketConfiguration'] = ['LocationConstraint' => $region];
                    }
                }

                return $handler($command, $request);
            };
        };
    }

    /**
     * Provides a middleware that supports the `SaveAs` parameter.
     *
     * @return \Closure
     */
    private function getSaveAsParameter()
    {
        return static function (callable $handler) {
            return function (Command $command, $request = null) use ($handler) {
                if ($command->getName() === 'GetObject' && isset($command['SaveAs'])) {
                    $command['@http']['sink'] = $command['SaveAs'];
                    unset($command['SaveAs']);
                }

                return $handler($command, $request);
            };
        };
    }

    /**
     * Provides a middleware that disables content decoding on HeadObject
     * commands.
     *
     * @return \Closure
     */
    private function getHeadObjectMiddleware()
    {
        return static function (callable $handler) {
            return function (
                CommandInterface $command,
                RequestInterface $request = null
            ) use ($handler) {
                if ($command->getName() === 'HeadObject'
                    && !isset($command['@http']['decode_content'])
                ) {
                    $command['@http']['decode_content'] = false;
                }

                return $handler($command, $request);
            };
        };
    }

    /**
     * Provides a middleware that autopopulates the EncodingType parameter on
     * ListObjects commands.
     *
     * @return \Closure
     */
    private function getEncodingTypeMiddleware()
    {
        return static function (callable $handler) {
            return function (Command $command, $request = null) use ($handler) {
                $autoSet = false;
                if ($command->getName() === 'ListObjects'
                    && empty($command['EncodingType'])
                ) {
                    $command['EncodingType'] = 'url';
                    $autoSet = true;
                }

                return $handler($command, $request)
                    ->then(function (ResultInterface $result) use ($autoSet) {
                        if ($result['EncodingType'] === 'url' && $autoSet) {
                            static $topLevel = [
                                'Delimiter',
                                'Marker',
                                'NextMarker',
                                'Prefix',
                            ];
                            static $nested = [
                                ['Contents', 'Key'],
                                ['CommonPrefixes', 'Prefix'],
                            ];

                            foreach ($topLevel as $key) {
                                if (isset($result[$key])) {
                                    $result[$key] = urldecode($result[$key]);
                                }
                            }
                            foreach ($nested as $steps) {
                                if (isset($result[$steps[0]])) {
                                    foreach ($result[$steps[0]] as &$part) {
                                        if (isset($part[$steps[1]])) {
                                            $part[$steps[1]]
                                                = urldecode($part[$steps[1]]);
                                        }
                                    }
                                }
                            }

                        }

                        return $result;
                    });
            };
        };
    }

    /** @internal */
    public static function _applyRetryConfig($value, $_, HandlerList $list)
    {
        if (!$value) {
            return;
        }

        $decider = RetryMiddleware::createDefaultDecider($value);
        $decider = function ($retries, $command, $request, $result, $error) use ($decider, $value) {
            $maxRetries = null !== $command['@retries']
                ? $command['@retries']
                : $value;

            if ($decider($retries, $command, $request, $result, $error)) {
                return true;
            } elseif ($error instanceof AwsException
                && $retries < $maxRetries
            ) {
                if (
                    $error->getResponse()
                    && $error->getResponse()->getStatusCode() >= 400
                ) {
                    return strpos(
                        $error->getResponse()->getBody(),
                        'Your socket connection to the server'
                    ) !== false;
                } elseif ($error->getPrevious() instanceof RequestException) {
                    // All commands except CompleteMultipartUpload are
                    // idempotent and may be retried without worry if a
                    // networking error has occurred.
                    return $command->getName() !== 'CompleteMultipartUpload';
                }
            }
            return false;
        };

        $delay = [RetryMiddleware::class, 'exponentialDelay'];
        $list->appendSign(Middleware::retry($decider, $delay), 'retry');
    }

    /** @internal */
    public static function _applyApiProvider($value, array &$args, HandlerList $list)
    {
        ClientResolver::_apply_api_provider($value, $args, $list);
        $args['parser'] = new GetBucketLocationParser(
            new AmbiguousSuccessParser(
                new RetryableMalformedResponseParser(
                    $args['parser'],
                    $args['exception_class']
                ),
                $args['error_parser'],
                $args['exception_class']
            )
        );
    }

    /**
     * @internal
     * @codeCoverageIgnore
     */
    public static function applyDocFilters(array $api, array $docs)
    {
        $b64 = '<div class="alert alert-info">This value will be base64 encoded on your behalf.</div>';
        $opt = '<div class="alert alert-info">This value will be computed for you it is not supplied.</div>';

        // Add the SourceFile parameter.
        $docs['shapes']['SourceFile']['base'] = 'The path to a file on disk to use instead of the Body parameter.';
        $api['shapes']['SourceFile'] = ['type' => 'string'];
        $api['shapes']['PutObjectRequest']['members']['SourceFile'] = ['shape' => 'SourceFile'];
        $api['shapes']['UploadPartRequest']['members']['SourceFile'] = ['shape' => 'SourceFile'];

        // Add the ContentSHA256 parameter.
        $docs['shapes']['ContentSHA256']['base'] = 'A SHA256 hash of the body content of the request.';
        $api['shapes']['ContentSHA256'] = ['type' => 'string'];
        $api['shapes']['PutObjectRequest']['members']['ContentSHA256'] = ['shape' => 'ContentSHA256'];
        $api['shapes']['UploadPartRequest']['members']['ContentSHA256'] = ['shape' => 'ContentSHA256'];
        unset($api['shapes']['PutObjectRequest']['members']['ContentMD5']);
        unset($api['shapes']['UploadPartRequest']['members']['ContentMD5']);
        $docs['shapes']['ContentSHA256']['append'] = $opt;

        // Add the SaveAs parameter.
        $docs['shapes']['SaveAs']['base'] = 'The path to a file on disk to save the object data.';
        $api['shapes']['SaveAs'] = ['type' => 'string'];
        $api['shapes']['GetObjectRequest']['members']['SaveAs'] = ['shape' => 'SaveAs'];

        // Several SSECustomerKey documentation updates.
        $docs['shapes']['SSECustomerKey']['append'] = $b64;
        $docs['shapes']['CopySourceSSECustomerKey']['append'] = $b64;
        $docs['shapes']['SSECustomerKeyMd5']['append'] = $opt;

        // Add the ObjectURL to various output shapes and documentation.
        $docs['shapes']['ObjectURL']['base'] = 'The URI of the created object.';
        $api['shapes']['ObjectURL'] = ['type' => 'string'];
        $api['shapes']['PutObjectOutput']['members']['ObjectURL'] = ['shape' => 'ObjectURL'];
        $api['shapes']['CopyObjectOutput']['members']['ObjectURL'] = ['shape' => 'ObjectURL'];
        $api['shapes']['CompleteMultipartUploadOutput']['members']['ObjectURL'] = ['shape' => 'ObjectURL'];

        // Fix references to Location Constraint.
        unset($api['shapes']['CreateBucketRequest']['payload']);
        $api['shapes']['BucketLocationConstraint']['enum'] = [
            "ap-northeast-1",
            "ap-southeast-2",
            "ap-southeast-1",
            "cn-north-1",
            "eu-central-1",
            "eu-west-1",
            "us-east-1",
            "us-west-1",
            "us-west-2",
            "sa-east-1",
        ];

        // Add a note that the ContentMD5 is optional.
        $docs['shapes']['ContentMD5']['append'] = '<div class="alert alert-info">The value will be computed on '
            . 'your behalf.</div>';

        return [
            new Service($api, ApiProvider::defaultProvider()),
            new DocModel($docs)
        ];
    }
}

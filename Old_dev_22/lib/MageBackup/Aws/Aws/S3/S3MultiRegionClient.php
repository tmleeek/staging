<?php
namespace MageBackup\Aws\S3;

use MageBackup\Aws\CacheInterface;
use MageBackup\Aws\CommandInterface;
use MageBackup\Aws\LruArrayCache;
use MageBackup\Aws\MultiRegionClient as BaseClient;
use MageBackup\Aws\S3\Exception\PermanentRedirectException;
use MageBackup\GuzzleHttp\Promise;

/**
 * **Amazon Simple Storage Service** multi-region client.
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
class S3MultiRegionClient extends BaseClient implements S3ClientInterface
{
    use S3ClientTrait {
        determineBucketRegionAsync as private lookupBucketRegion;
    }

    /** @var CacheInterface */
    private $cache;

    public static function getArguments()
    {
        $args = parent::getArguments();
        $args['region']['default'] = 'us-east-1';

        return $args + [
            'bucket_region_cache' => [
                'type' => 'config',
                'valid' => [CacheInterface::class],
                'doc' => 'Cache of regions in which given buckets are located.',
                'default' => function () { return new LruArrayCache; },
            ],
        ];
    }

    public function __construct(array $args)
    {
        parent::__construct($args);
        $this->cache = $this->getConfig('bucket_region_cache');
    }

    public function executeAsync(CommandInterface $c)
    {
        return Promise\coroutine(function () use ($c) {
            if ($region = $this->cache->get($this->getCacheKey($c['Bucket']))) {
                $c = $this->getRegionalizedCommand($c, $region);
            }

            try {
                yield parent::executeAsync($c);
            } catch (PermanentRedirectException $e) {
                if (empty($c['Bucket'])) {
                    throw $e;
                }
                $region = (yield $this->lookupBucketRegion($c['Bucket']));
                $this->cache->set($this->getCacheKey($c['Bucket']), $region);
                $c = $this->getRegionalizedCommand($c, $region);
                yield parent::executeAsync($c);
            }
        });
    }

    public function createPresignedRequest(CommandInterface $command, $expires)
    {
        if (empty($command['Bucket'])) {
            throw new \InvalidArgumentException('The S3\\MultiRegionClient'
                . ' cannot create presigned requests for commands without a'
                . ' specified bucket.');
        }

        /** @var S3ClientInterface $client */
        $client = $this->getClientFromPool(
            $this->determineBucketRegion($command['Bucket'])
        );
        return $client->createPresignedRequest(
            $client->getCommand($command->getName(), $command->toArray()),
            $expires
        );
    }

    public function getObjectUrl($bucket, $key)
    {
        /** @var S3Client $regionalClient */
        $regionalClient = $this->getClientFromPool(
            $this->determineBucketRegion($bucket)
        );

        return $regionalClient->getObjectUrl($bucket, $key);
    }

    public function determineBucketRegionAsync($bucketName)
    {
        if ($cached = $this->cache->get($this->getCacheKey($bucketName))) {
            return Promise\promise_for($cached);
        }

        return $this->lookupBucketRegion($bucketName)
            ->then(function ($region) use ($bucketName) {
                $this->cache->set($this->getCacheKey($bucketName), $region);

                return $region;
            });
    }

    private function getRegionalizedCommand(CommandInterface $command, $region)
    {
        return $this->getClientFromPool($region)
            ->getCommand($command->getName(), $command->toArray());
    }

    private function getCacheKey($bucketName)
    {
        return "aws:s3:{$bucketName}:location";
    }
}

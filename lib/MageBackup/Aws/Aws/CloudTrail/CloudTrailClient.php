<?php
namespace MageBackup\Aws\CloudTrail;

use MageBackup\Aws\AwsClient;

/**
 * This client is used to interact with the **AWS CloudTrail** service.
 *
 * @method \MageBackup\Aws\Result addTags(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise addTagsAsync(array $args = [])
 * @method \MageBackup\Aws\Result createTrail(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createTrailAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteTrail(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteTrailAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeTrails(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeTrailsAsync(array $args = [])
 * @method \MageBackup\Aws\Result getTrailStatus(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getTrailStatusAsync(array $args = [])
 * @method \MageBackup\Aws\Result listPublicKeys(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listPublicKeysAsync(array $args = [])
 * @method \MageBackup\Aws\Result listTags(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listTagsAsync(array $args = [])
 * @method \MageBackup\Aws\Result lookupEvents(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise lookupEventsAsync(array $args = [])
 * @method \MageBackup\Aws\Result removeTags(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise removeTagsAsync(array $args = [])
 * @method \MageBackup\Aws\Result startLogging(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise startLoggingAsync(array $args = [])
 * @method \MageBackup\Aws\Result stopLogging(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise stopLoggingAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateTrail(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateTrailAsync(array $args = [])
 */
class CloudTrailClient extends AwsClient {}

<?php
namespace MageBackup\Aws\Ecr;

use MageBackup\Aws\AwsClient;

/**
 * This client is used to interact with the **Amazon EC2 Container Registry** service.
 *
 * @method \MageBackup\Aws\Result batchCheckLayerAvailability(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise batchCheckLayerAvailabilityAsync(array $args = [])
 * @method \MageBackup\Aws\Result batchDeleteImage(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise batchDeleteImageAsync(array $args = [])
 * @method \MageBackup\Aws\Result batchGetImage(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise batchGetImageAsync(array $args = [])
 * @method \MageBackup\Aws\Result completeLayerUpload(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise completeLayerUploadAsync(array $args = [])
 * @method \MageBackup\Aws\Result createRepository(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createRepositoryAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteRepository(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteRepositoryAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteRepositoryPolicy(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteRepositoryPolicyAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeRepositories(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeRepositoriesAsync(array $args = [])
 * @method \MageBackup\Aws\Result getAuthorizationToken(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getAuthorizationTokenAsync(array $args = [])
 * @method \MageBackup\Aws\Result getDownloadUrlForLayer(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getDownloadUrlForLayerAsync(array $args = [])
 * @method \MageBackup\Aws\Result getRepositoryPolicy(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getRepositoryPolicyAsync(array $args = [])
 * @method \MageBackup\Aws\Result initiateLayerUpload(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise initiateLayerUploadAsync(array $args = [])
 * @method \MageBackup\Aws\Result listImages(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listImagesAsync(array $args = [])
 * @method \MageBackup\Aws\Result putImage(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putImageAsync(array $args = [])
 * @method \MageBackup\Aws\Result setRepositoryPolicy(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise setRepositoryPolicyAsync(array $args = [])
 * @method \MageBackup\Aws\Result uploadLayerPart(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise uploadLayerPartAsync(array $args = [])
 */
class EcrClient extends AwsClient {}

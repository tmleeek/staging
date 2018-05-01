<?php
namespace MageBackup\Aws\IotDataPlane;

use MageBackup\Aws\AwsClient;

/**
 * This client is used to interact with the **AWS IoT Data Plane** service.
 *
 * @method \MageBackup\Aws\Result deleteThingShadow(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteThingShadowAsync(array $args = [])
 * @method \MageBackup\Aws\Result getThingShadow(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getThingShadowAsync(array $args = [])
 * @method \MageBackup\Aws\Result publish(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise publishAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateThingShadow(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateThingShadowAsync(array $args = [])
 */
class IotDataPlaneClient extends AwsClient {}

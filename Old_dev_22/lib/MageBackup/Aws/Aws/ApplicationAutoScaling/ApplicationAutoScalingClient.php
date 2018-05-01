<?php
namespace MageBackup\Aws\ApplicationAutoScaling;

use MageBackup\Aws\AwsClient;

/**
 * This client is used to interact with the **Application Auto Scaling** service.
 * @method \MageBackup\Aws\Result deleteScalingPolicy(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteScalingPolicyAsync(array $args = [])
 * @method \MageBackup\Aws\Result deregisterScalableTarget(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deregisterScalableTargetAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeScalableTargets(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeScalableTargetsAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeScalingActivities(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeScalingActivitiesAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeScalingPolicies(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeScalingPoliciesAsync(array $args = [])
 * @method \MageBackup\Aws\Result putScalingPolicy(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putScalingPolicyAsync(array $args = [])
 * @method \MageBackup\Aws\Result registerScalableTarget(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise registerScalableTargetAsync(array $args = [])
 */
class ApplicationAutoScalingClient extends AwsClient {}

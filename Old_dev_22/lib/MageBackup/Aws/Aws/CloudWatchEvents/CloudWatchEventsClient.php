<?php
namespace MageBackup\Aws\CloudWatchEvents;

use MageBackup\Aws\AwsClient;

/**
 * This client is used to interact with the **Amazon CloudWatch Events** service.
 *
 * @method \MageBackup\Aws\Result deleteRule(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteRuleAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeRule(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeRuleAsync(array $args = [])
 * @method \MageBackup\Aws\Result disableRule(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise disableRuleAsync(array $args = [])
 * @method \MageBackup\Aws\Result enableRule(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise enableRuleAsync(array $args = [])
 * @method \MageBackup\Aws\Result listRuleNamesByTarget(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listRuleNamesByTargetAsync(array $args = [])
 * @method \MageBackup\Aws\Result listRules(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listRulesAsync(array $args = [])
 * @method \MageBackup\Aws\Result listTargetsByRule(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listTargetsByRuleAsync(array $args = [])
 * @method \MageBackup\Aws\Result putEvents(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putEventsAsync(array $args = [])
 * @method \MageBackup\Aws\Result putRule(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putRuleAsync(array $args = [])
 * @method \MageBackup\Aws\Result putTargets(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putTargetsAsync(array $args = [])
 * @method \MageBackup\Aws\Result removeTargets(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise removeTargetsAsync(array $args = [])
 * @method \MageBackup\Aws\Result testEventPattern(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise testEventPatternAsync(array $args = [])
 */
class CloudWatchEventsClient extends AwsClient {}

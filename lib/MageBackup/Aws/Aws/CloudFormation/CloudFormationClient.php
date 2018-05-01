<?php
namespace MageBackup\Aws\CloudFormation;

use MageBackup\Aws\AwsClient;

/**
 * This client is used to interact with the **AWS CloudFormation** service.
 *
 * @method \MageBackup\Aws\Result cancelUpdateStack(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise cancelUpdateStackAsync(array $args = [])
 * @method \MageBackup\Aws\Result continueUpdateRollback(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise continueUpdateRollbackAsync(array $args = [])
 * @method \MageBackup\Aws\Result createChangeSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createChangeSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result createStack(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createStackAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteChangeSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteChangeSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteStack(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteStackAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeAccountLimits(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeAccountLimitsAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeChangeSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeChangeSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeStackEvents(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeStackEventsAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeStackResource(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeStackResourceAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeStackResources(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeStackResourcesAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeStacks(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeStacksAsync(array $args = [])
 * @method \MageBackup\Aws\Result estimateTemplateCost(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise estimateTemplateCostAsync(array $args = [])
 * @method \MageBackup\Aws\Result executeChangeSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise executeChangeSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result getStackPolicy(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getStackPolicyAsync(array $args = [])
 * @method \MageBackup\Aws\Result getTemplate(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getTemplateAsync(array $args = [])
 * @method \MageBackup\Aws\Result getTemplateSummary(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getTemplateSummaryAsync(array $args = [])
 * @method \MageBackup\Aws\Result listChangeSets(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listChangeSetsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listStackResources(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listStackResourcesAsync(array $args = [])
 * @method \MageBackup\Aws\Result listStacks(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listStacksAsync(array $args = [])
 * @method \MageBackup\Aws\Result setStackPolicy(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise setStackPolicyAsync(array $args = [])
 * @method \MageBackup\Aws\Result signalResource(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise signalResourceAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateStack(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateStackAsync(array $args = [])
 * @method \MageBackup\Aws\Result validateTemplate(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise validateTemplateAsync(array $args = [])
 */
class CloudFormationClient extends AwsClient {}
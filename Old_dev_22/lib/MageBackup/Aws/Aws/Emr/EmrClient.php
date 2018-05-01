<?php
namespace MageBackup\Aws\Emr;

use MageBackup\Aws\AwsClient;

/**
 * This client is used to interact with the **Amazon Elastic MapReduce (Amazon EMR)** service.
 *
 * @method \MageBackup\Aws\Result addInstanceGroups(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise addInstanceGroupsAsync(array $args = [])
 * @method \MageBackup\Aws\Result addJobFlowSteps(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise addJobFlowStepsAsync(array $args = [])
 * @method \MageBackup\Aws\Result addTags(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise addTagsAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeCluster(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeClusterAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeJobFlows(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeJobFlowsAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeStep(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeStepAsync(array $args = [])
 * @method \MageBackup\Aws\Result listBootstrapActions(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listBootstrapActionsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listClusters(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listClustersAsync(array $args = [])
 * @method \MageBackup\Aws\Result listInstanceGroups(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listInstanceGroupsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listInstances(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listInstancesAsync(array $args = [])
 * @method \MageBackup\Aws\Result listSteps(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listStepsAsync(array $args = [])
 * @method \MageBackup\Aws\Result modifyInstanceGroups(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise modifyInstanceGroupsAsync(array $args = [])
 * @method \MageBackup\Aws\Result removeTags(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise removeTagsAsync(array $args = [])
 * @method \MageBackup\Aws\Result runJobFlow(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise runJobFlowAsync(array $args = [])
 * @method \MageBackup\Aws\Result setTerminationProtection(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise setTerminationProtectionAsync(array $args = [])
 * @method \MageBackup\Aws\Result setVisibleToAllUsers(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise setVisibleToAllUsersAsync(array $args = [])
 * @method \MageBackup\Aws\Result terminateJobFlows(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise terminateJobFlowsAsync(array $args = [])
 */
class EmrClient extends AwsClient {}

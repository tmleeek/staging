<?php
namespace MageBackup\Aws\DataPipeline;

use MageBackup\Aws\AwsClient;

/**
 * This client is used to interact with the **AWS Data Pipeline** service.
 *
 * @method \MageBackup\Aws\Result activatePipeline(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise activatePipelineAsync(array $args = [])
 * @method \MageBackup\Aws\Result addTags(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise addTagsAsync(array $args = [])
 * @method \MageBackup\Aws\Result createPipeline(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createPipelineAsync(array $args = [])
 * @method \MageBackup\Aws\Result deactivatePipeline(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deactivatePipelineAsync(array $args = [])
 * @method \MageBackup\Aws\Result deletePipeline(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deletePipelineAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeObjects(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeObjectsAsync(array $args = [])
 * @method \MageBackup\Aws\Result describePipelines(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describePipelinesAsync(array $args = [])
 * @method \MageBackup\Aws\Result evaluateExpression(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise evaluateExpressionAsync(array $args = [])
 * @method \MageBackup\Aws\Result getPipelineDefinition(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getPipelineDefinitionAsync(array $args = [])
 * @method \MageBackup\Aws\Result listPipelines(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listPipelinesAsync(array $args = [])
 * @method \MageBackup\Aws\Result pollForTask(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise pollForTaskAsync(array $args = [])
 * @method \MageBackup\Aws\Result putPipelineDefinition(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putPipelineDefinitionAsync(array $args = [])
 * @method \MageBackup\Aws\Result queryObjects(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise queryObjectsAsync(array $args = [])
 * @method \MageBackup\Aws\Result removeTags(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise removeTagsAsync(array $args = [])
 * @method \MageBackup\Aws\Result reportTaskProgress(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise reportTaskProgressAsync(array $args = [])
 * @method \MageBackup\Aws\Result reportTaskRunnerHeartbeat(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise reportTaskRunnerHeartbeatAsync(array $args = [])
 * @method \MageBackup\Aws\Result setStatus(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise setStatusAsync(array $args = [])
 * @method \MageBackup\Aws\Result setTaskStatus(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise setTaskStatusAsync(array $args = [])
 * @method \MageBackup\Aws\Result validatePipelineDefinition(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise validatePipelineDefinitionAsync(array $args = [])
 */
class DataPipelineClient extends AwsClient {}

<?php
namespace MageBackup\Aws\CodePipeline;

use MageBackup\Aws\AwsClient;

/**
 * This client is used to interact with the **Amazon CodePipeline** service.
 *
 * @method \MageBackup\Aws\Result acknowledgeJob(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise acknowledgeJobAsync(array $args = [])
 * @method \MageBackup\Aws\Result acknowledgeThirdPartyJob(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise acknowledgeThirdPartyJobAsync(array $args = [])
 * @method \MageBackup\Aws\Result createCustomActionType(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createCustomActionTypeAsync(array $args = [])
 * @method \MageBackup\Aws\Result createPipeline(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createPipelineAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteCustomActionType(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteCustomActionTypeAsync(array $args = [])
 * @method \MageBackup\Aws\Result deletePipeline(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deletePipelineAsync(array $args = [])
 * @method \MageBackup\Aws\Result disableStageTransition(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise disableStageTransitionAsync(array $args = [])
 * @method \MageBackup\Aws\Result enableStageTransition(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise enableStageTransitionAsync(array $args = [])
 * @method \MageBackup\Aws\Result getJobDetails(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getJobDetailsAsync(array $args = [])
 * @method \MageBackup\Aws\Result getPipeline(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getPipelineAsync(array $args = [])
 * @method \MageBackup\Aws\Result getPipelineState(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getPipelineStateAsync(array $args = [])
 * @method \MageBackup\Aws\Result getThirdPartyJobDetails(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getThirdPartyJobDetailsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listActionTypes(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listActionTypesAsync(array $args = [])
 * @method \MageBackup\Aws\Result listPipelines(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listPipelinesAsync(array $args = [])
 * @method \MageBackup\Aws\Result pollForJobs(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise pollForJobsAsync(array $args = [])
 * @method \MageBackup\Aws\Result pollForThirdPartyJobs(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise pollForThirdPartyJobsAsync(array $args = [])
 * @method \MageBackup\Aws\Result putActionRevision(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putActionRevisionAsync(array $args = [])
 * @method \MageBackup\Aws\Result putJobFailureResult(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putJobFailureResultAsync(array $args = [])
 * @method \MageBackup\Aws\Result putJobSuccessResult(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putJobSuccessResultAsync(array $args = [])
 * @method \MageBackup\Aws\Result putThirdPartyJobFailureResult(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putThirdPartyJobFailureResultAsync(array $args = [])
 * @method \MageBackup\Aws\Result putThirdPartyJobSuccessResult(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putThirdPartyJobSuccessResultAsync(array $args = [])
 * @method \MageBackup\Aws\Result startPipelineExecution(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise startPipelineExecutionAsync(array $args = [])
 * @method \MageBackup\Aws\Result updatePipeline(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updatePipelineAsync(array $args = [])
 */
class CodePipelineClient extends AwsClient {}
<?php
namespace MageBackup\Aws\ElasticTranscoder;

use MageBackup\Aws\AwsClient;

/**
 * This client is used to interact with the **Amazon Elastic Transcoder** service.
 *
 * @method \MageBackup\Aws\Result cancelJob(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise cancelJobAsync(array $args = [])
 * @method \MageBackup\Aws\Result createJob(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createJobAsync(array $args = [])
 * @method \MageBackup\Aws\Result createPipeline(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createPipelineAsync(array $args = [])
 * @method \MageBackup\Aws\Result createPreset(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createPresetAsync(array $args = [])
 * @method \MageBackup\Aws\Result deletePipeline(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deletePipelineAsync(array $args = [])
 * @method \MageBackup\Aws\Result deletePreset(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deletePresetAsync(array $args = [])
 * @method \MageBackup\Aws\Result listJobsByPipeline(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listJobsByPipelineAsync(array $args = [])
 * @method \MageBackup\Aws\Result listJobsByStatus(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listJobsByStatusAsync(array $args = [])
 * @method \MageBackup\Aws\Result listPipelines(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listPipelinesAsync(array $args = [])
 * @method \MageBackup\Aws\Result listPresets(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listPresetsAsync(array $args = [])
 * @method \MageBackup\Aws\Result readJob(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise readJobAsync(array $args = [])
 * @method \MageBackup\Aws\Result readPipeline(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise readPipelineAsync(array $args = [])
 * @method \MageBackup\Aws\Result readPreset(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise readPresetAsync(array $args = [])
 * @method \MageBackup\Aws\Result testRole(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise testRoleAsync(array $args = [])
 * @method \MageBackup\Aws\Result updatePipeline(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updatePipelineAsync(array $args = [])
 * @method \MageBackup\Aws\Result updatePipelineNotifications(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updatePipelineNotificationsAsync(array $args = [])
 * @method \MageBackup\Aws\Result updatePipelineStatus(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updatePipelineStatusAsync(array $args = [])
 */
class ElasticTranscoderClient extends AwsClient {}

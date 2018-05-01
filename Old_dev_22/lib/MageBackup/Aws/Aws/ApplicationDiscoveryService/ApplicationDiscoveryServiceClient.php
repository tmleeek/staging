<?php
namespace MageBackup\Aws\ApplicationDiscoveryService;

use MageBackup\Aws\AwsClient;

/**
 * This client is used to interact with the **AWS Application Discovery Service** service.
 * @method \MageBackup\Aws\Result createTags(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createTagsAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteTags(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteTagsAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeAgents(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeAgentsAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeConfigurations(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeConfigurationsAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeExportConfigurations(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeExportConfigurationsAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeTags(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeTagsAsync(array $args = [])
 * @method \MageBackup\Aws\Result exportConfigurations(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise exportConfigurationsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listConfigurations(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listConfigurationsAsync(array $args = [])
 * @method \MageBackup\Aws\Result startDataCollectionByAgentIds(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise startDataCollectionByAgentIdsAsync(array $args = [])
 * @method \MageBackup\Aws\Result stopDataCollectionByAgentIds(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise stopDataCollectionByAgentIdsAsync(array $args = [])
 */
class ApplicationDiscoveryServiceClient extends AwsClient {}

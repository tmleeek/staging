<?php
namespace MageBackup\Aws\ElasticsearchService;

use MageBackup\Aws\AwsClient;

/**
 * This client is used to interact with the **Amazon Elasticsearch Service** service.
 *
 * @method \MageBackup\Aws\Result addTags(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise addTagsAsync(array $args = [])
 * @method \MageBackup\Aws\Result createElasticsearchDomain(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createElasticsearchDomainAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteElasticsearchDomain(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteElasticsearchDomainAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeElasticsearchDomain(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeElasticsearchDomainAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeElasticsearchDomainConfig(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeElasticsearchDomainConfigAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeElasticsearchDomains(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeElasticsearchDomainsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listDomainNames(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listDomainNamesAsync(array $args = [])
 * @method \MageBackup\Aws\Result listTags(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listTagsAsync(array $args = [])
 * @method \MageBackup\Aws\Result removeTags(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise removeTagsAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateElasticsearchDomainConfig(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateElasticsearchDomainConfigAsync(array $args = [])
 */
class ElasticsearchServiceClient extends AwsClient {}

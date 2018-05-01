<?php
namespace MageBackup\Aws;

/**
 * Builds AWS clients based on configuration settings.
 *
 * @method \MageBackup\Aws\Acm\AcmClient createAcm(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionAcm(array $args = [])
 * @method \MageBackup\Aws\ApiGateway\ApiGatewayClient createApiGateway(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionApiGateway(array $args = [])
 * @method \MageBackup\Aws\ApplicationAutoScaling\ApplicationAutoScalingClient createApplicationAutoScaling(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionApplicationAutoScaling(array $args = [])
 * @method \MageBackup\Aws\ApplicationDiscoveryService\ApplicationDiscoveryServiceClient createApplicationDiscoveryService(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionApplicationDiscoveryService(array $args = [])
 * @method \MageBackup\Aws\AutoScaling\AutoScalingClient createAutoScaling(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionAutoScaling(array $args = [])
 * @method \MageBackup\Aws\CloudFormation\CloudFormationClient createCloudFormation(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionCloudFormation(array $args = [])
 * @method \MageBackup\Aws\CloudFront\CloudFrontClient createCloudFront(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionCloudFront(array $args = [])
 * @method \MageBackup\Aws\CloudHsm\CloudHsmClient createCloudHsm(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionCloudHsm(array $args = [])
 * @method \MageBackup\Aws\CloudSearch\CloudSearchClient createCloudSearch(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionCloudSearch(array $args = [])
 * @method \MageBackup\Aws\CloudSearchDomain\CloudSearchDomainClient createCloudSearchDomain(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionCloudSearchDomain(array $args = [])
 * @method \MageBackup\Aws\CloudTrail\CloudTrailClient createCloudTrail(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionCloudTrail(array $args = [])
 * @method \MageBackup\Aws\CloudWatch\CloudWatchClient createCloudWatch(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionCloudWatch(array $args = [])
 * @method \MageBackup\Aws\CloudWatchEvents\CloudWatchEventsClient createCloudWatchEvents(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionCloudWatchEvents(array $args = [])
 * @method \MageBackup\Aws\CloudWatchLogs\CloudWatchLogsClient createCloudWatchLogs(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionCloudWatchLogs(array $args = [])
 * @method \MageBackup\Aws\CodeCommit\CodeCommitClient createCodeCommit(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionCodeCommit(array $args = [])
 * @method \MageBackup\Aws\CodeDeploy\CodeDeployClient createCodeDeploy(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionCodeDeploy(array $args = [])
 * @method \MageBackup\Aws\CodePipeline\CodePipelineClient createCodePipeline(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionCodePipeline(array $args = [])
 * @method \MageBackup\Aws\CognitoIdentity\CognitoIdentityClient createCognitoIdentity(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionCognitoIdentity(array $args = [])
 * @method \MageBackup\Aws\CognitoIdentityProvider\CognitoIdentityProviderClient createCognitoIdentityProvider(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionCognitoIdentityProvider(array $args = [])
 * @method \MageBackup\Aws\CognitoSync\CognitoSyncClient createCognitoSync(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionCognitoSync(array $args = [])
 * @method \MageBackup\Aws\ConfigService\ConfigServiceClient createConfigService(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionConfigService(array $args = [])
 * @method \MageBackup\Aws\DataPipeline\DataPipelineClient createDataPipeline(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionDataPipeline(array $args = [])
 * @method \MageBackup\Aws\DatabaseMigrationService\DatabaseMigrationServiceClient createDatabaseMigrationService(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionDatabaseMigrationService(array $args = [])
 * @method \MageBackup\Aws\DeviceFarm\DeviceFarmClient createDeviceFarm(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionDeviceFarm(array $args = [])
 * @method \MageBackup\Aws\DirectConnect\DirectConnectClient createDirectConnect(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionDirectConnect(array $args = [])
 * @method \MageBackup\Aws\DirectoryService\DirectoryServiceClient createDirectoryService(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionDirectoryService(array $args = [])
 * @method \MageBackup\Aws\DynamoDb\DynamoDbClient createDynamoDb(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionDynamoDb(array $args = [])
 * @method \MageBackup\Aws\DynamoDbStreams\DynamoDbStreamsClient createDynamoDbStreams(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionDynamoDbStreams(array $args = [])
 * @method \MageBackup\Aws\Ec2\Ec2Client createEc2(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionEc2(array $args = [])
 * @method \MageBackup\Aws\Ecr\EcrClient createEcr(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionEcr(array $args = [])
 * @method \MageBackup\Aws\Ecs\EcsClient createEcs(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionEcs(array $args = [])
 * @method \MageBackup\Aws\Efs\EfsClient createEfs(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionEfs(array $args = [])
 * @method \MageBackup\Aws\ElastiCache\ElastiCacheClient createElastiCache(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionElastiCache(array $args = [])
 * @method \MageBackup\Aws\ElasticBeanstalk\ElasticBeanstalkClient createElasticBeanstalk(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionElasticBeanstalk(array $args = [])
 * @method \MageBackup\Aws\ElasticLoadBalancing\ElasticLoadBalancingClient createElasticLoadBalancing(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionElasticLoadBalancing(array $args = [])
 * @method \MageBackup\Aws\ElasticTranscoder\ElasticTranscoderClient createElasticTranscoder(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionElasticTranscoder(array $args = [])
 * @method \MageBackup\Aws\ElasticsearchService\ElasticsearchServiceClient createElasticsearchService(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionElasticsearchService(array $args = [])
 * @method \MageBackup\Aws\Emr\EmrClient createEmr(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionEmr(array $args = [])
 * @method \MageBackup\Aws\Firehose\FirehoseClient createFirehose(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionFirehose(array $args = [])
 * @method \MageBackup\Aws\GameLift\GameLiftClient createGameLift(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionGameLift(array $args = [])
 * @method \MageBackup\Aws\Glacier\GlacierClient createGlacier(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionGlacier(array $args = [])
 * @method \MageBackup\Aws\Iam\IamClient createIam(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionIam(array $args = [])
 * @method \MageBackup\Aws\ImportExport\ImportExportClient createImportExport(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionImportExport(array $args = [])
 * @method \MageBackup\Aws\Inspector\InspectorClient createInspector(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionInspector(array $args = [])
 * @method \MageBackup\Aws\Iot\IotClient createIot(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionIot(array $args = [])
 * @method \MageBackup\Aws\IotDataPlane\IotDataPlaneClient createIotDataPlane(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionIotDataPlane(array $args = [])
 * @method \MageBackup\Aws\Kinesis\KinesisClient createKinesis(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionKinesis(array $args = [])
 * @method \MageBackup\Aws\Kms\KmsClient createKms(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionKms(array $args = [])
 * @method \MageBackup\Aws\Lambda\LambdaClient createLambda(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionLambda(array $args = [])
 * @method \MageBackup\Aws\MachineLearning\MachineLearningClient createMachineLearning(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionMachineLearning(array $args = [])
 * @method \MageBackup\Aws\MarketplaceCommerceAnalytics\MarketplaceCommerceAnalyticsClient createMarketplaceCommerceAnalytics(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionMarketplaceCommerceAnalytics(array $args = [])
 * @method \MageBackup\Aws\MarketplaceMetering\MarketplaceMeteringClient createMarketplaceMetering(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionMarketplaceMetering(array $args = [])
 * @method \MageBackup\Aws\OpsWorks\OpsWorksClient createOpsWorks(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionOpsWorks(array $args = [])
 * @method \MageBackup\Aws\Rds\RdsClient createRds(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionRds(array $args = [])
 * @method \MageBackup\Aws\Redshift\RedshiftClient createRedshift(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionRedshift(array $args = [])
 * @method \MageBackup\Aws\Route53\Route53Client createRoute53(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionRoute53(array $args = [])
 * @method \MageBackup\Aws\Route53Domains\Route53DomainsClient createRoute53Domains(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionRoute53Domains(array $args = [])
 * @method \MageBackup\Aws\S3\S3Client createS3(array $args = [])
 * @method \MageBackup\Aws\S3\S3MultiRegionClient createMultiRegionS3(array $args = [])
 * @method \MageBackup\Aws\Ses\SesClient createSes(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionSes(array $args = [])
 * @method \MageBackup\Aws\Sns\SnsClient createSns(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionSns(array $args = [])
 * @method \MageBackup\Aws\Sqs\SqsClient createSqs(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionSqs(array $args = [])
 * @method \MageBackup\Aws\Ssm\SsmClient createSsm(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionSsm(array $args = [])
 * @method \MageBackup\Aws\StorageGateway\StorageGatewayClient createStorageGateway(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionStorageGateway(array $args = [])
 * @method \MageBackup\Aws\Sts\StsClient createSts(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionSts(array $args = [])
 * @method \MageBackup\Aws\Support\SupportClient createSupport(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionSupport(array $args = [])
 * @method \MageBackup\Aws\Swf\SwfClient createSwf(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionSwf(array $args = [])
 * @method \MageBackup\Aws\Waf\WafClient createWaf(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionWaf(array $args = [])
 * @method \MageBackup\Aws\WorkSpaces\WorkSpacesClient createWorkSpaces(array $args = [])
 * @method \MageBackup\Aws\MultiRegionClient createMultiRegionWorkSpaces(array $args = [])
 */
class Sdk
{
    const VERSION = '3.18.12';

    /** @var array Arguments for creating clients */
    private $args;

    /**
     * Constructs a new SDK object with an associative array of default
     * client settings.
     *
     * @param array $args
     *
     * @throws \InvalidArgumentException
     * @see MageBackup\Aws\AwsClient::__construct for a list of available options.
     */
    public function __construct(array $args = [])
    {
        $this->args = $args;

        if (!isset($args['handler']) && !isset($args['http_handler'])) {
            $this->args['http_handler'] = default_http_handler();
        }
    }

    public function __call($name, array $args)
    {
        $args = isset($args[0]) ? $args[0] : [];
        if (strpos($name, 'createMultiRegion') === 0) {
            return $this->createMultiRegionClient(substr($name, 17), $args);
        } elseif (strpos($name, 'create') === 0) {
            return $this->createClient(substr($name, 6), $args);
        }

        throw new \BadMethodCallException("Unknown method: {$name}.");
    }

    /**
     * Get a client by name using an array of constructor options.
     *
     * @param string $name Service name or namespace (e.g., DynamoDb, s3).
     * @param array  $args Arguments to configure the client.
     *
     * @return AwsClientInterface
     * @throws \InvalidArgumentException if any required options are missing or
     *                                   the service is not supported.
     * @see MageBackup\Aws\AwsClient::__construct for a list of available options for args.
     */
    public function createClient($name, array $args = [])
    {
        // Get information about the service from the manifest file.
        $service = manifest($name);
        $namespace = $service['namespace'];
        $args = $this->mergeArgs($namespace, $service, $args);

        // Instantiate the client class.
        $client = "MageBackup\Aws\\{$namespace}\\{$namespace}Client";
        return new $client($this->mergeArgs($namespace, $service, $args));
    }

    public function createMultiRegionClient($name, array $args = [])
    {
        // Get information about the service from the manifest file.
        $service = manifest($name);
        $namespace = $service['namespace'];

        $klass = "MageBackup\Aws\\{$namespace}\\{$namespace}MultiRegionClient";
        $klass = class_exists($klass) ? $klass : 'MageBackup\Aws\\MultiRegionClient';

        return new $klass($this->mergeArgs($namespace, $service, $args));
    }

    private function mergeArgs($namespace, array $manifest, array $args = [])
    {
        // Merge provided args with stored, service-specific args.
        if (isset($this->args[$namespace])) {
            $args += $this->args[$namespace];
        }

        // Provide the endpoint prefix in the args.
        if (!isset($args['service'])) {
            $args['service'] = $manifest['endpoint'];
        }

        return $args + $this->args;
    }

    /**
     * Determine the endpoint prefix from a client namespace.
     *
     * @param string $name Namespace name
     *
     * @return string
     * @internal
     * @deprecated Use the `\MageBackup\Aws\manifest()` function instead.
     */
    public static function getEndpointPrefix($name)
    {
        return manifest($name)['endpoint'];
    }
}

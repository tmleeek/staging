<?php
namespace MageBackup\Aws\ApiGateway;

use MageBackup\Aws\AwsClient;
use MageBackup\Aws\CommandInterface;
use MageBackup\Psr\Http\Message\RequestInterface;

/**
 * This client is used to interact with the **AWS API Gateway** service.
 *
 * @method \MageBackup\Aws\Result createApiKey(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createApiKeyAsync(array $args = [])
 * @method \MageBackup\Aws\Result createAuthorizer(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createAuthorizerAsync(array $args = [])
 * @method \MageBackup\Aws\Result createBasePathMapping(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createBasePathMappingAsync(array $args = [])
 * @method \MageBackup\Aws\Result createDeployment(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createDeploymentAsync(array $args = [])
 * @method \MageBackup\Aws\Result createDomainName(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createDomainNameAsync(array $args = [])
 * @method \MageBackup\Aws\Result createModel(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createModelAsync(array $args = [])
 * @method \MageBackup\Aws\Result createResource(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createResourceAsync(array $args = [])
 * @method \MageBackup\Aws\Result createRestApi(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createRestApiAsync(array $args = [])
 * @method \MageBackup\Aws\Result createStage(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createStageAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteApiKey(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteApiKeyAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteAuthorizer(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteAuthorizerAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteBasePathMapping(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteBasePathMappingAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteClientCertificate(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteClientCertificateAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteDeployment(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteDeploymentAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteDomainName(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteDomainNameAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteIntegration(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteIntegrationAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteIntegrationResponse(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteIntegrationResponseAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteMethod(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteMethodAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteMethodResponse(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteMethodResponseAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteModel(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteModelAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteResource(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteResourceAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteRestApi(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteRestApiAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteStage(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteStageAsync(array $args = [])
 * @method \MageBackup\Aws\Result flushStageAuthorizersCache(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise flushStageAuthorizersCacheAsync(array $args = [])
 * @method \MageBackup\Aws\Result flushStageCache(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise flushStageCacheAsync(array $args = [])
 * @method \MageBackup\Aws\Result generateClientCertificate(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise generateClientCertificateAsync(array $args = [])
 * @method \MageBackup\Aws\Result getAccount(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getAccountAsync(array $args = [])
 * @method \MageBackup\Aws\Result getApiKey(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getApiKeyAsync(array $args = [])
 * @method \MageBackup\Aws\Result getApiKeys(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getApiKeysAsync(array $args = [])
 * @method \MageBackup\Aws\Result getAuthorizer(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getAuthorizerAsync(array $args = [])
 * @method \MageBackup\Aws\Result getAuthorizers(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getAuthorizersAsync(array $args = [])
 * @method \MageBackup\Aws\Result getBasePathMapping(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getBasePathMappingAsync(array $args = [])
 * @method \MageBackup\Aws\Result getBasePathMappings(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getBasePathMappingsAsync(array $args = [])
 * @method \MageBackup\Aws\Result getClientCertificate(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getClientCertificateAsync(array $args = [])
 * @method \MageBackup\Aws\Result getClientCertificates(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getClientCertificatesAsync(array $args = [])
 * @method \MageBackup\Aws\Result getDeployment(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getDeploymentAsync(array $args = [])
 * @method \MageBackup\Aws\Result getDeployments(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getDeploymentsAsync(array $args = [])
 * @method \MageBackup\Aws\Result getDomainName(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getDomainNameAsync(array $args = [])
 * @method \MageBackup\Aws\Result getDomainNames(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getDomainNamesAsync(array $args = [])
 * @method \MageBackup\Aws\Result getExport(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getExportAsync(array $args = [])
 * @method \MageBackup\Aws\Result getIntegration(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getIntegrationAsync(array $args = [])
 * @method \MageBackup\Aws\Result getIntegrationResponse(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getIntegrationResponseAsync(array $args = [])
 * @method \MageBackup\Aws\Result getMethod(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getMethodAsync(array $args = [])
 * @method \MageBackup\Aws\Result getMethodResponse(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getMethodResponseAsync(array $args = [])
 * @method \MageBackup\Aws\Result getModel(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getModelAsync(array $args = [])
 * @method \MageBackup\Aws\Result getModelTemplate(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getModelTemplateAsync(array $args = [])
 * @method \MageBackup\Aws\Result getModels(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getModelsAsync(array $args = [])
 * @method \MageBackup\Aws\Result getResource(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getResourceAsync(array $args = [])
 * @method \MageBackup\Aws\Result getResources(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getResourcesAsync(array $args = [])
 * @method \MageBackup\Aws\Result getRestApi(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getRestApiAsync(array $args = [])
 * @method \MageBackup\Aws\Result getRestApis(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getRestApisAsync(array $args = [])
 * @method \MageBackup\Aws\Result getSdk(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getSdkAsync(array $args = [])
 * @method \MageBackup\Aws\Result getStage(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getStageAsync(array $args = [])
 * @method \MageBackup\Aws\Result getStages(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getStagesAsync(array $args = [])
 * @method \MageBackup\Aws\Result importRestApi(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise importRestApiAsync(array $args = [])
 * @method \MageBackup\Aws\Result putIntegration(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putIntegrationAsync(array $args = [])
 * @method \MageBackup\Aws\Result putIntegrationResponse(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putIntegrationResponseAsync(array $args = [])
 * @method \MageBackup\Aws\Result putMethod(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putMethodAsync(array $args = [])
 * @method \MageBackup\Aws\Result putMethodResponse(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putMethodResponseAsync(array $args = [])
 * @method \MageBackup\Aws\Result putRestApi(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putRestApiAsync(array $args = [])
 * @method \MageBackup\Aws\Result testInvokeAuthorizer(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise testInvokeAuthorizerAsync(array $args = [])
 * @method \MageBackup\Aws\Result testInvokeMethod(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise testInvokeMethodAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateAccount(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateAccountAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateApiKey(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateApiKeyAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateAuthorizer(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateAuthorizerAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateBasePathMapping(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateBasePathMappingAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateClientCertificate(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateClientCertificateAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateDeployment(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateDeploymentAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateDomainName(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateDomainNameAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateIntegration(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateIntegrationAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateIntegrationResponse(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateIntegrationResponseAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateMethod(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateMethodAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateMethodResponse(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateMethodResponseAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateModel(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateModelAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateResource(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateResourceAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateRestApi(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateRestApiAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateStage(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateStageAsync(array $args = [])
 */
class ApiGatewayClient extends AwsClient
{
    public function __construct(array $args)
    {
        parent::__construct($args);
        $stack = $this->getHandlerList();
        $stack->appendBuild([__CLASS__, '_add_accept_header']);
    }

    public static function _add_accept_header(callable $handler)
    {
        return function (
            CommandInterface $command,
            RequestInterface $request
        ) use ($handler) {
            $request = $request->withHeader('Accept', 'application/json');

            return $handler($command, $request);
        };
    }
}
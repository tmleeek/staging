<?php
namespace MageBackup\Aws\CloudHsm;

use MageBackup\Aws\Api\ApiProvider;
use MageBackup\Aws\Api\DocModel;
use MageBackup\Aws\Api\Service;
use MageBackup\Aws\AwsClient;

/**
 * This client is used to interact with **AWS CloudHSM**.
 *
 * @method \MageBackup\Aws\Result addTagsToResource(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise addTagsToResourceAsync(array $args = [])
 * @method \MageBackup\Aws\Result createHapg(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createHapgAsync(array $args = [])
 * @method \MageBackup\Aws\Result createHsm(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createHsmAsync(array $args = [])
 * @method \MageBackup\Aws\Result createLunaClient(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createLunaClientAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteHapg(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteHapgAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteHsm(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteHsmAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteLunaClient(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteLunaClientAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeHapg(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeHapgAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeHsm(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeHsmAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeLunaClient(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeLunaClientAsync(array $args = [])
 * @method \MageBackup\Aws\Result getConfig(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getConfigAsync(array $args = [])
 * @method \MageBackup\Aws\Result listAvailableZones(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listAvailableZonesAsync(array $args = [])
 * @method \MageBackup\Aws\Result listHapgs(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listHapgsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listHsms(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listHsmsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listLunaClients(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listLunaClientsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listTagsForResource(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listTagsForResourceAsync(array $args = [])
 * @method \MageBackup\Aws\Result modifyHapg(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise modifyHapgAsync(array $args = [])
 * @method \MageBackup\Aws\Result modifyHsm(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise modifyHsmAsync(array $args = [])
 * @method \MageBackup\Aws\Result modifyLunaClient(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise modifyLunaClientAsync(array $args = [])
 * @method \MageBackup\Aws\Result removeTagsFromResource(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise removeTagsFromResourceAsync(array $args = [])
 */
class CloudHsmClient extends AwsClient
{
    public function __call($name, array $args)
    {
        // Overcomes a naming collision with `AwsClient::getConfig`.
        if (lcfirst($name) === 'getConfigFiles') {
            $name = 'GetConfig';
        } elseif (lcfirst($name) === 'getConfigFilesAsync') {
            $name = 'GetConfigAsync';
        }

        return parent::__call($name, $args);
    }

    /**
     * @internal
     * @codeCoverageIgnore
     */
    public static function applyDocFilters(array $api, array $docs)
    {
        // Overcomes a naming collision with `AwsClient::getConfig`.
        $api['operations']['GetConfigFiles'] = $api['operations']['GetConfig'];
        $docs['operations']['GetConfigFiles'] = $docs['operations']['GetConfig'];
        unset($api['operations']['GetConfig'], $docs['operations']['GetConfig']);
        ksort($api['operations']);

        return [
            new Service($api, ApiProvider::defaultProvider()),
            new DocModel($docs)
        ];
    }
}

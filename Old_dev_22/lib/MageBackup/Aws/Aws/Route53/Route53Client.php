<?php
namespace MageBackup\Aws\Route53;

use MageBackup\Aws\AwsClient;
use MageBackup\Aws\CommandInterface;
use MageBackup\Psr\Http\Message\RequestInterface;

/**
 * This client is used to interact with the **Amazon Route 53** service.
 *
 * @method \MageBackup\Aws\Result associateVPCWithHostedZone(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise associateVPCWithHostedZoneAsync(array $args = [])
 * @method \MageBackup\Aws\Result changeResourceRecordSets(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise changeResourceRecordSetsAsync(array $args = [])
 * @method \MageBackup\Aws\Result changeTagsForResource(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise changeTagsForResourceAsync(array $args = [])
 * @method \MageBackup\Aws\Result createHealthCheck(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createHealthCheckAsync(array $args = [])
 * @method \MageBackup\Aws\Result createHostedZone(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createHostedZoneAsync(array $args = [])
 * @method \MageBackup\Aws\Result createReusableDelegationSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createReusableDelegationSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result createTrafficPolicy(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createTrafficPolicyAsync(array $args = [])
 * @method \MageBackup\Aws\Result createTrafficPolicyInstance(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createTrafficPolicyInstanceAsync(array $args = [])
 * @method \MageBackup\Aws\Result createTrafficPolicyVersion(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createTrafficPolicyVersionAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteHealthCheck(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteHealthCheckAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteHostedZone(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteHostedZoneAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteReusableDelegationSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteReusableDelegationSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteTrafficPolicy(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteTrafficPolicyAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteTrafficPolicyInstance(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteTrafficPolicyInstanceAsync(array $args = [])
 * @method \MageBackup\Aws\Result disassociateVPCFromHostedZone(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise disassociateVPCFromHostedZoneAsync(array $args = [])
 * @method \MageBackup\Aws\Result getChange(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getChangeAsync(array $args = [])
 * @method \MageBackup\Aws\Result getChangeDetails(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getChangeDetailsAsync(array $args = [])
 * @method \MageBackup\Aws\Result getCheckerIpRanges(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getCheckerIpRangesAsync(array $args = [])
 * @method \MageBackup\Aws\Result getGeoLocation(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getGeoLocationAsync(array $args = [])
 * @method \MageBackup\Aws\Result getHealthCheck(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getHealthCheckAsync(array $args = [])
 * @method \MageBackup\Aws\Result getHealthCheckCount(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getHealthCheckCountAsync(array $args = [])
 * @method \MageBackup\Aws\Result getHealthCheckLastFailureReason(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getHealthCheckLastFailureReasonAsync(array $args = [])
 * @method \MageBackup\Aws\Result getHealthCheckStatus(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getHealthCheckStatusAsync(array $args = [])
 * @method \MageBackup\Aws\Result getHostedZone(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getHostedZoneAsync(array $args = [])
 * @method \MageBackup\Aws\Result getHostedZoneCount(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getHostedZoneCountAsync(array $args = [])
 * @method \MageBackup\Aws\Result getReusableDelegationSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getReusableDelegationSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result getTrafficPolicy(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getTrafficPolicyAsync(array $args = [])
 * @method \MageBackup\Aws\Result getTrafficPolicyInstance(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getTrafficPolicyInstanceAsync(array $args = [])
 * @method \MageBackup\Aws\Result getTrafficPolicyInstanceCount(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getTrafficPolicyInstanceCountAsync(array $args = [])
 * @method \MageBackup\Aws\Result listChangeBatchesByHostedZone(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listChangeBatchesByHostedZoneAsync(array $args = [])
 * @method \MageBackup\Aws\Result listChangeBatchesByRRSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listChangeBatchesByRRSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result listGeoLocations(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listGeoLocationsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listHealthChecks(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listHealthChecksAsync(array $args = [])
 * @method \MageBackup\Aws\Result listHostedZones(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listHostedZonesAsync(array $args = [])
 * @method \MageBackup\Aws\Result listHostedZonesByName(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listHostedZonesByNameAsync(array $args = [])
 * @method \MageBackup\Aws\Result listResourceRecordSets(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listResourceRecordSetsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listReusableDelegationSets(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listReusableDelegationSetsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listTagsForResource(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listTagsForResourceAsync(array $args = [])
 * @method \MageBackup\Aws\Result listTagsForResources(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listTagsForResourcesAsync(array $args = [])
 * @method \MageBackup\Aws\Result listTrafficPolicies(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listTrafficPoliciesAsync(array $args = [])
 * @method \MageBackup\Aws\Result listTrafficPolicyInstances(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listTrafficPolicyInstancesAsync(array $args = [])
 * @method \MageBackup\Aws\Result listTrafficPolicyInstancesByHostedZone(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listTrafficPolicyInstancesByHostedZoneAsync(array $args = [])
 * @method \MageBackup\Aws\Result listTrafficPolicyInstancesByPolicy(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listTrafficPolicyInstancesByPolicyAsync(array $args = [])
 * @method \MageBackup\Aws\Result listTrafficPolicyVersions(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listTrafficPolicyVersionsAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateHealthCheck(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateHealthCheckAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateHostedZoneComment(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateHostedZoneCommentAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateTrafficPolicyComment(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateTrafficPolicyCommentAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateTrafficPolicyInstance(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateTrafficPolicyInstanceAsync(array $args = [])
 */
class Route53Client extends AwsClient
{
    public function __construct(array $args)
    {
        parent::__construct($args);
        $this->getHandlerList()->appendInit($this->cleanIdFn(), 'route53.clean_id');
    }

    private function cleanIdFn()
    {
        return function (callable $handler) {
            return function (CommandInterface $c, RequestInterface $r = null) use ($handler) {
                foreach (['Id', 'HostedZoneId', 'DelegationSetId'] as $clean) {
                    if ($c->hasParam($clean)) {
                        $c[$clean] = $this->cleanId($c[$clean]);
                    }
                }
                return $handler($c, $r);
            };
        };
    }

    private function cleanId($id)
    {
        static $toClean = ['/hostedzone/', '/change/', '/delegationset/'];

        return str_replace($toClean, '', $id);
    }
}

<?php
namespace MageBackup\Aws\Route53Domains;

use MageBackup\Aws\AwsClient;

/**
 * This client is used to interact with the **Amazon Route 53 Domains** service.
 *
 * @method \MageBackup\Aws\Result checkDomainAvailability(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise checkDomainAvailabilityAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteTagsForDomain(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteTagsForDomainAsync(array $args = [])
 * @method \MageBackup\Aws\Result disableDomainAutoRenew(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise disableDomainAutoRenewAsync(array $args = [])
 * @method \MageBackup\Aws\Result disableDomainTransferLock(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise disableDomainTransferLockAsync(array $args = [])
 * @method \MageBackup\Aws\Result enableDomainAutoRenew(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise enableDomainAutoRenewAsync(array $args = [])
 * @method \MageBackup\Aws\Result enableDomainTransferLock(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise enableDomainTransferLockAsync(array $args = [])
 * @method \MageBackup\Aws\Result getContactReachabilityStatus(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getContactReachabilityStatusAsync(array $args = [])
 * @method \MageBackup\Aws\Result getDomainDetail(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getDomainDetailAsync(array $args = [])
 * @method \MageBackup\Aws\Result getOperationDetail(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getOperationDetailAsync(array $args = [])
 * @method \MageBackup\Aws\Result listDomains(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listDomainsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listOperations(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listOperationsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listTagsForDomain(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listTagsForDomainAsync(array $args = [])
 * @method \MageBackup\Aws\Result registerDomain(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise registerDomainAsync(array $args = [])
 * @method \MageBackup\Aws\Result resendContactReachabilityEmail(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise resendContactReachabilityEmailAsync(array $args = [])
 * @method \MageBackup\Aws\Result retrieveDomainAuthCode(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise retrieveDomainAuthCodeAsync(array $args = [])
 * @method \MageBackup\Aws\Result transferDomain(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise transferDomainAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateDomainContact(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateDomainContactAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateDomainContactPrivacy(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateDomainContactPrivacyAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateDomainNameservers(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateDomainNameserversAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateTagsForDomain(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateTagsForDomainAsync(array $args = [])
 */
class Route53DomainsClient extends AwsClient {}

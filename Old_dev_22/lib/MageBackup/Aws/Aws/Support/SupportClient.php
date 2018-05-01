<?php
namespace MageBackup\Aws\Support;

use MageBackup\Aws\AwsClient;

/**
 * AWS Support client.
 *
 * @method \MageBackup\Aws\Result addAttachmentsToSet(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise addAttachmentsToSetAsync(array $args = [])
 * @method \MageBackup\Aws\Result addCommunicationToCase(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise addCommunicationToCaseAsync(array $args = [])
 * @method \MageBackup\Aws\Result createCase(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createCaseAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeAttachment(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeAttachmentAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeCases(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeCasesAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeCommunications(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeCommunicationsAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeServices(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeServicesAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeSeverityLevels(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeSeverityLevelsAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeTrustedAdvisorCheckRefreshStatuses(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeTrustedAdvisorCheckRefreshStatusesAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeTrustedAdvisorCheckResult(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeTrustedAdvisorCheckResultAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeTrustedAdvisorCheckSummaries(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeTrustedAdvisorCheckSummariesAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeTrustedAdvisorChecks(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeTrustedAdvisorChecksAsync(array $args = [])
 * @method \MageBackup\Aws\Result refreshTrustedAdvisorCheck(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise refreshTrustedAdvisorCheckAsync(array $args = [])
 * @method \MageBackup\Aws\Result resolveCase(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise resolveCaseAsync(array $args = [])
 */
class SupportClient extends AwsClient {}

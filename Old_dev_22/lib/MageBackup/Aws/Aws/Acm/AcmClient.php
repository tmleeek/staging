<?php
namespace MageBackup\Aws\Acm;

use MageBackup\Aws\AwsClient;

/**
 * This client is used to interact with the **AWS Certificate Manager** service.
 *
 * @method \MageBackup\Aws\Result addTagsToCertificate(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise addTagsToCertificateAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteCertificate(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteCertificateAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeCertificate(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeCertificateAsync(array $args = [])
 * @method \MageBackup\Aws\Result getCertificate(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getCertificateAsync(array $args = [])
 * @method \MageBackup\Aws\Result listCertificates(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listCertificatesAsync(array $args = [])
 * @method \MageBackup\Aws\Result listTagsForCertificate(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listTagsForCertificateAsync(array $args = [])
 * @method \MageBackup\Aws\Result removeTagsFromCertificate(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise removeTagsFromCertificateAsync(array $args = [])
 * @method \MageBackup\Aws\Result requestCertificate(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise requestCertificateAsync(array $args = [])
 * @method \MageBackup\Aws\Result resendValidationEmail(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise resendValidationEmailAsync(array $args = [])
 */
class AcmClient extends AwsClient {}

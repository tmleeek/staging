<?php
namespace MageBackup\Aws\ImportExport;

use MageBackup\Aws\AwsClient;

/**
 * This client is used to interact with the **AWS Import/Export** service.
 * @method \MageBackup\Aws\Result cancelJob(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise cancelJobAsync(array $args = [])
 * @method \MageBackup\Aws\Result createJob(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createJobAsync(array $args = [])
 * @method \MageBackup\Aws\Result getShippingLabel(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getShippingLabelAsync(array $args = [])
 * @method \MageBackup\Aws\Result getStatus(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getStatusAsync(array $args = [])
 * @method \MageBackup\Aws\Result listJobs(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listJobsAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateJob(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateJobAsync(array $args = [])
 */
class ImportExportClient extends AwsClient {}

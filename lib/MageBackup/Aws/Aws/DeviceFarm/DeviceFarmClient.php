<?php
namespace MageBackup\Aws\DeviceFarm;

use MageBackup\Aws\AwsClient;

/**
 * This client is used to interact with the **Amazon DeviceFarm** service.
 *
 * @method \MageBackup\Aws\Result createDevicePool(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createDevicePoolAsync(array $args = [])
 * @method \MageBackup\Aws\Result createProject(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createProjectAsync(array $args = [])
 * @method \MageBackup\Aws\Result createUpload(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createUploadAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteDevicePool(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteDevicePoolAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteProject(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteProjectAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteRun(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteRunAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteUpload(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteUploadAsync(array $args = [])
 * @method \MageBackup\Aws\Result getAccountSettings(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getAccountSettingsAsync(array $args = [])
 * @method \MageBackup\Aws\Result getDevice(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getDeviceAsync(array $args = [])
 * @method \MageBackup\Aws\Result getDevicePool(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getDevicePoolAsync(array $args = [])
 * @method \MageBackup\Aws\Result getDevicePoolCompatibility(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getDevicePoolCompatibilityAsync(array $args = [])
 * @method \MageBackup\Aws\Result getJob(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getJobAsync(array $args = [])
 * @method \MageBackup\Aws\Result getOfferingStatus(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getOfferingStatusAsync(array $args = [])
 * @method \MageBackup\Aws\Result getProject(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getProjectAsync(array $args = [])
 * @method \MageBackup\Aws\Result getRun(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getRunAsync(array $args = [])
 * @method \MageBackup\Aws\Result getSuite(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getSuiteAsync(array $args = [])
 * @method \MageBackup\Aws\Result getTest(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getTestAsync(array $args = [])
 * @method \MageBackup\Aws\Result getUpload(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getUploadAsync(array $args = [])
 * @method \MageBackup\Aws\Result listArtifacts(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listArtifactsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listDevicePools(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listDevicePoolsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listDevices(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listDevicesAsync(array $args = [])
 * @method \MageBackup\Aws\Result listJobs(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listJobsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listOfferingTransactions(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listOfferingTransactionsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listOfferings(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listOfferingsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listProjects(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listProjectsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listRuns(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listRunsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listSamples(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listSamplesAsync(array $args = [])
 * @method \MageBackup\Aws\Result listSuites(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listSuitesAsync(array $args = [])
 * @method \MageBackup\Aws\Result listTests(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listTestsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listUniqueProblems(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listUniqueProblemsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listUploads(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listUploadsAsync(array $args = [])
 * @method \MageBackup\Aws\Result purchaseOffering(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise purchaseOfferingAsync(array $args = [])
 * @method \MageBackup\Aws\Result renewOffering(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise renewOfferingAsync(array $args = [])
 * @method \MageBackup\Aws\Result scheduleRun(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise scheduleRunAsync(array $args = [])
 * @method \MageBackup\Aws\Result stopRun(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise stopRunAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateDevicePool(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateDevicePoolAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateProject(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateProjectAsync(array $args = [])
 */
class DeviceFarmClient extends AwsClient {}
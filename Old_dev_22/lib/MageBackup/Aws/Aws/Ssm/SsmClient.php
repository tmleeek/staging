<?php
namespace MageBackup\Aws\Ssm;

use MageBackup\Aws\AwsClient;

/**
 * Amazon EC2 Simple Systems Manager client.
 *
 * @method \MageBackup\Aws\Result cancelCommand(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise cancelCommandAsync(array $args = [])
 * @method \MageBackup\Aws\Result createAssociation(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createAssociationAsync(array $args = [])
 * @method \MageBackup\Aws\Result createAssociationBatch(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createAssociationBatchAsync(array $args = [])
 * @method \MageBackup\Aws\Result createDocument(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createDocumentAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteAssociation(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteAssociationAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteDocument(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteDocumentAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeAssociation(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeAssociationAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeDocument(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeDocumentAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeDocumentPermission(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeDocumentPermissionAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeInstanceInformation(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeInstanceInformationAsync(array $args = [])
 * @method \MageBackup\Aws\Result getDocument(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getDocumentAsync(array $args = [])
 * @method \MageBackup\Aws\Result listAssociations(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listAssociationsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listCommandInvocations(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listCommandInvocationsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listCommands(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listCommandsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listDocuments(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listDocumentsAsync(array $args = [])
 * @method \MageBackup\Aws\Result modifyDocumentPermission(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise modifyDocumentPermissionAsync(array $args = [])
 * @method \MageBackup\Aws\Result sendCommand(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise sendCommandAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateAssociationStatus(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateAssociationStatusAsync(array $args = [])
 */
class SsmClient extends AwsClient {}

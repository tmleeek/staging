<?php
namespace MageBackup\Aws\Efs;

use MageBackup\Aws\AwsClient;

/**
 * This client is used to interact with **Amazon EFS**.
 *
 * @method \MageBackup\Aws\Result createFileSystem(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createFileSystemAsync(array $args = [])
 * @method \MageBackup\Aws\Result createMountTarget(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createMountTargetAsync(array $args = [])
 * @method \MageBackup\Aws\Result createTags(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createTagsAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteFileSystem(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteFileSystemAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteMountTarget(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteMountTargetAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteTags(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteTagsAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeFileSystems(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeFileSystemsAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeMountTargetSecurityGroups(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeMountTargetSecurityGroupsAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeMountTargets(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeMountTargetsAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeTags(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeTagsAsync(array $args = [])
 * @method \MageBackup\Aws\Result modifyMountTargetSecurityGroups(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise modifyMountTargetSecurityGroupsAsync(array $args = [])
 */
class EfsClient extends AwsClient {}

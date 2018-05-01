<?php
namespace MageBackup\Aws\WorkSpaces;

use MageBackup\Aws\AwsClient;

/**
 * Amazon WorkSpaces client.
 *
 * @method \MageBackup\Aws\Result createTags(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createTagsAsync(array $args = [])
 * @method \MageBackup\Aws\Result createWorkspaces(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createWorkspacesAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteTags(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteTagsAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeTags(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeTagsAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeWorkspaceBundles(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeWorkspaceBundlesAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeWorkspaceDirectories(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeWorkspaceDirectoriesAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeWorkspaces(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeWorkspacesAsync(array $args = [])
 * @method \MageBackup\Aws\Result rebootWorkspaces(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise rebootWorkspacesAsync(array $args = [])
 * @method \MageBackup\Aws\Result rebuildWorkspaces(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise rebuildWorkspacesAsync(array $args = [])
 * @method \MageBackup\Aws\Result terminateWorkspaces(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise terminateWorkspacesAsync(array $args = [])
 */
class WorkSpacesClient extends AwsClient {}

<?php
namespace MageBackup\Aws\CodeCommit;

use MageBackup\Aws\AwsClient;

/**
 * This client is used to interact with the **AWS CodeCommit** service.
 *
 * @method \MageBackup\Aws\Result batchGetRepositories(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise batchGetRepositoriesAsync(array $args = [])
 * @method \MageBackup\Aws\Result createBranch(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createBranchAsync(array $args = [])
 * @method \MageBackup\Aws\Result createRepository(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createRepositoryAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteRepository(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteRepositoryAsync(array $args = [])
 * @method \MageBackup\Aws\Result getBranch(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getBranchAsync(array $args = [])
 * @method \MageBackup\Aws\Result getCommit(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getCommitAsync(array $args = [])
 * @method \MageBackup\Aws\Result getRepository(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getRepositoryAsync(array $args = [])
 * @method \MageBackup\Aws\Result getRepositoryTriggers(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getRepositoryTriggersAsync(array $args = [])
 * @method \MageBackup\Aws\Result listBranches(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listBranchesAsync(array $args = [])
 * @method \MageBackup\Aws\Result listRepositories(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listRepositoriesAsync(array $args = [])
 * @method \MageBackup\Aws\Result putRepositoryTriggers(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putRepositoryTriggersAsync(array $args = [])
 * @method \MageBackup\Aws\Result testRepositoryTriggers(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise testRepositoryTriggersAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateDefaultBranch(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateDefaultBranchAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateRepositoryDescription(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateRepositoryDescriptionAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateRepositoryName(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateRepositoryNameAsync(array $args = [])
 */
class CodeCommitClient extends AwsClient {}

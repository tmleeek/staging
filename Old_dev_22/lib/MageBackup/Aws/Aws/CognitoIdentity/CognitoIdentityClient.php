<?php
namespace MageBackup\Aws\CognitoIdentity;

use MageBackup\Aws\AwsClient;

/**
 * This client is used to interact with the **Amazon Cognito Identity** service.
 *
 * @method \MageBackup\Aws\Result createIdentityPool(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createIdentityPoolAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteIdentities(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteIdentitiesAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteIdentityPool(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteIdentityPoolAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeIdentity(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeIdentityAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeIdentityPool(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeIdentityPoolAsync(array $args = [])
 * @method \MageBackup\Aws\Result getCredentialsForIdentity(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getCredentialsForIdentityAsync(array $args = [])
 * @method \MageBackup\Aws\Result getId(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getIdAsync(array $args = [])
 * @method \MageBackup\Aws\Result getIdentityPoolRoles(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getIdentityPoolRolesAsync(array $args = [])
 * @method \MageBackup\Aws\Result getOpenIdToken(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getOpenIdTokenAsync(array $args = [])
 * @method \MageBackup\Aws\Result getOpenIdTokenForDeveloperIdentity(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getOpenIdTokenForDeveloperIdentityAsync(array $args = [])
 * @method \MageBackup\Aws\Result listIdentities(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listIdentitiesAsync(array $args = [])
 * @method \MageBackup\Aws\Result listIdentityPools(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listIdentityPoolsAsync(array $args = [])
 * @method \MageBackup\Aws\Result lookupDeveloperIdentity(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise lookupDeveloperIdentityAsync(array $args = [])
 * @method \MageBackup\Aws\Result mergeDeveloperIdentities(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise mergeDeveloperIdentitiesAsync(array $args = [])
 * @method \MageBackup\Aws\Result setIdentityPoolRoles(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise setIdentityPoolRolesAsync(array $args = [])
 * @method \MageBackup\Aws\Result unlinkDeveloperIdentity(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise unlinkDeveloperIdentityAsync(array $args = [])
 * @method \MageBackup\Aws\Result unlinkIdentity(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise unlinkIdentityAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateIdentityPool(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateIdentityPoolAsync(array $args = [])
 */
class CognitoIdentityClient extends AwsClient {}

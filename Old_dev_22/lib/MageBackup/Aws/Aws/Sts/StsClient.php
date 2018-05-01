<?php
namespace MageBackup\Aws\Sts;

use MageBackup\Aws\AwsClient;
use MageBackup\Aws\Result;
use MageBackup\Aws\Credentials\Credentials;

/**
 * This client is used to interact with the **AWS Security Token Service (AWS STS)**.
 *
 * @method \MageBackup\Aws\Result assumeRole(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise assumeRoleAsync(array $args = [])
 * @method \MageBackup\Aws\Result assumeRoleWithSAML(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise assumeRoleWithSAMLAsync(array $args = [])
 * @method \MageBackup\Aws\Result assumeRoleWithWebIdentity(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise assumeRoleWithWebIdentityAsync(array $args = [])
 * @method \MageBackup\Aws\Result decodeAuthorizationMessage(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise decodeAuthorizationMessageAsync(array $args = [])
 * @method \MageBackup\Aws\Result getCallerIdentity(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getCallerIdentityAsync(array $args = [])
 * @method \MageBackup\Aws\Result getFederationToken(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getFederationTokenAsync(array $args = [])
 * @method \MageBackup\Aws\Result getSessionToken(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getSessionTokenAsync(array $args = [])
 */
class StsClient extends AwsClient
{
    /**
     * Creates credentials from the result of an STS operations
     *
     * @param Result $result Result of an STS operation
     *
     * @return Credentials
     * @throws \InvalidArgumentException if the result contains no credentials
     */
    public function createCredentials(Result $result)
    {
        if (!$result->hasKey('Credentials')) {
            throw new \InvalidArgumentException('Result contains no credentials');
        }

        $c = $result['Credentials'];

        return new Credentials(
            $c['AccessKeyId'],
            $c['SecretAccessKey'],
            isset($c['SessionToken']) ? $c['SessionToken'] : null,
            isset($c['Expiration']) && $c['Expiration'] instanceof \DateTimeInterface
                ? (int) $c['Expiration']->format('U')
                : null
        );
    }
}

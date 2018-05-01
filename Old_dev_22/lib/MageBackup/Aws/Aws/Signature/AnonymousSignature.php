<?php
namespace MageBackup\Aws\Signature;

use MageBackup\Aws\Credentials\CredentialsInterface;
use MageBackup\Psr\Http\Message\RequestInterface;

/**
 * Provides anonymous client access (does not sign requests).
 */
class AnonymousSignature implements SignatureInterface
{
    public function signRequest(
        RequestInterface $request,
        CredentialsInterface $credentials
    ) {
        return $request;
    }

    public function presign(
        RequestInterface $request,
        CredentialsInterface $credentials,
        $expires
    ) {
        return $request;
    }
}

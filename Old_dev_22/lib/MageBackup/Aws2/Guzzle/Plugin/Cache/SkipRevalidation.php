<?php

namespace MageBackup\Guzzle\Plugin\Cache;

use MageBackup\Guzzle\Http\Message\RequestInterface;
use MageBackup\Guzzle\Http\Message\Response;

/**
 * Never performs cache revalidation and just assumes the request is still ok
 */
class SkipRevalidation extends DefaultRevalidation
{
    public function __construct() {}

    public function revalidate(RequestInterface $request, Response $response)
    {
        return true;
    }
}

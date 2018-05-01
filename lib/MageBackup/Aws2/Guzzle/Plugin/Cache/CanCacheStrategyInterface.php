<?php

namespace MageBackup\Guzzle\Plugin\Cache;

use MageBackup\Guzzle\Http\Message\RequestInterface;
use MageBackup\Guzzle\Http\Message\Response;

/**
 * Strategy used to determine if a request can be cached
 */
interface CanCacheStrategyInterface
{
    /**
     * Determine if a request can be cached
     *
     * @param RequestInterface $request Request to determine
     *
     * @return bool
     */
    public function canCacheRequest(RequestInterface $request);

    /**
     * Determine if a response can be cached
     *
     * @param Response $response Response to determine
     *
     * @return bool
     */
    public function canCacheResponse(Response $response);
}

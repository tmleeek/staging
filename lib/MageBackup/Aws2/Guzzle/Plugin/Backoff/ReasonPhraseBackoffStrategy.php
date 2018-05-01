<?php

namespace MageBackup\Guzzle\Plugin\Backoff;

use MageBackup\Guzzle\Http\Message\RequestInterface;
use MageBackup\Guzzle\Http\Message\Response;
use MageBackup\Guzzle\Http\Exception\HttpException;

/**
 * Strategy used to retry HTTP requests when the response's reason phrase matches one of the registered phrases.
 */
class ReasonPhraseBackoffStrategy extends AbstractErrorCodeBackoffStrategy
{
    public function makesDecision()
    {
        return true;
    }

    protected function getDelay($retries, RequestInterface $request, Response $response = null, HttpException $e = null)
    {
        if ($response) {
            return isset($this->errorCodes[$response->getReasonPhrase()]) ? true : null;
        }
    }
}

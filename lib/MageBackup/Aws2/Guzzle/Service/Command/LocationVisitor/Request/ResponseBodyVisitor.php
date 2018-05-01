<?php

namespace MageBackup\Guzzle\Service\Command\LocationVisitor\Request;

use MageBackup\Guzzle\Http\Message\RequestInterface;
use MageBackup\Guzzle\Service\Command\CommandInterface;
use MageBackup\Guzzle\Service\Description\Parameter;

/**
 * Visitor used to change the location in which a response body is saved
 */
class ResponseBodyVisitor extends AbstractRequestVisitor
{
    public function visit(CommandInterface $command, RequestInterface $request, Parameter $param, $value)
    {
        $request->setResponseBody($value);
    }
}

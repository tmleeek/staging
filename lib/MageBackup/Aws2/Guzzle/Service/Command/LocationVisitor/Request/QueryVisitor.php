<?php

namespace MageBackup\Guzzle\Service\Command\LocationVisitor\Request;

use MageBackup\Guzzle\Http\Message\RequestInterface;
use MageBackup\Guzzle\Service\Command\CommandInterface;
use MageBackup\Guzzle\Service\Description\Parameter;

/**
 * Visitor used to apply a parameter to a request's query string
 */
class QueryVisitor extends AbstractRequestVisitor
{
    public function visit(CommandInterface $command, RequestInterface $request, Parameter $param, $value)
    {
        $request->getQuery()->set($param->getWireName(), $this->prepareValue($value, $param));
    }
}

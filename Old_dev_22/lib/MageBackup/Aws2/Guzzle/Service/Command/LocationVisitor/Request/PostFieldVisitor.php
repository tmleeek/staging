<?php

namespace MageBackup\Guzzle\Service\Command\LocationVisitor\Request;

use MageBackup\Guzzle\Http\Message\RequestInterface;
use MageBackup\Guzzle\Service\Command\CommandInterface;
use MageBackup\Guzzle\Service\Description\Parameter;

/**
 * Visitor used to apply a parameter to a POST field
 */
class PostFieldVisitor extends AbstractRequestVisitor
{
    public function visit(CommandInterface $command, RequestInterface $request, Parameter $param, $value)
    {
        $request->setPostField($param->getWireName(), $this->prepareValue($value, $param));
    }
}

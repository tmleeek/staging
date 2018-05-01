<?php

namespace MageBackup\Guzzle\Service\Command\LocationVisitor\Request;

use MageBackup\Guzzle\Http\Message\RequestInterface;
use MageBackup\Guzzle\Http\Message\PostFileInterface;
use MageBackup\Guzzle\Service\Command\CommandInterface;
use MageBackup\Guzzle\Service\Description\Parameter;

/**
 * Visitor used to apply a parameter to a POST file
 */
class PostFileVisitor extends AbstractRequestVisitor
{
    public function visit(CommandInterface $command, RequestInterface $request, Parameter $param, $value)
    {
        $value = $param->filter($value);
        if ($value instanceof PostFileInterface) {
            $request->addPostFile($value);
        } else {
            $request->addPostFile($param->getWireName(), $value);
        }
    }
}

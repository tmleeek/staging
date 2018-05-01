<?php

namespace MageBackup\Guzzle\Service\Command\LocationVisitor\Response;

use MageBackup\Guzzle\Http\Message\Response;
use MageBackup\Guzzle\Service\Description\Parameter;
use MageBackup\Guzzle\Service\Command\CommandInterface;

/**
 * Location visitor used to add the reason phrase of a response to a key in the result
 */
class ReasonPhraseVisitor extends AbstractResponseVisitor
{
    public function visit(
        CommandInterface $command,
        Response $response,
        Parameter $param,
        &$value,
        $context =  null
    ) {
        $value[$param->getName()] = $response->getReasonPhrase();
    }
}

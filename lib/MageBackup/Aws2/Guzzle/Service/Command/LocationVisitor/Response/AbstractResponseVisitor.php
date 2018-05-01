<?php

namespace MageBackup\Guzzle\Service\Command\LocationVisitor\Response;

use MageBackup\Guzzle\Service\Command\CommandInterface;
use MageBackup\Guzzle\Http\Message\Response;
use MageBackup\Guzzle\Service\Description\Parameter;

/**
 * {@inheritdoc}
 * @codeCoverageIgnore
 */
abstract class AbstractResponseVisitor implements ResponseVisitorInterface
{
    public function before(CommandInterface $command, array &$result) {}

    public function after(CommandInterface $command) {}

    public function visit(
        CommandInterface $command,
        Response $response,
        Parameter $param,
        &$value,
        $context =  null
    ) {}
}

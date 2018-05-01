<?php

namespace MageBackup\Guzzle\Service\Command;

use MageBackup\Guzzle\Http\Message\RequestInterface;
use MageBackup\Guzzle\Service\Command\CommandInterface;

/**
 * Translates command options and operation parameters into a request object
 */
interface RequestSerializerInterface
{
    /**
     * Create a request for a command
     *
     * @param CommandInterface $command Command that will own the request
     *
     * @return RequestInterface
     */
    public function prepare(CommandInterface $command);
}

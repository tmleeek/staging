<?php

namespace MageBackup\Guzzle\Plugin\ErrorResponse;

use MageBackup\Guzzle\Service\Command\CommandInterface;
use MageBackup\Guzzle\Http\Message\Response;

/**
 * Interface used to create an exception from an error response
 */
interface ErrorResponseExceptionInterface
{
    /**
     * Create an exception for a command based on a command and an error response definition
     *
     * @param CommandInterface $command  Command that was sent
     * @param Response         $response The error response
     *
     * @return self
     */
    public static function fromCommand(CommandInterface $command, Response $response);
}

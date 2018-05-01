<?php
namespace MageBackup\Aws\Api\Parser;

use MageBackup\Aws\Api\Service;
use MageBackup\Aws\CommandInterface;
use MageBackup\Aws\ResultInterface;
use MageBackup\Psr\Http\Message\ResponseInterface;

/**
 * @internal
 */
abstract class AbstractParser
{
    /** @var \MageBackup\Aws\Api\Service Representation of the service API*/
    protected $api;

    /**
     * @param Service $api Service description.
     */
    public function __construct(Service $api)
    {
        $this->api = $api;
    }

    /**
     * @param CommandInterface  $command  Command that was executed.
     * @param ResponseInterface $response Response that was received.
     *
     * @return ResultInterface
     */
    abstract public function __invoke(
        CommandInterface $command,
        ResponseInterface $response
    );
}

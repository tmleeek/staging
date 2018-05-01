<?php
namespace MageBackup\Aws\Handler\GuzzleV5;

use MageBackup\GuzzleHttp\Stream\StreamDecoratorTrait;
use MageBackup\GuzzleHttp\Stream\StreamInterface as GuzzleStreamInterface;
use MageBackup\Psr\Http\Message\StreamInterface as Psr7StreamInterface;

/**
 * Adapts a PSR-7 Stream to a Guzzle 5 Stream.
 *
 * @codeCoverageIgnore
 */
class GuzzleStream implements GuzzleStreamInterface
{
    use StreamDecoratorTrait;

    /** @var Psr7StreamInterface */
    private $stream;

    public function __construct(Psr7StreamInterface $stream)
    {
        $this->stream = $stream;
    }
}

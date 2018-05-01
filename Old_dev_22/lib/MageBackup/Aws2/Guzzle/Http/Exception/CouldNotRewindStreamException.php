<?php

namespace MageBackup\Guzzle\Http\Exception;

use MageBackup\Guzzle\Common\Exception\RuntimeException;

class CouldNotRewindStreamException extends RuntimeException implements HttpException {}

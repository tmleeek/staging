<?php

namespace MageBackup\Aws\Lambda\Exception;

use MageBackup\Aws\Common\Exception\ServiceResponseException;

/**
 * Exception thrown by the Lambda service client.
 */
class LambdaException extends ServiceResponseException {}

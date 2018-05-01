<?php
namespace MageBackup\Aws\Efs\Exception;

use MageBackup\Aws\Common\Exception\ServiceResponseException;

/**
 * Exception thrown by the Amazon Elastic File System client.
 */
class EfsException extends ServiceResponseException {}

<?php
namespace MageBackup\Aws\CloudSearchDomain;

use MageBackup\Aws\AwsClient;
use MageBackup\GuzzleHttp\Psr7\Uri;

/**
 * This client is used to search and upload documents to an **Amazon CloudSearch** Domain.
 *
 * @method \MageBackup\Aws\Result search(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise searchAsync(array $args = [])
 * @method \MageBackup\Aws\Result suggest(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise suggestAsync(array $args = [])
 * @method \MageBackup\Aws\Result uploadDocuments(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise uploadDocumentsAsync(array $args = [])
 */
class CloudSearchDomainClient extends AwsClient
{
    public static function getArguments()
    {
        $args = parent::getArguments();
        $args['endpoint']['required'] = true;
        $args['region']['default'] = function (array $args) {
            // Determine the region from the provided endpoint.
            // (e.g. http://search-blah.{region}.cloudsearch.amazonaws.com)
            return explode('.', new Uri($args['endpoint']))[1];
        };

        return $args;
    }
}

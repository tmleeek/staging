<?php

namespace MageBackup\Aws\CloudTrail;

use MageBackup\Aws\Common\Exception\InvalidArgumentException;
use MageBackup\Aws\S3\S3Client;
use MageBackup\Aws\CloudTrail\Exception\CloudTrailException;
use MageBackup\Guzzle\Iterator\FilterIterator;

/**
 * The `MageBackup\Aws\CloudTrail\LogFileIterator` provides an easy way to iterate over log file generated by AWS CloudTrail.
 * CloudTrail log files contain data about your AWS API calls and are stored in Amazon S3 at a predictable path based on
 * a bucket name, a key prefix, an account ID, a region, and date information. This class allows you to specify options,
 * including a date range, and emits each log file that match the provided options.
 *
 * @yields array An array containing the Amazon S3 bucket and key of the log file
 */
class LogFileIterator extends \IteratorIterator
{
    // For internal use
    const DEFAULT_TRAIL_NAME = 'Default';
    const PREFIX_TEMPLATE = 'prefix/AWSLogs/account/CloudTrail/region/date/';
    const PREFIX_WILDCARD = '*';

    // Option names used internally or externally
    const TRAIL_NAME = 'trail_name';
    const KEY_PREFIX = 'key_prefix';
    const START_DATE = 'start_date';
    const END_DATE = 'end_date';
    const ACCOUNT_ID = 'account_id';
    const LOG_REGION = 'log_region';

    /**
     * @var S3Client The Amazon S3 client used to perform ListObjects operations
     */
    private $s3Client;

    /**
     * @var string The name of the Amazon S3 bucket that contains the log files published by AWS CloudTrail
     */
    private $s3BucketName;

    /**
     * Constructs a LogRecordIterator. This factory method is used if the name of the S3 bucket containing your logs is
     * not known. This factory method uses a CloudTrail client and the trail name (or "Default") to find the
     * information about the trail necessary for constructing the LogRecordIterator
     *
     * @param S3Client         $s3Client
     * @param CloudTrailClient $cloudTrailClient
     * @param array            $options
     *
     * @return LogRecordIterator
     * @throws \MageBackup\Aws\Common\Exception\InvalidArgumentException
     * @see LogRecordIterator::__contruct
     */
    public static function forTrail(S3Client $s3Client, CloudTrailClient $cloudTrailClient, array $options = array())
    {
        $trailName = isset($options[self::TRAIL_NAME]) ? $options[self::TRAIL_NAME] : self::DEFAULT_TRAIL_NAME;
        $s3BucketName = null;

        // Use the CloudTrail client to get information about the trail, including the bucket name
        try {
            $result = $cloudTrailClient->describeTrails(array(
                'trailNameList' => array($trailName),
            ));
            $s3BucketName = $result->getPath('trailList/0/S3BucketName');
            $options[self::KEY_PREFIX] = $result->getPath('trailList/0/S3KeyPrefix');
        } catch (CloudTrailException $e) {
            // There was an error describing the trail
        }

        // If the bucket name is still unknown, then throw an exception
        if (!$s3BucketName) {
            $prev = isset($e) ? $e : null;
            throw new InvalidArgumentException('The bucket name could not be determined from the trail.', 0, $prev);
        }

        return new self($s3Client, $s3BucketName, $options);
    }

    /**
     * Constructs a LogFileIterator using the specified options:
     *
     * - trail_name: The name of the trail that is generating our logs. If none is provided, then "Default" will be
     *               used, since that is the name of the trail created in the AWS Management Console.
     * - key_prefix: The S3 key prefix of your log files. This value will be overwritten when using the `fromTrail()`
     *               method. However, if you are using the constructor, then this value will be used.
     * - start_date: The timestamp of the beginning of date range of the log records you want to read. You can pass this
     *               in as a `DateTime` object, integer (unix timestamp), or a string compatible with `strtotime()`.
     * - end_date:   The timestamp of the end of date range of the log records you want to read. You can pass this in as
     *               a `DateTime` object, integer (unix timestamp), or a string compatible with `strtotime()`.
     * - account_id: This is your AWS account ID, which is the 12-digit number found on the *Account Identifiers*
     *               section of the *AWS Security Credentials* page. See https://console.aws.amazon.com/iam/home?#security_credential
     * - log_region: The region of the services of the log records you want to read.
     *
     * @param S3Client $s3Client
     * @param string   $s3BucketName
     * @param array    $options
     */
    public function __construct(S3Client $s3Client, $s3BucketName, array $options = array())
    {
        $this->s3Client = $s3Client;
        $this->s3BucketName = $s3BucketName;
        parent::__construct($this->buildListObjectsIterator($options));
    }

    /**
     * An override of the typical current behavior of \IteratorIterator to format the output such that the bucket and
     * key are returned in an array
     *
     * @return array|bool
     */
    public function current()
    {
        if ($object = parent::current()) {
            return array(
                'Bucket' => $this->s3BucketName,
                'Key'    => $object['Key']
            );
        }

        return false;
    }

    /**
     * Constructs an S3 ListObjects iterator, optionally decorated with FilterIterators, based on the provided options
     *
     * @param array $options
     *
     * @return \Iterator
     */
    private function buildListObjectsIterator(array $options)
    {
        // Extract and normalize the date values from the options
        $startDate = isset($options[self::START_DATE]) ? $this->normalizeDateValue($options[self::START_DATE]) : null;
        $endDate = isset($options[self::END_DATE]) ? $this->normalizeDateValue($options[self::END_DATE]) : null;

        // Determine the parts of the key prefix of the log files being read
        $keyPrefixParts = array(
            'prefix'  => isset($options[self::KEY_PREFIX]) ? $options[self::KEY_PREFIX] : null,
            'account' => isset($options[self::ACCOUNT_ID]) ? $options[self::ACCOUNT_ID] : self::PREFIX_WILDCARD,
            'region'  => isset($options[self::LOG_REGION]) ? $options[self::LOG_REGION] : self::PREFIX_WILDCARD,
            'date'    => $this->determineDateForPrefix($startDate, $endDate),
        );

        // Determine the longest key prefix that can be used to retrieve all of the relevant log files
        $candidatePrefix = ltrim(strtr(self::PREFIX_TEMPLATE, $keyPrefixParts), '/');
        $logKeyPrefix = $candidatePrefix;
        if (($index = strpos($candidatePrefix, self::PREFIX_WILDCARD)) !== false) {
            $logKeyPrefix = substr($candidatePrefix, 0, $index);
        }

        // Create an iterator that will emit all of the objects matching the key prefix
        $objectsIterator = $this->s3Client->getListObjectsIterator(array(
            'Bucket' => $this->s3BucketName,
            'Prefix' => $logKeyPrefix,
        ));

        // Apply regex and/or date filters to the objects iterator to emit only log files matching the options
        $objectsIterator = $this->applyRegexFilter($objectsIterator, $logKeyPrefix, $candidatePrefix);
        $objectsIterator = $this->applyDateFilter($objectsIterator, $startDate, $endDate);

        return $objectsIterator;
    }

    /**
     * Normalizes a date value to a unix timestamp
     *
     * @param string|\DateTime|int $date
     *
     * @return int
     * @throws \InvalidArgumentException if the value cannot be converted to a timestamp
     */
    private function normalizeDateValue($date)
    {
        // Normalize start date to a unix timestamp
        if (is_string($date)) {
            $date = strtotime($date);
        } elseif ($date instanceof \DateTime) {
            $date = $date->format('U');
        } elseif (!is_int($date)) {
            throw new \InvalidArgumentException('Date values must be a string, an int, or a DateTime object.');
        }

        return $date;
    }

    /**
     * Uses the provided date values to determine the date portion of the prefix
     */
    private function determineDateForPrefix($startDate, $endDate)
    {
        // The default date value should look like "*/*/*" after joining
        $dateParts = array_fill_keys(array('Y', 'm', 'd'), self::PREFIX_WILDCARD);

        // Narrow down the date by replacing the WILDCARDs with values if they are the same for the start and end date
        if ($startDate && $endDate) {
            foreach ($dateParts as $key => &$value) {
                $candidateValue = date($key, $startDate);
                if ($candidateValue === date($key, $endDate)) {
                    $value = $candidateValue;
                } else {
                    break;
                }
            }
        }

        return join('/', $dateParts);
    }

    /**
     * Applies a regex iterator filter that limits the ListObjects result set based on the provided options
     *
     * @param \Iterator $objectsIterator
     * @param string    $logKeyPrefix
     * @param string    $candidatePrefix
     *
     * @return \Iterator
     */
    private function applyRegexFilter($objectsIterator, $logKeyPrefix, $candidatePrefix)
    {
        // If the prefix and candidate prefix are not the same, then there were WILDCARDs
        if ($logKeyPrefix !== $candidatePrefix) {
            // Turn the candidate prefix into a regex by trimming and converting WILDCARDs to regex notation
            $regex = rtrim($candidatePrefix, '/' . self::PREFIX_WILDCARD) . '/';
            $regex = strtr($regex, array(self::PREFIX_WILDCARD => '[^/]+'));

            // After trimming WILDCARDs or the end, if the regex is the same as the prefix, then no regex is needed
            if ($logKeyPrefix !== $regex) {
                // Apply a regex filter iterator to remove files that don't match the provided options
                $objectsIterator = new FilterIterator($objectsIterator, function ($object) use ($regex) {
                    return preg_match("#{$regex}#", $object['Key']);
                });
            }
        }

        return $objectsIterator;
    }

    /**
     * Applies an iterator filter to restrict the ListObjects result set to the specified date range
     *
     * @param \Iterator $objectsIterator
     * @param int       $startDate
     * @param int       $endDate
     *
     * @return \Iterator
     */
    private function applyDateFilter($objectsIterator, $startDate, $endDate)
    {
        // If either a start or end date was provided, filter out dates that don't match the date range
        if ($startDate || $endDate) {
            $objectsIterator = new FilterIterator($objectsIterator, function ($object) use ($startDate, $endDate) {
                if (preg_match('/[0-9]{8}T[0-9]{4}Z/', $object['Key'], $matches)) {
                    $date = strtotime($matches[0]);
                    if ((!$startDate || $date >= $startDate) && (!$endDate || $date <= $endDate)) {
                        return true;
                    }
                }
                return false;
            });
        }

        return $objectsIterator;
    }
}

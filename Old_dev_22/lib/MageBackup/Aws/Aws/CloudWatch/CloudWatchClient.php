<?php
namespace MageBackup\Aws\CloudWatch;

use MageBackup\Aws\AwsClient;

/**
 * This client is used to interact with the **Amazon CloudWatch** service.
 *
 * @method \MageBackup\Aws\Result deleteAlarms(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteAlarmsAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeAlarmHistory(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeAlarmHistoryAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeAlarms(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeAlarmsAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeAlarmsForMetric(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeAlarmsForMetricAsync(array $args = [])
 * @method \MageBackup\Aws\Result disableAlarmActions(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise disableAlarmActionsAsync(array $args = [])
 * @method \MageBackup\Aws\Result enableAlarmActions(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise enableAlarmActionsAsync(array $args = [])
 * @method \MageBackup\Aws\Result getMetricStatistics(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getMetricStatisticsAsync(array $args = [])
 * @method \MageBackup\Aws\Result listMetrics(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise listMetricsAsync(array $args = [])
 * @method \MageBackup\Aws\Result putMetricAlarm(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putMetricAlarmAsync(array $args = [])
 * @method \MageBackup\Aws\Result putMetricData(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise putMetricDataAsync(array $args = [])
 * @method \MageBackup\Aws\Result setAlarmState(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise setAlarmStateAsync(array $args = [])
 */
class CloudWatchClient extends AwsClient {}

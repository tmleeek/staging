<?php
namespace MageBackup\Aws\MachineLearning;

use MageBackup\Aws\AwsClient;
use MageBackup\Aws\CommandInterface;
use MageBackup\GuzzleHttp\Psr7\Uri;
use MageBackup\Psr\Http\Message\RequestInterface;

/**
 * Amazon Machine Learning client.
 *
 * @method \MageBackup\Aws\Result createBatchPrediction(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createBatchPredictionAsync(array $args = [])
 * @method \MageBackup\Aws\Result createDataSourceFromRDS(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createDataSourceFromRDSAsync(array $args = [])
 * @method \MageBackup\Aws\Result createDataSourceFromRedshift(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createDataSourceFromRedshiftAsync(array $args = [])
 * @method \MageBackup\Aws\Result createDataSourceFromS3(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createDataSourceFromS3Async(array $args = [])
 * @method \MageBackup\Aws\Result createEvaluation(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createEvaluationAsync(array $args = [])
 * @method \MageBackup\Aws\Result createMLModel(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createMLModelAsync(array $args = [])
 * @method \MageBackup\Aws\Result createRealtimeEndpoint(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createRealtimeEndpointAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteBatchPrediction(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteBatchPredictionAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteDataSource(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteDataSourceAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteEvaluation(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteEvaluationAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteMLModel(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteMLModelAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteRealtimeEndpoint(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteRealtimeEndpointAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeBatchPredictions(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeBatchPredictionsAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeDataSources(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeDataSourcesAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeEvaluations(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeEvaluationsAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeMLModels(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeMLModelsAsync(array $args = [])
 * @method \MageBackup\Aws\Result getBatchPrediction(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getBatchPredictionAsync(array $args = [])
 * @method \MageBackup\Aws\Result getDataSource(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getDataSourceAsync(array $args = [])
 * @method \MageBackup\Aws\Result getEvaluation(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getEvaluationAsync(array $args = [])
 * @method \MageBackup\Aws\Result getMLModel(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise getMLModelAsync(array $args = [])
 * @method \MageBackup\Aws\Result predict(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise predictAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateBatchPrediction(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateBatchPredictionAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateDataSource(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateDataSourceAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateEvaluation(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateEvaluationAsync(array $args = [])
 * @method \MageBackup\Aws\Result updateMLModel(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise updateMLModelAsync(array $args = [])
 */
class MachineLearningClient extends AwsClient
{
    public function __construct(array $config)
    {
        parent::__construct($config);
        $list = $this->getHandlerList();
        $list->appendBuild($this->predictEndpoint(), 'ml.predict_endpoint');
    }

    /**
     * Changes the endpoint of the Predict operation to the provided endpoint.
     *
     * @return callable
     */
    private function predictEndpoint()
    {
        return static function (callable $handler) {
            return function (
                CommandInterface $command,
                RequestInterface $request = null
            ) use ($handler) {
                if ($command->getName() === 'Predict') {
                    $request = $request->withUri(new Uri($command['PredictEndpoint']));
                }
                return $handler($command, $request);
            };
        };
    }
}

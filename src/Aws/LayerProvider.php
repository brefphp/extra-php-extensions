<?php declare(strict_types=1);

namespace Bref\Extra\Aws;

use Aws\Lambda\LambdaClient;

/**
 * Fetches layers and details from AWS
 */
class LayerProvider
{
    /** @var string  */
    private $awsId;

    /** @var array */
    private $layerNames;

    /**
     * @param array  $layerNames the name of the layers to list.
     * @param string $awsId      The account id
     */
    public function __construct(array $layerNames, string $awsId)
    {
        $this->awsId = $awsId;
        $this->layerNames = $layerNames;
    }


    public function listLayers(string $selectedRegion): array
    {
        $lambda = new LambdaClient([
            'version' => 'latest',
            'region' => $selectedRegion,
        ]);

        $accountId = $this->awsId;
        // Run the API calls in parallel (thanks to async)
        $promises = array_combine($this->layerNames, array_map(function (string $layerName) use ($lambda, $selectedRegion, $accountId) {
            return $lambda->listLayerVersionsAsync([
                'LayerName' => "arn:aws:lambda:$selectedRegion:$accountId:layer:$layerName",
                'MaxItems' => 1,
            ]);
        }, $this->layerNames));

        // Wait on all of the requests to complete. Throws a ConnectException
        // if any of the requests fail
        $results = unwrap($promises);

        $layers = [];
        foreach ($results as $layerName => $result) {
            $versions = $result['LayerVersions'];
            $latestVersion = end($versions);
            $layers[$layerName] = $latestVersion['Version'];
        }

        return $layers;
    }
}

<?php declare(strict_types=1);

namespace Bref\Extra\Aws;

use AsyncAws\Lambda\LambdaClient;
use AsyncAws\Lambda\ValueObject\LayerVersionsListItem;

/**
 * Fetches layers and details from AWS
 */
class LayerProvider
{
    /** @var string  */
    private $awsId;

    /** @var array */
    private $layerNames;

    /** @var LambdaClient */
    private $lambda;

    /**
     * @param array  $layerNames the name of the layers to list.
     * @param string $awsId      The account id
     */
    public function __construct(LambdaClient $lambda, array $layerNames, string $awsId)
    {
        $this->awsId = $awsId;
        $this->layerNames = $layerNames;
        $this->lambda = $lambda;
    }

    public function listLayers(string $selectedRegion): array
    {
        // Run the API calls in parallel (thanks to async)
        $results = [];
        foreach ($this->layerNames as $layerName) {
            $results[$layerName] = $this->lambda->listLayerVersions([
                '@region' => $selectedRegion,
                'LayerName' => sprintf('arn:aws:lambda:%s:%s:layer:%s', $selectedRegion, $this->awsId, $layerName),
                'MaxItems' => 1,
            ]);
        }

        $layers = [];
        foreach ($results as $layerName => $result) {
            $versions = $result->getLayerVersions(true);
            $versionsArray = iterator_to_array($versions);
            if (! empty($versionsArray)) {
                /** @var LayerVersionsListItem $latestVersion */
                $latestVersion = end($versionsArray);
                $layers[$layerName] = (int) $latestVersion->getVersion();
            }
        }

        return $layers;
    }
}

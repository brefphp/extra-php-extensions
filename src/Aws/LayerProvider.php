<?php declare(strict_types=1);

namespace Bref\Extra\Aws;

use AsyncAws\Core\Result;
use AsyncAws\Lambda\LambdaClient;
use AsyncAws\Lambda\ValueObject\LayerVersionsListItem;

/**
 * Fetches layers and details from AWS
 */
class LayerProvider
{
    /** @var array */
    private $layerNames;

    /** @var LambdaClient */
    private $lambda;

    /**
     * @param array $layerNames the name of the layers to list.
     */
    public function __construct(LambdaClient $lambda, array $layerNames)
    {
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
                'LayerName' => $layerName,
                'MaxItems' => 1,
            ]);
        }

        $layers = [];
        foreach (Result::wait($results, null, true) as $result) {
            $versions = $result->getLayerVersions(true);
            $versionsArray = iterator_to_array($versions);
            if (! empty($versionsArray)) {
                /** @var LayerVersionsListItem $latestVersion */
                $latestVersion = end($versionsArray);
                $layers[$latestVersion->getDescription()] = (int) $latestVersion->getVersion();
            }
        }

        ksort($layers);

        return $layers;
    }
}

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
    private const CHUNK_SIZE = 1;
    private const SLEEP = 2;

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
        $layers = [];
        foreach (array_chunk($this->layerNames, self::CHUNK_SIZE) as $chunkLayers) {
            $results = [];
            foreach ($chunkLayers as $layerName) {
                $results[$layerName] = $this->lambda->listLayerVersions([
                    '@region' => $selectedRegion,
                    'LayerName' => $layerName,
                    'MaxItems' => 1,
                ]);
            }

            foreach (Result::wait($results, null, true) as $result) {
                $versions = $result->getLayerVersions(true);
                $versionsArray = iterator_to_array($versions);
                if (! empty($versionsArray)) {
                    /** @var LayerVersionsListItem $latestVersion */
                    $latestVersion = end($versionsArray);
                    $layers[$latestVersion->getDescription()] = (int) $latestVersion->getVersion();
                }
                echo '.';
            }

            sleep(self::SLEEP);
        }

        ksort($layers);

        return $layers;
    }
}

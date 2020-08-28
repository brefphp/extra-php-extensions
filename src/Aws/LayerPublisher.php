<?php declare(strict_types=1);

namespace Bref\Extra\Aws;

use AsyncAws\Core\Result;
use AsyncAws\Lambda\Enum\Runtime;
use AsyncAws\Lambda\LambdaClient;
use AsyncAws\Lambda\ValueObject\LayerVersionContentInput;

class LayerPublisher
{
    /** @var LambdaClient */
    private $lambda;

    public function __construct(LambdaClient $lambda)
    {
        $this->lambda = $lambda;
    }

    /**
     * @param array<string, string> $layers  Layer name and layer zip file path.
     * @param array                 $regions
     */
    public function publishLayers(array $layers, array $regions): void
    {
        foreach ($regions as $region) {
            echo $region . PHP_EOL;
            $versions = $this->uploadLayers($layers, $region);
            $this->makeLayersPublic($layers, $region, $versions);
            echo PHP_EOL;
        }
    }

    private function uploadLayers(array $layers, string $region): array
    {
        $results = [];
        foreach ($layers as $layerName => $layerFilePath) {
            $results[] = $this->lambda->publishLayerVersion([
                '@region' => $region,
                'LayerName' => $layerName,
                'Description' => $layerName,
                'LicenseInfo' => 'MIT',
                'CompatibleRuntimes' => [Runtime::PROVIDED],
                'Content' => new LayerVersionContentInput(['ZipFile' => file_get_contents($layerFilePath)]),
            ]);
        }

        $versions = [];
        foreach (Result::wait($results, null, true) as $result) {
            $versions[$region . $result->getDescription()] = $result->getVersion();
        }

        return $versions;
    }

    private function makeLayersPublic(array $layers, string $region, array $versions): void
    {
        $results = [];
        foreach ($layers as $layerName => $layerFilePath) {
            $layerVersion = $versions[$region . $layerName];

            $results[] = $this->lambda->addLayerVersionPermission([
                '@region' => $region,
                'LayerName' => $layerName,
                'VersionNumber' => $layerVersion,
                'StatementId' => 'public',
                'Action' => 'lambda:GetLayerVersion',
                'Principal' => '*',
            ]);
        }

        foreach (Result::wait($results) as $result) {
            echo '.';
        }
    }
}

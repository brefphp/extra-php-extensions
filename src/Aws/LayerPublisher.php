<?php declare(strict_types=1);

namespace Bref\Extra\Aws;

use AsyncAws\Core\Result;
use AsyncAws\Lambda\Enum\Runtime;
use AsyncAws\Lambda\LambdaClient;
use AsyncAws\Lambda\ValueObject\LayerVersionContentInput;

class LayerPublisher
{
    /**
     * @var LambdaClient
     */
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
        $versions = [];
        foreach ($regions as $region) {
            $result = [];
            foreach ($layers as $layerName => $layerFilePath) {
                $result[] = $this->lambda->publishLayerVersion([
                    '@region' => $region,
                    'LayerName' => $layerName,
                    'Description' => $layerName,
                    'LicenseInfo' => 'MIT',
                    'CompatibleRuntimes' => [Runtime::PROVIDED],
                    'Content' => new LayerVersionContentInput(['ZipFile' => file_get_contents($layerFilePath)]),
                ]);
            }

            foreach (Result::wait($result, null, true) as $result) {
                $versions[$region.$result->getDescription()] = $result->getVersion();
            }
        }

        // Add public permissions on the layers
        foreach ($regions as $region) {
            $result = [];
            foreach ($layers as $layerName => $layerFilePath) {
                $layerVersion = $versions[$region . $layerName];

                $result[] = $this->lambda->addLayerVersionPermission([
                    '@region' => $region,
                    'LayerName' => $layerName,
                    'VersionNumber' => $layerVersion,
                    'StatementId' => 'public',
                    'Action' => 'lambda:GetLayerVersion',
                    'Principal' => '*'
                ]);
            }

            foreach (Result::wait($result) as $result) {
                echo '.';
            }
        }
    }
}

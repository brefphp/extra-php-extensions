<?php

declare(strict_types=1);

namespace Bref\Extra\Command;

use Bref\Extra\Aws\LayerProvider;
use Bref\Extra\Service\RegionProvider;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This script updates `layers.json` at the root of the project.
 *
 * `layers.json` contains the layer versions that Bref should use.
 */
class ListCommand
{
    /**
     * @var LayerProvider
     */
    private $provider;


    /**
     * @var string
     */
    private $projectDir;
    /**
     * @var RegionProvider
     */
    private $regionProvider;

    public function __construct(LayerProvider $provider, RegionProvider $regionProvider, string $projectDir)
    {
        $this->provider = $provider;
        $this->projectDir = $projectDir;
        $this->regionProvider = $regionProvider;
    }

    public function __invoke(OutputInterface $output)
    {
        $export = [];
        foreach ($this->regionProvider->getAll() as $region) {
            $layers = $this->provider->listLayers($region);
            foreach ($layers as $layerName => $version) {
                $export[$layerName][$region] = $version;
            }

            $output->writeln($region);
        }
        file_put_contents($this->projectDir . '/layers.json', json_encode($export, JSON_PRETTY_PRINT));

        return 0;
    }
}
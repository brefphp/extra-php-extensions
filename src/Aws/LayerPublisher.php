<?php

declare(strict_types=1);

namespace Bref\Extra\Aws;

use Symfony\Component\Process\Process;

class LayerPublisher
{
    /**
     * @param array $layers
     * @param array $regions
     */
    public function publishLayers(array $layers, array $regions): void
    {
        /** @var Process[] $publishingProcesses */
        $publishingProcesses = [];
        foreach ($regions as $region) {
            foreach ($layers as $layer) {
                $publishingProcesses[$region.$layer] = $this->publishSingleLayer($region, $layer);
            }
        }
        $this->finishProcesses($publishingProcesses);


        // Add public permissions on the layers
        /** @var Process[] $permissionProcesses */
        $permissionProcesses = [];
        foreach ($regions as $region) {
            foreach ($layers as $layer) {
                $publishLayer = $publishingProcesses[$region . $layer];
                $layerVersion = trim($publishLayer->getOutput());

                $permissionProcesses[] = $this->addPublicLayerPermissions($region, $layer, $layerVersion);
            }
        }
        $this->finishProcesses($permissionProcesses);
    }

    /**
     * @param string $region The AWS region to publish the layer to
     * @param string $layer The file path to the layer relative ???
     * @return Process
     */
    private function publishSingleLayer(string $region, string $layer): Process
    {
        $file = __DIR__ . "/export/$layer.zip";

        $process = new Process([
            'aws',
            'lambda',
            'publish-layer-version',
            '--region',
            $region,
            '--layer-name',
            $layer,
            '--description',
            $layer,
            '--license-info',
            'MIT',
            '--zip-file',
            'fileb://' . $file,
            '--compatible-runtimes',
            'provided',
            // Output the version so that we can fetch it and use it
            '--output',
            'text',
            '--query',
            'Version',
        ]);
        $process->setTimeout(null);

        return $process;
    }

    /**
     * @param Process[] $processes
     */
    private function finishProcesses(array $processes): void
    {
        // Run the processes in batches to parallelize them without overloading the machine and the network
        foreach (array_chunk($processes, 4) as $batch) {
            // Start all the processes
            array_map(function (Process $process): void {
                $process->start();
            }, $batch);
            // Wait for them to finish
            array_map(function (Process $process): void {
                $status = $process->wait();
                echo '.';
                // Make sure the process ran successfully
                if ($status !== 0) {
                    echo 'Process ' . $process->getCommandLine() . ' failed:' . PHP_EOL;
                    echo $process->getErrorOutput();
                    echo $process->getOutput();
                    exit(1);
                }
            }, $batch);
        }
    }

    /**
     * @param string $region
     * @param string $layer name
     * @param string $layerVersion
     * @return Process
     */
    private function addPublicLayerPermissions(string $region, string $layer, string $layerVersion): Process
    {
        $process = new Process([
            'aws',
            'lambda',
            'add-layer-version-permission',
            '--region',
            $region,
            '--layer-name',
            $layer,
            '--version-number',
            $layerVersion,
            '--statement-id',
            'public',
            '--action',
            'lambda:GetLayerVersion',
            '--principal',
            '*',
        ]);
        $process->setTimeout(null);

        return $process;
    }



}
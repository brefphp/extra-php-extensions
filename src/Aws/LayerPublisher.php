<?php declare(strict_types=1);

namespace Bref\Extra\Aws;

use Symfony\Component\Process\Process;

class LayerPublisher
{
    /** @var string|null */
    private $awsProfile;

    public function __construct(?string $awsProfile)
    {
        $this->awsProfile = $awsProfile;
    }


    /**
     * @param array<string, string> $layers  Layer name and layer zip file path.
     * @param array                 $regions
     */
    public function publishLayers(array $layers, array $regions): void
    {
        /** @var Process[] $publishingProcesses */
        $publishingProcesses = [];
        foreach ($regions as $region) {
            foreach ($layers as $layerName => $layerFilePath) {
                $publishingProcesses[$region . $layerName] = $this->publishSingleLayer($region, $layerName, $layerFilePath);
            }
        }
        $this->finishProcesses($publishingProcesses);

        // Add public permissions on the layers
        /** @var Process[] $permissionProcesses */
        $permissionProcesses = [];
        foreach ($regions as $region) {
            foreach ($layers as $layerName => $layerFilePath) {
                $publishLayer = $publishingProcesses[$region . $layerName];
                $layerVersion = trim($publishLayer->getOutput());

                $permissionProcesses[] = $this->addPublicLayerPermissions($region, $layerName, $layerVersion);
            }
        }
        $this->finishProcesses($permissionProcesses);
    }

    /**
     * @param string $region The AWS region to publish the layer to
     * @param string $file   The absolute file path to the layer
     */
    private function publishSingleLayer(string $region, string $layerName, string $file): Process
    {
        $args = [
            'aws',
            'lambda',
            'publish-layer-version',
            '--region',
            $region,
            '--layer-name',
            $layerName,
            '--description',
            $layerName,
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
        ];

        if ($this->awsProfile !== null) {
            $args[] = '--profile';
            $args[] = $this->awsProfile;
        }

        $process = new Process($args);
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
     * @param string $layer name
     */
    private function addPublicLayerPermissions(string $region, string $layer, string $layerVersion): Process
    {
        $args = [
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
        ];

        if ($this->awsProfile !== null) {
            $args[] = '--profile';
            $args[] = $this->awsProfile;
        }

        $process = new Process($args);
        $process->setTimeout(null);

        return $process;
    }
}

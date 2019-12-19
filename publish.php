<?php declare(strict_types=1);

/**
 * This script publishes all the layers in all the regions.
 */

use Symfony\Component\Process\Process;
use Symfony\Component\Finder\Finder;

require_once __DIR__ . '/vendor/autoload.php';

/**
 * These are the regions on which the layers are published.
 */
$regions = json_decode(file_get_contents('https://raw.githubusercontent.com/brefphp/bref/master/runtime/layers/regions.json'), true);

$checksums = file_get_contents(__DIR__.'/export/checksums');
$discoveredChecksums = [];

$layers = [];
$finder = new Finder();
$finder->in(__DIR__.'/export')
    ->name('layer-*');
foreach ($finder->files() as $file) {
    /** @var SplFileInfo $file */
    $layer = $file->getFilenameWithoutExtension();
    $md5 = md5_file($file->getRealPath());
    $discoveredChecksums[] = $md5;
    if (false === strstr($checksums, $md5)) {
        // This layer is new.
        $layers[] = $layer;
    }
}
file_put_contents(__DIR__.'/export/checksums', implode("\n", $discoveredChecksums));

// Publish the layers
/** @var Process[] $publishingProcesses */
$publishingProcesses = [];
foreach ($regions as $region) {
    foreach ($layers as $layer) {
        $publishingProcesses[$region.$layer] = publishLayer($region, $layer);
    }
}
runProcessesInParallel($publishingProcesses);
echo sprintf("\n%d layers are published, adding permissions now\n", count($layers));

// Add public permissions on the layers
/** @var Process[] $permissionProcesses */
$permissionProcesses = [];
foreach ($regions as $region) {
    foreach ($layers as $layer) {
        $publishLayer = $publishingProcesses[$region . $layer];
        $layerVersion = trim($publishLayer->getOutput());

        $permissionProcesses[] = addPublicLayerPermissions($region, $layer, $layerVersion);
    }
}
runProcessesInParallel($permissionProcesses);

// Dump checksums
file_put_contents(__DIR__.'/export/checksums', implode("\n", $discoveredChecksums));
echo "\nDone\n";
echo "Remember to commit and push changes to export/checksums\n";

function publishLayer(string $region, string $layer): Process
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
function runProcessesInParallel(array $processes): void
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

function addPublicLayerPermissions(string $region, string $layer, string $layerVersion): Process
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

<?php declare(strict_types=1);

namespace Bref\Extra\Command;

use Bref\Extra\Aws\LayerPublisher;
use Bref\Extra\Service\RegionProvider;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

/**
 * This script publishes all the layers in all the regions.
 */
class PublishCommand
{
    /** @var LayerPublisher */
    private $publisher;
    /** @var string */
    private $projectDir;
    /** @var RegionProvider */
    private $regionProvider;

    public function __construct(LayerPublisher $publisher, RegionProvider $regionProvider, string $projectDir)
    {
        $this->publisher = $publisher;
        $this->projectDir = $projectDir;
        $this->regionProvider = $regionProvider;
    }

    public function __invoke(OutputInterface $output): int
    {
        $checksums = file_get_contents($this->projectDir . '/checksums.json');
        $discoveredChecksums = [];

        $layers = [];
        $finder = new Finder;
        $finder->in($this->projectDir . '/export')->name('layer-*');
        foreach ($finder->files() as $file) {
            /** @var \SplFileInfo $file */
            $layerFile = $file->getRealPath();
            $layerName = substr($file->getFilenameWithoutExtension(), 6);
            $md5 = md5_file($layerFile);
            $discoveredChecksums[$layerName] = $md5;
            if (strstr($checksums, $md5) === false) {
                // This layer is new.
                $layers[$layerName] = $layerFile;
            }
        }
        $output->writeln(sprintf('Found %d new layers', count($layers)));
        foreach ($layers as $layer => $file) {
            $output->writeln('- ' . $layer);
        }
        $output->writeln('');
        $output->writeln('Publishing new layers:');

        try {
            $this->publisher->publishLayers($layers, $this->regionProvider->getAll());
        } catch (\Throwable $e) {
            // TODO write output.
            exit(1);
        }

        ksort($discoveredChecksums);
        // Dump checksums
        file_put_contents($this->projectDir . '/checksums.json', json_encode($discoveredChecksums, \JSON_PRETTY_PRINT));

        $output->writeln('');
        $output->writeln('');
        $output->writeln('Done');
        $output->writeln('Remember to commit and push changes to ./checksums.json');

        return 0;
    }
}

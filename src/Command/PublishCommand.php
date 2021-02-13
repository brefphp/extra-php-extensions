<?php declare(strict_types=1);

namespace Bref\Extra\Command;

use AsyncAws\Core\Exception\Exception as AsyncAwsException;
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
        $layers = [];
        $finder = new Finder;
        $finder->in($this->projectDir . '/export')->name('layer-*');
        foreach ($finder->files() as $file) {
            /** @var \SplFileInfo $file */
            $layerFile = $file->getRealPath();
            $layerName = substr($file->getFilenameWithoutExtension(), 6);
            $layers[$layerName] = $layerFile;
        }

        ksort($layers);
        $output->writeln(sprintf('Found %d new layers and %d layers with the same checksum. They new layers are:', count($layers), $sameChecksumCount));
        foreach ($layers as $layer => $file) {
            $output->writeln('- ' . $layer);
        }
        $output->writeln('');
        $output->writeln('Publishing new layers:');

        try {
            $this->publisher->publishLayers($layers, $this->regionProvider->getAll());
        } catch (AsyncAwsException $e) {
            $output->writeln($e->getMessage());

            exit(1);
        } catch (\Throwable $e) {
            // TODO write output.
            exit(1);
        }

        $output->writeln('');
        $output->writeln('');
        $output->writeln('Done');

        return 0;
    }
}

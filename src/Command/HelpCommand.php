<?php

declare(strict_types=1);

namespace Bref\Extra\Command;

use Bref\Extra\Aws\LayerPublisher;
use Bref\Extra\Service\RegionProvider;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class HelpCommand
{
    public function __invoke(OutputInterface $output)
    {
        $output->writeln('With this small application you may publish new layers and list existing ones in layer.json');

        return 0;
    }
}
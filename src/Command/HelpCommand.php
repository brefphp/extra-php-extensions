<?php declare(strict_types=1);

namespace Bref\Extra\Command;

use Symfony\Component\Console\Output\OutputInterface;

class HelpCommand
{
    public function __invoke(OutputInterface $output): int
    {
        $output->writeln('With this small application you may publish new layers and list existing ones in layer.json');

        return 0;
    }
}

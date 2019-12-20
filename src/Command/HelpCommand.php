<?php declare(strict_types=1);

namespace Bref\Extra\Command;

use Symfony\Component\Console\Output\OutputInterface;

class HelpCommand
{
    public function __invoke(OutputInterface $output): int
    {
        $output->writeln('With this small application you may publish new layers and list existing ones in layer.json');
        $output->writeln('You may specify the following environment variables: AWS_ID, AWS_PROFILE');

        return 0;
    }
}

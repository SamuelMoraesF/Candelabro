<?php

namespace Candelabro\Commands;

use Candelabro\Traits\HasUserConfig;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'config:clear', description: 'Remove local configuration')]
class ClearConfig extends Command
{
    use HasUserConfig;

    protected function configure(): void
    {
        $this->setHelp('This command helps you to remove the local configuration.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        unlink($this->getConfigPath());

        $output->writeln('Local configuration removed.');

        return Command::SUCCESS;
    }
}

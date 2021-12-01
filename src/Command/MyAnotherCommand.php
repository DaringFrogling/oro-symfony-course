<?php

namespace App\Command;

use Symfony\Component\Console\{Command\Command, Input\InputArgument, Input\InputInterface, Output\OutputInterface};

class MyAnotherCommand extends Command implements ChainableCommandInterface
{
    protected static $defaultName = 'my:another:command';

    protected function configure(): void
    {
        $this->addArgument(
            'chain',
            InputArgument::OPTIONAL,
            '',
            'not_in'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Hello from '.$this->getName());

        return Command::SUCCESS;
    }
}

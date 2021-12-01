<?php

namespace App\EventListener\Console;

use App\Command\ChainableCommandInterface;
use App\Services\QueueManagers\QueueManagerInterface;
use Symfony\Component\Console\{Command\LockableTrait,
    Event\ConsoleCommandEvent,
    Input\ArrayInput,
    Output\ConsoleOutput};

class BeforeCommandExecuteEventListener
{
    use LockableTrait;

    public function __construct(
        private QueueManagerInterface $queueManager,
    ) {
    }

    public function __invoke(ConsoleCommandEvent $event): void
    {
        $command = $event->getCommand();

        if (!$command instanceof ChainableCommandInterface) {
            return;
        }

        $rootCommand = $this->queueManager->findFirstElement();
        $isRootCommand = $rootCommand === $command->getName();
        $this->lock(get_class($this));

        if ($this->queueManager->queueExists() && !$this->queueManager->isQueueEmpty()) {
            $queue = $this->queueManager->getQueue();
            if (in_array($command->getName(), $queue) && !$isRootCommand) {
                $event->getInput()->setArgument('chain', 'chained');
            }
        }

        if ($event->getInput()->getArgument('chain') === 'chained') {
            $event->getOutput()->writeln(
                sprintf(
                    "Error: %s command is a member of %s command chain and cannot be executed on its own.",
                    $command->getName(),
                    $rootCommand
                )
            );
            $event->disableCommand();

            return;
        }

        if (
            $this->queueManager->queueExists()
            && !$this->queueManager->isQueueEmpty()
            && $isRootCommand
        ) {
            $commandQueue = $this->queueManager->getQueue();
            foreach ($commandQueue as $element) {
                $nextCommand = $command->getApplication()->find($element);
                $nextCommand->run(
                    new ArrayInput([
                        'command' => $element,
                        'chain' => 'in_chain',
                    ]),
                    new ConsoleOutput()
                );
            }
            $this->queueManager->clear();
            $event->disableCommand();
        }
    }
}
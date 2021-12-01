<?php

namespace App\EventListener\Console;

use App\Command\ChainableCommandInterface;
use App\Services\QueueManagers\QueueManagerInterface;
use Symfony\Component\Console\{Command\LockableTrait, Event\ConsoleTerminateEvent};

class AfterCommandExecuteEventListener
{
    use LockableTrait;

    public function __construct(
        private QueueManagerInterface $queueManager
    ) { }

    public function __invoke(ConsoleTerminateEvent $event): void
    {
        $command = $event->getCommand();

        if (!$command instanceof ChainableCommandInterface) {
            return;
        }

        if ($event->getExitCode() == 113) {
            return;
        }

        $this->queueManager->enqueue($command);
    }
}
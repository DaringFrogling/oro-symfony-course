<?php

namespace App\Services\QueueManagers;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;

class TraceableQueueManager implements QueueManagerInterface
{
    /**
     * TraceableQueueManager constructor.
     * @param QueueManager $originalQueueManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        private QueueManager $originalQueueManager,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function enqueue(Command $command): void
    {
        if ($this->queueExists()) {
            $this->originalQueueManager->enqueue($command);
            $date = date("Y-m-d H:i:s");

            if ($this->isQueueEmpty()) {
                $this->logger->info(
                    sprintf(
                        "[%d], %s is a master command of a command chain that has registered member commands",
                        $date,
                        $command->getName()
                    )
                );
            } else {
                $queue = $this->getQueue();
                $rootCommand = array_shift($queue);

                $this->logger->info(
                    sprintf(
                        "[%d], %s registered as a member of %s command chain",
                        $date,
                        $command->getName(),
                        $rootCommand
                    )
                );
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getQueue(): array
    {
        return $this->originalQueueManager->getQueue();
    }

    public function findFirstElement(): ?string
    {
        return $this->originalQueueManager->findFirstElement();
    }

    /**
     * @inheritDoc
     */
    public function queueExists(): bool
    {
        return $this->originalQueueManager->queueExists();
    }

    /**
     * @inheritDoc
     */
    public function isQueueEmpty(): bool
    {
        return $this->originalQueueManager->isQueueEmpty();
    }

    public function clear(): void
    {
        $this->originalQueueManager->clear();
    }
}
<?php

namespace App\Services\QueueManagers;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class QueueManager implements QueueManagerInterface
{
    /**
     * QueueManager constructor.
     * @param string $queue
     */
    public function __construct(
        private string $queue
    ) {
    }

    /**
     * @inheritDoc
     */
    public function enqueue(Command $command): void
    {
        file_put_contents(
            $this->queue,
            $command->getName().PHP_EOL,
            FILE_APPEND | LOCK_EX
        );
    }

    /**
     * @inheritDoc
     */
    public function getQueue(): array
    {
        if (!$this->queueExists()) {
            throw new NotFoundHttpException("Queue doesn't exist");
        }

        $stack = explode(PHP_EOL, file_get_contents($this->queue));

        return array_filter($stack, fn(mixed $item) => $item != false);
    }

    /**
     * @return string|null
     */
    public function findFirstElement(): ?string
    {
        $queue = $this->getQueue();

        if ($this->isQueueEmpty()) {
            return null;
        }

        return $queue[0];
    }

    /**
     * @inheritDoc
     */
    public function queueExists(): bool
    {
        return file_exists($this->queue);
    }

    /**
     * @inheritDoc
     */
    public function isQueueEmpty(): bool
    {
        return empty(file_get_contents($this->queue));
    }

    public function clear(): void
    {
        file_put_contents(
            $this->queue,
            '',
        );
    }
}
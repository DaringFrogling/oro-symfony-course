<?php

namespace App\Services\QueueManagers;

use Symfony\Component\Console\Command\Command;

interface QueueManagerInterface
{
    /**
     * @param Command $command
     */
    public function enqueue(Command $command) : void;

    /**
     * @return array
     */
    public function dequeue() : array;

    /**
     * @return array
     */
    public function getQueue() : array;

    /**
     * @return string|null
     */
    public function findFirstElement() : ?string;

    /**
     * @return bool
     */
    public function queueExists(): bool;

    /**
     * @return bool
     */
    public function isQueueEmpty(): bool;

    /**
     * @return void
     */
    public function clear(): void;
}
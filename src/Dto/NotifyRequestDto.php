<?php

namespace App\Dto;

/**
 * NotifyRequestDto class
 */
class NotifyRequestDto
{
    /**
     * NotifyRequestDto constructor
     *
     * @param mixed $receiver
     * @param mixed $message
     * @param mixed $channel
     */
    public function __construct(
        private mixed $receiver,
        private mixed $message,
        private mixed $channel,
    ) {}

    /**
     * @return mixed
     */
    public function getReceiver(): mixed
    {
        return $this->receiver;
    }

    /**
     * @return mixed
     */
    public function getMessage(): mixed
    {
        return $this->message;
    }

    /**
     * @return mixed
     */
    public function getChannel(): mixed
    {
        return $this->channel;
    }
}
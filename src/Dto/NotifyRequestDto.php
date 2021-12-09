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
        public readonly mixed $receiver,
        public readonly mixed $message,
        public readonly mixed $channel,
    ) {}
}
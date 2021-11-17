<?php

namespace App\Services;

use App\Dto\NotifyRequestDto;

interface NotificationChannelInterface
{
    /**
     * Email channel.
     */
    public const EMAIL_CHANNEL = 'email';

    /**
     * Telegram channel.
     */
    public const TELEGRAM_CHANNEL = 'telegram';

    /**
     * Logging channel.
     */
    public const LOGGING_CHANNEL = 'log';

    /**
     * Sends message.
     *
     * @param NotifyRequestDto $dto
     */
    public function sendMessage(NotifyRequestDto $dto) : void;

    /**
     * Can message be sent.
     *
     * @param NotifyRequestDto $dto
     * @return bool
     */
    public function canSend(NotifyRequestDto $dto) : bool;
}
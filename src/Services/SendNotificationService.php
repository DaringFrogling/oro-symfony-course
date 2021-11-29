<?php

namespace App\Services;

use App\Dto\NotifyRequestDto;
use App\Services\NotificationChannels\NotificationChannelInterface;

class SendNotificationService
{
    /**
     * SendNotificationService constructor.
     *
     * @param iterable|NotificationChannelInterface[] $notificationChannels
     */
    public function __construct(
        private iterable $notificationChannels,
    ) {}

    public function notify(NotifyRequestDto $dto)
    {
        foreach ($this->notificationChannels as $notificationChannel) {
            if ($notificationChannel->canSend($dto->getChannel())) {
                $notificationChannel->sendMessage($dto);
            }
        }
    }
}
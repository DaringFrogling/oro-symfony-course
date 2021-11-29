<?php

namespace App\Services\NotificationChannels;

use App\Dto\NotifyRequestDto;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Notifier\{Notification\Notification, NotifierInterface, Recipient\Recipient};

class TelegramNotificationChannel implements NotificationChannelInterface
{
    /**
     * TelegramNotificationChannel constructor.
     *
     * @param NotifierInterface $notifier
     */
    public function __construct(
        private NotifierInterface $notifier,
    ) {}

    /**
     * @inheritDoc
     */
    public function sendMessage(NotifyRequestDto $dto): void
    {
        $notification = (new Notification('New message', ['telegram']))
            ->content($dto->getMessage());
        $recipient = new Recipient($dto->getReceiver());

        try {
            $this->notifier->send($notification, $recipient);
        } catch (\Throwable $e) {
            throw new HttpException('Unable to send message');
        }
    }

    /**
     * @inheritDoc
     */
    public function canSend(NotifyRequestDto $dto): bool
    {
        return $dto->getChannel() === self::TELEGRAM_CHANNEL;
    }
}
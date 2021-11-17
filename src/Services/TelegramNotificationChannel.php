<?php

namespace App\Services;

use App\Dto\NotifyRequestDto;
use Symfony\Bridge\Monolog\Handler\NotifierHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

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
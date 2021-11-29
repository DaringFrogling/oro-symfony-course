<?php

namespace App\Services\NotificationChannels;

use App\Dto\NotifyRequestDto;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailNotificationChannel implements NotificationChannelInterface
{
    /**
     * EmailNotificationChannel constructor.
     *
     * @param MailerInterface $mailer
     */
    public function __construct(
        private MailerInterface $mailer,
    ) {}

    /**
     * @inheritDoc
     */
    public function sendMessage(NotifyRequestDto $dto): void
    {
        $email = (new Email())
            ->from('daringfrogling@gmail.com')
            ->to($dto->getReceiver())
            ->text($dto->getMessage());

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            throw new HttpException('Unable to send email');
        }
    }

    /**
     * @inheritDoc
     */
    public function canSend(NotifyRequestDto $dto): bool
    {
        return $dto->getChannel() === self::EMAIL_CHANNEL;
    }
}
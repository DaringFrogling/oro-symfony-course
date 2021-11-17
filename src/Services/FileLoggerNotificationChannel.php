<?php

namespace App\Services;

use App\Dto\NotifyRequestDto;
use Symfony\Bridge\Monolog\Logger;

class FileLoggerNotificationChannel implements NotificationChannelInterface
{
    /**
     * FileLoggerNotificationChannel constructor.
     *
     * @param Logger $logger
     */
    public function __construct(
        private Logger $logger,
    ) {}

    /**
     * @inheritDoc
     */
    public function sendMessage(NotifyRequestDto $dto): void
    {
        $data = $this->toArray($dto);
        $this->logger->info('[Notification sends]', $data);
        $encoded = json_encode($data);
        file_put_contents('notification_log.txt', $encoded . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    /**
     * @inheritDoc
     */
    public function canSend(NotifyRequestDto $dto): bool
    {
        return $dto->getChannel() === self::LOGGING_CHANNEL;
    }

    /**
     * Creates array from dto.
     *
     * @param NotifyRequestDto $dto
     * @return array
     */
    private function toArray(NotifyRequestDto $dto): array
    {
        return [
            'receiver' => $dto->getReceiver(),
            'message' => $dto->getMessage(),
            'channel' => $dto->getChannel(),
        ];
    }
}
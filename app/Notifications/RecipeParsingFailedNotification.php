<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramFile;
use NotificationChannels\Telegram\TelegramMessage;

class RecipeParsingFailedNotification extends Notification
{
    public function __construct(
        private readonly string $errorMessage,
    )
    {
    }

    public function via($notifiable): array
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable): TelegramMessage|TelegramFile
    {
        return TelegramMessage::create()
            ->content($this->errorMessage)
            ->options(['parse_mode' => 'HTML'])
            ->token(config('services.telegram.token'));
    }
}

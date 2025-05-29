<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class RecipeUrlExcludedNotification extends Notification
{
    public function __construct(
        private readonly string $url,
    )
    {
    }

    public function via($notifiable): array
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable): TelegramMessage
    {
        return TelegramMessage::create()
            ->content("<b>[INFO]</b>\n"
                . "🚫 <b>URL excluded</b>\n"
                . "<b>URL:</b> {$this->url}")
            ->options([
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            ])
            ->token(config('services.telegram.token'));
    }
}

<?php

namespace App\Notifications;

use App\Models\Source\SourceRecipeUrl;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class RecipeBlockNotFoundNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly SourceRecipeUrl $sourceRecipeUrl,
    )
    {
    }

    public function via($notifiable)
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable): TelegramMessage
    {
        $text = "⚠️ <b>Recipe block not found</b>\n"
            . "<b>URL:</b> {$this->sourceRecipeUrl->url}\n"
            . "<b>Джерело:</b> " . ($this->sourceRecipeUrl->source?->title ?? '—');

        return TelegramMessage::create()
            ->content($text)
            ->options(['parse_mode' => 'HTML']);
    }
}

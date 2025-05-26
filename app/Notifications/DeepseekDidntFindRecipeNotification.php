<?php

namespace App\Notifications;

use App\Models\Source\SourceRecipeUrl;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class DeepseekDidntFindRecipeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly SourceRecipeUrl $sourceRecipeUrl,
        private readonly ?string         $deepseekResponse = null,
    )
    {
    }

    public function via($notifiable): array
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable): TelegramMessage
    {
        $text = "🤖 <b>Deepseek не знайшов рецепт</b>\n"
            . "<b>URL:</b> {$this->sourceRecipeUrl->url}\n"
            . "<b>Джерело:</b> " . ($this->sourceRecipeUrl->source?->title ?? '—');

        if ($this->deepseekResponse) {
            $short = mb_strlen($this->deepseekResponse) > 500
                ? mb_substr($this->deepseekResponse, 0, 500) . '...'
                : $this->deepseekResponse;
            $text .= "\n\n<pre>{$short}</pre>";
        }

        return TelegramMessage::create()
            ->content($text)
            ->options(['parse_mode' => 'HTML'])
            ->token(config('services.telegram.token'));
    }
}

<?php

namespace App\Notifications;

use App\Models\Source\SourceRecipeUrl;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class AiProviderDidntFindRecipeNotification extends Notification
{
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
        $text = "<b>[ERROR]</b>\n"
            . "🤖 <b>Deepseek не знайшов рецепт</b>\n"
            . "<b>URL:</b> {$this->sourceRecipeUrl->url}\n";

        if ($this->deepseekResponse) {
            $short = mb_strlen($this->deepseekResponse) > 500
                ? mb_substr($this->deepseekResponse, 0, 500) . '...'
                : $this->deepseekResponse;
            $text .= "\n\n<pre>{$short}</pre>";
        }

        return TelegramMessage::create()
            ->content($text)
            ->options([
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            ])
            ->token(config('services.telegram.token'));
    }
}

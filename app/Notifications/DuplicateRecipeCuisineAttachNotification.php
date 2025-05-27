<?php

namespace App\Notifications;

use App\Models\Cuisine;
use App\Models\Recipe\Recipe;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class DuplicateRecipeCuisineAttachNotification extends Notification
{
    public function __construct(
        private readonly Recipe  $recipe,
        private readonly Cuisine $cuisine,
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
            . "⚠️ <b>Помилка дублювання кухні</b>\n"
            . "<b>Рецепт:</b> {$this->recipe->title}\n"
            . "<b>Кухня:</b> {$this->cuisine->title}\n"
            . "<b>ID рецепта:</b> {$this->recipe->id}\n"
            . "<b>ID кухні:</b> {$this->cuisine->id}";

        return TelegramMessage::create()
            ->content($text)
            ->options(['parse_mode' => 'HTML'])
            ->token(config('services.telegram.token'));

    }
}

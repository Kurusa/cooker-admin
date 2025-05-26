<?php

namespace App\Notifications;

use App\Models\Recipe\Recipe;
use App\Models\Recipe\RecipeCategory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class DuplicateRecipeCategoryAttachNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Recipe         $recipe,
        private readonly RecipeCategory $category,
    )
    {
    }

    public function via($notifiable): array
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable): TelegramMessage
    {
        $text = "⚠️ <b>Помилка дублювання категорії</b>\n"
            . "<b>Рецепт:</b> {$this->recipe->title}\n"
            . "<b>Категорія:</b> {$this->category->title}\n"
            . "<b>ID рецепта:</b> {$this->recipe->id}\n"
            . "<b>ID категорії:</b> {$this->category->id}";

        return TelegramMessage::create()
            ->content($text)
            ->options(['parse_mode' => 'HTML'])
            ->token(config('services.telegram.token'));

    }
}

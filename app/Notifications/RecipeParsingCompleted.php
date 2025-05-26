<?php


namespace App\Notifications;

use App\Models\Recipe\Recipe;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\View;
use NotificationChannels\Telegram\TelegramFile;
use NotificationChannels\Telegram\TelegramMessage;

class RecipeParsingCompleted extends Notification
{
    public function __construct(
        private readonly Recipe $recipe,
    )
    {
    }

    public function via($notifiable): array
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable): TelegramMessage|TelegramFile
    {
        $text = View::make('notifications.recipe_parsing_completed', [
            'recipe' => $this->recipe,
            'source' => $this->recipe->sourceRecipeUrl->source,
        ])->render();

        if ($this->recipe->image_url && filter_var($this->recipe->image_url, FILTER_VALIDATE_URL)) {
            $message = TelegramFile::create()
                ->photo($this->recipe->image_url);
        } else {
            $message = TelegramMessage::create();
        }

        return $message
            ->content($text)
            ->options(['parse_mode' => 'HTML'])
            ->token(config('services.telegram.token'));
    }
}

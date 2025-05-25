<?php


namespace App\Notifications;

use App\Models\Recipe\Recipe;
use App\Models\Recipe\RecipeIngredient;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramFile;

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

    public function toTelegram($notifiable)
    {
        $text = "🍽️ <b>Рецепт додано:</b>\n"
            . "<b>Назва:</b> {$this->recipe->title}\n"
            . "<b>Категорії:</b> " . implode(', ', $this->recipe->categories->pluck('title')->all()) . "\n"
            . "<b>Кухня:</b> " . implode(', ', $this->recipe->cuisines->pluck('title')->all()) . "\n"
            . "<b>Складність:</b> " . ucfirst($this->recipe->complexity->value) . "\n"
            . "<b>Інгредієнти:</b>\n" . collect($this->recipe->recipeIngredients)
                ->map(fn(RecipeIngredient $ingredient) => '• ' . $ingredient->ingredientUnit->ingredient->title
                    . ($ingredient->quantity ? " — {$ingredient->quantity}" : '')
                    . ($ingredient->ingredientUnit->unit?->title ? " {$ingredient->ingredientUnit->unit->title}" : '')
                )->implode("\n")
            . "\n\n" . "<b>Джерело:</b> {$this->recipe->sourceRecipeUrl?->url}";

        return TelegramFile::create()
            ->content($text)
            ->options(['parse_mode' => 'HTML'])
            ->when($this->recipe->image_url, fn($m) => $m->photo($this->recipe->image_url, 'photo'))
            ->token(config('services.telegram.token'));
    }
}

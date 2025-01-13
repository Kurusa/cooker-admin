<?php

namespace App\Models;

use App\Enums\Recipe\Complexity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $title
 * @property string $complexity
 * @property string $advice
 * @property int $time
 * @property int $portions
 * @property string|null $source_url
 * @property int $category_id
 * @property string|null $image_url
 *
 * @property string $ingredient_list
 * @property Collection $ingredients_collection
 * @property-read Collection $ingredients
 * @property string $is_popular
 * @property-read string $header
 */
class Recipe extends Model
{
    protected $fillable = [
        'title',
        'complexity',
        'advice',
        'time',
        'portions',
        'source_url',
        'category_id',
        'source_url',
        'image_url',
        'is_popular',
    ];

    protected $casts = [
        'complexity' => Complexity::class,
        'is_popular' => 'boolean',
        'rating' => 'integer',
    ];

    protected function isPopular(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? '⭐ ' : '',
        )->shouldCache();
    }

    protected function ingredientsCollection(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->ingredients->map(function ($ingredient) {
                return [
                    'title' => $ingredient->title,
                    'quantity' => $ingredient->pivot->quantity,
                    'unit' => $ingredient->unit,
                ];
            })
        )->shouldCache();
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredients')->withPivot('quantity');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(Step::class);
    }

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('is_popular', 'desc');
            $builder->orderByRaw("
                CASE
                    WHEN complexity = 'easy' THEN 1
                    WHEN complexity = 'medium' THEN 2
                    WHEN complexity = 'hard' THEN 3
                    ELSE 4
                END
            ");
        });
    }
}

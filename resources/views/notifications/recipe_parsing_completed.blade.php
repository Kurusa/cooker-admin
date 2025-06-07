🍽️ <b>Рецепт додано:</b>
<b>Назва:</b> {{ $recipe->title }}
<b>Категорії:</b> {{ $recipe->categories->pluck('title')->implode(', ') }}
<b>Кухня:</b> {{ $recipe->cuisines->pluck('title')->implode(', ') }}
<b>Складність:</b> {{ ucfirst($recipe->complexity->value) }}
<b>Інгредієнти:</b>
@if($recipe->ingredientGroups->count())
@foreach($recipe->ingredientGroups as $group)
<b>{{ $group->title }}:</b>
@foreach($group->ingredients as $ingredient)
    @include('notifications.partials.ingredient', ['ingredient' => $ingredient])
@endforeach
@endforeach
@else
@foreach($recipe->recipeIngredients as $ingredient)
@include('notifications.partials.ingredient', ['ingredient' => $ingredient])
@endforeach
@endif

<b>Джерело:</b> {{ $recipe->sourceRecipeUrl?->url }}

{{ $source->getParsedSummaryText() }}

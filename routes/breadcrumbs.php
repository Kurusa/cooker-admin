<?php

use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Source;
use App\Models\Unit;
use App\Models\User;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push('Home', route('dashboard'));
});

Breadcrumbs::for('dashboard', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Dashboard', route('dashboard'));
});

Breadcrumbs::for('management.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push('Management', route('management.users.index'));
});

## Users
Breadcrumbs::for('management.users.index', function (BreadcrumbTrail $trail) {
    $trail->parent('management.index');
    $trail->push('Users', route('management.users.index'));
});

Breadcrumbs::for('management.users.show', function (BreadcrumbTrail $trail, User $user) {
    $trail->parent('management.users.index');
    $trail->push(ucwords($user->name), route('management.users.show', $user));
});

Breadcrumbs::for('management.sources.index', function (BreadcrumbTrail $trail) {
    $trail->parent('management.index');
    $trail->push('Sources', route('management.sources.index'));
});

Breadcrumbs::for('management.sources.show', function (BreadcrumbTrail $trail, Source $source) {
    $trail->parent('management.sources.index');
    $trail->push(
        ucwords($source->title),
        route('management.sources.show', $source)
    );
});

Breadcrumbs::for('management.categories.index', function (BreadcrumbTrail $trail) {
    $trail->parent('management.index');
    $trail->push('Categories', route('management.categories.index'));
});


## Recipe
Breadcrumbs::for('recipe.index', function (BreadcrumbTrail $trail) {
    $trail->push('Recipe', route('recipe.recipes.index'));
});

Breadcrumbs::for('recipe.steps.index', function (BreadcrumbTrail $trail) {
    $trail->parent('recipe.index');
    $trail->push('Steps', route('recipe.steps.index'));
});

Breadcrumbs::for('recipe.ingredients.index', function (BreadcrumbTrail $trail) {
    $trail->parent('recipe.index');
    $trail->push('Ingredients (' . Ingredient::count() . ')', route('recipe.ingredients.index'));
});

Breadcrumbs::for('recipe.units.index', function (BreadcrumbTrail $trail) {
    $trail->parent('recipe.index');
    $trail->push('Units (' . Unit::count() . ')', route('recipe.units.index'));
});

Breadcrumbs::for('recipe.recipes.index', function (BreadcrumbTrail $trail) {
    $trail->parent('recipe.index');

    $count = Recipe::count();
    if (request('source') && !empty(request('source'))) {
        $count = Source::where('title', request('source'))->first()->recipes()->count();
    }

    $trail->push('Recipes (' . $count . ')', route('recipe.recipes.index'));
});

Breadcrumbs::for('recipe.recipes.show', function (BreadcrumbTrail $trail, Recipe $recipe) {
    $trail->parent('recipe.recipes.index');
    $trail->push(ucwords($recipe->title), route('recipe.recipes.show', $recipe));
});

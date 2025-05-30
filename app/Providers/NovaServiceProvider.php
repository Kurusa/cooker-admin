<?php

namespace App\Providers;

use App\Models\User;
use App\Nova\Dashboards\Main;
use App\Nova\Ingredient\Ingredient;
use App\Nova\Ingredient\IngredientGroup;
use App\Nova\Ingredient\IngredientUnit;
use App\Nova\Ingredient\Unit;
use App\Nova\Recipe\Recipe;
use App\Nova\Recipe\RecipeCategory;
use App\Nova\Recipe\RecipeCuisine;
use App\Nova\Recipe\RecipeIngredient;
use App\Nova\Recipe\RecipeStep;
use App\Nova\Source\Source;
use App\Nova\Source\SourceRecipeUrl;
use App\Nova\Source\SourceSitemap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Actions\ActionEvent;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        Nova::withoutNotificationCenter();

        Nova::mainMenu(function (Request $request) {
            return [
                MenuSection::dashboard(Main::class)->icon('chart-bar'),

                MenuSection::resource(Recipe::class)->icon('bug-ant'),
                MenuSection::make('Recipe attributes', [
                    MenuItem::resource(RecipeCategory::class),
                    MenuItem::resource(RecipeCuisine::class),
                    MenuItem::resource(RecipeIngredient::class),
                    MenuItem::resource(RecipeStep::class),
                ])->icon('adjustments-horizontal')->collapsedByDefault(),

                MenuSection::resource(Ingredient::class)->icon('beaker'),
                MenuSection::make('Ingredient attributes', [
                    MenuItem::resource(IngredientGroup::class),
                    MenuItem::resource(IngredientUnit::class),
                    MenuItem::resource(Unit::class),
                ])->icon('adjustments-horizontal')->collapsedByDefault(),

                MenuSection::resource(Source::class)->icon('document-text'),
                MenuSection::make('Source attributes', [
                    MenuItem::resource(SourceRecipeUrl::class),
                    MenuItem::resource(SourceSitemap::class),
                ])->icon('adjustments-horizontal')->collapsedByDefault(),
            ];
        });

        ActionEvent::saving(function () {
            return false;
        });
    }

    protected function routes(): void
    {
        Nova::routes()
            ->withAuthenticationRoutes()
            ->withPasswordResetRoutes()
            ->withoutEmailVerificationRoutes()
            ->register();
    }

    protected function gate(): void
    {
        Gate::define('viewNova', function (User $user) {
            return in_array($user->email, [
                'kurusa03@gmail.com',
                'anton@gmail.com',
            ]);
        });
    }

    protected function dashboards(): array
    {
        return [
            new Main,
        ];
    }
}

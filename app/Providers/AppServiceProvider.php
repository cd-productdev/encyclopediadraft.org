<?php

namespace App\Providers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Add slug macro to Blueprint
        Blueprint::macro('slug', function (string $column = 'slug', string $fromColumn = 'name', bool $unique = true) {
            $columnDefinition = $this->string($column)->nullable();

            if ($unique) {
                $columnDefinition->unique();
            }

            $this->index($column);

            return $columnDefinition;
        });

        // Add slugWithSource macro (adds both slug and source column)
        Blueprint::macro('slugWithSource', function (string $slugColumn = 'slug', string $sourceColumn = 'name', bool $unique = true) {
            $slugDefinition = $this->string($slugColumn)->nullable();

            if ($unique) {
                $slugDefinition->unique();
            }

            $this->index($slugColumn);

            return $slugDefinition;
        });
    }
}

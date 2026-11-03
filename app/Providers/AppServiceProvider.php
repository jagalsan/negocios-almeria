<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Password::defaults(fn () => Password::min(10)->letters()->numbers());
        Paginator::useTailwind();
        View::composer(['layouts.app'], function ($view) {
            $view->with('navCategories', Category::query()->where('is_published', true)->orderBy('sort_order')->get());
        });
    }
}

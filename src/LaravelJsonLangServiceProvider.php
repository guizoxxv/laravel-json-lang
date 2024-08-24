<?php

namespace Guizoxxv\LaravelJsonLang;

use Guizoxxv\LaravelJsonLang\Console\ExportLanguageFilesAsJson;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class LaravelJsonLangServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/laravel-json-lang.php' => config_path('laravel-json-lang.php'),
        ]);

        $this->commands([
            ExportLanguageFilesAsJson::class,
        ]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [ExportLanguageFilesAsJson::class];
    }
}
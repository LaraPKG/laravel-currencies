<?php

declare(strict_types=1);

namespace LaraPKG\LaravelCurrencies;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register(): void
    {
        $this->publishConfig();
        $this->publishMigrations();
    }

    /**
     * Publish the package config file
     */
    protected function publishConfig(): void
    {
        $this->publishes(
            [
                __DIR__ . '/../config/intl/currencies.php' => config_path('intl/currencies.php')
            ],
            'config'
        );
    }

    /**
     * Publish the package migrations
     */
    protected function publishMigrations(): void
    {
        $this->publishes(
            [
                __DIR__ . '/../database/migrations/' => database_path('migrations')
            ],
            'migrations'
        );
    }
}
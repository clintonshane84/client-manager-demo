<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use App\Contracts\MailerFactoryInterface;
use App\Factories\MailerFactory;

class MailablesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/mailables.php', 'mailables');
        
        // Register singletons
        $this->app->singleton(MailerFactory::class, function($app){
            return new MailerFactory($app, Config::get(['mailables.map']));
        });
        
        // Bind mailable services
        $this->app->bind(MailerFactoryInterface::class, MailerFactory::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

    }
}

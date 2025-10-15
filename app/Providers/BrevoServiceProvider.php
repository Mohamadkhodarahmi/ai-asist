<?php

namespace App\Providers;

use App\Mail\BrevoTransport;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Mail;

class BrevoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Mail::extend('brevo', function (array $config) {
            return new BrevoTransport(
                $config['api_key'],
                $config['sender_email'],
                $config['sender_name']
            );
        });
    }
}

<?php
namespace Hoangnh283\Loginapi;

use Illuminate\Support\ServiceProvider;

class LoginApiServiceProvider extends ServiceProvider {
    public function boot()
    {
        
        // Load routes
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // // Publish config
        // $this->publishes([
        //     __DIR__.'/config/telegram.php' => config_path('telegram.php'),
        // ]);
    }
    public function register()
    {
        // $this->mergeConfigFrom(__DIR__.'/config/telegram.php', 'telegram');
    }
}
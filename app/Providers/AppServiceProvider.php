<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

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
        // override mail config
        try {
            if (Schema::hasTable('settings') && Setting::get('mail_host')) { // only run config overrides if the settings table actually exists and has a mail host set
                config([
                    'mail.default' => 'smtp',
                    "mail.mailers.smtp.host" => Setting::get('mail_host'),
                    "mail.mailers.smtp.port" => Setting::get('mail_port'),
                    "mail.mailers.smtp.username" => Setting::get('mail_username'),
                    "mail.mailers.smtp.password" => Setting::get('mail_password'),
                    "mail.mailers.smtp.encryption" => Setting::get('mail_encryption'),
                    'mail.from.address' => Setting::get('mail_from_address'),
                    'mail.from.name' => Setting::get('mail_from_name'),
                ]);
            }
        } catch (\Exception $e) {
            // database might not exist yet, or connection failed; safely ignore to allow installation
        }
    }
}

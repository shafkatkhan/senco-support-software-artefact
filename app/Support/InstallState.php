<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class InstallState
{
    private const STORE = 'file';

    public static function isInstalled(): bool
    {
        try {
            if (!static::store()->get('system_installed', false)) {
                return false;
            }

            if (!Schema::hasTable('users')) {
                static::reset();
                return false;
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function markInstalled(): void
    {
        static::store()->forever('system_installed', true);
    }

    public static function isLanguageSetupPending(): bool
    {
        // if users table does not exist, reset and redirect to install
        if(!Schema::hasTable('users')) {
            static::reset();
            return false;
        }
        return static::store()->get('lang_setup_pending', false);
    }

    public static function markLanguageSetupPending(): void
    {
        static::store()->forever('lang_setup_pending', true);
    }

    public static function clearLanguageSetupPending(): void
    {
        static::store()->forget('lang_setup_pending');
    }

    public static function clearInstalled(): void
    {
        static::store()->forget('system_installed');
    }

    public static function reset(): void
    {
        static::clearInstalled();
        static::clearLanguageSetupPending();
        static::store()->forget('auto_translations');
        static::store()->forget('locale_name');
    }

    public static function put(string $key, mixed $value): void
    {
        static::store()->forever($key, $value);
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::store()->get($key, $default);
    }

    private static function store()
    {
        return Cache::store(static::STORE);
    }
}

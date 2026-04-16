<?php

namespace Tests\Feature;

use Tests\TestCase;

class HelpersTest extends TestCase
{
    public function test_helpers_file_can_be_loaded_when_functions_already_exist(): void
    {
        include base_path('app/Helpers/helpers.php');

        $this->assertTrue(function_exists('is_rtl'));
        $this->assertTrue(function_exists('uses_latin_script'));
    }

    public function test_is_rtl_returns_true_for_rtl_language_direction(): void
    {
        config(['app.language_direction' => 'rtl']);

        $this->assertTrue(is_rtl());
    }

    public function test_is_rtl_returns_false_for_ltr_language_direction(): void
    {
        config(['app.language_direction' => 'ltr']);

        $this->assertFalse(is_rtl());
    }

    public function test_uses_latin_script_returns_true_for_latin_locale(): void
    {
        app()->setLocale('en');

        $this->assertTrue(uses_latin_script());
    }

    public function test_uses_latin_script_returns_false_for_non_latin_locale(): void
    {
        app()->setLocale('ar');

        $this->assertFalse(uses_latin_script());
    }

    public function test_uses_latin_script_handles_regional_non_latin_locale(): void
    {
        app()->setLocale('zh-CN');

        $this->assertFalse(uses_latin_script());
    }
}

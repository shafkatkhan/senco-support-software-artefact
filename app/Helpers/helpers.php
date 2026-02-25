<?php

if (!function_exists('is_rtl')) {
    /**
     * Check if the language direction is RTL.
     *
     * @return bool
     */
    function is_rtl()
    {
        $dir = config('app.language_direction');
        return $dir == 'rtl';
    }
}

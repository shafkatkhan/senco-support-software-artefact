<?php

if (!function_exists('is_rtl')) {
    /**
     * Check if the current application locale is RTL.
     *
     * @return bool
     */
    function is_rtl()
    {
        $rtl_locales = ['ar', 'he', 'fa', 'ur'];
        return in_array(app()->getLocale(), $rtl_locales);
    }
}

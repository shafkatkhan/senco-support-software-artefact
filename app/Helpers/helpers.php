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

if (!function_exists('uses_latin_script')) {
    /**
     * Check if the current locale uses a Latin-based script (ie. not Arabic, Chinese, Japanese, Korean, Hindi, Hebrew, etc.)
     * This is used to conditionally apply Excel wrapText, which breaks non-Latin rendering.
     *
     * @return bool
     */
    function uses_latin_script()
    {
        $nonLatinLocales = [
            'am', 'ar', 'hy', 'be', 'bn', 'brx', 'bg', 'ckb', 'ce', 'zh',
            'zh-HK', 'zh-CN', 'zh-TW', 'dv', 'dz', 'ka', 'el', 'gu', 'ha', 'he',
            'hi', 'iu', 'ja', 'kn', 'ks', 'kk', 'km', 'ko', 'ky', 'lo',
            'mk', 'mai', 'ml', 'mni', 'mr', 'mn', 'ne', 'or', 'os',
            'ps', 'fa', 'pa', 'ru', 'sat', 'sd', 'si', 'sr', 'tg',
            'ta', 'tt', 'te', 'th', 'ti', 'udm', 'uk', 'ur', 'ug', 'yi',
        ];
        return !in_array(app()->getLocale(), $nonLatinLocales);
    }
}

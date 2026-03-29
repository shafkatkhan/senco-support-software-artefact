<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Services\LlmService;
use App\Models\Setting;
use App\Models\User;
use App\Support\InstallState;

class InstallController extends Controller
{
    public function index() {
        try {
            if (InstallState::isInstalled()) {
                return redirect()->route('login')->with('error', 'System has already been installed.');
            }

            if (Schema::hasTable('users')) {
                if (InstallState::isLanguageSetupPending()) {
                    return redirect(route('install.lang_setup_view'));
                }

                // restore cache state (in case it was cleared)
                InstallState::markInstalled();
                return redirect()->route('login')->with('error', 'System has already been installed.');
            }
        } catch (\Exception $e) {
            // db not configured/migrated/seeded, continue to installation
        }
        return view('install');
    }

    public function process(Request $request) {

        $request->validate([
            'app_url' => 'required|url',
            'app_locale' => 'required',
            'db_host' => 'required',
            'db_port' => 'required',
            'db_name' => 'required',
            'db_username' => 'required',
            'db_password' => 'nullable',
            'seed_demo_data' => 'nullable|boolean',
            'llm_provider' => 'required|in:openai,mistral',
            'llm_api_key' => 'required|string',
        ]);

        try {
            // update runtime configuration to use new credentials
            config(['database.default' => 'mysql']);
            config(['database.connections.mysql.host' => $request->db_host]);
            config(['database.connections.mysql.port' => $request->db_port]);
            config(['database.connections.mysql.database' => $request->db_name]);
            config(['database.connections.mysql.username' => $request->db_username]);
            config(['database.connections.mysql.password' => $request->db_password]);
            
            // update runtime LLM config
            config(['services.' . $request->llm_provider . '.key' => $request->llm_api_key]);

            // force reconnection and verify connection
            DB::purge('mysql');
            DB::reconnect('mysql');
            DB::connection()->getPdo();

            if ($request->hasFile('restore_file')) {
                // backup provided: wipe the DB and run the raw SQL file
                Artisan::call('db:wipe', ['--force' => true]);
                $sqlInput = file_get_contents($request->file('restore_file')->getRealPath());
                DB::unprepared($sqlInput);
            } else {
                // no backup provided: run migrations and seed
                config(['app.seed_demo_data' => $request->has('seed_demo_data')]);
                Artisan::call('migrate:fresh', ['--seed' => true]);
            }

            // save LLM provider and API key to settings table
            Setting::set('llm_provider', $request->llm_provider);
            Setting::set('llm_api_key', $request->llm_api_key);

            // mapping of full language names
            $languages = [
                'af' => 'Afrikaans', 'sq' => 'Albanian - (Shqip)', 'am' => 'Amharic - (አማርኛ)', 'ar' => 'Arabic - (العربية)', 'an' => 'Aragonese - (Aragonés)',
                'hy' => 'Armenian - (Հայերեն)', 'ast' => 'Asturian - (Asturianu)', 'ay' => 'Aymara - (Aymar aru)', 'az' => 'Azerbaijani - (Azərbaycan dili)',
                'eu' => 'Basque - (Euskara)', 'be' => 'Belarusian - (Беларуская)', 'bn' => 'Bengali - (বাংলা)', 'brx' => 'Bodo - (बर\')', 'bs' => 'Bosnian - (Bosanski)',
                'br' => 'Breton - (Brezhoneg)', 'bg' => 'Bulgarian - (Български)', 'ca' => 'Catalan - (Català)', 'ckb' => 'Central - Kurdish (کوردی)',
                'ce' => 'Chechen (Нохчийн мотт)', 'zh' => 'Chinese (中文)', 'zh-HK' => 'Chinese Hong Kong - (中文 - 香港)', 'zh-CN' => 'Chinese Simplified - (中文简体)',
                'zh-TW' => 'Chinese Traditional - (中文繁體)', 'co' => 'Corsican - (Corsu)', 'hr' => 'Croatian - (Hrvatski)', 'cs' => 'Czech - (Čeština)',
                'da' => 'Danish - (Dansk)', 'dv' => 'Dhivehi - (ދިވެހި)', 'nl' => 'Dutch (Nederlands)', 'dz' => 'Dzongkha (རྫོང་ཁ)', 'en' => 'English',
                'en-AU' => 'English (Australia)', 'en-CA' => 'English (Canada)', 'en-IN' => 'English (India)', 'en-NZ' => 'English (New Zealand)',
                'en-ZA' => 'English (South Africa)', 'en-GB' => 'English (United Kingdom)', 'en-US' => 'English (United States)', 'eo' => 'Esperanto',
                'et' => 'Estonian (Eesti)', 'ee' => 'Ewe (Eʋegbe)', 'fo' => 'Faroese - (Føroyskt)', 'fil' => 'Filipino (Wikang Filipino)', 'fi' => 'Finnish (Suomi)',
                'fr' => 'French (Français)', 'fr-CA' => 'French Canada - (Français Canada)', 'fr-FR' => 'French France - (Français France)', 'fr-CH' => 'French Switzerland - (Français Suisse)',
                'gl' => 'Galician - (Galego)', 'ka' => 'Georgian - (ქართული)', 'de' => 'German - (Deutsch)', 'de-AT' => 'German Austria  - (Deutsch Österreich)',
                'de-DE' => 'German Germany  - (Deutsch Deutschland)', 'de-LI' => 'German Liechtenstein  - (Deutsch Liechtenstein)', 'de-CH' => 'German Switzerland  - (Deutsch Schweiz)',
                'el' => 'Greek - (Ελληνικά)', 'gn' => 'Guarani - (Avañe\'ẽ)', 'gu' => 'Gujarati - (ગુજરાતી)', 'ha' => 'Hausa - (هَوُسَ)', 'haw' => 'Hawaiian - (ʻŌlelo Hawaiʻi)',
                'he' => 'Hebrew - (עברית)', 'hi' => 'Hindi - (हिन्दी)', 'hu' => 'Hungarian - (Magyar)', 'is' => 'Icelandic - (Íslenska)', 'id' => 'Indonesian - (Bahasa Indonesia)',
                'ia' => 'Interlingua', 'iu' => 'Inuktitut - (ᐃᓄᒃᑎᑐᑦ)', 'ga' => 'Irish - (Gaeilge)', 'it' => 'Italian - (Italiano)', 'it-IT' => 'Italian Italy - (Italiano Italia)',
                'it-CH' => 'Italian Switzerland - (Italiano - Svizzera)', 'ja' => 'Japanese - (日本語)', 'kl' => 'Kalaallisut - (Kalaallisut)', 'kn' => 'Kannada - (ಕನ್ನಡ)',
                'ks' => 'Kashmiri - (कॉशुर / كٲشُر)', 'kk' => 'Kazakh - (Қазақ тілі)', 'km' => 'Khmer - (ខ្មែរ)', 'rw' => 'Kinyarwanda - (Ikinyarwanda)', 'ko' => 'Korean - (한국어)',
                'ku' => 'Kurdish - (Kurdî)', 'ky' => 'Kyrgyz - (Кыргызча)', 'lo' => 'Lao - (ລາວ)', 'la' => 'Latin - (Latina)', 'lv' => 'Latvian - (Latviešu)',
                'ln' => 'Lingala - (Lingála)', 'lt' => 'Lithuanian - (Lietuvių)', 'lg' => 'Luganda - (Luganda)', 'lb' => 'Luxembourgish - (Lëtzebuergesch)', 'mk' => 'Macedonian - (Македонски)',
                'mai' => 'Maithili - (मैथिली)', 'ms' => 'Malay - (Bahasa Melayu)', 'ml' => 'Malayalam - (മലയാളം)', 'mt' => 'Maltese - (Malti)', 'mni' => 'Manipuri - (ꯃꯅꯤꯄꯨꯔꯤ)',
                'mr' => 'Marathi - (मराठी)', 'mn' => 'Mongolian - (Монгол)', 'ne' => 'Nepali - (नेपाली)', 'nso' => 'Northern Sotho - (Sesotho sa Leboa)', 'no' => 'Norwegian (Norsk)',
                'nb' => 'Norwegian Bokmål - (Norsk bokmål)', 'nn' => 'Norwegian Nynorsk - (Nynorsk)', 'oc' => 'Occitan', 'or' => 'Oriya - (ଓଡ଼ିଆ)', 'om' => 'Oromo - (Afaan Oromoo)',
                'os' => 'Ossetian - (Ирон æвзаг)', 'ps' => 'Pashto - (پښتو)', 'fa' => 'Persian - (فارسی)', 'pl' => 'Polish - (Polski)', 'pt' => 'Portuguese - (Português)',
                'pt-BR' => 'Portuguese Brazil - (Português Brasil)', 'pt-PT' => 'Portuguese Portugal - (Português Portugal)', 'pa' => 'Punjabi - (ਪੰਜਾਬੀ)', 'qu' => 'Quechua - (Runa Simi)',
                'ro' => 'Romanian - (Română)', 'mo' => 'Romanian  Moldova - (Română Moldova)', 'rm' => 'Romansh - (Rumantsch)', 'ru' => 'Russian - (Русский)', 'sm' => 'Samoan - (Gagana Samoa)',
                'sat' => 'Santali - (ᱥᱟᱱᱛᱟᱲᱤ)', 'sc' => 'Sardinian - (Sardu)', 'gd' => 'Scottish Gaelic - (Gàidhlig)', 'sr' => 'Serbian - (Српски)', 'sh' => 'Serbo_Croatian - (Srpskohrvatski)',
                'sn' => 'Shona - (ChiShona)', 'sd' => 'Sindhi - (سنڌي)', 'si' => 'Sinhala - (සිංහල)', 'sk' => 'Slovak - (Slovenčina)', 'sl' => 'Slovenian - (Slovenščina)',
                'so' => 'Somali - (Soomaali)', 'st' => 'Southern - Sotho (Sesotho)', 'es' => 'Spanish - (Español)', 'es-AR' => 'Spanish  Argentina  - (Español Argentina)', 'es-419' => 'Spanish  Latin America  - (Español Latinoamérica)',
                'es-MX' => 'Spanish  Mexico  - (Español  México)', 'es-ES' => 'Spanish  Spain  - (Español España)', 'es-US' => 'Spanish  United States  - (Español Estados Unidos)', 'su' => 'Sundanese - (Basa Sunda)', 'sw' => 'Swahili - (Kiswahili)',
                'sv' => 'Swedish - (Svenska)', 'tg' => 'Tajik - (Тоҷикӣ)', 'ta' => 'Tamil - (தமிழ்)', 'tt' => 'Tatar - (Татар)', 'te' => 'Telugu - (తెలుగు)',
                'th' => 'Thai - (ไทย)', 'ti' => 'Tigrinya - (ትግርኛ)', 'to' => 'Tongan - (Lea fakatonga)', 'tn' => 'Tswana - (Setswana)', 'tr' => 'Turkish - (Türkçe)',
                'tk' => 'Turkmen - (Türkmençe)', 'tw' => 'Twi - (Twi)', 'udm' => 'Udmurt - (Удмурт кыл)', 'uk' => 'Ukrainian - (Українська)', 'ur' => 'Urdu - (اردو)',
                'ug' => 'Uyghur - (ئۇيغۇرچە)', 'uz' => 'Uzbek - (O\'zbek)', 've' => 'Venda - (Tshivenḓa)', 'vi' => 'Vietnamese - (Tiếng Việt)', 'wa' => 'Walloon - (Walon)',
                'cy' => 'Welsh - (Cymraeg)', 'fy' => 'Western Frisian - (Frysk)', 'wo' => 'Wolof - (Wollof)', 'xh' => 'Xhosa - (isiXhosa)', 'yi' => 'Yiddish - (ייִדיש)',
                'yo' => 'Yoruba - (Èdè Yorùbá)', 'za' => 'Zhuang - (Saɯ cueŋƅ)', 'zu' => 'Zulu - (isiZulu)'
            ];

            $locale_name = $languages[$request->app_locale] ?? $request->app_locale;

            // bypass translation setup and mark system as installed if locale is english
            $english_language = Str::startsWith($request->app_locale, 'en');
            if ($english_language) {
                InstallState::markInstalled();
            }else{
                // LLM Translation
                $translation_schema = config('translations.groups');
                $english_labels = [];
                foreach ($translation_schema as $group => $keys) {
                    $english_labels = array_merge($english_labels, $keys);
                }

                $apiKey = $request->llm_api_key;
                $auto_translations = [];

                if ($apiKey && !empty($english_labels)) {
                    $instructions = 
                        "Return a pure JSON object where the keys are EXACTLY the English strings provided to you, and the values are their highly accurate translations into: " . $locale_name . ". Do not change or omit any of the original english keys. Preserve all Laravel-style placeholders exactly as written, such as :name, :count, :date, :time, etc. Do not translate, remove, rename, or alter these placeholders in any way.";

                    try {
                        $auto_translations = LlmService::processRequest(json_encode($english_labels), $instructions);
                        if (empty($auto_translations)) {
                             throw new \Exception("(AI Translation failure) The LLM returned an empty translation. Please check your API key and try again.");
                        }
                    } catch (\Exception $e) {
                        throw new \Exception("(AI Translation failure) " . $e->getMessage() . ". Please check your API key and try again.");
                    }
                }
                // ---------------------------------------------

                InstallState::put('auto_translations', $auto_translations);
                InstallState::put('locale_name', $locale_name);
                InstallState::markLanguageSetupPending();
            }

            // update .env (triggers artisan serve restart)
            $environment_updates = [
                'APP_URL' => $request->app_url,
                'APP_LOCALE' => $request->app_locale,
                'DB_CONNECTION' => 'mysql',
                'DB_HOST' => $request->db_host,
                'DB_PORT' => $request->db_port,
                'DB_DATABASE' => $request->db_name,
                'DB_USERNAME' => $request->db_username,
                'DB_PASSWORD' => $request->db_password,
            ];
            
            if ($english_language) {
                $environment_updates['APP_LANGUAGE_DIRECTION'] = 'ltr';
            }
            $this->updateEnv($environment_updates);

            if ($english_language) {
                return redirect(route('login'))->with('success', 'Installation successful! Please login.');
            }

            return redirect(route('install.lang_setup_view'))->with('success', 'Database installed successfully! Please configure language translations.');

        } catch (\Exception $e) {
            try {
                // wipe the database if installation fails midway to prevent a "half-installed" state from redirecting to login
                Artisan::call('db:wipe', ['--force' => true]);
            } catch (\Exception $cleanupEx) {
                // ignore cleanup errors
            }
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    private function updateEnv(array $data = []): void
    {
        $path = base_path('.env');
        if (!File::exists($path)) {
            return;
        }
        $content = File::get($path);

        foreach ($data as $key => $value) {
            $value = $value ?? '';
            $value = '"' . addcslashes($value, '"\\') . '"';
            $pattern = "/^" . preg_quote($key, '/') . "=.*/m";
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, "{$key}={$value}", $content);
            } else {
                $content .= PHP_EOL . "{$key}={$value}";
            }
        }
        
        $tempPath = $path . '.tmp';
        File::put($tempPath, $content);
        rename($tempPath, $path);
    }

    public function lang_setup_view(Request $request) {
        try {
            if (!InstallState::isLanguageSetupPending()) {
                if (InstallState::isInstalled()) {
                    return redirect()->route('login')->with('error', 'Language has already been setup.');
                }

                return redirect(route('install.index'));
            }
        } catch (\Exception $e) {
            // db not configured/migrated/seeded, continue to installation
            return redirect(route('install.index'));
        }

        $locale = env('APP_LOCALE');
        $locale_name = InstallState::get('locale_name', $locale); // fallback to locale code if name isn't found
        $app_direction = in_array($locale, ['ar', 'he', 'fa', 'ur', 'ckb', 'dv', 'ks', 'ps', 'sd', 'ug', 'yi']) ? 'rtl' : 'ltr';

        $translation_schema = config('translations.groups');
        $auto_translations = InstallState::get('auto_translations', []);
        
        return view('lang_setup', [
            'translation_schema' => $translation_schema,
            'locale' => $locale,
            'locale_name' => $locale_name,
            'app_direction' => $app_direction,
            'auto_translations' => $auto_translations
        ]);
    }

    public function lang_setup(Request $request) {
        $request->validate([
            'app_locale' => 'required',
            'app_direction' => 'required|in:ltr,rtl',
            'translations' => 'required|array',
        ]);

        try {
            $locale = $request->app_locale;
            
            // decode base64 keys to get the original english labels
            $processed_translations = [];
            foreach ($request->translations as $base64Key => $translation) {
                $processed_translations[base64_decode($base64Key)] = $translation;
            }

            // create the lang json file
            $json_path = base_path("lang/{$locale}.json");
            File::put($json_path, json_encode($processed_translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            // mark system as fully installed in cache once translations are done (before updating .env, to avoid a server restart crashing the app)
            InstallState::markInstalled();
            InstallState::clearLanguageSetupPending();

            Setting::set('app_language_direction', $request->app_direction);
            config(['app.language_direction' => $request->app_direction]);

            return redirect(route('login'))->with('success', 'Installation and Language setup successful! Please login.');

        } catch (\Exception $e) {
            return back()->with('error', 'Language setup failed: ' . $e->getMessage())->withInput();
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

class InstallController extends Controller
{
    public function index() {
        try {
            // check cache first
            if (Cache::get('system_installed')) {
                return redirect()->back()->with('error', 'Error: system has already been installed.');
            }

            // redirect to login if a user exists (ie. already installed)
            if (User::exists()) {
                if(Cache::get('lang_setup_pending')) { // redirect to lang setup if a user exists (ie. db is installed but language not configured)
                    return redirect(route('install.lang_setup_view'));
                }else{
                    // cache it for next time
                    Cache::forever('system_installed', true);
                    return redirect()->back()->with('error', 'Error: system has already been installed.');
                }
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
        ]);

        try {
            // update runtime configuration to use new credentials
            config(['database.default' => 'mysql']);
            config(['database.connections.mysql.host' => $request->db_host]);
            config(['database.connections.mysql.port' => $request->db_port]);
            config(['database.connections.mysql.database' => $request->db_name]);
            config(['database.connections.mysql.username' => $request->db_username]);
            config(['database.connections.mysql.password' => $request->db_password]);

            // force reconnection and verify connection
            DB::purge('mysql');
            DB::reconnect('mysql');
            DB::connection()->getPdo();

            // run migrations and seed
            Artisan::call('migrate:fresh', ['--seed' => true]);


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

            #
            
            // LLM Translation
            $translation_schema = config('translations.groups');
            $english_labels = [];
            foreach ($translation_schema as $group => $keys) {
                $english_labels = array_merge($english_labels, $keys);
            }

            $apiKey = config('services.mistral.key');
            $auto_translations = [];

            if ($apiKey && !empty($english_labels)) {
                $payload = [
                    "model" => "mistral-small-latest",
                    "response_format" => ["type" => "json_object"],
                    "messages" => [
                        [
                            "role" => "system",
                            "content" =>
                                "Return a pure JSON object where the keys are EXACTLY the English strings provided to you, and the values are their highly accurate translations into: " . $locale_name . ". " .
                                "Do not change or omit any of the original english keys."
                        ],
                        [
                            "role" => "user",
                            "content" => json_encode($english_labels)
                        ]
                    ]
                ];

                try {
                    $response = \Illuminate\Support\Facades\Http::withToken($apiKey)
                        ->timeout(30)
                        ->post('https://api.mistral.ai/v1/chat/completions', $payload);

                    if ($response->successful()) {
                        $result = $response->json();
                        $rawContent = $result["choices"][0]["message"]["content"] ?? "{}";
                        $clean = preg_replace('/^```json\s*|\s*```$/i', '', trim($rawContent));
                        $auto_translations = json_decode($clean, true) ?: [];
                    }
                } catch (\Exception $e) {
                    // silently fail and fallback to empty boxes if the API drops
                }
            }
            // ---------------------------------------------

            Cache::forever('auto_translations', $auto_translations);
            #
            
            Cache::forever('locale_name', $locale_name);
            Cache::forever('lang_setup_pending', true);

            // update .env (triggers artisan serve restart)
            $this->updateEnv([
                'APP_URL' => $request->app_url,
                'APP_LOCALE' => $request->app_locale,
                'DB_CONNECTION' => 'mysql',
                'DB_HOST' => $request->db_host,
                'DB_PORT' => $request->db_port,
                'DB_DATABASE' => $request->db_name,
                'DB_USERNAME' => $request->db_username,
                'DB_PASSWORD' => $request->db_password,
            ]);

            return redirect(route('install.lang_setup_view'))->with('success', 'Database installed successfully! Please configure language translations.');

        } catch (\Exception $e) {
            return back()->with('error', 'Installation failed: ' . $e->getMessage())->withInput();
        }
    }

    private function updateEnv($data = [])
    {
        $path = base_path('.env');
        if (File::exists($path)) {
            $file_content = File::get($path);
            foreach ($data as $key => $value) {
                $value = $value ?? ''; // set null value to empty string
                $value = '"' . $value . '"';
                
                // regex to find the key and replace value
                $pattern = "/^" . preg_quote($key) . "=(.*)$/m";
                
                if (preg_match($pattern, $file_content)) {
                    $file_content = preg_replace_callback($pattern, function () use ($key, $value) {
                         return "$key=$value";
                    }, $file_content);
                } else {
                    $file_content .= "\n$key=$value"; // append it if not existing
                }
            }
            File::put($path, $file_content);
        }
    }

    public function lang_setup_view(Request $request) {
        try {
            if(!Cache::get('lang_setup_pending')){
                return redirect()->back()->with('error', 'Error: language has already been setup.');
            }
        } catch (\Exception $e) {
            // db not configured/migrated/seeded, continue to installation
            return redirect(route('install.index'));
        }

        $locale = env('APP_LOCALE');
        $locale_name = Cache::get('locale_name', $locale); // fallback to locale code if name isn't found
        $app_direction = in_array($locale, ['ar', 'he', 'fa', 'ur']) ? 'rtl' : 'ltr';

        $translation_schema = config('translations.groups');
        $auto_translations = Cache::get('auto_translations', []);
        
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

            // update .env direction
            $this->updateEnv([
                'APP_LANGUAGE_DIRECTION' => $request->app_direction,
            ]);
            

            // mark system as fully installed in cache once translations are done
            Cache::forever('system_installed', true);
            Cache::forget('lang_setup_pending');

            return redirect(route('login'))->with('success', 'Installation and Language setup successful! Please login.');

        } catch (\Exception $e) {
            return back()->with('error', 'Language setup failed: ' . $e->getMessage())->withInput();
        }
    }
}

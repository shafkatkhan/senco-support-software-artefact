<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>MySencoSupportSoftware | Installation</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/install.css') }}" type="text/css" />
</head>
<body>
    <div id="install-processing-container">
        <div class="install-card">
            <div class="card-header">
                <div class="title">SENCOSupportSoftware Setup</div>
                <div class="subtitle">Processing...</div>
            </div>
            <div class="card-body">
                <div class="loading-container">
                    <div class="spinner-grow" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div id="loading_status"></div>
                    <div id="loading_status_description"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="install-form-container">
        <div class="install-card">
            <div class="card-header">
                <div class="title">SENCOSupportSoftware Setup</div>
                <div class="subtitle">Initial Installation & Configuration</div>
            </div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('install.process') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-section-title mt-0">System Configuration</div>
                    <div class="mb-3">
                        <label class="form-label">App URL</label>
                        <input type="text" class="form-control" name="app_url" placeholder="http://localhost" value="http://localhost" required>
                        <div class="form-text text-muted">The root URL where this application is hosted.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">System Language</label>
                        <select class="form-select" name="app_locale" required>
                            <option value="af">Afrikaans</option>
                            <option value="sq">Albanian - (Shqip)</option>
                            <option value="am">Amharic - (አማርኛ)</option>
                            <option value="ar">Arabic - (العربية)</option>
                            <option value="an">Aragonese - (Aragonés)</option>
                            <option value="hy">Armenian - (Հայերեն)</option>
                            <option value="ast">Asturian - (Asturianu)</option>
                            <option value="ay">Aymara - (Aymar aru)</option>
                            <option value="az">Azerbaijani - (Azərbaycan dili)</option>
                            <option value="eu">Basque - (Euskara)</option>
                            <option value="be">Belarusian - (Беларуская)</option>
                            <option value="bn">Bengali - (বাংলা)</option>
                            <option value="brx">Bodo - (बर')</option>
                            <option value="bs">Bosnian - (Bosanski)</option>
                            <option value="br">Breton - (Brezhoneg)</option>
                            <option value="bg">Bulgarian - (Български)</option>
                            <option value="ca">Catalan - (Català)</option>
                            <option value="ckb">Central - Kurdish (کوردی)</option>
                            <option value="ce">Chechen (Нохчийн мотт)</option>
                            <option value="zh">Chinese (中文)</option>
                            <option value="zh-HK">Chinese Hong Kong - (中文 - 香港)</option>
                            <option value="zh-CN">Chinese Simplified - (中文简体)</option>
                            <option value="zh-TW">Chinese Traditional - (中文繁體)</option>
                            <option value="co">Corsican - (Corsu)</option>
                            <option value="hr">Croatian - (Hrvatski)</option>
                            <option value="cs">Czech - (Čeština)</option>
                            <option value="da">Danish - (Dansk)</option>
                            <option value="dv">Dhivehi - (ދިވެހި)</option>
                            <option value="nl">Dutch (Nederlands)</option>
                            <option value="dz">Dzongkha (རྫོང་ཁ)</option>
                            <option value="en" selected>English</option>
                            <option value="en-AU">English (Australia)</option>
                            <option value="en-CA">English (Canada)</option>
                            <option value="en-IN">English (India)</option>
                            <option value="en-NZ">English (New Zealand)</option>
                            <option value="en-ZA">English (South Africa)</option>
                            <option value="en-GB">English (United Kingdom)</option>
                            <option value="en-US">English (United States)</option>
                            <option value="eo">Esperanto</option>
                            <option value="et">Estonian (Eesti)</option>
                            <option value="ee">Ewe (Eʋegbe)</option>
                            <option value="fo">Faroese - (Føroyskt)</option>
                            <option value="fil">Filipino (Wikang Filipino)</option>
                            <option value="fi">Finnish (Suomi)</option>
                            <option value="fr">French (Français)</option>
                            <option value="fr-CA">French Canada - (Français Canada)</option>
                            <option value="fr-FR">French France - (Français France)</option>
                            <option value="fr-CH">French Switzerland - (Français Suisse)</option>
                            <option value="gl">Galician - (Galego)</option>
                            <option value="ka">Georgian - (ქართული)</option>
                            <option value="de">German - (Deutsch)</option>
                            <option value="de-AT">German Austria  - (Deutsch Österreich)</option>
                            <option value="de-DE">German Germany  - (Deutsch Deutschland)</option>
                            <option value="de-LI">German Liechtenstein  - (Deutsch Liechtenstein)</option>
                            <option value="de-CH">German Switzerland  - (Deutsch Schweiz)</option>
                            <option value="el">Greek - (Ελληνικά)</option>
                            <option value="gn">Guarani - (Avañe'ẽ)</option>
                            <option value="gu">Gujarati - (ગુજરાતી)</option>
                            <option value="ha">Hausa - (هَوُسَ)</option>
                            <option value="haw">Hawaiian - (ʻŌlelo Hawaiʻi)</option>
                            <option value="he">Hebrew - (עברית)</option>
                            <option value="hi">Hindi - (हिन्दी)</option>
                            <option value="hu">Hungarian - (Magyar)</option>
                            <option value="is">Icelandic - (Íslenska)</option>
                            <option value="id">Indonesian - (Bahasa Indonesia)</option>
                            <option value="ia">Interlingua</option>
                            <option value="iu">Inuktitut - (ᐃᓄᒃᑎᑐᑦ)</option>
                            <option value="ga">Irish - (Gaeilge)</option>
                            <option value="it">Italian - (Italiano)</option>
                            <option value="it-IT">Italian Italy - (Italiano Italia)</option>
                            <option value="it-CH">Italian Switzerland - (Italiano - Svizzera)</option>
                            <option value="ja">Japanese - (日本語)</option>
                            <option value="kl">Kalaallisut - (Kalaallisut)</option>
                            <option value="kn">Kannada - (ಕನ್ನಡ)</option>
                            <option value="ks">Kashmiri - (कॉशुर / كٲشُر)</option>
                            <option value="kk">Kazakh - (Қазақ тілі)</option>
                            <option value="km">Khmer - (ខ្មែរ)</option>
                            <option value="rw">Kinyarwanda - (Ikinyarwanda)</option>
                            <option value="ko">Korean - (한국어)</option>
                            <option value="ku">Kurdish - (Kurdî)</option>
                            <option value="ky">Kyrgyz - (Кыргызча)</option>
                            <option value="lo">Lao - (ລາວ)</option>
                            <option value="la">Latin - (Latina)</option>
                            <option value="lv">Latvian - (Latviešu)</option>
                            <option value="ln">Lingala - (Lingála)</option>
                            <option value="lt">Lithuanian - (Lietuvių)</option>
                            <option value="lg">Luganda - (Luganda)</option>
                            <option value="lb">Luxembourgish - (Lëtzebuergesch)</option>
                            <option value="mk">Macedonian - (Македонски)</option>
                            <option value="mai">Maithili - (मैथिली)</option>
                            <option value="ms">Malay - (Bahasa Melayu)</option>
                            <option value="ml">Malayalam - (മലയാളം)</option>
                            <option value="mt">Maltese - (Malti)</option>
                            <option value="mni">Manipuri - (ꯃꯅꯤꯄꯨꯔꯤ)</option>
                            <option value="mr">Marathi - (मराठी)</option>
                            <option value="mn">Mongolian - (Монгол)</option>
                            <option value="ne">Nepali - (नेपाली)</option>
                            <option value="nso">Northern Sotho - (Sesotho sa Leboa)</option>
                            <option value="no">Norwegian (Norsk)</option>
                            <option value="nb">Norwegian Bokmål - (Norsk bokmål)</option>
                            <option value="nn">Norwegian Nynorsk - (Nynorsk)</option>
                            <option value="oc">Occitan</option>
                            <option value="or">Oriya - (ଓଡ଼ିଆ)</option>
                            <option value="om">Oromo - (Afaan Oromoo)</option>
                            <option value="os">Ossetian - (Ирон æвзаг)</option>
                            <option value="ps">Pashto - (پښتو)</option>
                            <option value="fa">Persian - (فارسی)</option>
                            <option value="pl">Polish - (Polski)</option>
                            <option value="pt">Portuguese - (Português)</option>
                            <option value="pt-BR">Portuguese Brazil - (Português Brasil)</option>
                            <option value="pt-PT">Portuguese Portugal - (Português Portugal)</option>
                            <option value="pa">Punjabi - (ਪੰਜਾਬੀ)</option>
                            <option value="qu">Quechua - (Runa Simi)</option>
                            <option value="ro">Romanian - (Română)</option>
                            <option value="mo">Romanian  Moldova - (Română Moldova)</option>
                            <option value="rm">Romansh - (Rumantsch)</option>
                            <option value="ru">Russian - (Русский)</option>
                            <option value="sm">Samoan - (Gagana Samoa)</option>
                            <option value="sat">Santali - (ᱥᱟᱱᱛᱟᱲᱤ)</option>
                            <option value="sc">Sardinian - (Sardu)</option>
                            <option value="gd">Scottish Gaelic - (Gàidhlig)</option>
                            <option value="sr">Serbian - (Српски)</option>
                            <option value="sh">Serbo_Croatian - (Srpskohrvatski)</option>
                            <option value="sn">Shona - (ChiShona)</option>
                            <option value="sd">Sindhi - (سنڌي)</option>
                            <option value="si">Sinhala - (සිංහල)</option>
                            <option value="sk">Slovak - (Slovenčina)</option>
                            <option value="sl">Slovenian - (Slovenščina)</option>
                            <option value="so">Somali - (Soomaali)</option>
                            <option value="st">Southern - Sotho (Sesotho)</option>
                            <option value="es">Spanish - (Español)</option>
                            <option value="es-AR">Spanish  Argentina  - (Español Argentina)</option>
                            <option value="es-419">Spanish  Latin America  - (Español Latinoamérica)</option>
                            <option value="es-MX">Spanish  Mexico  - (Español  México)</option>
                            <option value="es-ES">Spanish  Spain  - (Español España)</option>
                            <option value="es-US">Spanish  United States  - (Español Estados Unidos)</option>
                            <option value="su">Sundanese - (Basa Sunda)</option>
                            <option value="sw">Swahili - (Kiswahili)</option>
                            <option value="sv">Swedish - (Svenska)</option>
                            <option value="tg">Tajik - (Тоҷикӣ)</option>
                            <option value="ta">Tamil - (தமிழ்)</option>
                            <option value="tt">Tatar - (Татар)</option>
                            <option value="te">Telugu - (తెలుగు)</option>
                            <option value="th">Thai - (ไทย)</option>
                            <option value="ti">Tigrinya - (ትግርኛ)</option>
                            <option value="to">Tongan - (Lea fakatonga)</option>
                            <option value="tn">Tswana - (Setswana)</option>
                            <option value="tr">Turkish - (Türkçe)</option>
                            <option value="tk">Turkmen - (Türkmençe)</option>
                            <option value="tw">Twi - (Twi)</option>
                            <option value="udm">Udmurt - (Удмурт кыл)</option>
                            <option value="uk">Ukrainian - (Українська)</option>
                            <option value="ur">Urdu - (اردو)</option>
                            <option value="ug">Uyghur - (ئۇيغۇرچە)</option>
                            <option value="uz">Uzbek - (O'zbek)</option>
                            <option value="ve">Venda - (Tshivenḓa)</option>
                            <option value="vi">Vietnamese - (Tiếng Việt)</option>
                            <option value="wa">Walloon - (Walon)</option>
                            <option value="cy">Welsh - (Cymraeg)</option>
                            <option value="fy">Western Frisian - (Frysk)</option>
                            <option value="wo">Wolof - (Wollof)</option>
                            <option value="xh">Xhosa - (isiXhosa)</option>
                            <option value="yi">Yiddish - (ייִדיש)</option>
                            <option value="yo">Yoruba - (Èdè Yorùbá)</option>
                            <option value="za">Zhuang - (Saɯ cueŋƅ)</option>
                            <option value="zu">Zulu - (isiZulu)</option>
                        </select>
                        <div class="form-text text-muted">Select the default language for the application.</div>
                    </div>
                    <!--  -->
                    <div class="form-section-title">Database Configuration</div>
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label">Database Host</label>
                            <input type="text" class="form-control" name="db_host" placeholder="127.0.0.1" value="127.0.0.1" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Port</label>
                            <input type="text" class="form-control" name="db_port" placeholder="3306" value="3306" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Database Name</label>
                        <input type="text" class="form-control" name="db_name" placeholder="senco_db" required>
                        <div class="form-text text-muted">Ensure this database exists in your MySQL server.</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Database Username</label>
                            <input type="text" class="form-control" name="db_username" placeholder="root" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Database Password</label>
                            <input type="password" class="form-control" name="db_password" placeholder="">
                        </div>
                    </div>
                    <!--  -->
                    <div class="form-section-title">Restore from Backup (Optional)</div>
                    <div class="mb-4">
                        <label class="form-label">Database Backup File (.sql)</label>
                        <input class="form-control" type="file" name="restore_file" accept=".sql">
                        <div class="form-text text-muted">Upload a <b>.sql</b> file to automatically restore the entire system state.</div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Install System</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script>
        $(function () {
            var formContainer = $('#install-form-container');
            var processingContainer = $('#install-processing-container');
            var appLocale = $('select[name="app_locale"]');
            var statusText = $('#loading_status');
            var descriptionText = $('#loading_status_description');

            $('#install-form-container form').on('submit', function() {
                var locale = appLocale.val();
                var isEnglish = locale && locale.startsWith('en');
                
                if (isEnglish) {
                    statusText.text('Configuring database and environment...');
                    descriptionText.text('This should only take a moment.');
                } else {
                    statusText.text('Connecting to AI Translation Service...');
                    descriptionText.text('This may take a few minutes, depending on your language.');
                }

                // immediately hide and show
                formContainer.hide();
                processingContainer.show();
                
                // force layout scroll to ensure visibility
                window.scrollTo(0, 0);
            });
        });
    </script>
</body>
</html>

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
    <div class="install-card">
        <div class="card-header">
            <div class="title">SENCOSupportSoftware Setup</div>
            <div class="subtitle">Language Configuration</div>
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
            <div class="translation_top row">
                <div class="col-sm-3">
                    <div class="left">
                        Original: <span>English</span>
                    </div>
                </div>
                <div class="col-sm-9">
                    <div class="right">
                        Translated: <span class="text-uppercase">{{ $locale_name }}</span>
                    </div>
                </div>
            </div>
            <form action="{{ route('install.lang_setup') }}" method="POST">
                @csrf
                <input type="hidden" name="app_locale" value="{{ $locale }}">
                
                @foreach($translation_schema as $group => $keys)
                    <div class="form-section-title">{{ $group }}</div>
                    
                    @foreach($keys as $english_label)
                        <div class="row mb-3 align-items-center">
                            <label class="col-sm-3 col-form-label">{{ $english_label }}</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="translations[{{ base64_encode($english_label) }}]" placeholder="{{ $english_label }}" required>
                            </div>
                        </div>
                    @endforeach
                @endforeach

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">Save Translations</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>

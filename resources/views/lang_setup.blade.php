<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>MySencoSupportSoftware | Installation</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link id="bootstrap-css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/install.css') }}" type="text/css" />
</head>
<body>
    <div id="install-form-container">
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
                    <div class="col-sm-4">
                        <div class="left">
                            Original: <span>English</span>
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="right">
                            Translated: <span class="text-uppercase">{{ $locale_name }}</span>
                        </div>
                    </div>
                </div>
                <form action="{{ route('install.lang_setup') }}" method="POST">
                    @csrf
                    <input type="hidden" name="app_locale" value="{{ $locale }}">
                    
                    <div class="form-section-title">Language Direction</div>
                    <div class="row mb-3 align-items-center">
                        <label class="col-sm-4 col-form-label">Direction</label>
                        <div class="col-sm-8">
                            <select class="form-control" name="app_direction"required>
                                <option value="ltr" {{ $app_direction == 'ltr' ? 'selected' : '' }}>Left-to-Right (LTR)</option>
                                <option value="rtl" {{ $app_direction == 'rtl' ? 'selected' : '' }}>Right-to-Left (RTL)</option>
                            </select>
                        </div>
                    </div>
                    
                    @foreach($translation_schema as $group => $keys)
                        <div class="form-section-title">{{ $group }}</div>
                        
                        @foreach($keys as $english_label)
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-4 col-form-label">{{ $english_label }}</label>
                                <div class="col-sm-8">
                                    <textarea class="form-control auto_resize_textarea" rows="1" name="translations[{{ base64_encode($english_label) }}]" placeholder="{{ $english_label }}" required>{{ $auto_translations[$english_label] ?? '' }}</textarea>
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
    </div>

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script>
        $(function () {
            $('select[name="app_direction"]').on('change', function() {
                var dir = $(this).val();
                if (dir == 'rtl') {
                    $('html').attr('dir', 'rtl');
                    $('#bootstrap-css').attr('href', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.rtl.min.css');
                } else {
                    $('html').attr('dir', 'ltr');
                    $('#bootstrap-css').attr('href', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css');
                }
            });
            $('select[name="app_direction"]').trigger('change');

            function resizeTextarea(el) {
                $(el).css('height', 'auto').css('height', el.scrollHeight + 2 + 'px');
            }
            $('.auto_resize_textarea').on('input', function() {
                resizeTextarea(this);
            });
            $('.auto_resize_textarea').each(function() {
                resizeTextarea(this);
            });
        });
    </script>
</body>
</html>

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ is_rtl() ? 'rtl' : 'ltr' }}" class="{{ request()->is('pupil-*') ? 'has-secondary-nav' : '' }}">
<head>
    <meta charset="utf-8">
    <title>{{ 'EduSen | '.$title ?? 'EduSen' }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="{{ asset('img/favicon.ico') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @if(is_rtl())
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.rtl.min.css" integrity="sha384-CfCrinSRH2IR6a4e6fy2q6ioOX7O6Mtm1L9vRvFZ1trBncWmMePhzvafv7oIcWiW" crossorigin="anonymous">
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    @endif
    <link href="https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" type="text/css" />
    <script>
        (function() {
            var view_preference = localStorage.getItem('sen_view_preference') || 'card';
            document.documentElement.classList.add('view-pref-' + view_preference);

            var nav_hidden = localStorage.getItem('sen_nav_hidden');
            if (nav_hidden == 'true') {
                document.documentElement.classList.add('nav-hidden');
            }
        })();
    </script>
    <style>
        html.view-pref-card #toggleViewTable, 
        html.view-pref-card #pupilsTable { display: none !important; }
        
        html.view-pref-card #toggleViewGrid, 
        html.view-pref-card #pupilsGrid { display: flex !important; }

        html.view-pref-table #toggleViewTable, 
        html.view-pref-table #pupilsTable { display: block !important; }
        
        html.view-pref-table #toggleViewGrid, 
        html.view-pref-table #pupilsGrid { display: none !important; }
    </style>
</head>
<body>

<div id="alert-container" class="sen_alert" style="display: none;"></div>

@include('components.nav')
@include('components.top_nav')
<div id="nav_overlay"></div>

@yield('content')

<script>
    // get the current locale's JSON file contents, or an empty object if it doesn't exist
    window.translations = @json(
        file_exists(base_path('lang/' . app()->getLocale() . '.json')) 
            ? json_decode(file_get_contents(base_path('lang/' . app()->getLocale() . '.json')), true) 
            : []
    );

    // function to get the translation in same way as laravel
    window.__ = function(key) {
        return window.translations[key] || key;
    };
</script>

<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/2.3.7/js/dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('js/script.js') }}"></script>
<script src="{{ asset('js/file_data_extraction.js') }}"></script>
@stack('scripts')

@if($errors->any())
    <script>
         niceAlert("danger", "Error:", "{{ $errors->first() }}")
    </script>
@endif

@if(session('error'))
    <script>
        niceAlert("danger", "Error:", "{{ session('error') }}")
    </script>
@endif

@if(session('success'))
    <script>
        niceAlert("success", "", "{{ session('success') }}")
    </script>
@endif

@if(session('warning'))
    <script>
        niceAlert("warning", "", "{{ session('warning') }}")
    </script>
@endif
</body>
</html>
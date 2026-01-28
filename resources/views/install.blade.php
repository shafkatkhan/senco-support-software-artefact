<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>MySencoSupportSoftware - Installation</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/install.css') }}" type="text/css" />
</head>
<body>
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

            <form action="{{ route('install.process') }}" method="POST">
                @csrf
                <div class="form-section-title mt-0">System Configuration</div>
                <div class="mb-3">
                    <label class="form-label">App URL</label>
                    <input type="text" class="form-control" name="app_url" placeholder="http://localhost" value="http://localhost" required>
                    <div class="form-text text-muted">The root URL where this application is hosted.</div>
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
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">Install System</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>

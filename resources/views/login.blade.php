<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/fontawesome.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" type="text/css" />
    <link type="text/css" rel="stylesheet" href="{{ asset('css/login.css') }}">
    <title>MySencoSupportSoftware | Login</title>
</head>
<body class="login_body">
    <section id="loginBox">
        <div class="login_logo">
            <img src="{{ asset('img/logo.png') }}" alt="MySencoSupportSoftware">
        </div>
        <div class="login_form">
            <div class="text">
                <span>Welcome,</span> please login
            </div>
            <form action="" method="POST">
                <div class="form-group">
                    <input id="email" type="email" class="form-control" placeholder="Email address" name="email">
                </div>
                <div class="form-group">
                    <input id="password" type="password" class="form-control" placeholder="Password" name="password">
                </div>
                <button id="login_btn" class="btn btn-lg btn-primary btn-block" type="submit" name="sign_in">Login</button>
            </form>
            <div class="alert alert-danger alert-dismissible fade" role="alert">
                Email address or password is incorrect. <strong style="font-weight:500;">Please try again.</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
        </div>
        <div class="login_info">
            <div>
               © MySencoSupportSoftware {{ date("Y") }} 
            </div>
            <div>
                <a href="https://shafkatkhan.com">
                    Shafkat Khan | KCL 2026
                </a>                
            </div>
        </div>
    </section>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>
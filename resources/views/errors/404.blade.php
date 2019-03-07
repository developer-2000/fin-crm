<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>404</title>
        <link rel='stylesheet' href="{{ URL::asset('css/bootstrap.min.css') }}" type='text/css' media='all' />
        <link rel='stylesheet' href="{{ URL::asset('css/custom.min.css') }}" type='text/css' media='all' />
    </head>
    <body class="nav-md">
        <div class="container body">
            <div class="main_container">
                <div class="col-md-12">
                    <div class="col-middle">
                        <div class="text-center text-center">
                            <h1 class="error-number">404</h1>
                            <h2> @lang('errors.page-not-found') </h2>
                            <p><a href="{{ route('index') }}"> @lang('errors.go-to-mian') </a></p>
                            {{-- Хотите перейти на Главную? --}}
                            <div class="mid_center"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

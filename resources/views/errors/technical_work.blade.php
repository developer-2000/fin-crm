<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title> @lang('errors.error')</title>
        <link rel='stylesheet' href="{{ URL::asset('css/bootstrap.min.css') }}" type='text/css' media='all' />
        <link rel='stylesheet' href="{{ URL::asset('css/custom.min.css') }}" type='text/css' media='all' />

        <link rel="stylesheet" type="text/css" href="{{ URL::asset('x_css/font-awesome.css')}}"/>
    </head>
    <body class="nav-md">
        <div class="container body">
            <div class="main_container">
                <div class="col-md-12">
                    <div class="col-middle">
                        <div class="text-center text-center">
                            <h1 class="error-number">
                                <span class="fa-stack">
                                    <i class="fa fa-wrench" ></i>
                                </span>
                            </h1>
                            <h2> @lang('errors.technical-work') </h2>
                            {{-- На сайте ведутся технические работы --}}
                            <div class="mid_center"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Koperasi') }} |
        {{ Helper::set_value('judul_web', @Helper::SettingApp()['judul_web']) }}</title>
    <link rel="shortcut icon"
        href="{{ asset('assets/img/' . Helper::set_value('favicon', @Helper::SettingApp()['favicon'])) }}" />
    @stack('css')

    <!-- Bootstrap CSS -->
    <link href="{{ asset('assets/css/bootstrap-5.0.2/css/bootstrap.min.css') }}" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/utilities.css') }}">

    <link href="{{ asset('assets/css/fonts.css') }}" rel="stylesheet">
    <!-- Styles -->

</head>

<body>

    @include('layouts.partials.header')

    @include('layouts.partials.sidebar')

    <div class="main-wrapper app-wrapper" id="app-wrapper">
        <div class="content">

            <div class="home-two-column">
                <div id="primary">
                    <div class="grid">
                        <div class="header">
                            <div class="sub-header">
                                @yield('breadcumb')
                            </div>
                        </div>

                        <div class="content-main">
                            <div class="container-fluid">
                                @yield('content')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- jquery -->
    <script src="{{ asset('assets/js/jquery/jquery.min.js') }}"></script>

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // function preview(target, image) {
        //     $(target)
        //         .attr('src', window.URL.createObjectURL(image));

        // }
    </script>

    @stack('script')
    <script src="{{ asset('assets/js/main.js') }}"></script>

</body>

</html>

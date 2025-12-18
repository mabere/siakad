<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="js">

<head>
    <base href="../">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Siakad | @yield('title', 'Dashboard')</title>

    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/dashlite.css?ver=3.1.3') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css?ver=3.1.3') }}" id="skin-default">

    <style>
        .swal2-confirm.success {
            background-color: #28a745 !important;
        }

        .swal2-confirm.update {
            background-color: #17a2b8 !important;
        }

        .swal2-confirm.delete {
            background-color: #dc3545 !important;
        }

        .swal2-confirm.error {
            background-color: #dc3545 !important;
        }
    </style>

    @stack('style')
</head>

<body class="nk-body bg-lighter npc-general has-sidebar">
    <div class="nk-app-root">
        <div class="nk-main">
            @include('layouts._sidebar')
            <div class="nk-wrap">
                @include('layouts._header')
                <div class="nk-content">
                    <div class="container-fluid">
                        <div class="nk-content-inner">
                            <div class="nk-content-body">
                                @yield('content')
                            </div>
                        </div>
                    </div>
                </div>

                @include('layouts._footer')
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('assets/js/bundle.js?ver=3.1.3') }}"></script>
    <script src="{{ asset('assets/js/scripts.js?ver=3.1.3') }}"></script>
    <script src="{{ asset('assets/js/charts/gd-default.js?ver=3.1.3') }}"></script>
    <script src="{{ asset('assets/js/libs/datatable-btns.js?ver=3.1.3') }}"></script>
    <script src="{{ asset('assets/js/example-sweetalert.js?ver=3.1.3') }}"></script>
    @stack('scripts')
</body>

</html>

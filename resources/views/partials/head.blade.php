<head>
    <!-- META DATA -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="Author" content="">
    <!-- TITLE -->
    <title> {{ __('gigat_platform') }} | @yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- FAVICON -->
    <link rel="icon" href="{{ asset('build/assets/images/media/favicon.png') }}" type="image/x-icon">
    <!-- ICONS CSS -->
    <link href="{{ asset('build/assets/iconfonts/icons.css') }}" rel="stylesheet">
    <!-- APP SCSS -->
    <link rel="preload" as="style" href="{{ asset('build/assets/app-698853b8.css') }}" />
    <link rel="stylesheet" href="{{ asset('build/assets/app-698853b8.css') }}" />
    <!-- NODE WAVES CSS -->
    <link href="{{ asset('build/assets/libs/node-waves/waves.min.css') }}" rel="stylesheet">
    <!-- SIMPLEBAR CSS -->
    <link rel="stylesheet" href="{{ asset('build/assets/libs/simplebar/simplebar.min.css') }}">
    <!-- COLOR PICKER CSS -->
    <link rel="stylesheet" href="{{ asset('build/assets/libs/%40simonwep/pickr/themes/nano.min.css') }}">

    <!-- TOASTR-->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/css/toastr.min.css"
          integrity="sha384-ZSs6LKr2GoUPDyHrN+rCQgyHL1yUyok5xMniSrgeRG7rUvA6vTmxronM1eZOfjgz"
          crossorigin="anonymous">

    <!-- SWEET ALERT -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.5/dist/sweetalert2.min.css"
          integrity="sha384-tInLLRmWKTSYCM075omF3Hyr4Bk9U2LkO8JAlalZ1OXH6jl8NRjni5vSYqzovHvT"
          crossorigin="anonymous">

    <!-- MAIN JS — defer prevents render-blocking (P2-08/FPERF-01) -->
    <script src="{{ asset('build/assets/main.js') }}" defer></script>

    <style>
        .error-help-block{
            color: red;
        }
        /* .select2-search {
            border: 1px solid #e6eaeb !important;
            padding: 8px !important;
            border-radius: 11px !important;
        }
        .select2-search{
            border: 1px solid #e6eaeb !important;
            padding: 8px !important;
            border-radius: 11px !important;
        } */
    </style>
</head>

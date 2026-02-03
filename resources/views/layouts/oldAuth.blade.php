<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-vertical-style="overlay" class="light"
    data-header-styles="light" data-menu-styles="light" data-toggled="close">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    {{-- <title>Gigat Platform | Login</title> --}}
    <title>@yield('title', 'Gigat Platform')</title>

    <!-- FAVICON -->
    <link rel="icon" href="" type="image/x-icon">
    <!-- ICONS CSS -->
    <link href="{{ asset('build/assets/iconfonts/icons.css') }}" rel="stylesheet">
    <!-- APP SCSS -->
    <link rel="preload" as="style" href="{{ asset('build/assets/app-698853b8.css') }}" />
    <link rel="stylesheet" href="{{ asset('build/assets/app-698853b8.css') }}" />
    <!-- TOASTR-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    @stack('styles')

</head>

<body>
    @yield('content')
    @stack('scripts')

    <!-- SCRIPTS -->
    <!-- Show Password JS -->
    <script src="{{ asset('build/assets/show-password.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif

        @if (session('info'))
            toastr.info("{{ session('info') }}");
        @endif

        @if (session('warning'))
            toastr.warning("{{ session('warning') }}");
        @endif
    </script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#prefixLogin').select2({
                placeholder: "{{ __('select_prefix') }}",
                allowClear: false,
                width: '100%',
                closeOnSelect: true
            });
        });
    </script>
    <!-- END SCRIPTS -->
</body>

</html>

<!DOCTYPE html>
<html lang="en" dir="ltr" class="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Gigat Platform')</title>

    <link rel="icon" href="{{ asset('build/assets/images/media/favicon.png') }}" type="image/x-icon">

    {{-- Tailwind + Icons (CDN is fine for auth pages; switch to your build if you prefer) --}}
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    {{-- Auth styles (compiled) --}}
    <link rel="stylesheet" href="{{ asset('build/assets/auth.css') }}" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    @stack('styles')
</head>

<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
    {{-- Background decorative bubbles (optional) --}}
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-10 left-10 w-20 h-20 bg-white opacity-10 rounded-full floating-animation"></div>
        <div class="absolute top-1/3 right-20 w-16 h-16 bg-white opacity-10 rounded-full floating-animation"
            style="animation-delay:-2s;"></div>
        <div class="absolute bottom-20 left-1/4 w-12 h-12 bg-white opacity-10 rounded-full floating-animation"
            style="animation-delay:-4s;"></div>
    </div>

    {{-- Page content --}}
    @yield('content')

    {{-- Shared scripts for auth pages (optional) --}}
    @stack('scripts')

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
    {{-- <script>
        $(document).ready(function() {
            $('#prefixLogin').select2({
                placeholder: "{{ __('select_prefix') }}",
                allowClear: false,
                width: '100%',
                closeOnSelect: true
            });
        });
    </script> --}}
    <script>
        (function initPrefixSelect() {
            if (!window.jQuery || !jQuery.fn.select2) return setTimeout(initPrefixSelect, 100);

            $('#prefixLogin').select2({
                width: 'style', // use the element’s width (7rem)
                dropdownAutoWidth: true,
                minimumResultsForSearch: 0, // 👈 ALWAYS show the search box
                placeholder: "{{ __('select_prefix') }}",
                allowClear: false,
                dropdownParent: $(document.body) // avoids clipping inside containers
            });

            // optional: match the input look
            const $sel = $('#prefixLogin').data('select2').$container.find('.select2-selection--single');
            $sel.css({
                height: '48px',
                borderRadius: '12px',
                borderColor: '#D1D5DB',
                display: 'flex',
                alignItems: 'center',
                padding: '0 .75rem'
            });
        })();
    </script>

</body>

</html>

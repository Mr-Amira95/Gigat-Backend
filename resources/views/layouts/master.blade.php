<!DOCTYPE html>
<html lang="en" data-nav-layout="vertical" class="light" data-header-styles="light" data-menu-styles="dark">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
@include('partials.head')
@stack('styles')

<body>
    <!-- LOADER -->
    <div id="loader">
        <img src="{{ asset('build/assets/images/media/loader.svg') }}" alt="">
    </div>
    <!-- END LOADER -->
    <!-- PAGE -->
    <div class="page">


        @include('partials.header')
        @include('partials.switcher')
        @include('partials.sidebar')
        <!-- MAIN-CONTENT -->
        @yield('content')
        <!-- END MAIN-CONTENT -->
        @include('partials.footer')

    </div>
    <!-- END PAGE-->
    @include('partials.scripts')
    @stack('scripts')
</body>

</html>

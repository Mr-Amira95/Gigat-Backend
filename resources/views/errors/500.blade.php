<!DOCTYPE html>
<html lang="ar" dir="rtl" data-nav-layout="vertical" data-vertical-style="overlay" class="light"
    data-header-styles="light" data-menu-styles="light" data-toggled="close">

<head>
    <!-- TITLE -->
    <title>500 SERVER ERROR</title>

    <!-- FAVICON -->
    <link rel="icon" href="{{ asset('build/assets/images/favicon.ico') }}" type="image/x-icon">

    <!-- ICONS CSS -->
    <link href="{{ asset('build/assets/iconfonts/icons.css') }}" rel="stylesheet">

    <!-- APP SCSS -->
    <link rel="preload" as="style" href="{{ asset('build/assets/app-698853b8.css') }}" />
    <link rel="stylesheet" href="{{ asset('build/assets/app-698853b8.css') }}" />

    <!-- MAIN JS -->
    <script src="{{ asset('build/assets/authentication-main.js') }}"></script>
</head>

<body>
    <div class="page error-bg dark:!bg-bodybg" id="particles-js">
        <!-- Start::error-page -->
        <div class="error-page">
            <div class="container text-defaulttextcolo dark:text-defaulttextcolor/70 text-defaultsize">
                <div class="text-center p-5 my-auto">
                                       <h1 style="font-size: 10rem">500</h1>

                    <div class="flex items-center justify-center h-full !text-defaulttextcolor">
                        <div class="xl:col-span-3"></div>
                        <div class="xl:col-span-6 col-span-12">
                            <h2 class="text-[1.125rem] font-semibold mb-4">
                                SORRY, THERE IS A PROBLEM WITH THE SERVER
                            </h2>
                            <div class="flex justify-center items-center mb-[3rem]">
                                <div class="xl:col-span-6 w-[75%]">
                                    <h3 class="mb-0 opacity-[0.7]">
                                        WE APOLOGIZE FOR THE INCONVENIENCE. A SERVER ISSUE HAS OCCURRED. PLEASE TRY
                                        AGAIN LATER </h3>
                                </div>
                            </div>
                            <a href="javascript:void(0);" onclick="window.history.back();"
                                class="ti-btn btn-wave bg-primary text-white font-semibold">
                                <i class="ri-arrow-left-line align-middle inline-block"></i>
                                Back
                            </a>
                        </div>
                        <div class="xl:col-span-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPTS -->
    <!-- Particles JS -->
    <script src="{{ asset('build/assets/libs/particles.js/particles.js') }}"></script>

    <!-- Error JS -->
    <link rel="modulepreload" href="{{ asset('build/assets/error-97cc9c6c.js') }}" />
    <script type="module" src="{{ asset('build/assets/error-97cc9c6c.js') }}"></script>
</body>

</html>

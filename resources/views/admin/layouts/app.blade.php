<!DOCTYPE html>

<html lang="en" class="light-style layout-menu-fixed" dir="ltr"
    data-theme="theme-default"
    data-assets-path="{{ asset('backend') }}/assets/" data-template="vertical-menu-template-free">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport"
            content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

        <title>Dashboard - CSV Import</title>

        <meta name="description" content />

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon"
            href="{{ asset('backend') }}/assets/img/favicon/favicon.ico" />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
            rel="stylesheet" />

        @yield('css')

        <!-- Page CSS -->

        <!-- Helpers -->
        <script src="{{ asset('backend') }}/assets/vendor/js/helpers.js"></script>

        <script src="{{ asset('backend') }}/assets/js/config.js"></script>
    </head>

    <body>

        <div class="layout-wrapper layout-content-navbar">
            <div class="layout-container">
                @include('admin.partials.sidebar')
                <!-- Layout container -->
                <div class="layout-page">
                    @include('admin.partials.header')

                    @yield('main')
                    @include('admin.partials.footer')
                </div>
                <!-- / Layout page -->
            </div>


            <div class="layout-overlay layout-menu-toggle"></div>
        </div>


       @yield('script')
    </body>

</html>

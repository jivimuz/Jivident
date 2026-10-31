<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from templates.iqonic.design/aprycot/html/dashboard/dist/dashboard/admin-dashboard.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 29 Feb 2024 13:35:24 GMT -->

@include('layout/header')
@yield('style')


<body>
    <div class="app-wrapper">
        <div class="loader-wrapper">
            <div class="app-loader">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>

        @include('layout/navbar')
        <div class="app-content ">
            <div class="">
                @include('layout/topcontent')
                <main>
                    <div class="container-fluid mt-3">
                        @yield('content')
                    </div>
                </main>
            </div>
        </div>
        <!-- tap on top -->
        <div class="go-top">
            <span class="progress-value">
                <i class="ti ti-chevron-up"></i>
            </span>
        </div>

        @include('layout/footer')
    </div>

    @yield('script')

</body>

</html>

<?php
use App\Models\Setting;
// ys note: tidak semua user adalah employee
$LoginName = auth()->user()->load('employee')->employee->fullname;

$logo = Setting::where('parameter', 'company_logo')->first()->value ?: url('/assets/logo.png');
// $app_name = Setting::where('parameter', 'app_name')->first()->value ?: 'AppName';
// $company_name = Setting::where('parameter', 'company_name')->first()->value ?: 'Company Name';
?>
<!-- Header Section starts -->
<header class="header-main">
    <div class="container-fluid">
        <div class="row">
            <div class="col-6 col-sm-4 d-flex align-items-center header-left p-0">
                <span class="header-toggle me-3">
                    <i class="iconoir-view-grid"></i>
                </span>
            </div>

            <div class="col-6 col-sm-8 d-flex align-items-center justify-content-end header-right p-0">
                <a href="{{ url('monitor/antrian') }}" target="_blank" class="btn btn-sm btn-primary rounded-pill"> <svg
                        width="23" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path opacity="0.4"
                            d="M21.3309 7.44251C20.9119 7.17855 20.3969 7.1552 19.9579 7.37855L18.4759 8.12677C17.9279 8.40291 17.5879 8.96129 17.5879 9.58261V15.4161C17.5879 16.0374 17.9279 16.5948 18.4759 16.873L19.9569 17.6202C20.1579 17.7237 20.3729 17.7735 20.5879 17.7735C20.8459 17.7735 21.1019 17.7004 21.3309 17.5572C21.7499 17.2943 21.9999 16.8384 21.9999 16.339V8.66179C21.9999 8.1623 21.7499 7.70646 21.3309 7.44251Z"
                            fill="currentColor"></path>
                        <path
                            d="M11.9051 20H6.11304C3.69102 20 2 18.3299 2 15.9391V9.06091C2 6.66904 3.69102 5 6.11304 5H11.9051C14.3271 5 16.0181 6.66904 16.0181 9.06091V15.9391C16.0181 18.3299 14.3271 20 11.9051 20Z"
                            fill="currentColor"></path>
                    </svg> Monitor </a>
                <ul class="d-flex align-items-center">

                    <li class="header-cloud mobile-hidden">
                        <a aria-controls="cloudoffcanvasTops" class="head-icon" data-bs-target="#cloudoffcanvasTops"
                            data-bs-toggle="offcanvas" href="#" role="button">
                            <i class="iconoir-dew-point text-primary f-s-26 me-1"></i>
                            <span class="f-w-600">32 <sup class="f-s-10">°C</sup></span>
                        </a>

                        <div aria-labelledby="cloudoffcanvasTops" class="offcanvas offcanvas-end header-cloud-canvas"
                            id="cloudoffcanvasTops" tabindex="-1">
                            <div class="offcanvas-body p-0">
                                <div class="cloud-body">

                                    <div class="cloud-content-box">
                                        <div class="cloud-box bg-primary-900">
                                            <p class="mb-3">Mon</p>
                                            <h6 class="mt-4 f-s-13"> +31°C</h6>
                                            <span>
                                                <i class="ph-duotone  ph-cloud-fog text-white f-s-25"></i>
                                            </span>
                                            <p class="f-s-13 mt-3"><i class="wi wi-raindrop"></i> Sen</p>
                                        </div>
                                        <div class="cloud-box bg-primary-800">
                                            <p class="mb-3">Tue</p>
                                            <h6 class="mt-4 f-s-13"> +34°C</h6>
                                            <span>
                                                <i class="ph-duotone  ph-cloud-sun text-white f-s-25"></i>
                                            </span>
                                            <p class="f-s-13 mt-3"><i class="wi wi-raindrop"></i> Sel</p>
                                        </div>
                                        <div class="cloud-box bg-primary-700">
                                            <p class="mb-3 text-light">Wed</p>
                                            <h6 class="mt-4 f-s-13"> +35°C</h6>
                                            <span>
                                                <i class="ph-duotone  ph-sun-dim text-white f-s-25"></i>
                                            </span>
                                            <p class="f-s-13 mt-3"><i class="wi wi-raindrop"></i> Rab</p>
                                        </div>
                                        <div class="cloud-box bg-primary-600">
                                            <p class="mb-3">Thu</p>
                                            <h6 class="mt-4 f-s-13"> +28°C</h6>
                                            <span>
                                                <i class="ph-duotone  ph-sun-dim text-white f-s-25"></i>
                                            </span>
                                            <p class="f-s-13 mt-3"><i class="wi wi-raindrop"></i> Kam</p>
                                        </div>
                                        <div class="cloud-box bg-primary-500">
                                            <p class="mb-3">Fri</p>
                                            <h6 class="mt-4 f-s-13"> +25°C</h6>
                                            <span>
                                                <i class="ph-duotone  ph-sun-dim text-white f-s-25"></i>
                                            </span>
                                            <p class="f-s-13 mt-3"><i class="wi wi-raindrop"></i> Jum</p>
                                        </div>
                                        <div class="cloud-box bg-primary-400">
                                            <p class="mb-3">Sat</p>
                                            <h6 class="mt-4 f-s-13"> +33°C</h6>
                                            <span>
                                                <i class="ph-duotone  ph-sun-dim text-white f-s-25"></i>
                                            </span>
                                            <p class="f-s-13 mt-3"><i class="wi wi-raindrop"></i> Sab</p>
                                        </div>
                                        <div class="cloud-box bg-primary-300">
                                            <p class="mb-3">Sun</p>
                                            <h6 class="mt-4 f-s-13"> +29°C</h6>
                                            <span class="mb-3">
                                                <i class="ph-duotone  ph-sun-dim text-white f-s-25"></i>
                                            </span>
                                            <p class="f-s-13 mt-3"><i class="wi wi-raindrop"></i> Min</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>

                    <li class="header-language mobile-hidden">
                        <div class="flex-shrink-0 dropdown" id="lang_selector">
                            <a aria-expanded="false" class="d-block head-icon ps-0" data-bs-toggle="dropdown"
                                href="#">
                                <div class="lang-flag lang-en ">
                                    <span class="flag rounded-circle overflow-hidden">
                                        <i class=""></i>
                                    </span>
                                </div>
                            </a>
                            <ul class="dropdown-menu language-dropdown header-card border-0">
                                <li class="lang lang-en selected dropdown-item p-2" data-bs-placement="top"
                                    data-bs-toggle="tooltip" title="US">
                                    <span class="d-flex align-items-center">
                                        <i class="flag-icon flag-icon-idn flag-icon-squared b-r-10 f-s-22"></i>
                                        <span class="ps-2">Indonesia</span>
                                    </span>
                                </li>
                            </ul>
                        </div>

                    </li>

                    <li class="header-searchbar mobile-hidden">
                        <a aria-controls="offcanvasRight" class="d-block head-icon" data-bs-target="#offcanvasRight"
                            data-bs-toggle="offcanvas" href="#" role="button">
                            <i class="iconoir-search"></i>
                        </a>

                        <div aria-labelledby="offcanvasRight" class="offcanvas offcanvas-end header-searchbar-canvas"
                            id="offcanvasRight" tabindex="-1">
                            <div class="header-searchbar-header">
                                <div class="d-flex justify-content-between mb-3">
                                    <form action="#" class="app-form app-icon-form w-100">
                                        <div class="position-relative">
                                            <input aria-label="Search" class="form-control search-filter"
                                                placeholder="Search..." type="search">
                                            <i class="ti ti-search text-dark"></i>
                                        </div>
                                    </form>

                                    <div class="app-dropdown flex-shrink-0">
                                        <a aria-expanded="false"
                                            class="h-35 w-35 d-flex-center b-r-15 overflow-hidden bg-light-secondary search-list-avtar ms-2"
                                            data-bs-auto-close="outside" data-bs-toggle="dropdown" href="#"
                                            role="button">
                                            <i class="ph-duotone  ph-gear f-s-20"></i>
                                        </a>

                                        <ul class="dropdown-menu mb-3">
                                            <li class="dropdown-item mt-2">
                                                <a href="#">
                                                    <h6 class="mb-0">Search Settings</h6>
                                                </a>
                                            </li>
                                            <li
                                                class="dropdown-item d-flex align-items-center justify-content-between">
                                                <a href="#">
                                                    <h6 class="mb-0 text-secondary f-s-14">Safe Search
                                                        Filtering</h6>
                                                </a>
                                                <div class="flex-shrink-0">
                                                    <div class="form-check form-switch">
                                                        <input checked class="form-check-input form-check-primary"
                                                            id="searchSwitch" type="checkbox">
                                                    </div>
                                                </div>
                                            </li>
                                            <li
                                                class="dropdown-item d-flex align-items-center justify-content-between">
                                                <a href="#">
                                                    <h6 class="mb-0 text-secondary f-s-14">Search
                                                        Suggestions</h6>
                                                </a>
                                                <div class="flex-shrink-0">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input form-check-primary"
                                                            id="searchSwitch1" type="checkbox">
                                                    </div>
                                                </div>
                                            </li>
                                            <li
                                                class="dropdown-item d-flex align-items-center justify-content-between">
                                                <h6 class="mb-0 text-secondary f-s-14"> Search History</h6>
                                                <i class="ti ti-message-circle me-3  text-success"></i>
                                            </li>
                                            <li class="dropdown-divider"></li>
                                            <li
                                                class="dropdown-item d-flex align-items-center justify-content-between mb-2">
                                                <a href="#">
                                                    <h6 class="mb-0 text-dark f-s-14">Custom Search
                                                        Preferences</h6>
                                                </a>
                                                <div class="flex-shrink-0">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input form-check-primary"
                                                            id="searchSwitch2" type="checkbox">
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <p class="mb-0 text-secondary f-s-15 mt-2">Recently Searched Data:</p>
                            </div>
                            <div class="offcanvas-body app-scroll p-0">
                                <div>
                                    <ul class="search-list">
                                        <li class="search-list-item">
                                            <div
                                                class="h-35 w-35 d-flex-center b-r-15 overflow-hidden bg-light-primary search-list-avtar">
                                                <i class="ph-duotone  ph-gear f-s-20"></i>
                                            </div>
                                            <div class="search-list-content">
                                                <a href="api.html" target="_blank">
                                                    <h6 class="mb-0 text-dark">Setting</h6>
                                                </a>
                                                <p class="f-s-13 mb-0 text-secondary">#RA789</p>
                                            </div>
                                        </li>
                                        <li class="search-list-item">
                                            <div
                                                class="h-35 w-35 d-flex-center b-r-15 overflow-hidden bg-light-warning search-list-avtar">
                                                <i class="ph-duotone  ph-projector-screen-chart f-s-20"></i>
                                            </div>
                                            <div class="search-list-content">
                                                <a href="privacy_policy.html" target="_blank">
                                                    <h6 class="mb-0 text-dark">Pasien</h6>
                                                </a>
                                                <p class="f-s-13 mb-0 text-secondary">#RY810</p>
                                            </div>
                                        </li>
                                        <li class="search-list-item">
                                            <div
                                                class="h-35 w-35 d-flex-center b-r-15 overflow-hidden bg-light-danger search-list-avtar">
                                                <i class="ph-duotone  ph-shield-check f-s-20"></i>
                                            </div>
                                            <div class="search-list-content">
                                                <a href="terms_condition.html" target="_blank">
                                                    <h6 class="mb-0 text-dark">Tindakan</h6>
                                                </a>
                                                <p class="f-s-13 mb-0 text-secondary">#ATR56</p>
                                            </div>
                                        </li>
                                        <li class="search-list-item">
                                            <div
                                                class="h-35 w-35 d-flex-center b-r-15 overflow-hidden bg-light-info search-list-avtar">
                                                <i class="ph-duotone  ph-app-window f-s-20"></i>
                                            </div>
                                            <div class="search-list-content">
                                                <a href="sign_in.html" target="_blank">
                                                    <h6 class="mb-0 text-dark">Billing</h6>
                                                </a>
                                                <p class="f-s-13 mb-0 text-secondary">#YE615</p>
                                            </div>
                                        </li>
                                        <li class="search-list-item">
                                            <div
                                                class="h-35 w-35 d-flex-center b-r-15 overflow-hidden bg-light-dark search-list-avtar">
                                                <i class="ph-duotone  ph-table f-s-20"></i>
                                            </div>
                                            <div class="search-list-content">
                                                <a href="data_table.html" target="_blank">
                                                    <h6 class="mb-0 text-dark">Laporan</h6>
                                                </a>
                                                <p class="f-s-13 mb-0 text-secondary">#YE615</p>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </li>





                    <li class="header-dark">
                        <div class="sun-logo head-icon">
                            <i class="iconoir-sun-light"></i>
                        </div>
                        <div class="moon-logo head-icon">
                            <i class="iconoir-half-moon"></i>
                        </div>
                    </li>

                    <li class="header-notification">
                        <a aria-controls="notificationcanvasRight" class="d-block head-icon position-relative"
                            data-bs-target="#notificationcanvasRight" data-bs-toggle="offcanvas" href="#"
                            role="button">
                            <i class="iconoir-bell"></i>
                            <span
                                class="position-absolute translate-middle p-1 bg-success border border-light rounded-circle animate__animated animate__fadeIn animate__infinite animate__slower"></span>
                        </a>
                        <div aria-labelledby="notificationcanvasRightLabel"
                            class="offcanvas offcanvas-end header-notification-canvas" id="notificationcanvasRight"
                            tabindex="-1">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="notificationcanvasRightLabel">
                                    Notification</h5>
                                <button aria-label="Close" class="btn-close" data-bs-dismiss="offcanvas"
                                    type="button"></button>
                            </div>
                            <div class="offcanvas-body notification-offcanvas-body app-scroll p-0">
                                <div class="head-container notification-head-container">
                                    <div class="notification-message head-box">
                                        <div class="message-images">
                                            <span
                                                class="bg-secondary h-35 w-35 d-flex-center b-r-10 position-relative">
                                                <img alt="avtar" class="img-fluid b-r-10"
                                                    src="{{ url('') }}/assets/images/avtar/7.png">
                                                <span
                                                    class="position-absolute bottom-30 end-0 p-1 bg-secondary border border-light rounded-circle notification-avtar"></span>
                                            </span>
                                        </div>
                                        <div class="message-content-box flex-grow-1 ps-2">

                                            <a class="f-s-15 text-secondary mb-0" href="#"
                                                target="_blank"><span class="f-w-500 text-secondary">You</span>
                                                Anda berhasil login</a>
                                            <div>
                                                <a class="d-inline-block f-w-500 text-success me-1" href="#">IP
                                                    Anda: {{ request()->ip() }}</a>
                                            </div>
                                            <span class="badge text-light-primary mt-2">
                                                {{ WIB(date('Y-m-d H:i:s')) }}

                                            </span>

                                        </div>
                                        <div class="align-self-start text-end">
                                            <i class="iconoir-xmark close-btn"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </li>

                    <li class="header-profile">
                        <a aria-controls="profilecanvasRight" class="d-block head-icon"
                            data-bs-target="#profilecanvasRight" data-bs-toggle="offcanvas" href="#"
                            role="button">
                            <img alt="avtar" class="b-r-50 h-35 w-35 bg-dark"
                                src="{{ url('') }}/assets/images/avtar/7.png">
                        </a>

                        <div aria-labelledby="profilecanvasRight"
                            class="offcanvas offcanvas-end header-profile-canvas" id="profilecanvasRight"
                            tabindex="-1">
                            <div class="offcanvas-body app-scroll">
                                <ul class="">
                                    <li class="d-flex gap-3 mb-3">
                                        <div class="d-flex-center border-b">
                                            <span class="h-45 w-45 d-flex-center b-r-10 position-relative">
                                                <img alt="" class="img-fluid b-r-10"
                                                    src="{{ url('') }}/assets/images/avtar/7.png">
                                            </span>
                                        </div>
                                        <div class=" mt-2">
                                            <h6 class="mb-0"> {{ $LoginName }} <img alt="instagram-check-mark"
                                                    class="w-20 h-20"
                                                    src="{{ url('') }}/assets/images/profile-app/01.png"></h6>
                                            <p class="f-s-12 mb-0 text-secondary">{{ auth()->user()->email }}</p>
                                        </div>
                                    </li>

                                    <li>
                                        <a class="f-w-500 hover-border-b" href="{{ url('auth-license') }}">
                                            <i class="iconoir-window-lock pe-1 f-s-20"></i> License
                                        </a>
                                    </li>
                                    <li>

                                        <a class="f-w-500 hover-border-b" onclick="changePass()">
                                            <i class="iconoir-password-cursor pe-1 f-s-20"></i> Change Password
                                        </a>
                                    </li>
                                    <li class="app-divider-v dotted py-1"></li>

                                    <li>
                                        <a class="mb-0 btn btn-light-danger btn-sm justify-content-center "
                                            href="{{ url('/logout') }}" role="button">
                                            <i class="ph-duotone  ph-sign-out pe-1 f-s-20"></i> Log Out
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
<!-- Header Section ends -->

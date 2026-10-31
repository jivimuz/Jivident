<!DOCTYPE html>
<html lang="en">

{{--
    Layout tanpa sidebar/topcontent — untuk halaman yang ingin tampil penuh
    layar dengan menu sendiri (pertama dipakai oleh POS Apotek). Tetap
    @include header/footer yang sama dengan layout/main supaya semua asset
    (jQuery, Bootstrap, DataTables, SweetAlert2, Select2, Toast, dll) dan
    modal profil (ganti password/tanda tangan) tetap tersedia tanpa
    duplikasi — bedanya cuma navbar & topcontent tidak di-include, dan
    wrapper konten tidak pakai class `app-content` (yang di style.css
    reserve `padding-left: var(--sidebar-width)` buat sidebar).
--}}

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

        @yield('content')

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

<?php
use App\Models\Setting;
$app_name = Setting::where('parameter', 'app_name')->first()?->value ?: 'JiviDent';
$company_icon = Setting::where('parameter', 'company_icon')->first()?->value ? url(Setting::where('parameter', 'company_icon')->first()->value) : url('/assets/favicon.ico');
?>
<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from phpstack-1384472-5121645.cloudwaysapps.com/template/html/axelit/template/error_500.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 11 Sep 2025 07:01:28 GMT -->

<head>
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Multipurpose, super flexible, powerful, clean modern responsive bootstrap 5 admin template"
        name="description">
    <meta
        content="admin template, axelit admin template, dashboard template, flat admin template, responsive admin template, web app"
        name="keywords">
    <meta content="la-themes" name="author">
    <link href="{{ $company_icon }}" rel="icon" type="image/x-icon">
    <link href="{{ $company_icon }}" rel="shortcut icon" type="image/x-icon">

    <title>{{ $app_name }}</title>

    <!--font-awesome-css-->
    <link href="{{ url('') }}/assets/vendor/fontawesome/css/all.css" rel="stylesheet">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com/" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&amp;display=swap"
        rel="stylesheet">

    <!-- iconoir icon css  -->
    <link href="{{ url('') }}/assets/vendor/ionio-icon/css/iconoir.css" rel="stylesheet">

    <!-- tabler icons-->
    <link href="{{ url('') }}/assets/vendor/tabler-icons/tabler-icons.css" rel="stylesheet" type="text/css">

    <!-- Bootstrap css-->
    <link href="{{ url('') }}/assets/vendor/bootstrap/bootstrap.min.css" rel="stylesheet" type="text/css">

    <!-- App css-->
    <link href="{{ url('') }}/assets/css/style.css" rel="stylesheet" type="text/css">

    <!-- Responsive css-->
    <link href="{{ url('') }}/assets/css/responsive.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ url('') }}/assets/css/sweetalert2.min.css">

</head>

<body>

    <div class="error-container p-0">
        <div class="container content-inner mt-5 py-0">
            <div class="row justify-content-center mt-5">
                <div class="col-12 col-md-8 col-lg-5">
                    <div class="card license-card">
                        <div class="card-body p-5 text-center">
                            <div class="d-flex justify-content-end mb-2">
                                <a href="{{ url('native-config') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                                    <i class="iconoir-settings"></i> Setup DB &amp; Server
                                </a>
                            </div>
                            <div class="icon-box">
                                <i class="iconoir-key-command text-theme" style="font-size: 40px;"></i>
                            </div>
                            <h4 class="fw-bold mb-2">Aktivasi Lisensi</h4>
                            @if (session('isExpired'))
                                <p class="text-muted mb-4 text-sm">Lisensi ada kadaluwarsa. Harap masukan Lisensi yang
                                    baru!</p>
                            @else
                                <p class="text-muted mb-4 text-sm">Masukkan kode lisensi Anda untuk memverifikasi dan
                                    mengaktifkan aplikasi.</p>
                            @endif

                            <form id="form-license">
                                <div class="form-group text-start mb-4">
                                    <label class="fw-bold mb-2">License Key</label>
                                    <input type="text" id="license_key" name="license_key"
                                        class="form-control form-control-lg text-center"
                                        placeholder="XXXX-XXXX-XXXX-XXXX" required
                                        style="letter-spacing: 2px; text-transform: uppercase;">
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold"
                                    id="btn-activate">
                                    <i class="iconoir-check-circle"></i> Verifikasi Lisensi
                                </button>
                                <a href="{{ url('/') }}"
                                    class="btn btn-warning btn-lg w-100 rounded-pill fw-bold mt-2" id="btn-activate">
                                    <i class="iconoir-home"></i>Kembali
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ url('') }}/assets/js/sweetalert2.min.js"></script>
    <!-- latest jquery-->
    <script src="{{ url('') }}/assets/js/jquery-3.6.3.min.js"></script>

    <!-- Bootstrap js-->
    <script src="{{ url('') }}/assets/vendor/bootstrap/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {

            // Format input huruf besar (Uppercase)
            $('#license_key').on('input', function() {
                $(this).val($(this).val().toUpperCase());
            });

            // Submit Form via AJAX
            $('#form-license').on('submit', function(e) {
                e.preventDefault();

                let btnSubmit = $('#btn-activate');
                let originalText = btnSubmit.html();
                let licenseKey = $('#license_key').val();

                if (!licenseKey) {
                    Swal.fire('Peringatan', 'License Key tidak boleh kosong!', 'warning');
                    return;
                }

                btnSubmit.html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memverifikasi...'
                ).prop('disabled', true);

                $.ajax({
                    url: "/license/validate", // URL Endpoint Backend Anda
                    type: "POST",
                    data: {
                        license_key: licenseKey
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Aktivasi Berhasil',
                                text: response.message,
                                confirmButtonText: 'Lanjutkan',
                                confirmButtonColor: '#3a57e8'
                            }).then((result) => {
                                // Redirect ke dashboard setelah sukses
                                window.location.href = "/";
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Aktivasi Gagal',
                                html: (response.message || 'Lisensi tidak valid.') + '<br><br><a href="https://wa.me/6282120741970?text=Halo%20Admin,%20lisensi%20Jivident%20saya%20tidak%20valid." target="_blank" class="btn btn-success btn-sm rounded-pill mt-2"><i class="fab fa-whatsapp"></i> Hubungi Admin (+6282120741970)</a>',
                                confirmButtonText: 'Tutup'
                            });
                            btnSubmit.html(originalText).prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = "Terjadi kesalahan saat menghubungi server lisensi.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Koneksi / Aktivasi Gagal',
                            html: errorMsg + '<br><br><a href="https://wa.me/6282120741970?text=Halo%20Admin,%20saya%20mengalami%20kendala%20aktivasi%20lisensi%20Jivident." target="_blank" class="btn btn-success btn-sm rounded-pill mt-2"><i class="fab fa-whatsapp"></i> Hubungi Admin (+6282120741970)</a>',
                            confirmButtonText: 'Tutup'
                        });
                        btnSubmit.html(originalText).prop('disabled', false);
                    }
                });
            });
        });
    </script>

</body>


<!-- Mirrored from phpstack-1384472-5121645.cloudwaysapps.com/template/html/axelit/template/error_500.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 11 Sep 2025 07:01:29 GMT -->

</html>

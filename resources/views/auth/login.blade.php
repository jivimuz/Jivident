<!DOCTYPE html>
<html lang="en">
<?php
use App\Models\Setting;

$logo = Setting::where('parameter', 'company_logo')->first()->value ?: url('/assets/logo.png');
?>

<head>
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <meta content="la-themes" name="author">
    <link href="{{ url('') }}/assets/favicon.ico" rel="icon" type="image/x-icon">
    <link href="{{ url('') }}/assets/favicon.ico" rel="shortcut icon" type="image/x-icon">

    <title>Jivident - Login</title>

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
    <div class="app-wrapper d-block">
        <div class="">
            <!-- Body main section starts -->
            <main class="w-100 p-0">
                <!-- Login to your Account start -->
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12 p-0">
                            <div class="login-form-container">
                                <div class="mb-4">
                                    <a class="logo d-inline-block" href="index-2.html">
                                        <img alt="#" src="{{ $logo }}" width="250">
                                    </a>
                                </div>
                                <div class="form_container" style="max-width:95% !important">

                                    <form class="app-form rounded-control" id="login-form">
                                        <div class="mb-3 text-center">
                                            <h3 class="text-primary-dark">Login to your Account</h3>
                                            <p class="f-s-12 text-secondary">Get started with our app, just create an
                                                account and enjoy the experience.</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Username/Email address</label>
                                            <input class="form-control" type="text" id="username">
                                            <div class="form-text text">We'll never share your account with anyone else.
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Password</label>
                                            <input class="form-control" type="password" id="password">
                                        </div>
                                        <div class="mb-3 form-check">
                                            <input class="form-check-input" id="formCheck1" type="checkbox">
                                            <label class="form-check-label" for="formCheck1">remember me</label>
                                        </div>
                                        <div>
                                            <button class="btn btn-light-primary w-100" role="button">Submit</button>
                                        </div>
                                        <div class="app-divider-v justify-content-center">
                                            <p>Follow My Social</p>
                                        </div>
                                        <div class="mb-3">
                                            <div class="text-center">
                                                <a target="_blank" href="https://www.linkedin.com/in/jivimuz"
                                                    class="btn btn-light-primary icon-btn b-r-5 m-1" type="button"><i
                                                        class="ti ti-brand-linkedin "></i></a>
                                                <a target="_blank" href="https://www.instagram.com/jivimz_/"
                                                    class="btn btn-light-danger icon-btn b-r-5 m-1" type="button"><i
                                                        class="ti ti-brand-instagram "></i></a>
                                                <a target="_blank" href="https://github.com/jivimuz"
                                                    class="btn btn-light-dark icon-btn b-r-5 m-1" type="button"><i
                                                        class="ti ti-brand-github "></i></a>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Login to your Account end -->
            </main>
            <!-- Body main section ends -->
        </div>
    </div>
    <!-- latest jquery-->
    <script src="{{ url('') }}/assets/js/jquery-3.6.3.min.js"></script>

    <!-- Bootstrap js-->
    <script src="{{ url('') }}/assets/vendor/bootstrap/bootstrap.bundle.min.js"></script>

    <script src="{{ url('') }}/assets/js/sweetalert2.min.js"></script>

</body>
<script>
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
        }
    });

    $('#login-form').on("submit", function(e) {
        e.preventDefault()
        var username = $('#username').val();
        var password = $('#password').val();
        if (!username || !password) {
            return Toast.fire({
                icon: "warning",
                text: "Fill the Username & Password first"
            });
        }

        $.ajax({
            url: "{{ url('/') }}/login",
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                username: username,
                password: password,
            },
            success: function(data) {
                console.log(data)
                Swal.fire({
                    position: "top-end",
                    icon: "success",
                    title: "Login Success",
                    text: "Redirecting..",
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    location.href = '{{ url('/') }}'
                });
            },
            error: function(xhr, status, error) {
                Toast.fire({
                    icon: "error",
                    text: JSON.parse(xhr.responseText).error
                });

            }
        });

    })
</script>

</html>

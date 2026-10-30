<head>
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Turbo Drive: intercepts same-origin link clicks/form submits and swaps <body> via fetch
    instead of a full page reload, so navigation feels like an SPA without a JS framework rewrite.
    Hover-prefetch is disabled since a few links in this app (e.g. logout) are plain GET and must
    only fire on an actual click. --}}
    <meta name="turbo-prefetch" content="false">
    <script src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.23/dist/turbo.es2017-umd.js"></script>

    <meta content="la-themes" name="author">
    <link href="{{ url('') }}/assets/favicon.ico" rel="icon" type="image/x-icon">
    <link href="{{ url('') }}/assets/favicon.ico" rel="shortcut icon" type="image/x-icon">

    <title>Jivident </title>
    <!--font-awesome-css-->
    <link href="{{ url('') }}/assets/vendor/fontawesome/css/all.css" rel="stylesheet">



    <!-- iconoir icon css  -->
    <link href="{{ url('') }}/assets/vendor/ionio-icon/css/iconoir.css" rel="stylesheet">

    <!-- Animation css -->
    <link href="{{ url('') }}/assets/vendor/animation/animate.min.css" rel="stylesheet">

    <!-- tabler icons-->
    <link href="{{ url('') }}/assets/vendor/tabler-icons/tabler-icons.css" rel="stylesheet" type="text/css">

    <!--flag Icon css-->
    <link href="{{ url('') }}/assets/vendor/flag-icons-master/flag-icon.css" rel="stylesheet" type="text/css">

    <!-- Bootstrap css-->
    <link href="{{ url('') }}/assets/vendor/bootstrap/bootstrap.min.css" rel="stylesheet" type="text/css">

    <!-- apexcharts css-->
    <link href="{{ url('') }}/assets/vendor/apexcharts/apexcharts.css" rel="stylesheet" type="text/css">

    <!-- simplebar css-->
    <link href="{{ url('') }}/assets/vendor/simplebar/simplebar.css" rel="stylesheet" type="text/css">

    <!-- slick css -->
    <link href="{{ url('') }}/assets/vendor/slick/slick.css" rel="stylesheet">
    <link href="{{ url('') }}/assets/vendor/slick/slick-theme.css" rel="stylesheet">

    <!-- filepond css -->
    <link href="{{ url('') }}/assets/vendor/filepond/filepond.css" rel="stylesheet">
    <link href="{{ url('') }}/assets/vendor/filepond/image-preview.min.css" rel="stylesheet">

    <!-- App css-->
    <link href="{{ url('') }}/assets/css/style.css" rel="stylesheet" type="text/css">

    <!-- Responsive css-->
    <link href="{{ url('') }}/assets/css/responsive.css" rel="stylesheet" type="text/css">


    <link href="{{ url('') }}/assets/vendor/datepikar/flatpickr.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ url('') }}/assets/css/datatables.min.css">
    <link rel="stylesheet" href="{{ url('') }}/assets/css/select2.min.css">
    <link rel="stylesheet" href="{{ url('') }}/assets/css/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ url('') }}/assets/css/dropify.min.css">
    {{-- <link rel="stylesheet" href="{{url('')}}/assets/font-awesome/css/font-awesome.min.css"> --}}
    <link rel="stylesheet" href="{{ url('') }}/assets/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="{{ url('') }}/assets/css/customStyle.css">
</head>

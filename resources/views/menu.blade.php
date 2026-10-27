@extends('layout/main')
@section('style')
    <style>
        .menu-list {
            border-radius: 50px;
            width: 100%;
            height: 130px;
            vertical-align: middle;
        }
    </style>
    {{-- <div id="loading">
        <div class="loader simple-loader">
            <img src="assets/images/logo.png" style="width: 50%" alt="">
        </div>
    </div> --}}
@endsection

@section('content')
    <div class="content-inner mt-5 py-0">
        <div class="row">
            <div class="col-md-12 mb-3">
                <div class="bg-white w-100 rounded-pill p-2 d-flex align-items-center justify-content-between shadow-sm">
                    {{-- <a href="javascript:history.back()"
                        class="btn btn-primary rounded-pill d-flex align-items-center justify-content-center"
                        style="width:45px; height:45px;">
                        <i class="iconoir-fast-arrow-left" style="font-size:20px;"></i>
                    </a> --}}
                    <span class="text-primary fw-bold text-center flex-grow-1" style="font-size:24px;">
                        Welcome to JiviDent
                    </span>
                    <div style="width:45px;"></div> <!-- spacer biar teks tetap di tengah -->
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Pasien Hari Ini</small>
                            <h3 class="mb-0">{{ $patientsToday }}</h3>
                        </div>
                        <i class="iconoir-group" style="font-size:28px"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Omset Hari Ini</small>
                            <h3 class="mb-0">{{ rupiah($revenueToday) }}</h3>
                        </div>
                        <i class="iconoir-coins" style="font-size:28px"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Produk Stok Habis</small>
                            <h3 class="mb-0">{{ $outOfStockCount }}</h3>
                        </div>
                        <i class="iconoir-warning-triangle" style="font-size:28px"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-12 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Omset 7 Hari Terakhir</h4>
                    </div>
                    <div class="card-body">
                        <div id="revenueChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card" data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40" data-iq-duration=".6"
                    data-iq-delay=".4" data-iq-trigger="scroll" data-iq-ease="none">
                    <div class="card-header">
                        <div class="float-end">
                            <input type="search" class="form-control" placeholder="Search..."
                                data-listener-added_aa86fd06="true"id="namaMenu" onchange="searchMenu()"
                                style="width: 200px">
                        </div>
                        <h4 class="card-title">Home</h4>
                        <small>List Menu</small>
                    </div>
                    <div class="card-body" data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40"
                        data-iq-duration=".6" data-iq-delay=".6" data-iq-trigger="scroll" data-iq-ease="none"
                        style="padding-left: 20px; padding-right:20px">

                        {{-- isi Content --}}
                        <div id="menuList">
                            Loading...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            searchMenu()

            new ApexCharts(document.querySelector("#revenueChart"), {
                chart: {
                    type: 'bar',
                    height: 250,
                    toolbar: {
                        show: false
                    }
                },
                series: [{
                    name: 'Omset',
                    data: @json($revenueChartValues)
                }],
                xaxis: {
                    categories: @json($revenueChartLabels)
                },
                yaxis: {
                    labels: {
                        formatter: function(val) {
                            return 'Rp ' + val.toLocaleString('id-ID')
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return 'Rp ' + val.toLocaleString('id-ID')
                        }
                    }
                },
                colors: ['#3b82f6']
            }).render()
        })

        function searchMenu() {
            $.ajax({
                url: "{{ url('menu') }}",
                method: 'post',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    menu: $('#namaMenu').val(),
                },
                success: function(data) {
                    $('#menuList').html(data)

                },
                error: function(xhr, status, error) {
                    Toast.fire({
                        icon: "error",
                        title: JSON.parse(xhr.responseText).error
                    });

                }
            });
        }

        // document.addEventListener('DOMContentLoaded', (event) => {
        //     loaderInit()
        // });
        // const loaderInit = () => {
        //     const loader = document.querySelector('.loader')

        //     loader.classList.add('animate__animated', 'animate__fadeOut')
        //     setTimeout(() => {
        //         loader.classList.add('d-none')
        //     }, 1000)
        // }
    </script>
@endsection

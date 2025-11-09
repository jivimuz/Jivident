@extends('layout/main')
@section('style')
    <style>
        .hideThis {
            display: none !important
        }
    </style>
@endsection

@section('content')
    <div class="content-inner mt-5 py-0">
        <div class="row">
            <div class="col-md-12">
                <div class="card" data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40" data-iq-duration=".6"
                    data-iq-delay=".4" data-iq-trigger="scroll" data-iq-ease="none">
                    <div class="card-header">
                        <div class="float-end">

                        </div>
                        <h4 class="card-title">Ruang Praktek</h4>
                        <small>List Ruang Praktek</small>
                    </div>
                    <div class="card-body" data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40"
                        data-iq-duration=".6" data-iq-delay=".6" data-iq-trigger="scroll" data-iq-ease="none"
                        style="padding-left: 40px; padding-right:40px">
                        <div class="row justify-content-center align-items-center text-center" id="listRuangan">
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
            getRooms()
            reDropdown()
        });

        function reDropdown() {
            $('.select2').each(function() {
                let id = this.dokterid ?? null;
                $(this).select2({
                    ajax: {
                        url: "{{ url('ruang-praktek/getDokterList') }}",
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        delay: 250,
                        dataType: 'json',
                        data: function(params) {
                            return {
                                params: params.term,
                                currentDr: id
                            };
                        },
                        processResults: function(response) {
                            let option = response.data.map(d => ({
                                id: d.id,
                                text: d.nik + ' | ' + d.fullname
                            }));
                            return {
                                results: option
                            };
                        }
                    },
                    placeholder: "Input NIK or Name for search",
                    width: '100%'
                });
            });
        }

        function editRoom(id) {
            $('#btnr-' + id).addClass('hideThis');
            $('#btnc-' + id).removeClass('hideThis');
            $('#editRoom-' + id).removeClass('hideThis');
            $('#showedRoom-' + id).addClass('hideThis');
        }

        function cancelEdit(id) {
            $('#btnr-' + id).removeClass('hideThis');
            $('#btnc-' + id).addClass('hideThis');
            $('#editRoom-' + id).addClass('hideThis');
            $('#showedRoom-' + id).removeClass('hideThis');
        }
        const bgnum = Math.floor(Math.random() * 900000) + 100000;

        function getRooms() {

            $.ajax({
                url: "{{ url('ruang-praktek/getRooms') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {},
                success: function(data) {
                    let a = ""
                    data.data.map((item) => {
                        a += `
                            <div class="col col-sm-4 col-lg-3 col-xxl-2 mb-4 position-relative">
                                <div class="box-shadow-box box-shadow-21  p-3"
                                    style="background-color:  #${bgnum}">
                                    <!-- Tombol Edit & Delete -->
                                    <div class="position-absolute top-0 mt-2 me-2 d-flex gap-2" style="right: 10px;"
                                        id="btnr-${item.id}">
                                        <button class="btn btn-xs btn-outline-warning rounded-pill" title="Edit"
                                            onclick="editRoom(${item.id})">
                                            <i class="iconoir-page-edit"></i>
                                        </button>
                                        <button class="btn btn-xs btn-outline-danger rounded-pill" title="Hapus" onclick="deleteRoom(${item.id})">
                                            <i class="iconoir-bin-minus-in"></i>
                                        </button>
                                    </div>

                                    <div class="position-absolute top-0 mt-2 me-2 d-flex gap-2 hideThis"
                                        style="right: 10px;" id="btnc-${item.id}">
                                        <button class="btn btn-xs btn-outline-success rounded-pill" title="Simpan" onclick="saveRoom(${item.id})">
                                            <i class="iconoir-double-check"></i>
                                        </button>
                                        <button class="btn btn-xs btn-outline-warning rounded-pill" title="Batal"
                                            onclick="cancelEdit(${item.id})">
                                            <i class="iconoir-xmark"></i>
                                        </button>
                                    </div>

                                    <div class="text-center w-full mt-4 ">
                                        <i class="iconoir-home-simple-door text-white " style="font-size:35px"></i>
                                    </div>
                                    <div id="showedRoom-${item.id}">
                                        <!-- Isi card -->
                                        <h5 class="mb-1 text-white" id="sNamaRuangan-${item.id}">${item.name}</h5>
                                        <h6 class="mb-1 text-secondary" id="sNamaDokter-${item.id}">${item.dokter ?? '[Kosong]'}</h6>
                                        <span class="badge bg-info mt-2">Kode Tiket: <span id="sCode-${item.id}">${item.code}</span></span>
                                    </div>
                                    <div id="editRoom-${item.id}" class="hideThis">
                                        <!-- Isi card -->
                                        <div class="mx-2">
                                            <input class="form-control rounded-pill text-center" id="nm-ruangan-${item.id}"
                                                maxlength="25" placeholder="~ Nama Ruangan" value="${item.name}" />
                                            <select class="form-control select2" id="dr-ruangan-${item.id}" dokterid="${item.dokterid}"></select>
                                            <input class="form-control rounded-pill text-center" id="cd-ruangan-${item.id}" value="${item.code}"
                                                maxlength="3" placeholder="~ Kode Antrian {1 - 3 digit}" />
                                        </div>
                                    </div>

                                </div>
                            </div>`
                    })
                    a += `
                            <div
                                class="col col-sm-4 col-lg-3 col-xxl-2 mb-4 position-relative justify-center align-items-center">
                                <a class="box-shadow-box box-shadow-21  btn w-full h-full" style="padding:45px"
                                    onclick="addRoom()">
                                    <i class="iconoir-plus-square-dashed text-primary hover:text-secondary"
                                        style="font-size: 80px"></i>
                                </a>
                            </div>`
                    $('#listRuangan').html(a)
                    reDropdown()

                },
                error: function(xhr, status, error) {
                    Toast.fire({
                        icon: "error",
                        title: JSON.parse(xhr.responseText).error
                    });

                }
            });
        }

        function addRoom() {
            Swal.fire({
                title: "Anda yakin?",
                text: "Tambahkan ruangan baru?",
                icon: "warning",
                showCancelButton: true,
                // showDenyButton: false,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya?",
                // denyButtonText: `No, Delete User Only!`
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('ruang-praktek/addRoom') }}",
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {},
                        success: function(data) {
                            Toast.fire({
                                icon: "success",
                                title: "Ruangan baru ditambahkan"
                            });
                            getRooms()
                        },
                        error: function(xhr, status, error) {
                            Toast.fire({
                                icon: "error",
                                title: JSON.parse(xhr.responseText).error ?? JSON.parse(xhr
                                    .responseText).message
                            });

                        }
                    });
                }
            });
        }

        function saveRoom(id) {
            Swal.fire({
                title: "Anda yakin?",
                text: "Update ruangan ini?",
                icon: "warning",
                showCancelButton: true,
                // showDenyButton: false,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya?",
                // denyButtonText: `No, Delete User Only!`
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('ruang-praktek/updateRoom') }}",
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            roomsid: id,
                            roomsname: $('#nm-ruangan-' + id).val(),
                            roomsdr: $('#dr-ruangan-' + id).val(),
                            roomscd: $('#cd-ruangan-' + id).val(),
                        },
                        success: function(data) {
                            Toast.fire({
                                icon: "success",
                                title: "Ruangan diupdate"
                            });
                            // $('#sNamaRuangan-' + id).val($('#nm-ruangan-' + id).val())
                            // $('#sNamaDokter-' + id).val($('#nm-ruangan-' + id).val())
                            // $('#sCode-' + id).val($('#nm-ruangan-' + id).val())
                            getRooms()
                        },
                        error: function(xhr, status, error) {
                            Toast.fire({
                                icon: "error",
                                title: JSON.parse(xhr.responseText).error ?? JSON.parse(xhr
                                    .responseText).message
                            });

                        }
                    });
                }
            });
        }

        function deleteRoom(id) {
            Swal.fire({
                title: "Anda yakin?",
                text: "Hapus ruangan ini?",
                icon: "warning",
                showCancelButton: true,
                // showDenyButton: false,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya?",
                // denyButtonText: `No, Delete User Only!`
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('ruang-praktek/deleteRoom') }}",
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            roomsid: id,
                        },
                        success: function(data) {
                            Toast.fire({
                                icon: "success",
                                title: "Ruangan dihapus"
                            });
                            getRooms()
                        },
                        error: function(xhr, status, error) {
                            Toast.fire({
                                icon: "error",
                                title: JSON.parse(xhr.responseText).error ?? JSON.parse(xhr
                                    .responseText).message
                            });

                        }
                    });
                }
            });
        }
    </script>
@endsection

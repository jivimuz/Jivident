@extends('layout/main')
@section('style')
    <style>
        .menu-list:hover {
            background-color: #B6E2FF;
        }

        .menu-list {
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
                        Akses
                    </span>
                    <div style="width:45px;"></div> <!-- spacer biar teks tetap di tengah -->
                </div>
            </div>
            <div class="col-md-12">
                <div class="card" data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40" data-iq-duration=".6"
                    data-iq-delay=".4" data-iq-trigger="scroll" data-iq-ease="none">
                    <div class="card-header">
                        {{-- <div class="float-end">
                            <input type="search" class="form-control" placeholder="Search..."
                                data-listener-added_aa86fd06="true"id="userId" onchange="searchMenu()"
                                style="width: 200px">
                        </div> --}}
                        <h4 class="card-title">Master Akses</h4>
                        <small>User Access Modules</small>
                    </div>
                    <div class="card-body" data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40"
                        data-iq-duration=".6" data-iq-delay=".6" data-iq-trigger="scroll" data-iq-ease="none"
                        style="padding-left: 40px; padding-right:40px">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">User</label>
                                    <select class=" select2 form-control" data-listener-added_aa86fd06="true"
                                        id="userId">
                                        <option value="" selected> Select One</option>
                                        {{-- @foreach ($userList as $i)
                                            <option value="{{ $i->id }}">{{ $i->employee->nik ?? '-' }} |
                                                {{ $i->employee->fullname ?? '-' }}
                                            </option>
                                        @endforeach --}}
                                    </select>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-12" id="employeeField" hidden>
                <div class="card" data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40"
                    data-iq-duration=".6" data-iq-delay=".4" data-iq-trigger="scroll" data-iq-ease="none">
                    <div class="card-header">
                        <div class="float-end">
                            <select class=" select2 form-control" style="width: 200px" data-listener-added_aa86fd06="true"
                                id="listRole" onchange="callMenu()">
                            </select>
                        </div>
                        <small>
                            List of
                            <b>
                                <span id="nameEmployee"></span>
                            </b>
                        </small>
                    </div>
                    <div class="card-body" data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40"
                        data-iq-duration=".6" data-iq-delay=".6" data-iq-trigger="scroll" data-iq-ease="none"
                        style="padding-left: 40px; padding-right:40px" id="employeeData">


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // $(".select2").select2()

            $('#userId').select2({
                ajax: {
                    url: "<?= url('access/getUserList') ?>",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    delay: 250,
                    datatype: 'json',
                    data: function(params) {
                        return {
                            params: params.term
                        }
                    },
                    processResults: function(response) {
                        var option = [];
                        for (data of response.data) {
                            option.push({
                                text: data.nik + ' | ' + data.fullname,
                                id: data.id,

                            })
                        }
                        return {
                            results: option
                        };
                    },

                },
                placeholder: "Input NIK or Name for search",
            })
        })


        $('#userId').on('change', function() {
            if ($(this).val()) {
                callMenu()
            } else {
                $('#employeeField').attr('hidden', true)
            }
        })

        $('#listRole').on('change', function() {
            callMenu()
        })

        function callMenu() {
            $.ajax({
                url: "{{ url('access/getMenuList') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    userId: $("#userId").val(),
                    role: $("#listRole").val(),
                },
                success: function(data) {
                    $('#nameEmployee').html($('#userId :selected').html())
                    $('#employeeField').attr('hidden', false)
                    $('#employeeData').html(data)

                },
                error: function(xhr, status, error) {
                    $('#employeeField').attr('hidden', true)
                    Toast.fire({
                        icon: "error",
                        title: JSON.parse(xhr.responseText).error
                    });

                }
            });
        }
    </script>
@endsection

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
@endsection

@section('content')
    <div class="content-inner mt-5 py-0">
        <div class="row">
            <div class="col-md-12">
                <div class="card" data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40" data-iq-duration=".6"
                    data-iq-delay=".4" data-iq-trigger="scroll" data-iq-ease="none">
                    <div class="card-header">
                        <div class="float-end">
                            <a class="btn btn-sm btn-outline-primary rounded-pill mt-2 ml-2">
                                <svg width="23" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4"
                                        d="M21.101 9.58786H19.8979V8.41162C19.8979 7.90945 19.4952 7.5 18.999 7.5C18.5038 7.5 18.1 7.90945 18.1 8.41162V9.58786H16.899C16.4027 9.58786 16 9.99731 16 10.4995C16 11.0016 16.4027 11.4111 16.899 11.4111H18.1V12.5884C18.1 13.0906 18.5038 13.5 18.999 13.5C19.4952 13.5 19.8979 13.0906 19.8979 12.5884V11.4111H21.101C21.5962 11.4111 22 11.0016 22 10.4995C22 9.99731 21.5962 9.58786 21.101 9.58786Z"
                                        fill="currentColor"></path>
                                    <path
                                        d="M9.5 15.0156C5.45422 15.0156 2 15.6625 2 18.2467C2 20.83 5.4332 21.5001 9.5 21.5001C13.5448 21.5001 17 20.8533 17 18.269C17 15.6848 13.5668 15.0156 9.5 15.0156Z"
                                        fill="currentColor"></path>
                                    <path opacity="0.4"
                                        d="M9.50023 12.5542C12.2548 12.5542 14.4629 10.3177 14.4629 7.52761C14.4629 4.73754 12.2548 2.5 9.50023 2.5C6.74566 2.5 4.5376 4.73754 4.5376 7.52761C4.5376 10.3177 6.74566 12.5542 9.50023 12.5542Z"
                                        fill="currentColor"></path>
                                </svg> Add Employee
                            </a>
                        </div>
                        <h4 class="card-title">Employees</h4>
                        <small>List of Employees</small>
                    </div>
                    <div class="card-body" data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40"
                        data-iq-duration=".6" data-iq-delay=".6" data-iq-trigger="scroll" data-iq-ease="none"
                        style="padding-left: 40px; padding-right:40px">

                        <div class="app-datatable-default overflow-auto">
                            <table class="display app-data-table default-data-table table-hover" id="main-table">
                                <thead>
                                    <tr>
                                        {{-- <th>No</th> --}}
                                        <th>NIK</th>
                                        <th>Name</th>
                                        <th>Gender</th>
                                        <th>Designation</th>
                                        <th>Department</th>
                                        <th>Job Type</th>
                                        <th>is User?</th>
                                        <th>-</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
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
            getList()
            var table = $('#main-table').DataTable();
        })

        function getList() {
            let noD = 1
            const columns = [
                // {
                //     data: "id",
                //     render: function(data, b, c) {

                //         return `${noD++}.`
                //     },
                //     className: 'text-center'
                // },
                {
                    data: "nik",
                },
                {
                    data: "fullname",
                },
                {
                    data: "gender",
                },
                {
                    data: "designation",
                },
                {
                    data: "department",
                },
                {
                    data: "jobtype",
                },
                {
                    data: "isUser",
                    render: function(data, b, c) {
                        var a = `<a>`
                        if (parseInt(data) == 1) {
                            a += `<small class=" rounded-pill bg-success badge-xs" style="padding: 2px;font-size:8px">Yes</small>`
                        } else {
                            a += `<small class=" rounded-pill bg-warning badge-xs" style="padding: 5px;font-size:8px">No</small>`
                        }
                        a += `</a>`
                        return a
                    },
                    className: 'text-center'
                },
                {
                    data: "id",
                    render: function(data, b, c) {
                        return `<a  class="btn btn-xs btn-outline-primary rounded-pill mt-2 ml-2" style="margin-right:5px !important" href="<?= url('employees') ?>/${btoa(data)}}">
                                <svg width="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M22.4541 11.3918C22.7819 11.7385 22.7819 12.2615 22.4541 12.6082C21.0124 14.1335 16.8768 18 12 18C7.12317 18 2.98759 14.1335 1.54586 12.6082C1.21811 12.2615 1.21811 11.7385 1.54586 11.3918C2.98759 9.86647 7.12317 6 12 6C16.8768 6 21.0124 9.86647 22.4541 11.3918Z" fill="currentColor" fill-opacity="0.4" stroke="currentColor"></path>
                                    <circle cx="12" cy="12" r="5" stroke="currentColor"></circle>
                                    <circle cx="12" cy="12" r="3" fill="currentColor"></circle>
                                    <mask maskUnits="userSpaceOnUse" x="9" y="9" width="6" height="6">
                                    <circle cx="12" cy="12" r="3" fill="currentColor"></circle>
                                    </mask>
                                    <circle opacity="0.53" cx="13.5" cy="10.5" r="1.5" fill="white"></circle>
                                    </svg>
                                               </a>`

                    },
                },
            ]

            var tabled = $('#main-table').DataTable({
                searching: true,
                destroy: true,
                lengthChange: false,
                responsive: true,
                // pageLength: 2,
                processing: true,
                serverSide: true,
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    url: "{{ url('employees/getList') }}",
                    type: "POST",
                    data: {}

                },
                columns: columns,
                // initComplete: function(data) {}
            });

        }
    </script>
@endsection

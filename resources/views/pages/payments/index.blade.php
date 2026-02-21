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
                            <a class="btn btn-sm btn-outline-primary rounded-pill mt-2 ml-2" onclick="addModal()">
                                <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4"
                                        d="M16.6667 2H7.33333C3.92889 2 2 3.92889 2 7.33333V16.6667C2 20.0622 3.92 22 7.33333 22H16.6667C20.0711 22 22 20.0622 22 16.6667V7.33333C22 3.92889 20.0711 2 16.6667 2Z"
                                        fill="currentColor"></path>
                                    <path
                                        d="M15.3205 12.7083H12.7495V15.257C12.7495 15.6673 12.4139 16 12 16C11.5861 16 11.2505 15.6673 11.2505 15.257V12.7083H8.67955C8.29342 12.6687 8 12.3461 8 11.9613C8 11.5765 8.29342 11.2539 8.67955 11.2143H11.2424V8.67365C11.2824 8.29088 11.6078 8 11.996 8C12.3842 8 12.7095 8.29088 12.7495 8.67365V11.2143H15.3205C15.7066 11.2539 16 11.5765 16 11.9613C16 12.3461 15.7066 12.6687 15.3205 12.7083Z"
                                        fill="currentColor"></path>
                                </svg> Add Payments
                            </a>

                        </div>
                        <h4 class="card-title">Payments</h4>
                        <small>List of Payments</small>
                    </div>
                    <div class="card-body" data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40"
                        data-iq-duration=".6" data-iq-delay=".6" data-iq-trigger="scroll" data-iq-ease="none"
                        style="padding-left: 40px; padding-right:40px">

                        <div class="table-responsive">
                            <table class="table table-striped" id="main-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Img</th>
                                        <th>Via</th>
                                        <th>Atas Nama</th>
                                        <th>No Rekening</th>
                                        <th>is Qr</th>
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
    <div class="modal fade" id="ThisModal" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
        aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ThisModalLabel"></h5>
                    <a type="button" class="btn rounded-pill btn-outline-dark btn-sm" onclick="closeModal('ThisModal')"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </a>
                </div>
                <form id="payments-form" class="modal-body" id="thisModalBody">
                    <input type="number" id="payment_id" name="payment_id" value="0" hidden>
                    <div class="form-group">
                        <label for="image_url" class="form-label">Image URL (if local start with /):</label>
                        <input type="text" class="form-control" id="image_url" name="image_url"
                            placeholder="https://images.com/image.jpg or /assets/images/images.jpg">
                    </div>
                    <div class="form-group">
                        <label for="title" class="form-label">Via: *</label>
                        <input type="text" class="form-control" id="title" name="title" maxlength="30" required
                            placeholder="John Doe">
                    </div>
                    <div class="form-group">
                        <label for="accountname" class="form-label">Atas Nama: *</label>
                        <input type="text" class="form-control" id="accountname" name="accountname" maxlength="30"
                            placeholder="John Doe">
                    </div>
                    <div class="form-group">
                        <label for="accountnumber" class="form-label">No Rekening:</label>
                        <input type="text" class="form-control" id="accountnumber" name="accountnumber" maxlength="30"
                            placeholder="John Doe">
                    </div>
                    <div class="form-group">
                        <label for="is_qr" class="form-label">is QR:</label>
                        <select id="is_qr" name="is_qr" class="form-control">
                            <option value="1" style="color: green">✔️ Yes</option>
                            <option value="0" style="color: red" selected>❌ No</option>
                        </select>
                    </div>

                    <div class="float-end">
                        <button class="btn rounded-pill btn-sm btn-outline-primary">Save</button>
                    </div>

                </form>
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
            const columns = [{
                    data: "id",
                    render: function(data, type, row, meta) {
                        // meta.row = index di halaman
                        // meta.settings._iDisplayStart = offset (start) dari serverSide
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    className: 'text-center'
                },


                {
                    data: "image_url",
                    render: function(data, b, c) {
                        var p = ''
                        var urli = c.image_url && c.image_url != "" ? c.image_url :
                            'https://cdn.pixabay.com/photo/2021/11/23/12/49/question-6818617_1280.png'
                        if (urli.startsWith("http") || urli.startsWith("www")) {
                            p = urli
                        } else {
                            p = `{{ url('') }}${urli}`

                        }
                        return `<image src="${p}" style="width: 50px;height: 50px; border-radius: 50px; border-width:1px; border-color:  grey; border-style: solid" />`
                    },
                    className: 'text-center'
                },
                {
                    data: "title"
                },
                {
                    data: "accountname",
                    render: function(data, b, c) {
                        if (data) {
                            if (data.length > 50) {
                                return data.substring(0, 50) + "...";
                            } else {
                                return data;
                            }
                        }
                        return '-';
                    },
                    className: 'text-center'
                },
                {
                    data: "accountnumber",
                    render: function(data, b, c) {
                        if (data) {
                            return data;
                        }
                        return '-';
                    },
                    className: 'text-center'
                },

                {
                    data: "is_qr",
                    render: function(data, b, c) {
                        var p = ''
                        if (parseInt(c.is_qr) == 1) {
                            p = '✔️ '
                        } else {
                            p = '❌ '
                        }

                        return p
                    },
                    className: 'text-center'
                },

                {
                    data: "id",
                    render: function(data, b, c) {
                        return `<a  class="btn btn-sm btn-outline-primary rounded-pill mt-2 ml-2" style="margin-right:5px !important" onclick="editModal(${data})">
                                <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M19.9927 18.9534H14.2984C13.7429 18.9534 13.291 19.4124 13.291 19.9767C13.291 20.5422 13.7429 21.0001 14.2984 21.0001H19.9927C20.5483 21.0001 21.0001 20.5422 21.0001 19.9767C21.0001 19.4124 20.5483 18.9534 19.9927 18.9534Z" fill="currentColor"></path>
                                    <path d="M10.309 6.90385L15.7049 11.2639C15.835 11.3682 15.8573 11.5596 15.7557 11.6929L9.35874 20.0282C8.95662 20.5431 8.36402 20.8344 7.72908 20.8452L4.23696 20.8882C4.05071 20.8903 3.88775 20.7613 3.84542 20.5764L3.05175 17.1258C2.91419 16.4915 3.05175 15.8358 3.45388 15.3306L9.88256 6.95545C9.98627 6.82108 10.1778 6.79743 10.309 6.90385Z" fill="currentColor"></path>
                                    <path opacity="0.4" d="M18.1208 8.66544L17.0806 9.96401C16.9758 10.0962 16.7874 10.1177 16.6573 10.0124C15.3927 8.98901 12.1545 6.36285 11.2561 5.63509C11.1249 5.52759 11.1069 5.33625 11.2127 5.20295L12.2159 3.95706C13.126 2.78534 14.7133 2.67784 15.9938 3.69906L17.4647 4.87078C18.0679 5.34377 18.47 5.96726 18.6076 6.62299C18.7663 7.3443 18.597 8.0527 18.1208 8.66544Z" fill="currentColor"></path>
                                    </svg>
                                               </a>` +

                            `<a  class="btn btn-sm btn-outline-danger rounded-pill mt-2 ml-2" onclick="deletePayment(${data})">
                                <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M20.2871 5.24297C20.6761 5.24297 21 5.56596 21 5.97696V6.35696C21 6.75795 20.6761 7.09095 20.2871 7.09095H3.71385C3.32386 7.09095 3 6.75795 3 6.35696V5.97696C3 5.56596 3.32386 5.24297 3.71385 5.24297H6.62957C7.22185 5.24297 7.7373 4.82197 7.87054 4.22798L8.02323 3.54598C8.26054 2.61699 9.0415 2 9.93527 2H14.0647C14.9488 2 15.7385 2.61699 15.967 3.49699L16.1304 4.22698C16.2627 4.82197 16.7781 5.24297 17.3714 5.24297H20.2871ZM18.8058 19.134C19.1102 16.2971 19.6432 9.55712 19.6432 9.48913C19.6626 9.28313 19.5955 9.08813 19.4623 8.93113C19.3193 8.78413 19.1384 8.69713 18.9391 8.69713H5.06852C4.86818 8.69713 4.67756 8.78413 4.54529 8.93113C4.41108 9.08813 4.34494 9.28313 4.35467 9.48913C4.35646 9.50162 4.37558 9.73903 4.40755 10.1359C4.54958 11.8992 4.94517 16.8102 5.20079 19.134C5.38168 20.846 6.50498 21.922 8.13206 21.961C9.38763 21.99 10.6811 22 12.0038 22C13.2496 22 14.5149 21.99 15.8094 21.961C17.4929 21.932 18.6152 20.875 18.8058 19.134Z" fill="currentColor"></path>
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
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    url: "{{ url('payments/getList') }}",
                    type: "POST",
                    data: {}

                },
                columns: columns,
                // initComplete: function(data) {}
            });

        }

        function addModal() {
            $('#payment_id').val(0)
            $('#image_url').val('')
            $('#title').val('')
            $('#is_qr').val('0').change()
            $('#accountname').val('')
            $('#accountnumber').val('')
            $('#ThisModalLabel').html("Add Payments")
            $('#ThisModal').modal('show')
        }

        function editModal(id) {
            $.ajax({
                url: "{{ url('payments/getPaymentsById') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    id: id
                },
                success: function(res) {
                    $('#payment_id').val(res.data.id)
                    $('#image_url').val(res.data.image_url)
                    $('#title').val(res.data.title)
                    $('#is_qr').val(res.data.is_qr).change()
                    $('#accountname').val(res.data.accountname)
                    $('#accountnumber').val(res.data.accountnumber)
                    $('#ThisModalLabel').html("Edit Payments :" + res.data.title.substring(0, 10))
                    $('#ThisModal').modal('show')
                },
                error: function(xhr, status, error) {
                    Toast.fire({
                        icon: "error",
                        title: JSON.parse(xhr.responseText).error
                    });

                }
            });
        }

        function deletePayment(id) {
            Swal.fire({
                title: "You Sure?",
                text: "Do you want to delete this data!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, Delete Data",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('payments/deletePayment') }}",
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            id: btoa(id)
                        },
                        success: function(data) {
                            getList()
                        },
                        error: function(xhr, status, error) {
                            Toast.fire({
                                icon: "error",
                                title: JSON.parse(xhr.responseText).error
                            });

                        }
                    });
                }
            });
        }

        $('#payments-form').submit(function(e) {
            e.preventDefault();
            var pForm = new FormData(this);
            $.ajax({
                url: "{{ url('payments/insertOrUpdatePayments') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: pForm,
                processData: false, // 🚀 penting
                contentType: false, // 🚀 penting
                success: function(res) {
                    console.log(res)
                    Toast.fire({
                        icon: "success",
                        title: res.message // langsung ambil dari JSON Laravel
                    });
                    getList()
                    $('#ThisModal').modal('hide')
                },
                error: function(xhr) {
                    let res = {};
                    try {
                        res = JSON.parse(xhr.responseText);
                    } catch (e) {
                        res.error = "Terjadi kesalahan server!";
                    }
                    Toast.fire({
                        icon: "error",
                        title: res.error
                    });
                }
            });
        })
    </script>
@endsection

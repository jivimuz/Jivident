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
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100"
                style="background: linear-gradient(135deg,  #38ef7d, #11998e); color: #fff; border-radius: 12px;">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <p class="mb-1" style="font-size: 14px; opacity: 0.8;">Saldo Bulan (<span id="month-title"></span>)
                        </p>
                        <h4 class="mb-0 fw-bold text-white" id="month-omset">Loading...</h4>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                        style="width:50px; height:50px; background: rgba(255,255,255,0.2);">
                        <i class="fa fa-money" style="font-size: 22px;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100"
                style="background: linear-gradient(135deg, #1e3c72, #2a5298); color: #fff; border-radius: 12px;">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <p class="mb-1" style="font-size: 14px; opacity: 0.8;">Total Saldo</p>
                        <h4 class="mb-0 fw-bold text-white" id="total-omset">Loading...</h4>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                        style="width:50px; height:50px; background: rgba(255,255,255,0.2);">
                        <i class="fa fa-calendar-o" style="font-size: 22px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-inner mt-5 py-0">

        <div class="row">
            <div class="col-md-12">
                <div class="card" data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40"
                    data-iq-duration=".6" data-iq-delay=".4" data-iq-trigger="scroll" data-iq-ease="none">
                    <div class="card-header">
                        <div class="float-end">
                            <a class="btn btn-sm btn-outline-danger rounded-pill mt-2 ml-2" id="add-transaction"
                                onclick="addTransaction()">
                                <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4"
                                        d="M18.8088 9.021C18.3573 9.021 17.7592 9.011 17.0146 9.011C15.1987 9.011 13.7055 7.508 13.7055 5.675V2.459C13.7055 2.206 13.5026 2 13.253 2H7.96363C5.49517 2 3.5 4.026 3.5 6.509V17.284C3.5 19.889 5.59022 22 8.16958 22H16.0453C18.5058 22 20.5 19.987 20.5 17.502V9.471C20.5 9.217 20.298 9.012 20.0465 9.013C19.6247 9.016 19.1168 9.021 18.8088 9.021Z"
                                        fill="currentColor"></path>
                                    <path opacity="0.4"
                                        d="M16.0842 2.56737C15.7852 2.25637 15.2632 2.47037 15.2632 2.90137V5.53837C15.2632 6.64437 16.1742 7.55437 17.2792 7.55437C17.9772 7.56237 18.9452 7.56437 19.7672 7.56237C20.1882 7.56137 20.4022 7.05837 20.1102 6.75437C19.0552 5.65737 17.1662 3.69137 16.0842 2.56737Z"
                                        fill="currentColor"></path>
                                    <path
                                        d="M14.3672 12.2364H12.6392V10.5094C12.6392 10.0984 12.3062 9.7644 11.8952 9.7644C11.4842 9.7644 11.1502 10.0984 11.1502 10.5094V12.2364H9.4232C9.0122 12.2364 8.6792 12.5704 8.6792 12.9814C8.6792 13.3924 9.0122 13.7264 9.4232 13.7264H11.1502V15.4524C11.1502 15.8634 11.4842 16.1974 11.8952 16.1974C12.3062 16.1974 12.6392 15.8634 12.6392 15.4524V13.7264H14.3672C14.7782 13.7264 15.1122 13.3924 15.1122 12.9814C15.1122 12.5704 14.7782 12.2364 14.3672 12.2364Z"
                                        fill="currentColor"></path>
                                </svg> Tambah Transaksi
                            </a>
                        </div>

                        <h4 class="card-title">Transaction</h4>
                        <small>List of Transaction</small>
                    </div>
                    <div class="card-body" data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40"
                        data-iq-duration=".6" data-iq-delay=".6" data-iq-trigger="scroll" data-iq-ease="none"
                        style="padding-left: 40px; padding-right:40px">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <input type="month" id="monthYear" class="form-control" max="{{ date('Y-m') }}"
                                    value="{{ date('Y-m') }}" onchange="getList()">
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped" id="main-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Waktu</th>
                                        <th>No Transaksi</th>
                                        <th>Type</th>
                                        <th>Nominal</th>
                                        <th>Keterangan</th>
                                        <th>Bukti</th>
                                        <th>Aksi</th>
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
    @include('component/modal-md')

    <!-- Modal Preview Bukti -->
    <div class="modal fade" id="previewAttachmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius:16px;">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewAttachmentTitle">Bukti</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="previewAttachmentBody" style="min-height: 60vh;">
                    <!-- injected by JS -->
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
                    data: "created_at",
                    render: function(data) {
                        if (!data) return "-";
                        return new Date(data).toLocaleString("id-ID", {
                            year: "numeric",
                            month: "2-digit",
                            day: "2-digit",
                            hour: "2-digit",
                            minute: "2-digit"
                        });
                    },
                    className: 'text-center'
                },
                {
                    data: "no_transaction"
                },
                {
                    data: "transaction_type",
                    render: function(data, b, c) {
                        var a = `<a>`
                        if (data == "in") {
                            a += `<div class="badge rounded-pill bg-success badge-xs" style="padding: 5px;font-size:8px; width:100%">IN</div>`
                        } else {
                            a += `<div class="badge rounded-pill bg-danger badge-xs" style="padding: 5px;font-size:8px; width:100%">OUT</div>`
                        }
                        a += `</a>`
                        return a
                    },
                    className: 'text-center'
                },
                {
                    data: "amount",
                    render: function(data, b, c) {

                        var formattedPrice = parseFloat(c.amount).toLocaleString("id-ID", {
                            style: "currency",
                            currency: "IDR"
                        });
                        return formattedPrice
                    },
                },
                {
                    data: "notes",
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
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        // ✅ sesuaikan nama field dari backend
                        const url = row.attachment_url || row.attachment_path || null;
                        const at = row.attachment_type || (url && url.toLowerCase().endsWith('.pdf') ? 'pdf' : (
                            url ? 'image' : null));

                        if (!url) return `<span class="text-muted">-</span>`;
                        var reurl = `{{ url('') }}/storage/${url}`
                        if (at === 'pdf') {
                            return `
        <div class="proof-circle proof-pdf text-danger"
             onclick="openAttachmentModal('${reurl}', 'pdf')"
             title="Klik untuk preview PDF">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
      <path d="M7 2h7l5 5v15a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2Z" fill="currentColor" opacity=".15"/>
      <path d="M14 2v5h5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
      <path d="M8 16h8M8 19h6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
      <path d="M8 11h8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
    </svg>
        </div>
      `;
                        }

                        // image
                        return `
      <div class="proof-circle"
           onclick="openAttachmentModal('${reurl}', 'image')"
           title="Klik untuk preview image">
        <img class="proof-img" src="${reurl}" alt="bukti">
      </div>
    `;
                    },
                    className: 'text-center'
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `
      <button class="btn btn-sm btn-outline-primary rounded-pill"
        onclick="openPrintPopup('${row.no_transaction}')">
        🖨️
      </button>
    `;
                    },
                    className: 'text-center'
                }



            ]

            var tabled = $('#main-table').DataTable({
                searching: true,
                destroy: true,
                lengthChange: false,
                responsive: true,
                processing: true,
                serverSide: true,
                // pageLength: 2,
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    url: "{{ url('transaction/getList') }}",
                    type: "POST",
                    data: function(d) {
                        d.monthYear = $('#monthYear').val();
                    }

                },
                columns: columns,
                initComplete: function(data, res) {
                    var formattedData = parseFloat(res.monthly.total_saldo ?? 0).toLocaleString("id-ID", {
                        style: "currency",
                        currency: "IDR"
                    });
                    $("#month-omset").html(formattedData)
                    $("#month-title").html(convertMonthYear($('#monthYear').val()))
                    getOmset()

                }
            });

        }

        function getOmset() {
            $.ajax({
                url: "{{ url('transaction/getOmset') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {},
                success: function(res) {
                    var formattedData = parseFloat(res.data.total_saldo ?? 0).toLocaleString("id-ID", {
                        style: "currency",
                        currency: "IDR"
                    });
                    $("#total-omset").html(formattedData)
                },
                error: function(xhr, status, error) {
                    Toast.fire({
                        icon: "error",
                        title: JSON.parse(xhr.responseText).error
                    });

                }
            });
        }
        async function addTransaction() {
            $.ajax({
                url: "{{ url('transaction/addTransaction') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {},
                success: function(data) {
                    $('#ThisModalLabel').html("Tambah Transaksi")
                    $('#thisModalBody').html(data)
                    $('#ThisModal').modal('show')
                },
                error: function(xhr, status, error) {
                    Toast.fire({
                        icon: "error",
                        title: JSON.parse(xhr.responseText).error ? JSON.parse(xhr
                                .responseText).error : JSON.parse(xhr.responseText)
                            .message
                    });

                }
            });
        }

        let isSubmittingTransaction = false;

        async function sendTransaction() {
            // anti-spam: block kalau lagi proses
            if (isSubmittingTransaction) return;

            var no_project = $('#projectID').val();
            var nominal = $('#nominal').val();
            var transaction_type = $('#transaction_type').val();
            var notes = $('#notes').val();
            var file = $('#attachment')[0]?.files?.[0] || null;

            if ((!nominal || !parseFloat(nominal)) || !transaction_type || !notes) {
                return Toast.fire({
                    icon: "error",
                    title: "Silahkan Isi Tipe, Nominal, dan Keterangan",
                });
            }

            // optional: validasi file di client (Dropify udah bantu, tapi kita guard dikit)
            if (file) {
                const allowed = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
                if (!allowed.includes(file.type)) {
                    return Toast.fire({
                        icon: "error",
                        title: "Format file harus JPG/PNG/WEBP/PDF"
                    });
                }
                const maxBytes = 1 * 1024 * 1024; // 1MB
                if (file.size > maxBytes) {
                    return Toast.fire({
                        icon: "error",
                        title: "Ukuran file max 1MB"
                    });
                }
            }

            const result = await Swal.fire({
                title: "You Sure?",
                text: "Do you want to submit this data?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, submit Data",
            });

            if (!result.isConfirmed) return;

            // lock + disable button (anti spam)
            isSubmittingTransaction = true;
            $('#btnSubmitTransaction').addClass('disabled').attr('aria-disabled', 'true').css('pointer-events', 'none');

            Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu sebentar.',
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => Swal.showLoading()
            });

            // FormData for file upload
            const fd = new FormData();
            fd.append('no_project', no_project);
            fd.append('nominal', nominal);
            fd.append('transaction_type', transaction_type);
            fd.append('notes', notes);

            if (file) fd.append('attachment', file);

            // // optional: idempotency key (server bisa pakai untuk cegah double submit)
            // const idempotencyKey = (crypto?.randomUUID?.() || (Date.now() + '-' + Math.random().toString(16).slice(2)));
            // fd.append('idempotency_key', idempotencyKey);

            $.ajax({
                url: "{{ url('transaction/submitTransaction') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: fd,
                processData: false,
                contentType: false,
                success: function(data) {
                    Swal.fire({
                        icon: "success",
                        title: "Proses Dikonfirmasi!",
                        text: "Data selesai di proses.",
                        showConfirmButton: true,
                        confirmButtonText: "OK"
                    }).then(() => {
                        closeModal('ThisModal');
                        getList();
                    });
                },
                error: function(xhr) {
                    let msg = "Terjadi kesalahan.";
                    try {
                        msg = JSON.parse(xhr.responseText).error || msg;
                    } catch (e) {}
                    Toast.fire({
                        icon: "error",
                        title: msg
                    });
                },
                complete: function() {
                    // release lock (anti spam)
                    isSubmittingTransaction = false;
                    $('#btnSubmitTransaction').removeClass('disabled').removeAttr('aria-disabled').css(
                        'pointer-events', '');
                }
            });
        }

        function openAttachmentModal(url, type) {
            $('#previewAttachmentTitle').text(type === 'pdf' ? 'Preview PDF' : 'Preview Image');

            if (!url) {
                $('#previewAttachmentBody').html(`<div class="text-center text-muted">Tidak ada bukti</div>`);
                $('#previewAttachmentModal').modal('show');
                return;
            }

            if (type === 'pdf') {
                $('#previewAttachmentBody').html(`
      <iframe src="${url}" style="width:100%; height:70vh; border:0; border-radius:12px;"></iframe>

    `);
            } else {
                $('#previewAttachmentBody').html(`
      <div class="text-center">
        <img src="${url}" alt="Bukti" style="max-width:100%; max-height:70vh; border-radius:12px;" />
      </div>
    `);
            }

            $('#previewAttachmentModal').modal('show');
        }

        function openPrintPopup(no_transaction) {
            Swal.fire({
                title: "Print Invoice",
                text: `No Transaksi: ${no_transaction}`,
                icon: "question",
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: "🧾 Thermal",
                denyButtonText: "📄 A4",
                cancelButtonText: "Batal",
            }).then((res) => {
                if (res.isConfirmed) {
                    openPopup(`{{ url('transaction/print') }}/${no_transaction}/thermal`, "_blank");
                } else if (res.isDenied) {
                    openPopup(`{{ url('transaction/print') }}/${no_transaction}/a4`, "_blank");
                }
            });
        }
    </script>
@endsection

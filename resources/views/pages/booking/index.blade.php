@extends('layout/main')
@section('style')
    <style>
        .slot-btn {
            margin: 3px;
            min-width: 80px;
        }
    </style>
@endsection

@section('content')
    <div class="content-inner mt-5 py-0">
        <div class="row">
            <div class="col-md-5 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Booking Appointment</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Dokter</label>
                            <select class="form-control select2" id="bookDokter" style="width:100%"></select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ruang Praktek</label>
                            <select class="form-control" id="bookRuangPraktek">
                                <option value="">- Pilih dokter dulu -</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="bookTanggal">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Pilih Jam</label>
                            <div id="slotContainer" class="text-muted">Pilih dokter, ruang praktek, dan tanggal dulu.</div>
                        </div>

                        <div class="mb-3" id="patientSection" style="display:none">
                            <label class="form-label">Pasien</label>
                            <select class="form-control select2" id="bookPatient" style="width:100%"></select>
                        </div>
                        <div class="mb-3" id="notesSection" style="display:none">
                            <label class="form-label">Catatan (opsional)</label>
                            <input type="text" class="form-control" id="bookNotes">
                        </div>

                        <button class="btn btn-success" id="confirmBookingBtn" style="display:none">Booking Sekarang</button>
                    </div>
                </div>
            </div>

            <div class="col-md-7 mb-3">
                <div class="card">
                    <div class="card-header">
                        <div class="float-end">
                            <select class="form-control" id="filterBookingStatus" style="width:160px">
                                <option value="">Semua Status</option>
                                <option value="booked">Booked</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <h4 class="card-title">Daftar Booking</h4>
                    </div>
                    <div class="card-body">
                        <table class="display app-data-table table-hover" id="bookingTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jam</th>
                                    <th>Dokter</th>
                                    <th>Pasien</th>
                                    <th>Status</th>
                                    <th>-</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var bookingTable;
        var selectedSlot = null;

        $('#bookDokter').select2({
            ajax: {
                url: "{{ url('booking/getDokterList') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                delay: 250,
                data: function(params) {
                    return {
                        params: params.term
                    };
                },
                processResults: function(res) {
                    return {
                        results: res.data.map(d => ({
                            id: d.id,
                            text: d.nik + ' | ' + d.fullname
                        }))
                    };
                }
            },
            placeholder: 'Cari nama/NIK dokter'
        });

        $('#bookPatient').select2({
            ajax: {
                url: "{{ url('booking/getPatientList') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                delay: 250,
                data: function(params) {
                    return {
                        params: params.term
                    };
                },
                processResults: function(res) {
                    return {
                        results: res.data.map(p => ({
                            id: p.id,
                            text: p.norm + ' | ' + p.nama
                        }))
                    };
                }
            },
            placeholder: 'Cari nama/No. RM pasien'
        });

        $('#bookDokter').on('change', function() {
            var dokterId = $(this).val();
            resetSlots();
            if (!dokterId) return;
            $.get("{{ url('booking/getRuangPraktekByDokter') }}/" + dokterId, function(res) {
                var options = res.data.length ?
                    res.data.map(r => `<option value="${r.id}">${r.name}</option>`).join('') :
                    '<option value="">Dokter ini belum punya Ruang Praktek</option>';
                $('#bookRuangPraktek').html(options);
                loadSlots();
            });
        });

        $('#bookRuangPraktek, #bookTanggal').on('change', loadSlots);

        function resetSlots() {
            selectedSlot = null;
            $('#slotContainer').html('<span class="text-muted">Pilih dokter, ruang praktek, dan tanggal dulu.</span>');
            $('#patientSection, #notesSection, #confirmBookingBtn').hide();
        }

        function loadSlots() {
            var dokterId = $('#bookDokter').val();
            var ruangId = $('#bookRuangPraktek').val();
            var tanggal = $('#bookTanggal').val();
            selectedSlot = null;
            $('#patientSection, #notesSection, #confirmBookingBtn').hide();

            if (!dokterId || !ruangId || !tanggal) return;

            $.ajax({
                url: "{{ url('booking/getAvailableSlots') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    fk_dokter: dokterId,
                    fk_ruangpraktek: ruangId,
                    tanggal: tanggal
                },
                success: function(res) {
                    if (res.data.length === 0) {
                        $('#slotContainer').html('<span class="text-muted">Dokter tidak praktek pada tanggal ini.</span>');
                        return;
                    }
                    var html = '';
                    res.data.forEach(function(slot) {
                        var cls = slot.available ? 'btn-outline-primary' : 'btn-outline-secondary disabled';
                        html += `<button type="button" class="btn btn-sm ${cls} slot-btn" data-jam="${slot.jam_mulai}" ${slot.available ? '' : 'disabled'}>${slot.jam_mulai}</button>`;
                    });
                    $('#slotContainer').html(html);
                }
            });
        }

        $(document).on('click', '.slot-btn:not(.disabled)', function() {
            $('.slot-btn').removeClass('btn-primary').addClass('btn-outline-primary');
            $(this).removeClass('btn-outline-primary').addClass('btn-primary');
            selectedSlot = $(this).data('jam');
            $('#patientSection, #notesSection, #confirmBookingBtn').show();
        });

        $('#confirmBookingBtn').on('click', function() {
            if (!selectedSlot || !$('#bookPatient').val()) {
                return Toast.fire({
                    icon: 'warning',
                    title: 'Pilih jam dan pasien dulu'
                });
            }
            $.ajax({
                url: "{{ url('booking/store') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    fk_dokter: $('#bookDokter').val(),
                    fk_ruangpraktek: $('#bookRuangPraktek').val(),
                    fk_pasien: $('#bookPatient').val(),
                    tanggal: $('#bookTanggal').val(),
                    jam_mulai: selectedSlot,
                    notes: $('#bookNotes').val(),
                },
                success: function() {
                    Toast.fire({
                        icon: 'success',
                        title: 'Booking berhasil'
                    });
                    $('#bookNotes').val('');
                    loadSlots();
                    bookingTable.ajax.reload();
                },
                error: function(xhr) {
                    Toast.fire({
                        icon: 'error',
                        title: JSON.parse(xhr.responseText).error ?? 'Gagal booking'
                    });
                }
            });
        });

        function cancelBooking(id) {
            Swal.fire({
                title: 'Batalkan booking ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Batalkan',
                cancelButtonText: 'Tutup'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('booking/cancel') }}/" + id,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function() {
                            Toast.fire({
                                icon: 'success',
                                title: 'Booking dibatalkan'
                            });
                            bookingTable.ajax.reload();
                            loadSlots();
                        },
                        error: function(xhr) {
                            Toast.fire({
                                icon: 'error',
                                title: JSON.parse(xhr.responseText).error ?? 'Gagal membatalkan'
                            });
                        }
                    });
                }
            });
        }

        $(document).ready(function() {
            bookingTable = $('#bookingTable').DataTable({
                destroy: true,
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    url: "{{ url('booking/getList') }}",
                    type: "POST",
                    data: function(d) {
                        d.status = $('#filterBookingStatus').val();
                    }
                },
                columns: [
                    { data: 'tanggal' },
                    { data: 'jam' },
                    { data: 'dokter' },
                    { data: 'pasien' },
                    {
                        data: 'status',
                        render: function(s) {
                            var cls = s === 'booked' ? 'success' : (s === 'cancelled' ? 'danger' : 'secondary');
                            return `<span class="badge bg-${cls}">${s}</span>`;
                        }
                    },
                    {
                        data: 'id',
                        render: function(id, type, row) {
                            return row.status === 'booked' ?
                                `<a class="btn btn-xs btn-outline-danger" onclick="cancelBooking(${id})">Batalkan</a>` :
                                '-';
                        }
                    },
                ],
            });

            $('#filterBookingStatus').on('change', function() {
                bookingTable.ajax.reload();
            });
        });
    </script>
@endsection

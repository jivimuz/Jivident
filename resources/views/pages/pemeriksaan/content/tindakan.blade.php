<div class="container mt-4">
    <h3>Tindakan Pasien</h3>


    @if ($status->status != 'Dalam Antrian' && $status->status != 'Selesai')
        <!-- Form Input -->
        <form id="form-add" class="row g-3 mb-4">
            <div class="col-md-5">
                <label class="form-label">Pilih Tindakan</label>
                <select id="tindakanSelect" class="form-control" style="width: 100%"></select>
            </div>


            <div class="col-md-4">
                <label class="form-label">Pilih Dokter</label>
                <select id="dokterSelect" class="form-control" style="width: 100%"></select>
            </div>


            <div class="col-md-2">
                <label class="form-label">Qty</label>
                <input type="number" id="qty" value="1" class="form-control rounded-pill" min="1" />
            </div>


            <div class="col-md-1 d-flex align-items-end">
                <a id="btnAdd"
                    class="btn btn-sm btn-primary  rounded-pill text-center align-items-center justify-content-center">
                    <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path opacity="0.4"
                            d="M16.6667 2H7.33333C3.92889 2 2 3.92889 2 7.33333V16.6667C2 20.0622 3.92 22 7.33333 22H16.6667C20.0711 22 22 20.0622 22 16.6667V7.33333C22 3.92889 20.0711 2 16.6667 2Z"
                            fill="currentColor"></path>
                        <path
                            d="M15.3205 12.7083H12.7495V15.257C12.7495 15.6673 12.4139 16 12 16C11.5861 16 11.2505 15.6673 11.2505 15.257V12.7083H8.67955C8.29342 12.6687 8 12.3461 8 11.9613C8 11.5765 8.29342 11.2539 8.67955 11.2143H11.2424V8.67365C11.2824 8.29088 11.6078 8 11.996 8C12.3842 8 12.7095 8.29088 12.7495 8.67365V11.2143H15.3205C15.7066 11.2539 16 11.5765 16 11.9613C16 12.3461 15.7066 12.6687 15.3205 12.7083Z"
                            fill="currentColor"></path>
                    </svg> </a>
            </div>
        </form>
    @endif


    <!-- Table List -->
    <table class="table table-bordered" id="tindakanTable">
        <thead>
            <tr>
                <th>Tindakan</th>
                <th>Dokter</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Subtotal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-end">Total</th>
                <th id="stotalPrice">Rp 0</th>
                <th id="totalPrice">Rp 0</th>
                <th></th>
            </tr>
        </tfoot>

    </table>
    @if ($status->status != 'Dalam Antrian' && $status->status != 'Selesai')
        <button id="btnSave" class="btn btn-primary rounded-pill">
            <i class="fa fa-save"></i>
            {{ $tindakan ? 'Update' : 'Save' }}
        </button>
    @endif
</div>
<script>
    tindakanInitial = @json($tindakan ? [$tindakan] : []);
</script>
<script>
    tindakanList = [];
    $(document).ready(function() {
        // Load existing tindakan
        if (tindakanInitial.length > 0) {
            tindakanInitial.forEach(item => {
                tindakanList.push({
                    tindakan_id: item.fk_tindakan,
                    tindakan_text: `${item.tindakan} | ${parseFloat(item.amount).toLocaleString("id-ID", {
                style: "currency",
                currency: "IDR"
            })}`,
                    tindakan_name: item.tindakan,
                    tindakan_amount: item.amount,
                    dokter_id: item.fk_dokter,
                    dokter_text: item.dokter,
                    qty: item.qty ?? 1
                });
            });

            renderTable();
        }

        // Select2 Tindakan
        $('#tindakanSelect').select2({
            placeholder: 'Cari tindakan...',
            ajax: {
                url: `<?= url('tindakan/getTindakanList') ?>`,
                dataType: 'json',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                delay: 250,
                processResults: function(data) {
                    return {
                        results: data.data.map(item => ({
                            id: item.id,
                            text: `${item.name} | ${parseFloat(item.amount).toLocaleString("id-ID", {
                            style: "currency",
                            currency: "IDR"
                        })}`, // tampil di dropdown
                            name: item.name, // tampil di dropdown
                            amount: item.amount // kalau butuh harga ikut naik
                        }))
                    }
                }
            }
        });
    });


    // Select2 Dokter
    $('#dokterSelect').select2({
        placeholder: 'Cari dokter...',
        ajax: {
            url: `<?= url('tindakan/getDokterList') ?>`,
            dataType: 'json',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            delay: 250,
            processResults: function(data) {
                return {
                    results: data.data.map(item => ({
                        id: item.id,
                        text: item.name, // tampil di dropdown
                    }))
                }
            }
        }
    });


    // Add to Table
    $('#btnAdd').click(() => {
        const tindakan = $('#tindakanSelect').select2('data')[0];
        const dokter = $('#dokterSelect').select2('data')[0];
        const qty = $('#qty').val();

        if (!tindakan || !dokter) {
            return Toast.fire({
                icon: "warning",
                title: "Pilih tindakan & dokter"
            });
        }

        // 👉 Cek apakah tindakan_id sudah ada di array
        const isDuplicate = tindakanList.some(item => item.tindakan_id == tindakan.id);
        if (isDuplicate) {
            return Toast.fire({
                icon: "warning",
                title: "Tindakan sudah ditambahkan"
            });
        }

        const item = {
            tindakan_id: tindakan.id,
            tindakan_text: tindakan.text,
            tindakan_name: tindakan.name,
            tindakan_amount: tindakan.amount,
            dokter_id: dokter.id,
            dokter_text: dokter.text,
            qty: qty
        };

        tindakanList.push(item);
        renderTable();
    });



    function renderTable() {
        const tbody = $('#tindakanTable tbody');
        tbody.empty();

        let total = 0;
        let stotal = 0;

        tindakanList.forEach((item, i) => {
            const subtotal = item.tindakan_amount * item.qty;
            stotal += item.tindakan_amount;
            total += subtotal;

            tbody.append(`
                    <tr>
                        <td>${item.tindakan_name}</td>
                        <td>${item.dokter_text}</td>
                        <td>
                           <input
                                type="number"
                                class="form-control form-control-sm qty-input"
                                data-index="${i}"
                                value="${item.qty}"
                                min="1"
                                {{ $status->status != 'Dalam Antrian' && $status->status != 'Selesai' ? '' : 'disabled' }}
                            />

                        </td>

                        <td>${item.tindakan_amount.toLocaleString("id-ID", {
                            style: "currency",
                            currency: "IDR"
                        })}</td>
                        <td>${parseFloat(subtotal).toLocaleString("id-ID", {
                            style: "currency",
                            currency: "IDR"
                        })}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-danger rounded-pill" {{ $status->status != 'Dalam Antrian' && $status->status != 'Selesai' ? '' : 'hidden' }}
                                onclick="removeItem(${i})">
                                <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4" d="M19.643 9.48851C19.643 9.5565 19.11 16.2973 18.8056 19.1342C18.615 20.8751 17.4927 21.9311 15.8092 21.9611C14.5157 21.9901 13.2494 22.0001 12.0036 22.0001C10.6809 22.0001 9.38741 21.9901 8.13185 21.9611C6.50477 21.9221 5.38147 20.8451 5.20057 19.1342C4.88741 16.2873 4.36418 9.5565 4.35445 9.48851C4.34473 9.28351 4.41086 9.08852 4.54507 8.93053C4.67734 8.78453 4.86796 8.69653 5.06831 8.69653H18.9388C19.1382 8.69653 19.3191 8.78453 19.4621 8.93053C19.5953 9.08852 19.6624 9.28351 19.643 9.48851Z" fill="currentColor"></path>
                                    <path d="M21 5.97686C21 5.56588 20.6761 5.24389 20.2871 5.24389H17.3714C16.7781 5.24389 16.2627 4.8219 16.1304 4.22692L15.967 3.49795C15.7385 2.61698 14.9498 2 14.0647 2H9.93624C9.0415 2 8.26054 2.61698 8.02323 3.54595L7.87054 4.22792C7.7373 4.8219 7.22185 5.24389 6.62957 5.24389H3.71385C3.32386 5.24389 3 5.56588 3 5.97686V6.35685C3 6.75783 3.32386 7.08982 3.71385 7.08982H20.2871C20.6761 7.08982 21 6.75783 21 6.35685V5.97686Z" fill="currentColor"></path>
                                </svg>
                            </button>
                        </td>
                    </tr>
                `);
        });

        // update footer total
        $('#stotalPrice').text(
            stotal.toLocaleString("id-ID", {
                style: "currency",
                currency: "IDR"
            })
        );
        $('#totalPrice').text(
            total.toLocaleString("id-ID", {
                style: "currency",
                currency: "IDR"
            })
        );
    }



    window.removeItem = function(index) {
        tindakanList.splice(index, 1);
        renderTable();
    }

    // Save
    $('#btnSave').click(() => {
        if (tindakanList.length <= 0) {
            return Toast.fire({
                icon: "warning",
                title: "Masukan tindakan terlebih dahulu"
            });
        }
        $.ajax({
            url: `<?= url('pemeriksaan/pasien/' . $id) ?>/tindakan/save`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                tindakan: tindakanList,
            },
            success: function(response) {
                Toast.fire({
                    icon: "success",
                    title: "Data berhasil disimpan"
                });
            },
            error: function(xhr) {
                Swal.close();
                const msg = xhr.responseJSON?.error || 'Terjadi kesalahan.';
                Toast.fire({
                    icon: "error",
                    title: msg
                });
            },
        })
    });
    // event qty change
    $('#tindakanTable').on('change', '.qty-input', function() {
        const index = $(this).data('index');
        const newQty = parseInt($(this).val());

        if (newQty <= 0 || isNaN(newQty)) {
            $(this).val(tindakanList[index].qty);
            return Toast.fire({
                icon: "warning",
                title: "Qty minimal 1"
            });
        }

        tindakanList[index].qty = newQty;
        renderTable();
    });
</script>

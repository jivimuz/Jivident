@extends('layout/main')
@section('style')
    <style>
        a {
            margin-right: 5px
        }

        .setting-value-cell {
            min-width: 260px;
        }

        .setting-unit {
            min-width: 52px;
            justify-content: center;
        }

        .setting-description {
            display: block;
            margin-top: 2px;
            font-size: .75rem;
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
                        <div class="float-end d-flex gap-2">
                            <a href="{{ url('setting/backup') }}" class="btn btn-sm btn-outline-dark rounded-pill">
                                Download Backup Database
                            </a>
                            <input type="search" class="form-control" placeholder="Search..."
                                data-listener-added_aa86fd06="true"id="namaMenu" onchange="searchMenu()"
                                style="width: 200px">
                        </div>
                        <h4 class="card-title">Setting</h4>
                        <small>Setting Config</small>
                    </div>
                    <div class="card-body" data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40"
                        data-iq-duration=".6" data-iq-delay=".6" data-iq-trigger="scroll" data-iq-ease="none"
                        style="padding-left: 40px; padding-right:40px">

                        <div class="app-datatable-default overflow-auto">
                            <table class="display app-data-table default-data-table table-hover" id="main-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Value</th>
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
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            getList()
            var table = $('#main-table').DataTable();
        })

        function getList() {
            var noD = 1
            var columns = [{
                    data: "id",
                    render: function(data, b, c) {

                        return `${noD++}.`
                    },
                    className: 'text-center'
                },
                {
                    data: 'name'
                    ,
                    render: function(data, b, c) {
                        var meta = settingMeta(c);
                        return `<strong>${escapeSettingHtml(data)}</strong><small class="setting-description text-muted">${meta.description}</small>`;
                    }
                },
                {
                    data: 'value',
                    className: 'setting-value-cell',
                    render: function(a, b, c) {
                        var value = c.value ?? '';
                        if (c.type == "file") {
                            var fileValue = (`${ value && value != '' ? `  <div class="iq-avatar me-2" id='lab-${c.id}'><img style="max-height:50px" src="<?= url('') ?>/${escapeSettingHtml(value)}"></div>` :
                                    `<span id='lab-${c.id}'><i class="fa fa-cube"></i></span>`}`)
                            return `<span id='lab-${c.id}'>${fileValue}</span>
                                <form id='form-${c.id}'>
                                    <input type="text" name="param[]" value="${c.id}" hidden>
                                    <input type="file" class="form-control" id="val-${c.id}" name="val[]" hidden>
                                </form>`;
                        } else {
                            var meta = settingMeta(c);
                            var displayValue = formatSettingValue(value, meta);
                            var inputType = meta.inputType || c.type || 'text';
                            var inputConstraints = `${meta.min ? ` min="${meta.min}"` : ''}${meta.max ? ` max="${meta.max}"` : ''}${meta.step ? ` step="${meta.step}"` : ''}`;
                            var control = `<input type="${inputType}" class="form-control" id="val-${c.id}" value="${escapeSettingAttribute(value)}"${inputConstraints} hidden>`;
                            if (meta.prefix || meta.suffix) {
                                control = `<div class="input-group" id="input-group-${c.id}" hidden>
                                    ${meta.prefix ? `<span class="input-group-text setting-unit">${meta.prefix}</span>` : ''}
                                    ${control.replace(' hidden', '')}
                                    ${meta.suffix ? `<span class="input-group-text setting-unit">${meta.suffix}</span>` : ''}
                                </div>`;
                            }
                            return `<span id="lab-${c.id}">${displayValue}</span>${control}`;
                        }
                    }
                },
                {
                    width: '50px',
                    data: "id",
                    render: function(data, b, c) {
                        var a = `<a  class="btn btn-xs btn-outline-primary rounded-pill mt-2 ml-2" id="edit-${c.id}" onclick="editVal(${c.id})">
                            <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M10.0833 15.958H3.50777C2.67555 15.958 2 16.6217 2 17.4393C2 18.2559 2.67555 18.9207 3.50777 18.9207H10.0833C10.9155 18.9207 11.5911 18.2559 11.5911 17.4393C11.5911 16.6217 10.9155 15.958 10.0833 15.958Z" fill="currentColor"></path>
                                <path opacity="0.4" d="M22.0001 6.37867C22.0001 5.56214 21.3246 4.89844 20.4934 4.89844H13.9179C13.0857 4.89844 12.4102 5.56214 12.4102 6.37867C12.4102 7.1963 13.0857 7.86 13.9179 7.86H20.4934C21.3246 7.86 22.0001 7.1963 22.0001 6.37867Z" fill="currentColor"></path>
                                <path d="M8.87774 6.37856C8.87774 8.24523 7.33886 9.75821 5.43887 9.75821C3.53999 9.75821 2 8.24523 2 6.37856C2 4.51298 3.53999 3 5.43887 3C7.33886 3 8.87774 4.51298 8.87774 6.37856Z" fill="currentColor"></path>
                                <path d="M21.9998 17.3992C21.9998 19.2648 20.4609 20.7777 18.5609 20.7777C16.6621 20.7777 15.1221 19.2648 15.1221 17.3992C15.1221 15.5325 16.6621 14.0195 18.5609 14.0195C20.4609 14.0195 21.9998 15.5325 21.9998 17.3992Z" fill="currentColor"></path>
                                </svg>
                                </a>`

                        a += `<a  class="btn btn-xs btn-outline-success rounded-pill mt-2 ml-2" id="send-${c.id}" onclick="sendVal(${c.id})" hidden>
                                <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M16.3345 2.75024H7.66549C4.64449 2.75024 2.75049 4.88924 2.75049 7.91624V16.0842C2.75049 19.1112 4.63549 21.2502 7.66549 21.2502H16.3335C19.3645 21.2502 21.2505 19.1112 21.2505 16.0842V7.91624C21.2505 4.88924 19.3645 2.75024 16.3345 2.75024Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M8.43994 12.0002L10.8139 14.3732L15.5599 9.6272" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </a>`
                        a += `<a  class="btn btn-xs btn-outline-danger rounded-pill mt-2 ml-2" id="cancel-${c.id}" onclick="cancelVal(${c.id})" hidden>
                                <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14.3955 9.59497L9.60352 14.387" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M14.3971 14.3898L9.60107 9.59277" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M16.3345 2.75024H7.66549C4.64449 2.75024 2.75049 4.88924 2.75049 7.91624V16.0842C2.75049 19.1112 4.63549 21.2502 7.66549 21.2502H16.3335C19.3645 21.2502 21.2505 19.1112 21.2505 16.0842V7.91624C21.2505 4.88924 19.3645 2.75024 16.3345 2.75024Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </a>`


                        return a

                    },
                    className: 'text-center'
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
                    url: "{{ url('setting/getParameter') }}",
                    type: "POST",
                    data: {}

                },
                columns: columns,
                // initComplete: function(data) {}
            });

        }

        function settingMeta(setting) {
            var parameter = setting.parameter || '';
            var meta = {
                inputType: setting.type === 'number' ? 'number' : 'text',
                prefix: '',
                suffix: '',
                description: setting.type === 'number' ? 'Nilai angka' : 'Nilai teks',
                min: '',
                max: '',
                step: ''
            };

            if (parameter.endsWith('_persen') || parameter === 'ppn_persen') {
                meta.suffix = '%';
                meta.description = 'Persentase (0 sampai 100)';
                meta.inputType = 'number';
                meta.min = '0';
                meta.max = '100';
                meta.step = '0.01';
            } else if (parameter.includes('_price') || parameter.endsWith('_cap')) {
                meta.prefix = 'Rp';
                meta.description = 'Nominal dalam Rupiah';
                meta.inputType = 'number';
                meta.min = '0';
                meta.step = '0.01';
            } else if (parameter.includes('email')) {
                meta.description = 'Alamat email';
                meta.inputType = 'email';
            } else if (parameter.includes('base_url')) {
                meta.description = 'Alamat URL endpoint';
                meta.inputType = 'url';
            } else if (parameter.includes('secret') || parameter.includes('user_key')) {
                meta.description = 'Credential rahasia';
                meta.inputType = 'password';
            } else if (parameter.includes('cons_id') || parameter.includes('client_id')) {
                meta.description = 'ID credential integrasi';
            } else if (parameter.includes('youtube')) {
                meta.description = 'ID video YouTube';
            } else if (parameter === 'company_logo') {
                meta.description = 'File gambar logo';
            } else if (parameter === 'app_name' || parameter.includes('company_')) {
                meta.description = 'Informasi identitas klinik';
            }

            return meta;
        }

        function formatSettingValue(value, meta) {
            if (value === null || value === '') {
                return '<span class="text-muted">Belum diatur</span>';
            }
            if (meta.prefix === 'Rp') {
                return `<span class="fw-semibold">Rp ${Number(value).toLocaleString('id-ID')}</span>`;
            }
            if (meta.suffix) {
                return `<span class="fw-semibold">${escapeSettingHtml(value)} ${meta.suffix}</span>`;
            }
            if (meta.inputType === 'password') {
                return '<span class="text-muted">Tersimpan (klik edit untuk mengganti)</span>';
            }
            return escapeSettingHtml(value);
        }

        function escapeSettingHtml(value) {
            return $('<div>').text(value ?? '').html();
        }

        function escapeSettingAttribute(value) {
            return escapeSettingHtml(value).replace(/"/g, '&quot;');
        }


        function editVal(id) {
            $('#edit-' + id).attr('hidden', true);
            $('#lab-' + id).attr('hidden', true)
            $('#val-' + id).attr('hidden', false)
            $('#input-group-' + id).attr('hidden', false)
            $('#cancel-' + id).attr('hidden', false);
            $('#send-' + id).attr('hidden', false);
        }

        function cancelVal(id) {
            $('#edit-' + id).attr('hidden', false);
            $('#lab-' + id).attr('hidden', false)
            $('#val-' + id).attr('hidden', true)
            $('#input-group-' + id).attr('hidden', true)
            $('#cancel-' + id).attr('hidden', true);
            $('#send-' + id).attr('hidden', true);
        }

        function sendVal(id) {
            if ($('#val-' + id).attr('type') == 'file') {
                var fData = new FormData($('#form-' + id)[0])
                $.ajax({
                    url: "{{ url('setting/saveSettingF') }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    contentType: false,
                    processData: false,
                    data: fData,
                    success: function(data) {
                        Toast.fire({
                            icon: "success",
                            title: "Data Updated"
                        });
                        cancelVal(id)
                        getList()
                    },
                    error: function(xhr, status, error) {
                        Toast.fire({
                            icon: "error",
                            title: JSON.parse(xhr.responseText).error
                        });

                    }
                });
            } else {
                $.ajax({
                    url: "{{ url('setting/saveSettingD') }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        param: id,
                        val: $('#val-' + id).val(),
                    },
                    success: function(data) {
                        cancelVal(id)
                        getList()
                        Toast.fire({
                            icon: "success",
                            title: "Data Updated"
                        });
                    },
                    error: function(xhr, status, error) {
                        Toast.fire({
                            icon: "error",
                            title: JSON.parse(xhr.responseText).error
                        });

                    }
                });
            }
        }
    </script>
@endsection

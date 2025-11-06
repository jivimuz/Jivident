<div class="card-body" data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40" data-iq-duration=".6"
    data-iq-delay=".6" data-iq-trigger="scroll" data-iq-ease="none" style="padding-left: 40px; padding-right:40px">
    <a class="btn btn-sm btn-outline-primary rounded-pill mt-2 ml-2" onclick="addContract()">
        <svg width="23" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path opacity="0.4"
                d="M18.8088 9.021C18.3573 9.021 17.7592 9.011 17.0146 9.011C15.1987 9.011 13.7055 7.508 13.7055 5.675V2.459C13.7055 2.206 13.5026 2 13.253 2H7.96363C5.49517 2 3.5 4.026 3.5 6.509V17.284C3.5 19.889 5.59022 22 8.16958 22H16.0453C18.5058 22 20.5 19.987 20.5 17.502V9.471C20.5 9.217 20.298 9.012 20.0465 9.013C19.6247 9.016 19.1168 9.021 18.8088 9.021Z"
                fill="currentColor"></path>
            <path opacity="0.4"
                d="M16.0842 2.56737C15.7852 2.25637 15.2632 2.47037 15.2632 2.90137V5.53837C15.2632 6.64437 16.1742 7.55437 17.2792 7.55437C17.9772 7.56237 18.9452 7.56437 19.7672 7.56237C20.1882 7.56137 20.4022 7.05837 20.1102 6.75437C19.0552 5.65737 17.1662 3.69137 16.0842 2.56737Z"
                fill="currentColor"></path>
            <path
                d="M14.3672 12.2364H12.6392V10.5094C12.6392 10.0984 12.3062 9.7644 11.8952 9.7644C11.4842 9.7644 11.1502 10.0984 11.1502 10.5094V12.2364H9.4232C9.0122 12.2364 8.6792 12.5704 8.6792 12.9814C8.6792 13.3924 9.0122 13.7264 9.4232 13.7264H11.1502V15.4524C11.1502 15.8634 11.4842 16.1974 11.8952 16.1974C12.3062 16.1974 12.6392 15.8634 12.6392 15.4524V13.7264H14.3672C14.7782 13.7264 15.1122 13.3924 15.1122 12.9814C15.1122 12.5704 14.7782 12.2364 14.3672 12.2364Z"
                fill="currentColor"></path>
        </svg> Add Contract
    </a>
    <div class="table-responsive">
        <table class="table table-striped table-hover" id="contract-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Date Start</th>
                    <th>Date End</th>
                    <th>Contract Type</th>
                    <th>Contract Nature</th>
                    <th>Is Renewal</th>
                    <th>-</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

</div>

<script>
    $(document).ready(function() {
        getContractList()
        var table = $('#main-table').DataTable();
    })

    function getContractList(lastId = null) {
        let noD = 1
        const columns = [{
                data: "id",
                render: function(data, b, c) {

                    return `${noD++}.`
                },
                className: 'text-center'
            },

            {
                data: 'datestart',
                render: function(a, b, c) {
                    var a = ``

                    a += `<span id='datestart-${c.id}'>${c.datestart ?? "-"}</span>`
                    a += `<input type="date" class="form-control" id='datestartval-${c.id}' value="${c.datestart}" hidden>`
                    return a
                }
            },
            {
                data: 'dateend',
                render: function(a, b, c) {
                    var a = ``

                    a += `<span id='dateend-${c.id}'>${c.dateend ?? "-"}</span>`
                    a += `<input type="date" class="form-control" id='dateendval-${c.id}' value="${c.dateend}" hidden>`
                    return a
                }
            },
            {
                data: 'contracttype',
                render: function(data, b, c) {
                    var a = ``

                    a += `<span id='contracttype-${c.id}'>${data ?? "-"}</span>`
                    a += `<select class="form-control" id='contracttypeval-${c.id}' value="${data}" hidden>
                        <option value='' hidden ${!data ? 'selected' : '-'}>-- Select --</option>
                        <option value='Kontrak ke 1' ${data == 'Kontrak ke 1' ? 'selected' : '-'}>Kontrak ke 1</option>
                        <option value='Kontrak ke 2' ${data == 'Kontrak ke 2' ? 'selected' : '-'}>Kontrak ke 2</option>
                        <option value='Kontrak ke 3' ${data == 'Kontrak ke 3' ? 'selected' : '-'}>Kontrak ke 3</option>
                        <option value='Permanent' ${data == 'Permanent' ? 'selected' : '-'}>Permanent</option>
                        </select>`
                    return a
                }
            },
            {
                data: 'contractnature',
                render: function(data, b, c) {
                    var a = ``

                    a += `<span id='contractnature-${c.id}'>${data ?? "-"}</span>`
                    a += `<select class="form-control" id='contractnatureval-${c.id}' value="${data}" hidden>
                        <option value='' hidden ${!data ? 'selected' : '-'}>-- Select --</option>
                        <option value='Fixed Contract' ${data == 'Fixed Contract' ? 'selected' : '-'}>Fixed Contract</option>
                        <option value='Open End Contract' ${data == 'Open End Contract' ? 'selected' : '-'}>Open End Contract</option>
                        </select>`
                    return a
                }
            },

            {
                data: 'isrenewal',
                render: function(data, b, c) {
                    var a = ``
                    var renewal = data ?? 0
                    if (parseFloat(renewal) == 1) {
                        renewal = `<span class="badge  bg-success">Yes</span>`
                    } else {
                        renewal = `<span class="badge  bg-warning">No</span>`
                    }
                    a += `<span id='isrenewal-${c.id}'>${renewal}</span>`
                    a += `<select class="form-control" id='isrenewalval-${c.id}' value="${data}" hidden>
                        <option value='' hidden ${!data ? 'selected' : '-'}>-- Select --</option>
                        <option value='1' ${data == 1 ? 'selected' : '-'}>Yes</option>
                        <option value='0' ${data == 0 ? 'selected' : '-'}>No</option>
                        </select>`
                    return a
                },
                className: 'text-center'
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

        var tabled = $('#contract-table').DataTable({
            searching: true,
            destroy: true,
            lengthChange: false,
            responsive: true,
            // pageLength: 2,
            ajax: {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                url: "{{ url('employees/' . $idn . '/contract/getEmployeeContract') }}",
                type: "POST",
                data: {}

            },
            columns: columns,
            initComplete: function(data) {
                // console.log(lastId)
                if (lastId) {
                    editVal(lastId)
                }
            }
        });

    }


    function editVal(id) {
        $('#edit-' + id).attr('hidden', true);
        $('#datestart-' + id).attr('hidden', true)
        $('#datestartval-' + id).attr('hidden', false)
        $('#dateend-' + id).attr('hidden', true)
        $('#dateendval-' + id).attr('hidden', false)
        $('#contracttype-' + id).attr('hidden', true)
        $('#contracttypeval-' + id).attr('hidden', false)
        $('#contractnature-' + id).attr('hidden', true)
        $('#contractnatureval-' + id).attr('hidden', false)
        $('#isrenewal-' + id).attr('hidden', true)
        $('#isrenewalval-' + id).attr('hidden', false)
        $('#cancel-' + id).attr('hidden', false);
        $('#send-' + id).attr('hidden', false);
    }

    function cancelVal(id) {
        $('#edit-' + id).attr('hidden', false);
        $('#datestart-' + id).attr('hidden', false)
        $('#datestartval-' + id).attr('hidden', true)
        $('#dateend-' + id).attr('hidden', false)
        $('#dateendval-' + id).attr('hidden', true)
        $('#contracttype-' + id).attr('hidden', false)
        $('#contracttypeval-' + id).attr('hidden', true)
        $('#contractnature-' + id).attr('hidden', false)
        $('#contractnatureval-' + id).attr('hidden', true)
        $('#isrenewal-' + id).attr('hidden', false)
        $('#isrenewalval-' + id).attr('hidden', true)
        $('#cancel-' + id).attr('hidden', true);
        $('#send-' + id).attr('hidden', true);
    }

    function addContract() {
        $.ajax({
            url: "{{ url('employees/' . $idn . '/contract/addContract') }}",
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                idn: "<?= $idn ?>"
            },
            success: function(data) {
                // console.log(data.data)
                getContractList(data.data.insertID)
            },
            error: function(xhr, status, error) {
                Toast.fire({
                    icon: "error",
                    title: JSON.parse(xhr.responseText).error
                });

            }
        });
    }
</script>

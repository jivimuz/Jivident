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
                            <a class="btn btn-sm btn-outline-primary rounded-pill mt-2 ml-2" onclick="addMember()">
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
                                </svg> Tambah Agent
                            </a>
                            <a class="btn btn-sm btn-outline-warning rounded-pill mt-2 ml-2"
                                href="{{ url('agents/emailCSV') }}">
                                <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M21.419 15.732C21.419 19.31 19.31 21.419 15.732 21.419H7.95C4.363 21.419 2.25 19.31 2.25 15.732V7.932C2.25 4.359 3.564 2.25 7.143 2.25H9.143C9.861 2.251 10.537 2.588 10.967 3.163L11.88 4.377C12.312 4.951 12.988 5.289 13.706 5.29H16.536C20.123 5.29 21.447 7.116 21.447 10.767L21.419 15.732Z"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                    <path d="M7.48145 14.4629H16.2164" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                                Email CSV
                            </a>

                        </div>
                        <h4 class="card-title">Agents</h4>
                        <small>List of agents</small>
                    </div>
                    <div class="card-body" data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40"
                        data-iq-duration=".6" data-iq-delay=".6" data-iq-trigger="scroll" data-iq-ease="none"
                        style="padding-left: 40px; padding-right:40px">

                        <div class="table-responsive">
                            <table class="table table-striped" id="main-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tgl Masuk</th>
                                        <th>Username</th>
                                        <th>Name</th>
                                        <th>Upline</th>
                                        <th>Sponsor</th>
                                        <th>Active</th>
                                        <th>Email</th>
                                        <th>Tunggakan</th>
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
@section('modal')
    <div class="modal fade" id="DecisionModal" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
        aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="DecisionModalLabel"></h5>
                    <a type="button" class="btn rounded-pill btn-outline-dark btn-sm" onclick="closeModal('DecisionModal')"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </a>
                </div>
                <div class="modal-body" id="DecisionModalBody">
                    <input type="text" hidden id="decisionID" value="0">
                    <br>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="number" hidden id="decisionAmount" value="0">
                                <label for="">Tagihan</label>
                                <input type="text" disabled id="decisionPrice" class="form-control">debtPayment
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Dibayarkan</label>
                                <input type="number" inputmode="numeric" class="form-control"
                                    oninput="noMinus(this);maxValue(this, parseFloat($('#decisionAmount').val() ?? 0))"
                                    id="paymentPrice">
                            </div>
                        </div>
                    </div>
                    <div class="float-end">
                        <a onclick="decision()" class="btn btn-sm rounded-pill btn-outline-success">Submit</a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @include('component/modal-full')
@endsection

@section('script')
    <script>
        let ancestor = 0;
        $(document).ready(function() {
            getList()
            var table = $('#main-table').DataTable();
        })

        async function getList() {
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
                    render: function(data, b, c) {
                        if (!data) return "-"; // kalau null/empty
                        let d = new Date(data);
                        let year = d.getFullYear();
                        let month = String(d.getMonth() + 1).padStart(2, '0');
                        let day = String(d.getDate()).padStart(2, '0');
                        return `${year}-${month}-${day}`;
                    },
                    className: 'text-center'
                },
                {
                    data: "username",
                },
                {
                    data: "name",
                },


                {
                    data: "upline_name",
                    render: function(data, b, c) {
                        return data ?? "-"
                    }
                },
                {
                    data: "sponsor_name",
                    render: function(data, b, c) {
                        return data ?? "-"
                    }
                },

                {
                    data: "is_active",
                    render: function(data, b, c) {
                        // activateAgent(${c.id})
                        var a = `<a>`
                        if (data) {
                            a += `<small style="cursor: pointer;font-size: 10px" data-bs-toggle="tooltip" title="Deactivate Agent?"  alt="Deactivate Agent" class="btn btn-sm rounded-pill btn-outline-success " onclick="">Active</small>`
                        } else {
                            a += `<small style="cursor: pointer;font-size: 10px" data-bs-toggle="tooltip" title="Activate Agent?" alt="Activate Agent" class="btn btn-sm rounded-pill btn-outline-danger " onclick="">Inactive</small>`
                        }
                        a += `</a>`
                        return a
                    },
                    className: 'text-center'
                },
                {
                    data: "email",
                },
                {
                    data: "debt_balance",
                    render: function(data, b, c) {
                        var formattedPrice = parseFloat(data).toLocaleString("id-ID", {
                            style: "currency",
                            currency: "IDR"
                        });

                        return `<input type="number" id="debt-${c.id}" hidden value="${data}" />` +
                            formattedPrice
                    },
                },
                {
                    data: "id",
                    render: function(data, b, c) {
                        var a = ''
                        if (parseFloat(c.debt_balance) > 0) {
                            a += `<a  class="btn btn-sm btn-outline-warning rounded-pill  m-1" onclick="debtPaymnet(${data})" data-bs-toggle="tooltip" title="Bayar Tunggakan?" >
                                <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7366 2.76175H8.08455C6.00455 2.75375 4.29955 4.41075 4.25055 6.49075V17.3397C4.21555 19.3897 5.84855 21.0807 7.89955 21.1167C7.96055 21.1167 8.02255 21.1167 8.08455 21.1147H16.0726C18.1416 21.0937 19.8056 19.4087 19.8026 17.3397V8.03975L14.7366 2.76175Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M14.4741 2.75V5.659C14.4741 7.079 15.6231 8.23 17.0431 8.234H19.7971" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M14.2936 12.9141H9.39355" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M11.8442 15.3639V10.4639" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                           </a>`
                        }
                        a += `<a  class="btn btn-sm btn-outline-success rounded-pill  m-1" onclick="trinaryTree(${data})" data-bs-toggle="tooltip" title="Trinary tree" >
                                <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">                                <path opacity="0.4" d="M21.25 13.4764C20.429 13.4764 19.761 12.8145 19.761 12.001C19.761 11.1865 20.429 10.5246 21.25 10.5246C21.449 10.5246 21.64 10.4463 21.78 10.3076C21.921 10.1679 22 9.97864 22 9.78146L21.999 7.10415C21.999 4.84102 20.14 3 17.856 3H6.144C3.86 3 2.001 4.84102 2.001 7.10415L2 9.86766C2 10.0648 2.079 10.2541 2.22 10.3938C2.36 10.5325 2.551 10.6108 2.75 10.6108C3.599 10.6108 4.239 11.2083 4.239 12.001C4.239 12.8145 3.571 13.4764 2.75 13.4764C2.336 13.4764 2 13.8093 2 14.2195V16.8949C2 19.158 3.858 21 6.143 21H17.857C20.142 21 22 19.158 22 16.8949V14.2195C22 13.8093 21.664 13.4764 21.25 13.4764Z" fill="currentColor"></path>                                <path d="M15.4303 11.5887L14.2513 12.7367L14.5303 14.3597C14.5783 14.6407 14.4653 14.9177 14.2343 15.0837C14.0053 15.2517 13.7063 15.2727 13.4543 15.1387L11.9993 14.3737L10.5413 15.1397C10.4333 15.1967 10.3153 15.2267 10.1983 15.2267C10.0453 15.2267 9.89434 15.1787 9.76434 15.0847C9.53434 14.9177 9.42134 14.6407 9.46934 14.3597L9.74734 12.7367L8.56834 11.5887C8.36434 11.3907 8.29334 11.0997 8.38134 10.8287C8.47034 10.5587 8.70034 10.3667 8.98134 10.3267L10.6073 10.0897L11.3363 8.61268C11.4633 8.35868 11.7173 8.20068 11.9993 8.20068H12.0013C12.2843 8.20168 12.5383 8.35968 12.6633 8.61368L13.3923 10.0897L15.0213 10.3277C15.2993 10.3667 15.5293 10.5587 15.6173 10.8287C15.7063 11.0997 15.6353 11.3907 15.4303 11.5887Z" fill="currentColor"></path>                                </svg>
                                               </a>`


                        a += `<a  class="btn btn-sm btn-outline-primary rounded-pill  m-1" onclick="editMember(${data})" data-bs-toggle="tooltip" title="Edit agent?" >
                            <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M19.9927 18.9534H14.2984C13.7429 18.9534 13.291 19.4124 13.291 19.9767C13.291 20.5422 13.7429 21.0001 14.2984 21.0001H19.9927C20.5483 21.0001 21.0001 20.5422 21.0001 19.9767C21.0001 19.4124 20.5483 18.9534 19.9927 18.9534Z" fill="currentColor"></path>
                                <path d="M10.309 6.90385L15.7049 11.2639C15.835 11.3682 15.8573 11.5596 15.7557 11.6929L9.35874 20.0282C8.95662 20.5431 8.36402 20.8344 7.72908 20.8452L4.23696 20.8882C4.05071 20.8903 3.88775 20.7613 3.84542 20.5764L3.05175 17.1258C2.91419 16.4915 3.05175 15.8358 3.45388 15.3306L9.88256 6.95545C9.98627 6.82108 10.1778 6.79743 10.309 6.90385Z" fill="currentColor"></path>
                                <path opacity="0.4" d="M18.1208 8.66544L17.0806 9.96401C16.9758 10.0962 16.7874 10.1177 16.6573 10.0124C15.3927 8.98901 12.1545 6.36285 11.2561 5.63509C11.1249 5.52759 11.1069 5.33625 11.2127 5.20295L12.2159 3.95706C13.126 2.78534 14.7133 2.67784 15.9938 3.69906L17.4647 4.87078C18.0679 5.34377 18.47 5.96726 18.6076 6.62299C18.7663 7.3443 18.597 8.0527 18.1208 8.66544Z" fill="currentColor"></path>
                                </svg>
                                           </a>`
                        a += `<a  class="btn btn-sm btn-outline-warning rounded-pill m-1"  onclick="resetPass(${data})" data-bs-toggle="tooltip" title="reset password agent?" >
                            <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M20.8064 7.62361L20.184 6.54352C19.6574 5.6296 18.4905 5.31432 17.5753 5.83872V5.83872C17.1397 6.09534 16.6198 6.16815 16.1305 6.04109C15.6411 5.91402 15.2224 5.59752 14.9666 5.16137C14.8021 4.88415 14.7137 4.56839 14.7103 4.24604V4.24604C14.7251 3.72922 14.5302 3.2284 14.1698 2.85767C13.8094 2.48694 13.3143 2.27786 12.7973 2.27808H11.5433C11.0367 2.27807 10.5511 2.47991 10.1938 2.83895C9.83644 3.19798 9.63693 3.68459 9.63937 4.19112V4.19112C9.62435 5.23693 8.77224 6.07681 7.72632 6.0767C7.40397 6.07336 7.08821 5.98494 6.81099 5.82041V5.82041C5.89582 5.29601 4.72887 5.61129 4.20229 6.52522L3.5341 7.62361C3.00817 8.53639 3.31916 9.70261 4.22975 10.2323V10.2323C4.82166 10.574 5.18629 11.2056 5.18629 11.8891C5.18629 12.5725 4.82166 13.2041 4.22975 13.5458V13.5458C3.32031 14.0719 3.00898 15.2353 3.5341 16.1454V16.1454L4.16568 17.2346C4.4124 17.6798 4.82636 18.0083 5.31595 18.1474C5.80554 18.2866 6.3304 18.2249 6.77438 17.976V17.976C7.21084 17.7213 7.73094 17.6516 8.2191 17.7822C8.70725 17.9128 9.12299 18.233 9.37392 18.6717C9.53845 18.9489 9.62686 19.2646 9.63021 19.587V19.587C9.63021 20.6435 10.4867 21.5 11.5433 21.5H12.7973C13.8502 21.5001 14.7053 20.6491 14.7103 19.5962V19.5962C14.7079 19.088 14.9086 18.6 15.2679 18.2407C15.6272 17.8814 16.1152 17.6807 16.6233 17.6831C16.9449 17.6917 17.2594 17.7798 17.5387 17.9394V17.9394C18.4515 18.4653 19.6177 18.1544 20.1474 17.2438V17.2438L20.8064 16.1454C21.0615 15.7075 21.1315 15.186 21.001 14.6964C20.8704 14.2067 20.55 13.7894 20.1108 13.5367V13.5367C19.6715 13.284 19.3511 12.8666 19.2206 12.3769C19.09 11.8873 19.16 11.3658 19.4151 10.928C19.581 10.6383 19.8211 10.3982 20.1108 10.2323V10.2323C21.0159 9.70289 21.3262 8.54349 20.8064 7.63277V7.63277V7.62361Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>                                    <circle cx="12.1747" cy="11.8891" r="2.63616" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></circle>                                </svg>
                                               </a>`

                        if (!c.is_active) {
                            a += `<a  class="btn btn-sm btn-outline-warning rounded-pill  m-1" onclick="notifAgent(${data})" data-bs-toggle="tooltip" title="Notif Agent?" >
                                    <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M19.7695 11.6453C19.039 10.7923 18.7071 10.0531 18.7071 8.79716V8.37013C18.7071 6.73354 18.3304 5.67907 17.5115 4.62459C16.2493 2.98699 14.1244 2 12.0442 2H11.9558C9.91935 2 7.86106 2.94167 6.577 4.5128C5.71333 5.58842 5.29293 6.68822 5.29293 8.37013V8.79716C5.29293 10.0531 4.98284 10.7923 4.23049 11.6453C3.67691 12.2738 3.5 13.0815 3.5 13.9557C3.5 14.8309 3.78723 15.6598 4.36367 16.3336C5.11602 17.1413 6.17846 17.6569 7.26375 17.7466C8.83505 17.9258 10.4063 17.9933 12.0005 17.9933C13.5937 17.9933 15.165 17.8805 16.7372 17.7466C17.8215 17.6569 18.884 17.1413 19.6363 16.3336C20.2118 15.6598 20.5 14.8309 20.5 13.9557C20.5 13.0815 20.3231 12.2738 19.7695 11.6453Z" fill="currentColor"></path>
                                        <path opacity="0.4" d="M14.0088 19.2283C13.5088 19.1215 10.4627 19.1215 9.96275 19.2283C9.53539 19.327 9.07324 19.5566 9.07324 20.0602C9.09809 20.5406 9.37935 20.9646 9.76895 21.2335L9.76795 21.2345C10.2718 21.6273 10.8632 21.877 11.4824 21.9667C11.8123 22.012 12.1482 22.01 12.4901 21.9667C13.1083 21.877 13.6997 21.6273 14.2036 21.2345L14.2026 21.2335C14.5922 20.9646 14.8734 20.5406 14.8983 20.0602C14.8983 19.5566 14.4361 19.327 14.0088 19.2283Z" fill="currentColor"></path>                                </svg>
                                           </a>`

                            if (!c.downline_count || parseInt(c.downline_count) == 0) {
                                a += `<a  class="btn btn-sm btn-outline-danger rounded-pill  m-1" onclick="deleteAgent(${data})" data-bs-toggle="tooltip" title="Delete Agent?" >
                                            <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M20.2871 5.24297C20.6761 5.24297 21 5.56596 21 5.97696V6.35696C21 6.75795 20.6761 7.09095 20.2871 7.09095H3.71385C3.32386 7.09095 3 6.75795 3 6.35696V5.97696C3 5.56596 3.32386 5.24297 3.71385 5.24297H6.62957C7.22185 5.24297 7.7373 4.82197 7.87054 4.22798L8.02323 3.54598C8.26054 2.61699 9.0415 2 9.93527 2H14.0647C14.9488 2 15.7385 2.61699 15.967 3.49699L16.1304 4.22698C16.2627 4.82197 16.7781 5.24297 17.3714 5.24297H20.2871ZM18.8058 19.134C19.1102 16.2971 19.6432 9.55712 19.6432 9.48913C19.6626 9.28313 19.5955 9.08813 19.4623 8.93113C19.3193 8.78413 19.1384 8.69713 18.9391 8.69713H5.06852C4.86818 8.69713 4.67756 8.78413 4.54529 8.93113C4.41108 9.08813 4.34494 9.28313 4.35467 9.48913C4.35646 9.50162 4.37558 9.73903 4.40755 10.1359C4.54958 11.8992 4.94517 16.8102 5.20079 19.134C5.38168 20.846 6.50498 21.922 8.13206 21.961C9.38763 21.99 10.6811 22 12.0038 22C13.2496 22 14.5149 21.99 15.8094 21.961C17.4929 21.932 18.6152 20.875 18.8058 19.134Z" fill="currentColor"></path>
                                                </svg>
                                           </a>`
                            }
                        }

                        return a
                    },
                },
            ]

            var tabled = $('#main-table').DataTable({
                searching: true,
                destroy: true,
                lengthChange: false,
                responsive: true,
                ordering: false,


                serverSide: true,
                processing: true,
                // pageLength: 2,

                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    url: "{{ url('agents/getList') }}",
                    type: "POST",
                    data: function(d) {
                        // d.start = offset
                        // d.length = limit
                        return $.extend({}, d, {
                            "limit": d.length,
                            "offset": d.start
                        });
                    },

                },
                columns: columns,
                // initComplete: function(data) {}
            });

        }


        async function addMember(ancestorID = null, parentID = null, position = null) {
            $.ajax({
                url: "{{ url('agents/addMember') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    parentID: btoa(btoa(parentID)),
                    ancestorID: btoa(btoa(ancestorID)),
                },
                success: function(data) {
                    $('#ThisModalLabel').html("Tambah Agent")
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

        async function trinaryTree(id) {
            $.ajax({
                url: "{{ url('agents/getTrinaryTree') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    id: btoa(btoa(id))
                },
                success: function(data) {
                    ancestor = id
                    $('#ThisModalLabel').html("Trinary View")
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

        async function editMember(agent) {
            $.ajax({
                url: "{{ url('agents/editMember') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    agent: btoa(btoa(agent))
                },
                success: function(data) {
                    $('#ThisModalLabel').html("Edit Agent")
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

        async function notifAgent(id) {
            $.ajax({
                url: "{{ url('agents/notifAgent') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    agent: btoa(btoa(id))
                },
                success: function(data) {
                    Toast.fire({
                        icon: "success",
                        title: "Email pengingat terikim!"
                    });

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

        async function debtPaymnet(id) {
            $('#decisionID').val(id)
            $('#decisionAmount').attr('oninput',
                    "noMinus(this);maxValue(this, parseFloat($('#decisionAmount').val() ?? 0))")
                .val($('#debt-' + id).val())
            $('#decisionPrice').val(parseFloat($('#debt-' + id).val() ?? 0).toLocaleString("id-ID", {
                style: "currency",
                currency: "IDR"
            }))
            $('#paymentPrice').val(0)
            showModal('DecisionModal')
        }

        function decision() {
            var url = ''
            var met = ''
            var fix = ''

            let tagihan = parseFloat($('#decisionAmount').val() ?? 0)
            let dibayar = parseFloat($('#paymentPrice').val() ?? 0)

            url = "{{ url('agents/debtPayment') }}"
            if (tagihan > dibayar) {
                fix = "bayar tagihan dengan sisa : " + parseFloat(tagihan - dibayar).toLocaleString("id-ID", {
                    style: "currency",
                    currency: "IDR"
                })
            } else if (tagihan == dibayar) {
                fix = "Tagihan dibayar LUNAS"
            }


            Swal.fire({
                title: "Do you want to Process this data?",
                text: fix,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, Process Data",
            }).then((result) => {
                if (result.isConfirmed) {
                    if (tagihan < dibayar) {
                        return Toast.fire({
                            icon: "error",
                            title: "Pembayaran Overpay"
                        });
                    }
                    if (dibayar < 10000) {
                        return Toast.fire({
                            icon: "error",
                            title: "Pembayaran minimal Rp 10.000"
                        });
                    }

                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Mohon tunggu sebentar.',
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });
                    $.ajax({
                        url: url,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            id: btoa(btoa($('#decisionID').val())),
                            paid: dibayar
                        },
                        success: function(data) {
                            Swal.fire({
                                icon: "success",
                                title: "Proses Dikonfirmasi!",
                                text: "Data selesai di proses.",
                                showConfirmButton: true,
                                confirmButtonText: "OK"
                            }).then(() => {
                                closeModal('DecisionModal')
                                getList()
                            });
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: "error",
                                title: "Gagal!",
                                text: JSON.parse(xhr.responseText).error ? JSON.parse(xhr
                                        .responseText).error : JSON.parse(xhr.responseText)
                                    .message,
                                showConfirmButton: true,
                                confirmButtonText: "OK"
                            })

                        }
                    });
                }
            });
        }

        // async function activateAgent(id) {
        //     Swal.fire({
        //         title: "You sure?",
        //         text: "Agent can login using Active Account!",
        //         icon: "warning",
        //         showCancelButton: true,
        //         confirmButtonColor: "#3085d6",
        //         cancelButtonColor: "#d33",
        //         confirmButtonText: "Yes"
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             $.ajax({
        //                 url: "{{ url('agents/activateAgent') }}",
        //                 method: 'POST',
        //                 headers: {
        //                     'X-CSRF-TOKEN': '{{ csrf_token() }}'
        //                 },
        //                 data: {
        //                     id: id
        //                 },
        //                 success: function(data) {
        //                     Toast.fire({
        //                         icon: "success",
        //                         title: "Agent Actived"
        //                     });
        //                     getList()
        //                 },
        //                 error: function(xhr, status, error) {
        //                     Toast.fire({
        //                         icon: "error",
        //                         title: JSON.parse(xhr.responseText).error ? JSON.parse(xhr
        //                                 .responseText).error : JSON.parse(xhr.responseText)
        //                             .message
        //                     });

        //                 }
        //             });
        //         }
        //     });
        // }

        // async function deactivateAgent(id) {
        //     Swal.fire({
        //         title: "You sure?",
        //         text: "Agent cannot login using Not Active Account!",
        //         icon: "warning",
        //         showCancelButton: true,
        //         confirmButtonColor: "#3085d6",
        //         cancelButtonColor: "#d33",
        //         confirmButtonText: "Yes"
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             $.ajax({
        //                 url: "{{ url('agents/deactivateAgent') }}",
        //                 method: 'POST',
        //                 headers: {
        //                     'X-CSRF-TOKEN': '{{ csrf_token() }}'
        //                 },
        //                 data: {
        //                     id: id
        //                 },
        //                 success: function(data) {
        //                     Toast.fire({
        //                         icon: "success",
        //                         title: "Agent Deactived"
        //                     });
        //                     getList()
        //                 },
        //                 error: function(xhr, status, error) {
        //                     Toast.fire({
        //                         icon: "error",
        //                         title: JSON.parse(xhr.responseText).error ? JSON.parse(xhr
        //                                 .responseText).error : JSON.parse(xhr.responseText)
        //                             .message
        //                     });

        //                 }
        //             });
        //         }
        //     });
        // }

        async function deleteAgent(id) {
            Swal.fire({
                title: "Anda yakin?",
                text: "Do you want to delete this data!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, Delete Data",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('agents/deleteMember') }}",
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            id: btoa(btoa(id))
                        },
                        success: function(data) {
                            Toast.fire({
                                icon: "success",
                                title: "Agent Dihapus"
                            });
                            getList()

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
            });
        }

        async function getChildren(parentID, btn) {
            let $btn = $(btn);
            let $nodeEl = $('#node-' + parentID); // ✅ selector by unique ID
            let $childrenWrapper = $nodeEl.children('.tree-children');

            // collapse jika sudah ada
            if ($childrenWrapper.length > 0) {
                $childrenWrapper.remove();
                $btn.find('.arrow').html(
                    `<svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path opacity="0.4" d="M12 1.99976C17.515 1.99976 22 6.48576 22 11.9998C22 17.5138 17.515 21.9998 12 21.9998C6.486 21.9998 2 17.5138 2 11.9998C2 6.48576 6.486 1.99976 12 1.99976Z" fill="currentColor"></path>
                        <path d="M10.5575 7.77917C10.7485 7.77917 10.9405 7.85217 11.0865 7.99817L14.5735 11.4682C14.7145 11.6092 14.7935 11.8002 14.7935 12.0002C14.7935 12.1992 14.7145 12.3902 14.5735 12.5312L11.0865 16.0032C10.7935 16.2952 10.3195 16.2952 10.0265 16.0012C9.73448 15.7072 9.73548 15.2322 10.0285 14.9402L12.9815 12.0002L10.0285 9.06017C9.73548 8.76817 9.73448 8.29417 10.0265 8.00017C10.1725 7.85217 10.3655 7.77917 10.5575 7.77917Z" fill="currentColor"></path>
                        </svg>
                         `
                );
                return;
            }

            // load via ajax
            $.ajax({
                url: `{{ url('agents/getChildTree') }}`,
                method: 'post',
                data: {
                    parentID: btoa(btoa(parentID))
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(res) {
                    let data = res.data || [];
                    let $childrenWrapper = $('<div class="tree-children"></div>');

                    // render max 3 slot
                    for (let i = 0; i < 3; i++) {
                        if (data[i]) {
                            let child = data[i];
                            let photo = child.img_url ? '{{ url('') }}/image/user/' + child.img_url :
                                '{{ url('') }}/assets/images/user.png';
                            let childNode = `
                                <div id="node-${child.id}" class="tree-node" data-id="${child.id}">
                                    <div class="card tree-card text-center p-2">
                                        <img src="${photo}" class="rounded-circle mx-auto" width="60" height="60">
                                        <h6 class="mt-2">${child.name}</h6>
                                        <small>ID: BM${String(child.id).padStart(5, "0")}</small>`
                            if (child.is_active && parseInt(child.is_active) == 1) {
                                childNode += `
                                            <div style="width: 100%; text-align: center">
                                            <span style="width:100px;font-size: 10px" data-bs-toggle="tooltip" title="Deactivate Agent?"
                                                    alt="Deactivate Agent" class="badge bg-success rounded-pill  ">
                                                    <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path opacity="0.4"
                                                            d="M12.0865 22C11.9627 22 11.8388 21.9716 11.7271 21.9137L8.12599 20.0496C7.10415 19.5201 6.30481 18.9259 5.68063 18.2336C4.31449 16.7195 3.5544 14.776 3.54232 12.7599L3.50004 6.12426C3.495 5.35842 3.98931 4.67103 4.72826 4.41215L11.3405 2.10679C11.7331 1.96656 12.1711 1.9646 12.5707 2.09992L19.2081 4.32684C19.9511 4.57493 20.4535 5.25742 20.4575 6.02228L20.4998 12.6628C20.5129 14.676 19.779 16.6274 18.434 18.1581C17.8168 18.8602 17.0245 19.4632 16.0128 20.0025L12.4439 21.9088C12.3331 21.9686 12.2103 21.999 12.0865 22Z"
                                                            fill="currentColor"></path>
                                                        <path
                                                            d="M11.3194 14.3209C11.1261 14.3219 10.9328 14.2523 10.7838 14.1091L8.86695 12.2656C8.57097 11.9793 8.56795 11.5145 8.86091 11.2262C9.15387 10.9369 9.63207 10.934 9.92906 11.2193L11.3083 12.5451L14.6758 9.22479C14.9698 8.93552 15.448 8.93258 15.744 9.21793C16.041 9.50426 16.044 9.97004 15.751 10.2574L11.8519 14.1022C11.7049 14.2474 11.5127 14.3199 11.3194 14.3209Z"
                                                            fill="currentColor"></path>
                                                    </svg> Active
                                            </span>
                                            </div>`
                            } else {
                                childNode += `
                                                    <div style="width: 100%; text-align: center">
                                                    <span style="width:100px;font-size: 10px" data-bs-toggle="tooltip" title="Activate Agent?"
                                                        alt="Activate Agent" class="badge bg-danger rounded-pill  ">
                                                        <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path opacity="0.4"
                                                                d="M12.086 22C11.9622 22 11.8393 21.9716 11.7276 21.9137L8.12627 20.0496C7.10336 19.5201 6.30397 18.9259 5.68076 18.2336C4.31353 16.7195 3.55441 14.776 3.54132 12.7599L3.50004 6.12426C3.495 5.35842 3.98833 4.67103 4.72732 4.41215L11.34 2.10679C11.7336 1.96656 12.1716 1.9646 12.5703 2.09992L19.2081 4.32684C19.9511 4.57493 20.4535 5.25742 20.4575 6.02228L20.4998 12.6628C20.5129 14.676 19.7799 16.6274 18.4349 18.1581C17.8167 18.8602 17.0253 19.4632 16.0135 20.0025L12.4444 21.9088C12.3337 21.9686 12.2098 21.999 12.086 22Z"
                                                                fill="currentColor"></path>
                                                            <path
                                                                d="M13.0679 11.7249L14.426 10.4021C14.721 10.1148 14.721 9.65001 14.426 9.3627C14.131 9.07539 13.6528 9.07539 13.3578 9.3627L11.9996 10.6845L10.6425 9.3627C10.3475 9.07539 9.86926 9.07539 9.57427 9.3627C9.27928 9.65001 9.27928 10.1148 9.57427 10.4021L10.9324 11.7249L9.57427 13.0478C9.27928 13.3351 9.27928 13.7999 9.57427 14.0872C9.72227 14.2313 9.91557 14.3029 10.1089 14.3029C10.3012 14.3029 10.4945 14.2313 10.6425 14.0872L11.9996 12.7653L13.3578 14.0872C13.5058 14.2313 13.6981 14.3029 13.8914 14.3029C14.0847 14.3029 14.278 14.2313 14.426 14.0872C14.721 13.7999 14.721 13.3351 14.426 13.0478L13.0679 11.7249Z"
                                                                fill="currentColor"></path>
                                                        </svg>
                                                    Not Active
                                                    </span>
                                            </div>`
                            }
                            childNode += ` <button class="btn btn-sm btn-outline-primary toggle-children mt-2"
                                                onclick="getChildren(${child.id}, this)">
                                            <span class="arrow">
                                                <svg width="23" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path opacity="0.4"
                                                        d="M22 12C22 17.515 17.514 22 12 22C6.486 22 2 17.515 2 12C2 6.486 6.486 2 12 2C17.514 2 22 6.486 22 12Z"
                                                        fill="currentColor"></path>
                                                    <path
                                                        d="M16.2211 10.5575C16.2211 10.7485 16.1481 10.9405 16.0021 11.0865L12.5321 14.5735C12.3911 14.7145 12.2001 14.7935 12.0001 14.7935C11.8011 14.7935 11.6101 14.7145 11.4691 14.5735L7.99707 11.0865C7.70507 10.7935 7.70507 10.3195 7.99907 10.0265C8.29307 9.73448 8.76807 9.73548 9.06007 10.0285L12.0001 12.9815L14.9401 10.0285C15.2321 9.73548 15.7061 9.73448 16.0001 10.0265C16.1481 10.1725 16.2211 10.3655 16.2211 10.5575Z"
                                                        fill="currentColor"></path>
                                                </svg></span>
                                        </button>
                                    </div>
                                </div>
                            `;
                            $childrenWrapper.append(childNode);
                        } else {
                            // slot kosong
                            if (res.parent && parseInt(res.parent.is_active) == 1) {
                                let $slot = $(`
                                    <div class="tree-node">
                                        <div class="tree-slot" onclick="addMember(${ancestor},${parentID}, ${i+1})">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 4v16m8-8H4"/>
                                            </svg>
                                        </div>
                                    </div>
                                `);
                                $childrenWrapper.append($slot);
                            } else {
                                let $slot = $(`
                                    <div class="tree-node">
                                        <div class="tree-slot" >
                                    <svg width="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">                                <path opacity="0.4" d="M16.34 1.99976H7.67C4.28 1.99976 2 4.37976 2 7.91976V16.0898C2 19.6198 4.28 21.9998 7.67 21.9998H16.34C19.73 21.9998 22 19.6198 22 16.0898V7.91976C22 4.37976 19.73 1.99976 16.34 1.99976Z" fill="currentColor"></path>                                <path d="M15.0158 13.7703L13.2368 11.9923L15.0148 10.2143C15.3568 9.87326 15.3568 9.31826 15.0148 8.97726C14.6728 8.63326 14.1198 8.63426 13.7778 8.97626L11.9988 10.7543L10.2198 8.97426C9.87782 8.63226 9.32382 8.63426 8.98182 8.97426C8.64082 9.31626 8.64082 9.87126 8.98182 10.2123L10.7618 11.9923L8.98582 13.7673C8.64382 14.1093 8.64382 14.6643 8.98582 15.0043C9.15682 15.1763 9.37982 15.2613 9.60382 15.2613C9.82882 15.2613 10.0518 15.1763 10.2228 15.0053L11.9988 13.2293L13.7788 15.0083C13.9498 15.1793 14.1728 15.2643 14.3968 15.2643C14.6208 15.2643 14.8448 15.1783 15.0158 15.0083C15.3578 14.6663 15.3578 14.1123 15.0158 13.7703Z" fill="currentColor"></path>                                </svg>

                                    </div>
                                    Belum Aktivasi

                                    </div>
                                `);
                                $childrenWrapper.append($slot);
                            }
                        }
                    }

                    $nodeEl.append($childrenWrapper);
                    $btn.find('.arrow').html(`  <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path opacity="0.4"
                                d="M22 12C22 17.515 17.514 22 12 22C6.486 22 2 17.515 2 12C2 6.486 6.486 2 12 2C17.514 2 22 6.486 22 12Z"
                                fill="currentColor"></path>
                            <path
                                d="M16.2211 10.5575C16.2211 10.7485 16.1481 10.9405 16.0021 11.0865L12.5321 14.5735C12.3911 14.7145 12.2001 14.7935 12.0001 14.7935C11.8011 14.7935 11.6101 14.7145 11.4691 14.5735L7.99707 11.0865C7.70507 10.7935 7.70507 10.3195 7.99907 10.0265C8.29307 9.73448 8.76807 9.73548 9.06007 10.0285L12.0001 12.9815L14.9401 10.0285C15.2321 9.73548 15.7061 9.73448 16.0001 10.0265C16.1481 10.1725 16.2211 10.3655 16.2211 10.5575Z"
                                fill="currentColor"></path>
                        </svg>`);
                },
                error: function(err) {
                    console.error(err);
                }
            });
        }

        function resetPass(id) {
            var newPass = buatPasswordAcak();
            Swal.fire({
                title: "You sure to reset the password!?",
                text: "New Password : " + newPass,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('agents/resetPass') }}",
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            id: id,
                            pass: newPass,
                        },
                        success: function(data) {
                            Toast.fire({
                                icon: "success",
                                title: "Password has reset"
                            });
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

        function buatPasswordAcak(toID = null, panjang = 10, sertakanHurufBesar = true, sertakanAngka = true,
            sertakanSimbol =
            false) {
            const hurufKecil = 'abcdefghijklmnopqrstuvwxyz';
            const hurufBesar = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            const angka = '0123456789';
            const simbol = '!@#$%^&*()_+-=[]{}|;:,.<>?';

            let kumpulanKarakter = hurufKecil;
            if (sertakanHurufBesar) kumpulanKarakter += hurufBesar;
            if (sertakanAngka) kumpulanKarakter += angka;
            if (sertakanSimbol) kumpulanKarakter += simbol;

            let password = '';
            for (let i = 0; i < panjang; i++) {
                const indeksAcak = Math.floor(Math.random() * kumpulanKarakter.length);
                password += kumpulanKarakter[indeksAcak];
            }
            if (Array.isArray(toID)) {
                toID.map(index => {
                    $('#' + index).val(password);
                })
                return true
            } else if (toID) {
                return $('#' + toID).val(password);

            } else {
                return password;

            }
        }
    </script>
@endsection

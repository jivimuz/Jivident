@extends('layout/main')
@section('style')
    <style>
        .tab-bawah {
            margin-left: 20px
        }

        .nav-item {
            cursor: pointer;
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
                        <div class="row">
                            <div class="col-sm-1">
                                <a href="{{ url('employees') }}">
                                    <svg width="18" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M16.7857 10.8672L9.93756 15.0865C9.78418 15.1805 9.58782 15.1715 9.44422 15.0605C8.7936 14.5594 8.21234 14.0514 7.82451 13.6244C7.82451 13.6244 7.49041 13.2824 7.34582 13.0654C7.11234 12.7693 7 12.3823 7 12.0063C7 11.5843 7.12309 11.1853 7.36829 10.8662C7.42398 10.8092 7.63597 10.5582 7.83623 10.3532C9.00461 9.07715 12.0545 6.96905 13.6586 6.33002C13.8921 6.22701 14.5153 6.012 14.8387 6C15.1503 6 15.4512 6.068 15.7404 6.21701C16.096 6.42202 16.3744 6.74004 16.5307 7.11706C16.6313 7.37907 16.7866 8.16511 16.7866 8.18811C16.8873 8.74914 16.9625 9.53718 16.9996 10.4572C17.0064 10.6222 16.9234 10.7822 16.7857 10.8672Z"
                                            fill="currentColor"></path>
                                        <path opacity="0.4"
                                            d="M16.3275 13.1397C16.6293 12.9527 17.0094 13.1917 16.9947 13.5507C16.9586 14.3928 16.896 15.1348 16.8198 15.6869C16.8081 15.6989 16.6528 16.6779 16.474 17.0089C16.1624 17.6239 15.5508 18 14.8934 18H14.8387C14.4147 17.989 13.513 17.6129 13.513 17.5899C13.0588 17.4009 12.4658 17.0809 11.8279 16.6959C11.5406 16.5219 11.5338 16.0949 11.821 15.9179L16.3275 13.1397Z"
                                            fill="currentColor"></path>
                                    </svg>
                                    Back
                                </a>
                            </div>
                            <div class="col-sm-11">
                                <div class="pull-right " style="text-align: right">
                                    <h4 class="card-title ">{{ $employee->fullname }}</h4>
                                    <small>{{ $employee->designation }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" data-iq-gsap="onStart" data-iq-opacity="0" data-iq-position-y="-40"
                        data-iq-duration=".6" data-iq-delay=".6" data-iq-trigger="scroll" data-iq-ease="none"
                        style="padding-left: 40px; padding-right:40px">
                        <div class="panel-body">
                            <div id="responsive-tabs">
                                <ul class="nav nav-tabs">
                                    <li class="nav-item">
                                        <a data-bs-toggle="tab" role="tab" aria-controls="ctab1" aria-selected="false"
                                            class="nav-link active" id="tab1" data-toggle="tab"
                                            href-target="profile">Profile</a>
                                    </li>
                                    <li class="nav-item">
                                        <a data-bs-toggle="tab" role="tab" aria-controls="ctab2" aria-selected="false"
                                            class="nav-link" id="tab2" data-toggle="tab"
                                            href-target="employment">Employment</a>
                                    </li>
                                    <li class="nav-item">
                                        <a data-bs-toggle="tab" role="tab" aria-controls="ctab3" aria-selected="false"
                                            class="nav-link" id="tab3" data-toggle="tab"
                                            href-target="assignment">Assignment</a>
                                    </li>
                                    <li class="nav-item">
                                        <a data-bs-toggle="tab" role="tab" aria-controls="ctab4" aria-selected="false"
                                            class="nav-link" id="tab4" data-toggle="tab"
                                            href-target="contract">Contract</a>
                                    </li>
                                    <li class="nav-item">
                                        <a data-bs-toggle="tab" role="tab" aria-controls="ctab5" aria-selected="false"
                                            class="nav-link" id="tab5" data-toggle="tab"
                                            href-target="statutory">Statutory</a>
                                    </li>
                                    <li class="nav-item">
                                        <a data-bs-toggle="tab" role="tab" aria-controls="ctab6" aria-selected="false"
                                            class="nav-link" id="tab6" data-toggle="tab"
                                            href-target="salary">Salary</a>
                                    </li>
                                    <li class="nav-item">
                                        <a data-bs-toggle="tab" role="tab" aria-controls="ctab7" aria-selected="false"
                                            class="nav-link" id="tab7" data-toggle="tab" href-target="bank">Bank</a>
                                    </li>
                                    <li class="nav-item">
                                        <a data-bs-toggle="tab" role="tab" aria-controls="ctab8"
                                            aria-selected="false" class="nav-link" id="tab8" data-toggle="tab"
                                            href-target="allowance">Allowance</a>
                                    </li>
                                    <li class="nav-item">
                                        <a data-bs-toggle="tab" role="tab" aria-controls="ctab9"
                                            aria-selected="false" class="nav-link" id="tab9" data-toggle="tab"
                                            href-target="attendace">Attendace</a>
                                    </li>
                                    <li class="nav-item">
                                        <a data-bs-toggle="tab" role="tab" aria-controls="ctab10"
                                            aria-selected="false" class="nav-link" id="tab10" data-toggle="tab"
                                            href-target="education">Education</a>
                                    </li>
                                    <li class="nav-item">
                                        <a data-bs-toggle="tab" role="tab" aria-controls="ctab11"
                                            aria-selected="false" class="nav-link" id="tab11" data-toggle="tab"
                                            href-target="family">Family</a>
                                    </li>
                                    <li class="nav-item">
                                        <a data-bs-toggle="tab" role="tab" aria-controls="ctab12"
                                            aria-selected="false" class="nav-link" id="tab12" data-toggle="tab"
                                            href-target="attachment">Attachment</a>
                                    </li>
                                    <li class="nav-item">
                                        <a data-bs-toggle="tab" role="tab" aria-controls="ctab13"
                                            aria-selected="false" class="nav-link" id="tab13" data-toggle="tab"
                                            href-target="others">Others</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div id="tab-bawah">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ url('') }}/assets/js/tabsdrop.js"></script>
    <script>
        var currentTab = '';
        $(document).ready(function() {
            // Mengaktifkan tab pertama
            Tabs('tab1')

            $('.nav-link').click(function() {
                Tabs($(this).attr('id'))
            })
        });

        function Tabs(tabId) {
            var url = '';
            if (currentTab == tabId) {
                console.log(0)
                return false
            }

            // switch (tabId) {
            //     case 'tab1':
            //         url = "<?= url('employees/' . $idn) ?>/profile";
            //         break;
            // }

            currentTab = tabId

            $.ajax({
                url: `<?= url('employees/' . $idn) ?>/${$('#'+tabId).attr('href-target')}`,
                type: 'GET',
                success: function(response) {
                    // Memasukkan konten ke dalam container
                    $("#tab-bawah").html(response);
                },
                error: function() {
                    // Menangani kesalahan jika terjadi
                    $("#tab-bawah").html("Terjadi kesalahan dalam memuat konten.");
                }
            });
        }
    </script>
@endsection

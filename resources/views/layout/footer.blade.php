 <!-- Footer Section Start -->
 <?php
 use App\Models\Setting;
 $app_name = Setting::where('parameter', 'app_name')->first()->value ?: 'AppName';
 ?>
 <!-- Footer Section starts-->
 <footer>
     <div class="container-fluid">
         <div class="row">
             <div class="col-md-9 col-12">
                 <ul class="footer-text">
                     <li>
                         <p class="mb-0">Copyright © {{ date('Y') }} JiviDent - Jivimz. All rights reserved </p>
                     </li>
                 </ul>
             </div>
             <div class="col-md-3">
                 <ul class="footer-text text-end">
                     <li><a href="mailto:jivirasgal@gmail.com."> Need Help? <i class="ti ti-help"></i></a></li>
                 </ul>
             </div>
         </div>
     </div>
 </footer>


 <div class="modal fade" id="changePass" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true">
     <div class="modal-dialog modal-sm" role="document">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="changePassLabel">Change Password</h5>
                 <a type="button" class="btn rounded-pill btn-outline-dark btn-sm r" onclick="closeModal('changePass')"
                     aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                 </a>
             </div>
             <div class="modal-body" id="changePassBody">
                 <div class="form-group">
                     <label for="accLog">Username</label>
                     <input type="text" disabled class="form-control" value="{{ auth()->user()->username }}"
                         id="accLog">
                 </div>
                 <div class="form-group">
                     <label for="newPass">New Password</label>
                     <input type="password" class="form-control cpinput " id="newPass">
                 </div>
                 <div class="form-group">
                     <label for="newPassConfirm">Confirm Password</label>
                     <input type="password" class="form-control cpinput " id="newPassConfirm">
                 </div><br>
                 <div class="float-end">
                     <a class="btn btn-outline-success btn-sm rounded-pill" id="submitPass"> <svg width="18"
                             viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                             <path opacity="0.4"
                                 d="M12.0865 22C11.9627 22 11.8388 21.9716 11.7271 21.9137L8.12599 20.0496C7.10415 19.5201 6.30481 18.9259 5.68063 18.2336C4.31449 16.7195 3.5544 14.776 3.54232 12.7599L3.50004 6.12426C3.495 5.35842 3.98931 4.67103 4.72826 4.41215L11.3405 2.10679C11.7331 1.96656 12.1711 1.9646 12.5707 2.09992L19.2081 4.32684C19.9511 4.57493 20.4535 5.25742 20.4575 6.02228L20.4998 12.6628C20.5129 14.676 19.779 16.6274 18.434 18.1581C17.8168 18.8602 17.0245 19.4632 16.0128 20.0025L12.4439 21.9088C12.3331 21.9686 12.2103 21.999 12.0865 22Z"
                                 fill="currentColor"></path>
                             <path
                                 d="M11.3194 14.3209C11.1261 14.3219 10.9328 14.2523 10.7838 14.1091L8.86695 12.2656C8.57097 11.9793 8.56795 11.5145 8.86091 11.2262C9.15387 10.9369 9.63207 10.934 9.92906 11.2193L11.3083 12.5451L14.6758 9.22479C14.9698 8.93552 15.448 8.93258 15.744 9.21793C16.041 9.50426 16.044 9.97004 15.751 10.2574L11.8519 14.1022C11.7049 14.2474 11.5127 14.3199 11.3194 14.3209Z"
                                 fill="currentColor"></path>
                         </svg> Change</a>
                 </div>
             </div>
         </div>
     </div>
 </div>

 <div class="modal fade" id="signatureModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="signatureModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered" role="document">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="signatureModalLabel">Tanda Tangan Elektronik Saya</h5>
                 <a type="button" class="btn rounded-pill btn-outline-dark btn-sm" onclick="closeModal('signatureModal')"
                     aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                 </a>
             </div>
             <div class="modal-body" id="signatureModalBody">
                 <p class="text-muted" style="font-size:12px">Tanda tangan ini otomatis dipakai setiap Anda mencetak
                     surat pasien (surat sakit/sehat/rujukan). Bukan tanda tangan elektronik tersertifikasi — cuma
                     gambar + timestamp.</p>
                 <div id="signatureExistingWrapper" style="display:none" class="text-center mb-3">
                     <img id="signatureExistingImg" src="" style="max-height:100px;border:1px solid #ddd;border-radius:6px;padding:5px">
                     <div><small class="text-muted">Tanda tangan tersimpan saat ini</small></div>
                 </div>
                 <canvas id="signatureCanvas" width="400" height="150"
                     style="border:1px dashed #999;border-radius:6px;width:100%;touch-action:none;cursor:crosshair;background:#fff"></canvas>
                 <div class="d-flex justify-content-between mt-2">
                     <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearSignatureCanvas()">Bersihkan</button>
                     <div>
                         <button type="button" class="btn btn-outline-danger btn-sm" id="deleteSignatureBtn"
                             style="display:none" onclick="deleteSignatureSaved()">Hapus Tersimpan</button>
                         <button type="button" class="btn btn-outline-success btn-sm" id="saveSignatureBtn">Simpan</button>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </div>




 <!-- modal -->
 {{--
<div class="modal" data-bs-backdrop="static" id="welcomeCard" >
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content welcome-card">

            <div class="modal-body p-0">
                <div class="text-center position-relative welcome-card-content z-1 p-3">
                    <div class="text-end position-relative z-1">
                        <i class="ti ti-x fs-5 text-dark f-w-600" data-bs-dismiss="modal"></i>
                    </div>
                    <h2 class="f-w-700 text-primary-dark mb-0"><span>Welcome!</span> <img alt="gif"
                                                                                          class="w-45 d-inline align-baseline"
                                                                                          src="{{url('')}}/assets/images/dashboard/ecommerce-dashboard/celebration.gif">
                    </h2>

                    <div class="modal-img-box">
                        <img alt="img" class=" img-fluid" src="{{url('')}}/assets/images/modals/welcome-1.png">
                    </div>
                    <div class="modal-btn mb-4">
                        <button class="btn btn-primary text-white btn-sm rounded" data-bs-dismiss="modal" type="button">
                            Get Started
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div> --}}


 <!--customizer-->
 <div id="customizer"></div>

 <!-- latest jquery-->
 <script src="{{ url('') }}/assets/js/jquery-3.6.3.min.js"></script>

 <!-- Bootstrap js-->
 <script src="{{ url('') }}/assets/vendor/bootstrap/bootstrap.bundle.min.js"></script>

 <!-- Simple bar js-->
 <script src="{{ url('') }}/assets/vendor/simplebar/simplebar.js"></script>

 <!-- apexcharts -->
 <script src="{{ url('') }}/assets/vendor/apexcharts/apexcharts.min.js"></script>
 <script src="{{ url('') }}/assets/vendor/apexcharts/column/dayjs.min.js"></script>
 <script src="{{ url('') }}/assets/vendor/apexcharts/column/quarterOfYear.min.js"></script>
 <script src="{{ url('') }}/assets/vendor/apexcharts/timelinechart/moment.min.js"></script>

 <!-- Customizer js-->
 <script src="{{ url('') }}/assets/js/customizer.js"></script>

 <!-- phosphor js -->
 <script src="{{ url('') }}/assets/vendor/phosphor/phosphor.js"></script>

 <!-- slick-file -->
 <script src="{{ url('') }}/assets/vendor/slick/slick.min.js"></script>

 <!-- filepond -->
 <script src="{{ url('') }}/assets/vendor/filepond/file-encode.min.js"></script>
 <script src="{{ url('') }}/assets/vendor/filepond/validate-size.min.js"></script>
 <script src="{{ url('') }}/assets/vendor/filepond/validate-type.js"></script>
 <script src="{{ url('') }}/assets/vendor/filepond/exif-orientation.min.js"></script>
 <script src="{{ url('') }}/assets/vendor/filepond/image-preview.min.js"></script>
 <script src="{{ url('') }}/assets/vendor/filepond/filepond.min.js"></script>

 <!-- Project Dashboard js-->

 <!-- App js-->
 <script src="{{ url('') }}/assets/js/script.js"></script>

 <script src="{{ url('') }}/assets/js/datatables.min.js"></script>
 <script src="{{ url('') }}/assets/vendor/datepikar/flatpickr.js"></script>

 <!--js-->
 <script src="{{ url('') }}/assets/js/date_picker.js"></script>

 <script src="{{ url('') }}/assets/js/sweetalert2.min.js"></script>
 <script src="{{ url('') }}/assets/js/select2.min.js"></script>
 <script src="{{ url('') }}/assets/js/responsive.dataTables.min.js"></script>
 <script src="{{ url('') }}/assets/js/dropify.min.js"></script>
 <script src="{{ url('') }}/assets/js/customFunction.js"></script>
 @if (session('expiredAlert'))
     <script>
         $(document).ready(function() {
             // Tampilkan modal
             setTimeout(() => {

                 Toast.fire({
                     icon: "warning",
                     title: "Lisensi habis pada {{ session('expiredDate') }}",
                     text: "Harap hubungi \n+6282120741970 (Jivi)",
                 })
             }, 2000);
         });
     </script>

     @php
         session()->forget('expiredAlert');
         session()->forget('expiredDate');
     @endphp
 @endif


 <script>
     function changePass() {
         showModal('changePass');
     }

     var sigCanvas, sigCtx, sigDrawing = false,
         sigHasDrawn = false;

     function openSignatureModal() {
         showModal('signatureModal');
         setTimeout(initSignatureCanvas, 150); // tunggu modal selesai animasi biar canvas tidak 0x0

         $.ajax({
             url: "{{ url('profile/signature') }}",
             method: 'POST',
             headers: {
                 'X-CSRF-TOKEN': '{{ csrf_token() }}'
             },
             success: function(res) {
                 if (res.data.signature_path) {
                     $('#signatureExistingImg').attr('src', "{{ url('') }}/" + res.data.signature_path + '?t=' +
                         Date.now());
                     $('#signatureExistingWrapper').show();
                     $('#deleteSignatureBtn').show();
                 } else {
                     $('#signatureExistingWrapper').hide();
                     $('#deleteSignatureBtn').hide();
                 }
             }
         });
     }

     function initSignatureCanvas() {
         sigCanvas = document.getElementById('signatureCanvas');
         if (!sigCanvas) return;
         sigCtx = sigCanvas.getContext('2d');
         sigCtx.clearRect(0, 0, sigCanvas.width, sigCanvas.height);
         sigHasDrawn = false;
         sigCtx.lineWidth = 2;
         sigCtx.lineCap = 'round';
         sigCtx.strokeStyle = '#000';

         function getSigPos(e) {
             var rect = sigCanvas.getBoundingClientRect();
             var scaleX = sigCanvas.width / rect.width;
             var scaleY = sigCanvas.height / rect.height;
             return {
                 x: (e.clientX - rect.left) * scaleX,
                 y: (e.clientY - rect.top) * scaleY
             };
         }

         sigCanvas.onpointerdown = function(e) {
             sigDrawing = true;
             var pos = getSigPos(e);
             sigCtx.beginPath();
             sigCtx.moveTo(pos.x, pos.y);
         };
         sigCanvas.onpointermove = function(e) {
             if (!sigDrawing) return;
             var pos = getSigPos(e);
             sigCtx.lineTo(pos.x, pos.y);
             sigCtx.stroke();
             sigHasDrawn = true;
         };
         sigCanvas.onpointerup = function() {
             sigDrawing = false;
         };
         sigCanvas.onpointerleave = function() {
             sigDrawing = false;
         };
     }

     function clearSignatureCanvas() {
         if (sigCtx) sigCtx.clearRect(0, 0, sigCanvas.width, sigCanvas.height);
         sigHasDrawn = false;
     }

     $('#saveSignatureBtn').on('click', function() {
         if (!sigHasDrawn) {
             return Toast.fire({
                 icon: 'warning',
                 title: 'Gambar tanda tangan dulu'
             });
         }
         var dataUrl = sigCanvas.toDataURL('image/png');
         $.ajax({
             url: "{{ url('profile/signature/save') }}",
             method: 'POST',
             headers: {
                 'X-CSRF-TOKEN': '{{ csrf_token() }}'
             },
             data: {
                 signature: dataUrl
             },
             success: function() {
                 Toast.fire({
                     icon: 'success',
                     title: 'Tanda tangan disimpan'
                 });
                 closeModal('signatureModal');
             },
             error: function(xhr) {
                 Toast.fire({
                     icon: 'error',
                     title: JSON.parse(xhr.responseText).error ?? 'Gagal menyimpan'
                 });
             }
         });
     });

     function deleteSignatureSaved() {
         Swal.fire({
             title: 'Hapus tanda tangan tersimpan?',
             icon: 'warning',
             showCancelButton: true,
             confirmButtonText: 'Hapus',
             cancelButtonText: 'Batal'
         }).then(function(result) {
             if (result.isConfirmed) {
                 $.ajax({
                     url: "{{ url('profile/signature/delete') }}",
                     method: 'POST',
                     headers: {
                         'X-CSRF-TOKEN': '{{ csrf_token() }}'
                     },
                     success: function() {
                         Toast.fire({
                             icon: 'success',
                             title: 'Tanda tangan dihapus'
                         });
                         $('#signatureExistingWrapper').hide();
                         $('#deleteSignatureBtn').hide();
                     }
                 });
             }
         });
     }

     $('#submitPass').on('click', function() {

         var np = $('#newPass').val();
         var cp = $('#newPassConfirm').val();
         if (!cp || !np) {
             return Toast.fire({
                 icon: 'warning',
                 title: "Please Insert new password!"
             })
         }
         if (np.length < 8 || cp.length < 8) {
             return Toast.fire({
                 icon: 'warning',
                 title: "Passwords must be at least 8 characters long!"
             });
         }

         if (cp !== np) {
             return Toast.fire({
                 icon: 'warning',
                 title: "New Password & Confirmation didn't same!"
             })
         }

         $.ajax({
             url: "{{ url('profile/changePassword') }}",
             method: 'POST',
             headers: {
                 'X-CSRF-TOKEN': '{{ csrf_token() }}'
             },
             data: {
                 np: np,
             },
             success: function(data) {
                 Toast.fire({
                     icon: "success",
                     title: "Password Updated"
                 });
                 $('#newPass').val('');
                 $('#newPassConfirm').val('');
                 closeModal('changePass')
             },
             error: function(xhr, status, error) {
                 Toast.fire({
                     icon: "error",
                     title: JSON.parse(xhr.responseText).error
                 });

             }
         });

     })
 </script>

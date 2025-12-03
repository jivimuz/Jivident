  <form method="POST" id="soap-form" class="p-4">

      @csrf
      <div class="modal-body row">

          <h1 class="col-md-1">S</h1>
          <div class="col-md-11">
              <textarea maxlength="255" class="form-control" name="s" id="s" rows="3" placeholder="Subjective"
                  style="background-color: #f0f0f0;">{{ $soap ? $soap->s : $data->keluhan }}</textarea> <br>
          </div>

          <h1 class="col-md-1">O</h1>
          <div class="col-md-11">
              <textarea maxlength="255" class="form-control" name="o" id="o" rows="3" placeholder="Objective"
                  style="background-color: #f0f0f0;">{{ $soap ? $soap->o : $objective }}</textarea> <br>
          </div>

          <h1 class="col-md-1">A</h1>
          <div class="col-md-11">
              <textarea maxlength="255" class="form-control" name="a" id="a" rows="3" placeholder="Assesment"
                  style="background-color: #f0f0f0;">{{ $soap ? $soap->a : '' }}</textarea> <br>
          </div>

          <h1 class="col-md-1">P</h1>
          <div class="col-md-11">
              <textarea maxlength="255" class="form-control" name="p" id="p" rows="3" placeholder="Plan"
                  style="background-color: #f0f0f0;">{{ $soap ? $soap->p : '' }}</textarea> <br>
          </div>


          <h1 class="col-md-1">E</h1>
          <div class="col-md-11">
              <textarea maxlength="255" class="form-control" name="e" id="e" rows="3" placeholder="Evaluation"
                  style="background-color: #f0f0f0;">{{ $soap ? $soap->e : '' }}</textarea> <br>
          </div>

          <h1 class="col-md-1"></h1>
          <div class="col-md-11">
              <div class="border row" style="border-radius:20px; padding:20px">
                  <div class="col-md-4">

                      <ul class="list-group d-flex flex-column ">
                          <li class="checkbox-wrapper">
                              <label class="check-box">
                                  <input type="checkbox" name="diabet" id="diabet"
                                      {{ $data->is_diabet != 0 || null ? 'checked' : '' }} />
                                  <span class="checkmark outline-primary-dark"></span>
                                  <span class="text-primary-dark f-w-600 me-1">Diabet</span>
                              </label>
                          </li>
                          <li class="checkbox-wrapper ">
                              <label class="check-box">
                                  <input type="checkbox" name="diare" id="diare"
                                      {{ $data->is_diare != 0 || null ? 'checked' : '' }} />
                                  <span class="checkmark outline-secondary-dark"></span>
                                  <span class="text-secondary-dark f-w-600 me-1">Diare</span>
                              </label>
                          </li>
                      </ul>
                  </div>
                  <div class="col-md-4">
                      <ul class="list-group d-flex flex-column ">
                          <li class="checkbox-wrapper ">
                              <label class="check-box">
                                  <input type="checkbox" name="hipertensi" id="hipertensi"
                                      {{ $data->is_hipertensi != 0 || null ? 'checked' : '' }} />
                                  <span class="checkmark outline-success-dark"></span>
                                  <span class="text-success-dark f-w-600 me-1">Hipertensi</span>
                              </label>
                          </li>
                          <li class="checkbox-wrapper ">
                              <label class="check-box">
                                  <input type="checkbox" name="pnemonea" id="pnemonea"
                                      {{ $data->is_pnemonea != 0 || null ? 'checked' : '' }} />
                                  <span class="checkmark outline-danger-dark"></span>
                                  <span class="text-danger-dark f-w-600 me-1">Pnemonia</span>
                              </label>
                          </li>
                      </ul>
                  </div>
                  <div class="col-md-4">
                      <ul class="list-group d-flex flex-column ">
                          <li class="checkbox-wrapper ">
                              <label class="check-box">
                                  <input type="checkbox" name="tbc" id="tbc"
                                      {{ $data->is_tbc != 0 || null ? 'checked' : '' }} />
                                  <span class="checkmark outline-warning-dark"></span>
                                  <span class="text-warning-dark f-w-600 me-1">tbc</span>
                              </label>
                          </li>
                      </ul>
                  </div>
              </div>
          </div>


          <div class="col-md-12">
              <br>
              <div class="float-end">

                  <button class="btn btn-primary btn-sm float-end rounded-pill" data-bs-dismiss="modal"><i
                          class="fa fa-save"></i>
                      {{ $soap ? 'Update' : 'Save' }}
                  </button>

              </div>
          </div>
      </div>
  </form>
  <script>
      $(function() {
          $('#soap-form').on('submit', function(e) {
              e.preventDefault();
              let form = $(this);
              $.ajax({
                  url: "<?= url('pemeriksaan/pasien/' . $id) ?>/soape/save",
                  type: 'POST',
                  data: form.serialize(),
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
              });
          });
      });
  </script>

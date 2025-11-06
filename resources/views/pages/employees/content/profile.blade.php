<div class="row">
    <div class="col-md-9">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">Full Name: <span class="text-danger">*</span></label>
                    <input type="text" class="form-control required" id="fullname" value="{{ $data->fullname }}"
                        name="fullname" placeholder="First Name" />
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">NIK: <span class="text-danger">*</span></label>
                    <input type="text" class="form-control required" id="nik" name="nik"
                        value="{{ $data->nik }}" placeholder="Contact No." />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">NPWP: </label>
                    <input type="text" class="form-control" id="npwp" name="npwp" value="{{ $data->npwp }}"
                        placeholder="NPWP" />
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">Born place: </label>
                    <input type="text" class="form-control" id="bornloc" name="bornloc" />
                    {{-- <select class="form-control select2modal " id="bornloc" name="bornloc"
                style="width:100% !important">
                <option hidden value="" selected>Choose one</option>

            </select> --}}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">Born Date: </label>
                    <input type="date" class="form-control" id="born_date" name="born_date"
                        max="{{ date('Y-m-d', strtotime($data->borndate)) }}" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">Gender: </label>
                    <select class="form-control select2modal " id="gender" name="gender"
                        style="width:100% !important">
                        <option hidden value="" selected>-</option>
                        <option value="LAKI-LAKI" {{ $data->fk_ptkp == 'LAKI-LAKI' ? 'selected' : '' }}>
                            LAKI-LAKI</option>
                        <option value="PEREMPUAN" {{ $data->fk_ptkp == 'PEREMPUAN' ? 'selected' : '' }}>
                            PEREMPUAN</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">Personal Email:</label>
                    <input type="text" class="form-control" id="personal_email" name="personal_email"
                        placeholder="Personal Email" {{ $data->personal_email }} />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">Phone Number: </label>
                    <input type="text" class="form-control" id="phone_number" {{ $data->phone_number }}
                        name="phone_number" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">Marial Status: </label>
                    <select class="form-control select2modal " id="fk_marialstatus" name="fk_marialstatus"
                        style="width:100% !important">
                        <option hidden value="" selected>Choose one</option>
                        @foreach ($ms as $i)
                            <option value="{{ $i->id }}"
                                {{ $data->fk_marialstatus == $i->id ? 'selected' : '' }}>
                                {{ $i->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">Religion: </label>
                    <select class="form-control select2modal " id="fk_religion" name="fk_religion"
                        style="width:100% !important">
                        <option hidden value="" selected>Choose one</option>
                        @foreach ($rl as $i)
                            <option value="{{ $i->id }}" {{ $data->fk_religion == $i->id ? 'selected' : '' }}>
                                {{ $i->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-8">
                <div class="form-group">
                    <label class="form-label">Alamat: </label>
                    <textarea rows="3" type="text" class="form-control" id="alamat" name="alamat">{{ $data->alamat }}</textarea>
                </div>
            </div>

        </div>
    </div>
    <div class="col-md-3">
        <div class="profile-img-edit position-relative align-items-center">
            <img class="profile-pic rounded avatar-100"
                src="{{ $data->foto ? asset($data->foto) : asset('assets/images/avatars/01.png') }}"
                alt="profile-pic">
            <div class="upload-icone bg-primary">
                <svg class="upload-button" width="14" height="14" viewBox="0 0 24 24">
                    <path fill="#ffffff"
                        d="M14.06,9L15,9.94L5.92,19H5V18.08L14.06,9M17.66,3C17.41,3 17.15,3.1 16.96,3.29L15.13,5.12L18.88,8.87L20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18.17,3.09 17.92,3 17.66,3M14.06,6.19L3,17.25V21H6.75L17.81,9.94L14.06,6.19Z" />
                </svg>
                <input class="file-upload" type="file" accept="image/*">
            </div>
        </div>
        <div class="img-extension mt-3">
            <div class="d-inline-block align-items-center">
                <span>Only</span>
                <a href="javascript:void();">.jpg</a>
                <a href="javascript:void();">.png</a>
                <a href="javascript:void();">.jpeg</a>
                <span>allowed</span>
            </div>
        </div>

    </div>
    <div>
        <button type="submit" name="next" class="btn btn-sm btn-outline-primary  float-end rounded"> Update
            <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path opacity="0.4"
                    d="M6.447 22C3.996 22 2 19.9698 2 17.4755V12.5144C2 10.0252 3.99 8 6.437 8L17.553 8C20.005 8 22 10.0302 22 12.5256V17.4846C22 19.9748 20.01 22 17.563 22H16.623H6.447Z"
                    fill="currentColor"></path>
                <path
                    d="M11.455 2.22103L8.54604 5.06682C8.24604 5.36094 8.24604 5.83427 8.54804 6.12742C8.85004 6.41959 9.33704 6.41862 9.63704 6.12547L11.23 4.56623V6.06119V14.4515C11.23 14.8654 11.575 15.2014 12 15.2014C12.426 15.2014 12.77 14.8654 12.77 14.4515V4.56623L14.363 6.12547C14.663 6.41862 15.15 6.41959 15.452 6.12742C15.603 5.98036 15.679 5.78849 15.679 5.59566C15.679 5.40477 15.603 5.21291 15.454 5.06682L12.546 2.22103C12.401 2.07981 12.205 1.99995 12 1.99995C11.796 1.99995 11.6 2.07981 11.455 2.22103Z"
                    fill="currentColor"></path>
            </svg> </button>
    </div>
</div>
<script src="../assets/js/form-wizard.js"></script>

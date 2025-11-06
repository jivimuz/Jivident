<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label class="form-label">Job Level: <span class="text-danger">*</span></label>
            <select class="form-control select2modal required" id="fk_joblevel" name="fk_joblevel"
                style="width:100% !important">
                <option hidden value="" selected>Choose one</option>
                @foreach ($jl as $i)
                    <option value="{{ $i->id }}" {{ $data->fk_joblevel == $i->id ? 'selected' : '' }}>
                        {{ $i->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label class="form-label">Job Type: <span class="text-danger">*</span></label>
            <select class="form-control select2modal required" id="fk_jobtype" name="fk_jobtype"
                style="width:100% !important">
                <option hidden value="" selected>Choose one</option>
                @foreach ($jt as $i)
                    <option value="{{ $i->id }}" {{ $data->fk_jobtype == $i->id ? 'selected' : '' }}>
                        {{ $i->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label class="form-label">Designation: <span class="text-danger">*</span></label>
            <select class="form-control select2modal required" id="fk_designation" name="fk_designation"
                style="width:100% !important">
                <option hidden value="" selected>Choose one</option>
                @foreach ($ds as $i)
                    <option value="{{ $i->id }}" {{ $data->fk_designation == $i->id ? 'selected' : '' }}>
                        {{ $i->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>


    <div class="col-md-4">
        <div class="form-group">
            <label class="form-label">PTKP: <span class="text-danger">*</span></label>
            <select class="form-control select2modal required" id="fk_ptkp" name="fk_ptkp"
                style="width:100% !important">
                <option hidden value="" selected>Choose one</option>
                @foreach ($ptkp as $i)
                    <option value="{{ $i->id }}" {{ $data->fk_ptkp == $i->id ? 'selected' : '' }}>
                        {{ $i->name }}
                    </option>
                @endforeach
            </select>
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
            <label class="form-label">Join Date: <span class="text-danger">*</span></label>
            <input type="date" class="form-control required" id="joindate" name="joindate"
                value="{{ date('Y-m-d', strtotime($data->joindate)) }}" />
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label class="form-label">Out Date: <small>(*only if the employee
                    resign)</small></label>
            <input type="date" class="form-control" id="outdate"
                value="{{ $data->outdate ? date('Y-m-d', strtotime($data->outdate)) : '' }}" name="outdate" />
        </div>
    </div>

</div>

<button type="submit" name="next" class="btn btn-sm btn-outline-primary  float-end rounded"> Update
    <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path opacity="0.4"
            d="M6.447 22C3.996 22 2 19.9698 2 17.4755V12.5144C2 10.0252 3.99 8 6.437 8L17.553 8C20.005 8 22 10.0302 22 12.5256V17.4846C22 19.9748 20.01 22 17.563 22H16.623H6.447Z"
            fill="currentColor"></path>
        <path
            d="M11.455 2.22103L8.54604 5.06682C8.24604 5.36094 8.24604 5.83427 8.54804 6.12742C8.85004 6.41959 9.33704 6.41862 9.63704 6.12547L11.23 4.56623V6.06119V14.4515C11.23 14.8654 11.575 15.2014 12 15.2014C12.426 15.2014 12.77 14.8654 12.77 14.4515V4.56623L14.363 6.12547C14.663 6.41862 15.15 6.41959 15.452 6.12742C15.603 5.98036 15.679 5.78849 15.679 5.59566C15.679 5.40477 15.603 5.21291 15.454 5.06682L12.546 2.22103C12.401 2.07981 12.205 1.99995 12 1.99995C11.796 1.99995 11.6 2.07981 11.455 2.22103Z"
            fill="currentColor"></path>
    </svg> </button>

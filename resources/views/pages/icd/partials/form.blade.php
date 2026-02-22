@php
    $isEdit = $mode === 'edit';
@endphp

<form id="formICD" action="{{ $action }}" method="POST">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="modal-body p-3">
        <div class="row">
            <div class="col-md-6 mb-2">
                <label class="form-label">Kode</label>
                <input type="text" name="code" class="form-control" value="{{ old('code', $row->code ?? '') }}"
                    placeholder="Contoh: A00 / DIX001">
                <small class="text-danger d-block error-text" data-field="code"></small>
            </div>

            <div class="col-md-6 mb-2">
                <label class="form-label">Nama</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $row->name ?? '') }}"
                    placeholder="Nama diagnosa">
                <small class="text-danger d-block error-text" data-field="name"></small>
            </div>

            <div class="col-md-3 mb-2">
                <label class="form-label">Tarif</label>
                <input type="number" step="0.01" min="0" name="tarif" class="form-control"
                    value="{{ old('tarif', $row->tarif ?? 0) }}">
                <small class="text-danger d-block error-text" data-field="tarif"></small>
            </div>


        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-light" onclick="tutup()">Tutup</button>
            <button type="submit" class="btn btn-primary" id="btnSaveICD">
                {{ $isEdit ? 'Update' : 'Simpan' }}
            </button>
        </div>
</form>

<form id="roleForm">
    <div class="mb-3">
        <label class="form-label">Nama Role</label>
        <input type="text" class="form-control" id="roleName" value="{{ $role->name ?? '' }}"
            placeholder="misal: Dokter, Kasir, Apoteker">
    </div>

    <label class="form-label">Akses Modul</label>
    <div class="accordion" id="roleModuleAccordion">
        <?php $mod = null; $no = 0; ?>
        @foreach ($menuList as $i)
            @if ($mod != $i->group_name)
                @if ($mod)
                </div>
            </div>
            </div>
            @endif
            <?php $mod = $i->group_name; ?>
            <div class="accordion-item">
                <h2 class="accordion-header" id="roleGroupHeading{{ $no }}">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                        data-bs-target="#roleGroupCollapse{{ $no }}" aria-expanded="true"
                        style="background-color: #F0F0F0; color: #003059">
                        {{ $i->group_name }}
                    </button>
                </h2>
                <div id="roleGroupCollapse{{ $no }}" class="accordion-collapse collapse show">
                    <div class="accordion-body row">
                        @endif
                        <div class="col-md-3">
                            <div class="form-check d-block">
                                <input class="form-check-input" type="checkbox" name="modules[]"
                                    value="{{ $i->id }}" id="roleModule-{{ $i->id }}"
                                    {{ in_array($i->id, $checkedIds) ? 'checked' : '' }}>
                                <label class="form-check-label" for="roleModule-{{ $i->id }}">
                                    {{ $i->name }}
                                </label>
                            </div>
                        </div>
                        <?php $no++; ?>
                    @endforeach
                    @if ($mod)
            </div>
        </div>
    </div>
    @endif
    </div>

    <div class="float-end mt-3">
        <a type="button" class="btn btn-success rounded-pill" id="saveRole">Simpan</a>
    </div>
</form>

<script>
    $('#saveRole').off('click').on('click', function(e) {
        e.preventDefault();

        var modules = $('#roleForm input[name="modules[]"]:checked').map(function() {
            return $(this).val();
        }).get();

        $.ajax({
            url: "{{ $role ? url('roles/update/' . $role->id) : url('roles/store') }}",
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                name: $('#roleName').val(),
                modules: modules,
            },
            success: function() {
                Toast.fire({
                    icon: "success",
                    title: "Role disimpan"
                });
                closeModal('ThisModal');
                table.ajax.reload();
            },
            error: function(xhr) {
                Toast.fire({
                    icon: "error",
                    title: JSON.parse(xhr.responseText).error ?? 'Gagal menyimpan role'
                });
            }
        });
    });
</script>

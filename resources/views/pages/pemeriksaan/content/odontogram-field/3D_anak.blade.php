<style>
    .canvas-container {
        height: 560px;
    }

    @media (min-width: 1024px) {
        .canvas-container {
            height: 720px;
        }
    }
</style>

<div class="row g-4">
    <!-- 3D Canvas -->
    <div class="col-lg-7">
        <div class="canvas-container bg-gray rounded-xl p-0 p-lg-4 position-relative shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-3 p-3 p-lg-0">
                <div>
                    <h2 class="h4 fw-bold text-teal-700">Mapping 3D Anak </h2>
                    <div class=" bg-primary text-white  fw-semibold small px-3 py-1 rounded-pill shadow-sm">
                        - Klik untuk menandai <br>
                        - Tahan klik kanan atau sentuh 2 jari untuk memindahkan
                    </div>
                </div>
            </div>
            <div class="position-relative w-100 h-100">
                <canvas id="glcanvas" class="w-100 h-100" style="cursor:pointer;"></canvas>

                <!-- Loading overlay -->
                <div id="loading-overlay"
                    class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white bg-opacity-75 z-1 d-none">
                    <div class="text-center">
                        <div class="spinner-border text-success mb-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="fw-semibold text-teal-700 mb-0">Loading 3D Model...</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Diagnostic Panel -->
    <div class="col-lg-5">
        <div class="bg-white rounded-xl shadow p-4 h-100">
            <h2 class="h4 fw-bold text-dark border-bottom pb-2 mb-3">Odontology Diagnostic Panel</h2>


            <h3 class="h6 fw-semibold text-secondary mb-2">
                Diagnostic Points
                <span id="points-count" class="text-muted small">(0)</span>
            </h3>

            <div id="points-list" class="border rounded bg-light p-3 overflow-auto" style="height: 26rem;"></div>

            <div class="mt-4">
                <button id="save-data" class="btn m-1 btn-success w-100 fw-semibold">
                    Save Odontogram
                </button>
                {{-- <button id="download-pdf" class="btn m-1 btn-primary w-100 fw-semibold" disabled>
                    Download Diagnostic PDF
                </button> --}}
                <button id="reset-mapping" class="btn m-1 btn-danger w-100 fw-semibold">
                    Reset All Mapping Points
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast -->
<div id="toast" class="position-fixed top-0 end-0 m-3 p-3 rounded text-white shadow-lg d-none z-3"></div>

<script src="{{ url('') }}/assets/js/3dmodel/child.js"></script>
<script>
    $(document).ready(function() {
        getDataOdonto()
    })
    $('#save-data').on('click', function() {
        $.ajax({
            url: "<?= url('pemeriksaan/pasien/' . $id) ?>/odontogram/save",
            type: 'POST',
            data: {
                mapping: JSON.stringify(window._jivident.getMapped()),
                odontotype: "{{ $odontotype }}",
            },
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
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
    })


    function getDataOdonto() {
        $.ajax({
            url: "<?= url('pemeriksaan/pasien/' . $id) ?>/odontogram/loadOdonto",
            type: 'POST',
            data: {
                odontotype: "{{ $odontotype }}",
            },
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.data && response.data.mappeddata) {
                    setTimeout(() => {

                        window._jivident.loadMarker(response.data.mappeddata)
                    }, 1000);
                }

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
    }
</script>

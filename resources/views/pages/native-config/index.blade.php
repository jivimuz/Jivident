@extends('layout/fullscreen')

@section('style')
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .native-config-container {
            width: 100vw;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: #f8fafc;
            position: fixed;
            top: 0;
            left: 0;
            overflow-y: auto;
            z-index: 9999;
        }

        .config-form {
            width: 100%;
            max-width: 850px;
            margin: auto;
            background: white;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #334155;
            font-size: 0.875rem;
        }

        input,
        select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 0.95rem;
            color: #1e293b;
            background-color: #fff;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        .btn {
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.95rem;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            border: none;
        }

        .btn:disabled {
            background: #cbd5e1 !important;
            cursor: not-allowed;
            transform: none !important;
        }

        .btn-test {
            background: #475569;
        }

        .spinner-overlay {
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, 0.85);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            backdrop-filter: blur(4px);
        }

        .spinner-border-lg {
            width: 3rem;
            height: 3rem;
            border: 4px solid #e2e8f0;
            border-top-color: #4f46e5;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .btn-save {
            background: #4f46e5;
        }

        .btn-save:hover:not(:disabled) {
            background: #4338ca;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.85rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-connected {
            background: #d1fae5;
            color: #065f46;
        }

        .status-disconnected {
            background: #fee2e2;
            color: #991b1b;
        }

        .toast {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            background: #1e293b;
            color: white;
            padding: 1rem 1.5Rem;
            border-radius: 8px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 50;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .toast.success {
            background: #065f46;
        }

        .toast.error {
            background: #991b1b;
        }

        .toast.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .toast:not(.hidden) {
            opacity: 1;
        }
    </style>
@endsection

@section('script')
    <script>
        let isTesting = false;
        let isSaving = false;
        let dbStatus = <?php echo json_encode($dbStatus ?? (object) []); ?>;

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            updateStatusBadge(dbStatus);
            loadSavedConfig();
            toggleDriverFields();
        });

        function loadSavedConfig() {
            // Form config loader
            const form = document.getElementById('configForm');
            const savedConfig = <?php echo json_encode($config ?? []); ?>;
            if (savedConfig) {
                Object.keys(savedConfig).forEach(key => {
                    const input = form.querySelector('[name="' + key + '"]');
                    if (input && savedConfig[key] !== '') {
                        input.value = savedConfig[key];
                    }
                });
            }
        }

        function updateStatusBadge(status) {
            const badge = document.getElementById('statusBadge');
            const text = document.getElementById('wizardStatusText');
            const btnSave = document.getElementById('btnSave');

            if (status.connected) {
                badge.className = 'status-badge status-connected';
                badge.textContent = 'Terhubung';
                text.textContent = status.message || 'Koneksi database berhasil!';
                btnSave.disabled = false;
            } else {
                badge.className = 'status-badge status-disconnected';
                badge.textContent = 'Belum Terhubung';
                text.textContent = status.message || 'Silakan test koneksi terlebih dahulu.';
                btnSave.disabled = true;
            }
        }

        function toggleDriverFields() {
            const driver = document.getElementById('db_connection').value;
            const groupHost = document.getElementById('groupHost');
            const groupMysqlFields = document.getElementById('groupMysqlFields');
            const groupAuthFields = document.getElementById('groupAuthFields');

            if (driver === 'sqlite') {
                groupHost.style.display = 'none';
                groupMysqlFields.style.display = 'none';
                groupAuthFields.style.display = 'none';
            } else {
                groupHost.style.display = 'block';
                groupMysqlFields.style.display = 'flex';
                groupAuthFields.style.display = 'flex';
            }
        }

        function showToast(message, type) {
            type = type || 'success';
            const toast = document.getElementById('toast');
            toast.className = 'toast ' + type;
            if (type === 'success') {
                toast.innerHTML =
                    '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg> <span>' +
                    message + '</span>';
            } else {
                toast.innerHTML =
                    '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg> <span>' +
                    message + '</span>';
            }
            toast.classList.remove('hidden');
            setTimeout(function() {
                toast.classList.add('hidden');
            }, 4000);
        }

        function showAlert(message, type) {
            type = type || 'success';
            const container = document.getElementById('alertContainer');
            container.innerHTML = '<div class="alert ' + type + '">' + message + '</div>';
            setTimeout(function() {
                container.innerHTML = '';
            }, 5000);
        }

        function setButtonLoading(btnId, loading, originalText) {
            const btn = document.getElementById(btnId);
            if (loading) {
                btn.innerHTML =
                    '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 14.21 3 7 7 0 0 0 21 12.79z"></path></svg> Processing...';
                btn.disabled = true;
            } else {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }

        async function testConnection() {
            if (isTesting) return;
            isTesting = true;

            const form = document.getElementById('configForm');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);

            setButtonLoading('btnTest', true, 'Testing Koneksi...');

            try {
                const response = await fetch('<?php echo e(url('native-config/test')); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(data),
                });

                const result = await response.json();

                if (result.success) {
                    showToast('Koneksi database berhasil!', 'success');
                    updateStatusBadge({
                        connected: true,
                        needsSetup: result.needsSetup || false,
                        message: result.message
                    });
                } else {
                    showToast('Gagal terhubung: ' + result.message, 'error');
                    updateStatusBadge({
                        connected: false,
                        needsSetup: true,
                        message: result.message
                    });
                }
            } catch (error) {
                showToast('Error: ' + error.message, 'error');
                updateStatusBadge({
                    connected: false,
                    needsSetup: true,
                    message: 'Error koneksi: ' + error.message
                });
            } finally {
                isTesting = false;
                setButtonLoading('btnTest', false, 'Test Koneksi Database');
            }
        }

        async function saveConfig(event) {
            event.preventDefault();
            if (isSaving) return;
            isSaving = true;

            const form = document.getElementById('configForm');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);

            setButtonLoading('btnSave', true, 'Menyimpan...');

            // Show full screen loading overlay
            const overlay = document.createElement('div');
            overlay.className = 'spinner-overlay';
            overlay.id = 'loadingOverlay';
            overlay.innerHTML = `
                <div class="spinner-border-lg mb-4"></div>
                <h2 class="text-xl font-bold text-gray-800">Menyimpan Konfigurasi & Inisialisasi Database...</h2>
                <p class="text-gray-500 mt-1">Mohon tunggu sebentar, migrasi dan seeder sedang dijalankan.</p>
            `;
            document.body.appendChild(overlay);

            try {
                const response = await fetch('<?php echo e(url('native-config/save')); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(data),
                });

                const result = await response.json();

                if (result.success) {
                    showToast('Setup berhasil! Mengalihkan...', 'success');
                    setTimeout(function() {
                        const typeServerInput = document.getElementById('type_local_server');
                        const typeVal = typeServerInput ? parseInt(typeServerInput.value, 10) : 0;
                        if (typeVal === 2) {
                            window.location.href = '<?php echo e(url('')); ?>';
                        } else {
                            window.location.href = '<?php echo e(url('license')); ?>';
                        }
                    }, 1000);
                } else {
                    const existingOverlay = document.getElementById('loadingOverlay');
                    if (existingOverlay) existingOverlay.remove();
                    showToast('Gagal: ' + result.message, 'error');
                    isSaving = false;
                    setButtonLoading('btnSave', false, 'Simpan & Inisialisasi DB');
                }
            } catch (error) {
                const existingOverlay = document.getElementById('loadingOverlay');
                if (existingOverlay) existingOverlay.remove();
                showToast('Error: ' + error.message, 'error');
                isSaving = false;
                setButtonLoading('btnSave', false, 'Simpan & Inisialisasi DB');
            }
        }
    </script>
@endsection

@section('content')
    <div class="native-config-container">
        <div class="config-form ">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Jivident Database Setup Wizard</h1>
                <p class="text-gray-500 mt-2">Konfigurasikan koneksi database, server lisensi, dan mode offline untuk
                    aplikasi desktop Anda.</p>
            </div>

            <div class="mb-6 p-4 rounded-lg bg-blue-50 border border-blue-200 flex items-center justify-between">
                <div>
                    <span class="font-semibold text-blue-900">Status Setup:</span>
                    <span id="wizardStatusText" class="text-blue-700 ml-2">Menunggu konfigurasi & uji koneksi...</span>
                </div>
                <div id="statusBadge" class="status-badge pending">Belum Terhubung</div>
            </div>

            <form id="configForm" onsubmit="saveConfig(event)">
                <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label for="db_connection" class="block text-sm font-medium text-gray-700">Driver Database</label>
                        <select id="db_connection" name="db_connection" class="input" onchange="toggleDriverFields()">
                            <option value="mysql" selected>MySQL / MariaDB</option>
                            <option value="sqlite">SQLite (File Lokal)</option>
                        </select>
                    </div>

                    <div class="form-group" id="groupHost">
                        <label for="db_host" class="block text-sm font-medium text-gray-700">Host Database</label>
                        <input type="text" id="db_host" name="db_host" class="input" value="127.0.0.1"
                            placeholder="127.0.0.1 atau domain">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="groupMysqlFields">
                    <div class="form-group">
                        <label for="db_port" class="block text-sm font-medium text-gray-700">Port</label>
                        <input type="number" id="db_port" name="db_port" class="input" min="1" max="65535"
                            value="3306" placeholder="3306">
                    </div>

                    <div class="form-group">
                        <label for="db_database" class="block text-sm font-medium text-gray-700">Nama Database</label>
                        <input type="text" id="db_database" name="db_database" class="input" value="jivident"
                            placeholder="jivident">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="groupAuthFields">
                    <div class="form-group">
                        <label for="db_username" class="block text-sm font-medium text-gray-700">Username</label>
                        <input type="text" id="db_username" name="db_username" class="input" value="root"
                            placeholder="root">
                    </div>

                    <div class="form-group">
                        <label for="db_password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" id="db_password" name="db_password" class="input" value=""
                            placeholder="Kosongkan jika tanpa password">
                    </div>
                </div>

                <div class="form-group">
                    <label for="auth_server" class="block text-sm font-medium text-gray-700">Auth URL</label>
                    <input type="text" id="auth_server" name="auth_server" class="input" value="<?php echo e($config['auth_server'] ?? 'https://management.antariks.id/api/license/validate'); ?>"
                        required placeholder="https://management.antariks.id/api/license/validate">
                    <p class="text-xs text-gray-500 mt-1">Dapat diubah secara offline sebelum koneksi divalidasi.</p>
                </div>

                <div class="form-group">
                    <label for="type_local_server" class="block text-sm font-medium text-gray-700">Server Mode</label>
                    <input type="number" id="type_local_server" name="type_local_server" class="input" min="0"
                        max="2" value="<?php echo e($config['type_local_server'] ?? 0); ?>" required placeholder="0">
                </div>

                <div class="flex gap-4 mt-6">
                    <button type="button" id="btnTest" class="btn bg-gray-600 hover:bg-gray-700 flex-1"
                        onclick="testConnection()">1. Uji Koneksi</button>
                    <button type="submit" id="btnSave" class="btn bg-indigo-600 hover:bg-indigo-700 flex-1" disabled
                        onclick="saveConfig(event)">2. Simpan &amp; Inisialisasi DB</button>
                </div>

                <div id="alertContainer" class="mt-4"></div>
            </form>
        </div>
    </div>

    <div id="toast" class="toast hidden"></div>
@endsection

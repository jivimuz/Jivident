<!DOCTYPE html>
<html lang="id">
<?php
use App\Models\Setting;

$logo = Setting::where('parameter', 'company_logo')->first()->value ?: url('/assets/logo.png');
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JiviDent - Monitor Ruangan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <link href="{{ url('') }}/assets/favicon.ico" rel="icon" type="image/x-icon">
    <link href="{{ url('') }}/assets/favicon.ico" rel="shortcut icon" type="image/x-icon">
    <style>
        html {
            font-size: clamp(10px, 1.1vw, 17px);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            overflow: hidden;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: #333;
            background: linear-gradient(135deg, #ffffff, #e6f7ff, #ffffff, #f0f8ff);
            background-size: 400% 400%;
            animation: gradientFlow 20s ease infinite;
        }

        @keyframes gradientFlow {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .wrap {
            max-width: 1700px;
            height: 100vh;
            margin: 0 auto;
            padding: 2rem 2.5rem;
            display: flex;
            flex-direction: column;
        }

        header {
            flex: 0 0 auto;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            border-bottom: 2px solid #eee;
            padding-bottom: 1.5rem;
            margin-bottom: 1.5rem;
        }

        header .logo {
            height: 4.5rem;
            border-radius: 8px;
        }

        header h1 {
            font-size: 2rem;
            font-weight: 600;
            color: #29A0B6;
        }

        header .subtitle {
            font-size: 1.1rem;
            color: #778;
            font-weight: 400;
        }

        .datetime {
            margin-left: auto;
            text-align: right;
            color: #29A0B6;
        }

        #clock {
            font-size: 2rem;
            font-weight: 700;
        }

        #date {
            font-size: 1rem;
            font-weight: 400;
            color: #556;
        }

        .legend {
            flex: 0 0 auto;
            display: flex;
            gap: 1.5rem;
            font-size: 1rem;
            color: #556;
            margin-bottom: 1.25rem;
        }

        .legend span {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
        }

        .legend .dot {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            display: inline-block;
        }

        .filters {
            flex: 0 0 auto;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: .75rem;
            margin-bottom: 1.25rem;
        }

        .lantai-pill {
            border: none;
            padding: .6rem 1.4rem;
            border-radius: 999px;
            font-size: 1rem;
            font-weight: 600;
            background: #fff;
            color: #557;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .06);
            cursor: pointer;
        }

        .lantai-pill.active {
            background: #29A0B6;
            color: #fff;
        }

        #roomSearch {
            flex: 1 1 260px;
            max-width: 340px;
            padding: .6rem 1.1rem;
            border-radius: 999px;
            border: 1px solid #dde3ea;
            font-size: 1rem;
            font-family: inherit;
        }

        /* flex:1 + min-height:0 so this scrolls *within itself* when there
           are more rooms than fit — the outer page (html/body/.wrap above)
           never scrolls, it always fills exactly the monitor's height. */
        .room-grid {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.25rem;
            align-content: flex-start;
            padding-bottom: .5rem;
        }

        .room-card {
            background: #fff;
            border-radius: 16px;
            padding: 1.25rem 1.4rem;
            box-shadow: 0 8px 24px rgba(0, 86, 179, .08);
            cursor: pointer;
            transition: transform .15s ease, box-shadow .15s ease;
        }

        .room-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(0, 86, 179, .16);
        }

        .room-card .room-title {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            margin-bottom: .9rem;
        }

        .room-card .room-title strong {
            font-size: 1.25rem;
            color: #0A6E80;
        }

        .room-card .room-badges {
            font-size: .85rem;
            color: #889;
        }

        .bed-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: .6rem;
        }

        .bed {
            border-radius: 10px;
            padding: .6rem .7rem;
            min-height: 68px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .bed.kosong {
            background: #e8f8ee;
            border: 1px solid #bfe8cf;
            color: #1f9d55;
        }

        .bed.terisi {
            background: #fdeceb;
            border: 1px solid #f6c9c6;
            color: #a3302a;
        }

        .bed .bed-no {
            font-size: .8rem;
            font-weight: 600;
            opacity: .75;
        }

        .bed .bed-status {
            font-size: .95rem;
            font-weight: 700;
        }

        .bed .bed-norm {
            font-size: .78rem;
            opacity: .8;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 1rem;
            color: #99a;
            font-size: 1.1rem;
        }

        /* ===== Fokus 1 ruangan, penuh 1 layar ===== */
        #roomFocusOverlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 50;
            background: linear-gradient(135deg, #ffffff, #e6f7ff, #ffffff, #f0f8ff);
            padding: 2rem 2.5rem;
            flex-direction: column;
        }

        #roomFocusOverlay.show {
            display: flex;
        }

        .focus-header {
            flex: 0 0 auto;
            display: flex;
            align-items: baseline;
            gap: 1rem;
            border-bottom: 2px solid #eee;
            padding-bottom: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .focus-header h2 {
            font-size: 2.6rem;
            color: #0A6E80;
        }

        .focus-header .focus-badges {
            font-size: 1.3rem;
            color: #778;
        }

        .focus-close {
            margin-left: auto;
            border: none;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .08);
            border-radius: 999px;
            padding: .7rem 1.6rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: #557;
            cursor: pointer;
        }

        .focus-bed-grid {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 1.5rem;
            align-content: flex-start;
        }

        .focus-bed-grid .bed {
            border-radius: 16px;
            padding: 1.5rem;
            min-height: 160px;
            justify-content: center;
            box-shadow: 0 6px 18px rgba(0, 0, 0, .05);
        }

        .focus-bed-grid .bed-no {
            font-size: 1.1rem;
        }

        .focus-bed-grid .bed-status {
            font-size: 1.8rem;
            margin-top: .4rem;
        }

        .focus-bed-grid .bed-norm {
            font-size: 1.15rem;
            margin-top: .3rem;
        }
    </style>
</head>

<body>
    <div class="wrap">
        <header>
            <img src="{{ $logo }}" alt="JiviDent" class="logo">
            <div>
                <h1>Monitor Ruangan</h1>
                <div class="subtitle">Status ranjang rawat inap — live</div>
            </div>
            <div class="datetime">
                <div id="clock">00:00:00</div>
                <div id="date">Memuat...</div>
            </div>
        </header>

        <div class="legend">
            <span><span class="dot" style="background:#1f9d55"></span> Kosong</span>
            <span><span class="dot" style="background:#d9453d"></span> Terisi</span>
        </div>

        <div class="filters">
            <div id="lantaiFilter"></div>
            <input type="text" id="roomSearch" placeholder="Cari nama ruangan...">
        </div>

        <div id="roomGrid" class="room-grid">
            <div class="empty-state">Memuat data ruangan...</div>
        </div>
    </div>

    <!-- Fokus 1 ruangan penuh 1 layar — dibuka dengan klik kartu ruangan -->
    <div id="roomFocusOverlay">
        <div class="focus-header">
            <h2 id="focusRoomName"></h2>
            <span class="focus-badges" id="focusRoomBadges"></span>
            <button type="button" class="focus-close" onclick="closeRoomFocus()">✕ Tutup</button>
        </div>
        <div id="focusBedGrid" class="focus-bed-grid"></div>
    </div>

    <script>
        const klienfk = {{ (int) $klienfk }};
        let ruanganCache = [];
        let lantaiAktif = null;
        let focusedRoomId = null;

        function updateClock() {
            const now = new Date();
            document.getElementById('clock').textContent = now.toTimeString().split(' ')[0];
            document.getElementById('date').textContent = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }
        setInterval(updateClock, 1000);
        updateClock();

        function renderLantaiFilter() {
            const lantaiList = [...new Set(ruanganCache.map(r => r.lantai).filter(Boolean))].sort();
            const el = document.getElementById('lantaiFilter');
            if (lantaiList.length < 2) {
                el.innerHTML = '';
                return;
            }
            let html =
                `<button type="button" class="lantai-pill ${lantaiAktif === null ? 'active' : ''}" onclick="pilihLantai(null)">Semua Lantai</button>`;
            lantaiList.forEach(l => {
                html +=
                    `<button type="button" class="lantai-pill ${lantaiAktif === l ? 'active' : ''}" onclick="pilihLantai('${l}')">Lantai ${l}</button>`;
            });
            el.innerHTML = html;
        }

        function pilihLantai(lantai) {
            lantaiAktif = lantai;
            renderLantaiFilter();
            renderRoomGrid();
        }

        function bedHtml(bed) {
            const kosong = bed.status === 'kosong';
            if (kosong) {
                return `<div class="bed kosong">
                    <span class="bed-no">Ranjang ${bed.nomor_ranjang ?? ''}</span>
                    <span class="bed-status">Kosong</span>
                </div>`;
            }
            return `<div class="bed terisi">
                <span class="bed-no">Ranjang ${bed.nomor_ranjang ?? ''}</span>
                <span class="bed-status">${bed.pasien_nama ?? 'Terisi'}</span>
                <span class="bed-norm">${bed.pasien_norm ? 'No. RM ' + bed.pasien_norm : ''}</span>
            </div>`;
        }

        function renderRoomGrid() {
            const q = (document.getElementById('roomSearch').value || '').toLowerCase().trim();

            const roomsToShow = ruanganCache.filter(r => {
                const matchLantai = lantaiAktif === null || r.lantai === lantaiAktif;
                const matchSearch = !q || r.nama.toLowerCase().includes(q);
                return matchLantai && matchSearch;
            });

            const grid = document.getElementById('roomGrid');

            if (!ruanganCache.length) {
                grid.innerHTML = '<div class="empty-state">Belum ada ruangan rawat inap yang terdaftar.</div>';
                return;
            }
            if (!roomsToShow.length) {
                grid.innerHTML = '<div class="empty-state">Tidak ada ruangan yang cocok.</div>';
                return;
            }

            grid.innerHTML = roomsToShow.map(r => `
                <div class="room-card" onclick="openRoomFocus(${r.id})">
                    <div class="room-title">
                        <strong>${r.nama}</strong>
                        <span class="room-badges">${[r.lantai ? 'Lantai ' + r.lantai : null, r.kelas].filter(Boolean).join(' · ')}</span>
                    </div>
                    <div class="bed-grid">${r.beds.map(bedHtml).join('')}</div>
                </div>
            `).join('');
        }

        // ===== Fokus 1 ruangan penuh 1 layar =====
        function renderRoomFocus(roomId) {
            const room = ruanganCache.find(r => r.id === roomId);
            if (!room) {
                closeRoomFocus();
                return;
            }
            document.getElementById('focusRoomName').textContent = room.nama;
            document.getElementById('focusRoomBadges').textContent = [room.lantai ? 'Lantai ' + room.lantai :
                null, room.kelas
            ].filter(Boolean).join(' · ');
            document.getElementById('focusBedGrid').innerHTML = room.beds.map(bedHtml).join('');
        }

        function openRoomFocus(roomId) {
            focusedRoomId = roomId;
            renderRoomFocus(roomId);
            document.getElementById('roomFocusOverlay').classList.add('show');
        }

        function closeRoomFocus() {
            focusedRoomId = null;
            document.getElementById('roomFocusOverlay').classList.remove('show');
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeRoomFocus();
        });

        function loadRoomData() {
            fetch(`{{ url('monitor/roomData') }}?klienfk=${klienfk}`)
                .then(res => res.json())
                .then(res => {
                    if (res.status !== 'ok') return;
                    ruanganCache = res.data;
                    renderLantaiFilter();
                    renderRoomGrid();
                    // Layar fokus ikut diperbarui kalau sedang terbuka, biar
                    // tetap live sama seperti grid utama.
                    if (focusedRoomId !== null) renderRoomFocus(focusedRoomId);
                })
                .catch(() => {});
        }

        document.getElementById('roomSearch').addEventListener('input', renderRoomGrid);

        loadRoomData();
        setInterval(loadRoomData, 15000);
    </script>
</body>

</html>

<!DOCTYPE html>
<html lang="id">
<?php
use App\Models\Setting;

$logo = Setting::where('parameter', 'company_logo')->first()->value ?: url('/assets/logo.png');
$app_name = Setting::where('parameter', 'app_name')->first()?->value ?: 'JiviDent';
$company_icon = Setting::where('parameter', 'company_icon')->first()?->value ? url(Setting::where('parameter', 'company_icon')->first()->value) : url('/assets/favicon.ico');
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $app_name }} - Monitor Operasi</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">

    <link href="{{ $company_icon }}" rel="icon" type="image/x-icon">
    <link href="{{ $company_icon }}" rel="shortcut icon" type="image/x-icon">
    <link href="{{ url('') }}/assets/vendor/fullcalendar/fullcalendar.bundle.css" rel="stylesheet">
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
            color: #26323a;
            background: linear-gradient(135deg, #eef6ff, #ffffff 45%, #eafbf3);
        }

        .countdown,
        .fc-event-time {
            font-family: 'Space Grotesk', monospace;
        }

        .wrap {
            max-width: 1800px;
            height: 100vh;
            margin: 0 auto;
            padding: 1.8rem 2.5rem;
            display: flex;
            flex-direction: column;
        }

        header {
            flex: 0 0 auto;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding-bottom: 1.2rem;
            margin-bottom: 1.2rem;
            border-bottom: 2px solid rgba(10, 110, 128, .12);
        }

        header .logo {
            height: 4rem;
            border-radius: 10px;
        }

        header h1 {
            font-size: 1.9rem;
            font-weight: 700;
            color: #0A6E80;
            letter-spacing: -.01em;
        }

        header .subtitle {
            font-size: 1rem;
            color: #6b7c87;
        }

        .datetime {
            margin-left: auto;
            text-align: right;
            color: #0A6E80;
        }

        #clock {
            font-family: 'Space Grotesk', monospace;
            font-size: 1.9rem;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
        }

        #date {
            font-size: .95rem;
            color: #6b7c87;
        }

        .toolbar {
            flex: 0 0 auto;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: .9rem;
            margin-bottom: 1.1rem;
        }

        .view-switch {
            display: flex;
            background: #fff;
            border-radius: 999px;
            padding: 4px;
            box-shadow: 0 2px 10px rgba(10, 70, 90, .08);
        }

        .view-switch button {
            border: none;
            background: transparent;
            padding: .55rem 1.3rem;
            border-radius: 999px;
            font-weight: 600;
            font-size: .95rem;
            color: #6b7c87;
            cursor: pointer;
        }

        .view-switch button.active {
            background: #0A6E80;
            color: #fff;
        }

        .room-pill {
            border: none;
            padding: .55rem 1.2rem;
            border-radius: 999px;
            font-size: .9rem;
            font-weight: 600;
            background: #fff;
            color: #557;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .06);
            cursor: pointer;
        }

        .room-pill.active {
            background: #0A6E80;
            color: #fff;
        }

        .legend {
            margin-left: auto;
            display: flex;
            gap: 1.1rem;
            font-size: .85rem;
            color: #6b7c87;
        }

        .legend span {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
        }

        .legend .dot {
            width: 11px;
            height: 11px;
            border-radius: 999px;
            display: inline-block;
        }

        .view-body {
            flex: 1 1 auto;
            min-height: 0;
        }

        /* ===== Room status grid ===== */
        #roomGrid {
            height: 100%;
            overflow-y: auto;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
            gap: 1.1rem;
            align-content: flex-start;
        }

        .room-card {
            background: #fff;
            border-radius: 18px;
            padding: 1.3rem 1.5rem;
            box-shadow: 0 10px 28px rgba(10, 70, 90, .08);
            cursor: pointer;
            border-top: 5px solid #cfd8e3;
            transition: transform .15s ease;
        }

        .room-card:hover {
            transform: translateY(-3px);
        }

        .room-card.state-berlangsung {
            border-top-color: #fd7e14;
            animation: cardPulse 2.4s ease-in-out infinite;
        }

        .room-card.state-next {
            border-top-color: #0d6efd;
        }

        .room-card.state-idle {
            border-top-color: #cfd8e3;
        }

        @keyframes cardPulse {
            0%, 100% {
                box-shadow: 0 10px 28px rgba(253, 126, 20, .12);
            }

            50% {
                box-shadow: 0 10px 34px rgba(253, 126, 20, .3);
            }
        }

        .room-card .room-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: #0A6E80;
        }

        .room-badge {
            display: inline-block;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .02em;
            padding: .3rem .7rem;
            border-radius: 999px;
            margin-bottom: .6rem;
        }

        .room-badge.state-berlangsung {
            background: #fff1e6;
            color: #c05a00;
        }

        .room-badge.state-next {
            background: #e7f0ff;
            color: #0d4ca3;
        }

        .room-badge.state-idle {
            background: #f1f3f5;
            color: #8a98a3;
        }

        .room-case {
            margin-top: .6rem;
            font-size: .98rem;
        }

        .room-case .pasien {
            font-weight: 700;
            color: #26323a;
        }

        .room-case .meta {
            color: #6b7c87;
            font-size: .85rem;
            margin-top: .15rem;
        }

        .countdown {
            margin-top: .7rem;
            font-size: 1.6rem;
            font-weight: 700;
            color: #0A6E80;
            font-variant-numeric: tabular-nums;
        }

        .countdown-label {
            font-size: .78rem;
            color: #8a98a3;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        .countdown.overdue {
            color: #d9453d;
        }

        .room-card.state-idle .room-case {
            color: #8a98a3;
        }

        .pager {
            flex: 0 0 auto;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            padding-top: .8rem;
            color: #6b7c87;
            font-size: .95rem;
            font-weight: 600;
        }

        .pager .dots {
            display: flex;
            gap: .4rem;
        }

        .pager .dot-page {
            width: 9px;
            height: 9px;
            border-radius: 999px;
            background: #cfd8e3;
        }

        .pager .dot-page.active {
            background: #0A6E80;
        }

        /* ===== Calendar view ===== */
        #calendarView {
            height: 100%;
            display: none;
        }

        #calendarView.show {
            display: block;
        }

        #roomGrid.hide {
            display: none;
        }

        #opCalendar {
            height: 100%;
            background: #fff;
            border-radius: 18px;
            padding: 1rem 1.3rem;
            box-shadow: 0 10px 28px rgba(10, 70, 90, .08);
        }

        .fc-toolbar-title {
            color: #0A6E80;
            font-weight: 700;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 1rem;
            color: #99a;
            font-size: 1.1rem;
        }

        /* ===== Fullscreen focus ===== */
        #focusOverlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 50;
            background: linear-gradient(135deg, #eef6ff, #ffffff 45%, #eafbf3);
            padding: 2.5rem 3rem;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        #focusOverlay.show {
            display: flex;
        }

        .focus-close {
            position: absolute;
            top: 2rem;
            right: 2.5rem;
            border: none;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .08);
            border-radius: 999px;
            padding: .8rem 1.8rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: #557;
            cursor: pointer;
        }

        #focusRoomName {
            font-size: 3rem;
            font-weight: 700;
            color: #0A6E80;
        }

        #focusBadge {
            margin: 1rem 0 2rem;
        }

        #focusBadge .room-badge {
            font-size: 1rem;
            padding: .5rem 1.4rem;
        }

        #focusPasien {
            font-size: 2.4rem;
            font-weight: 700;
            color: #26323a;
        }

        #focusMeta {
            font-size: 1.3rem;
            color: #6b7c87;
            margin-top: .5rem;
        }

        #focusCountdownLabel {
            margin-top: 2.5rem;
            font-size: 1.1rem;
            color: #8a98a3;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        #focusCountdown {
            font-size: 6rem;
            font-weight: 700;
            color: #0A6E80;
        }

        #focusCountdown.overdue {
            color: #d9453d;
        }

        #focusEmpty {
            font-size: 1.6rem;
            color: #8a98a3;
            margin-top: 2rem;
        }
    </style>
</head>

<body>
    <div class="wrap">
        <header>
            <img src="{{ $logo }}" alt="JiviDent" class="logo">
            <div>
                <h1>Monitor Operasi</h1>
                <div class="subtitle">Status &amp; jadwal ruang operasi — live</div>
            </div>
            <div class="datetime">
                <div id="clock">00:00:00</div>
                <div id="date">Memuat...</div>
            </div>
        </header>

        <div class="toolbar">
            <div class="view-switch">
                <button type="button" id="btnViewStatus" class="active">Status Ruangan</button>
                <button type="button" id="btnViewCalendar">Kalender</button>
            </div>
            <div id="roomFilter"></div>
            <div class="legend">
                <span><span class="dot" style="background:#fd7e14"></span> Sedang Operasi</span>
                <span><span class="dot" style="background:#0d6efd"></span> Berikutnya</span>
                <span><span class="dot" style="background:#cfd8e3"></span> Kosong</span>
            </div>
        </div>

        <div class="view-body">
            <div id="roomGrid"></div>
            <div id="calendarView">
                <div id="opCalendar"></div>
            </div>
        </div>

        <div id="roomPager" class="pager"></div>
    </div>

    <div id="focusOverlay">
        <button type="button" class="focus-close" onclick="closeFocus()">✕ Tutup</button>
        <h2 id="focusRoomName"></h2>
        <div id="focusBadge"></div>
        <div id="focusContent">
            <div id="focusPasien"></div>
            <div id="focusMeta"></div>
            <div id="focusCountdownLabel"></div>
            <div id="focusCountdown"></div>
        </div>
        <div id="focusEmpty" style="display:none">Tidak ada jadwal operasi untuk ruangan ini hari ini.</div>
    </div>

    <script src="{{ url('') }}/assets/vendor/fullcalendar/global.js"></script>
    <script>
        const klienfk = {{ (int) $klienfk }};
        const ROOMS_PER_PAGE = 6;
        const PAGE_CYCLE_MS = 8000;

        let roomStatusCache = [];
        let roomFilterAktif = null; // null = semua ruangan
        let roomPage = 1;
        let clockOffsetMs = 0; // koreksi ke jam server, biar countdown tidak meleset kalau jam perangkat monitor salah
        let focusedRoomId = null;
        let calendar = null;
        let activeView = 'status'; // 'status' | 'calendar'

        function serverNow() {
            return new Date(Date.now() + clockOffsetMs);
        }

        function updateClock() {
            const now = serverNow();
            document.getElementById('clock').textContent = now.toTimeString().split(' ')[0];
            document.getElementById('date').textContent = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }
        setInterval(updateClock, 1000);

        function pad2(n) {
            return String(n).padStart(2, '0');
        }

        // Format sisa waktu dalam jam:menit:detik — kalau sudah lewat (overrun
        // operasi yang belum ditandai selesai, atau jadwal berikutnya yang
        // harusnya sudah dimulai), ditandai "overdue" alih-alih angka minus.
        function formatCountdown(targetDate, nowDate) {
            let diffMs = targetDate.getTime() - nowDate.getTime();
            const overdue = diffMs < 0;
            diffMs = Math.abs(diffMs);

            const totalSec = Math.floor(diffMs / 1000);
            const h = Math.floor(totalSec / 3600);
            const m = Math.floor((totalSec % 3600) / 60);
            const s = totalSec % 60;
            const text = (h > 0 ? h + ':' : '') + pad2(m) + ':' + pad2(s);

            return { text, overdue };
        }

        function parseJamTanggal(tanggal, jam) {
            return new Date(tanggal + 'T' + jam + ':00');
        }

        // `toISOString()` konversi ke UTC dulu — bisa salah tanggal (kebalik
        // hari) kalau dipakai sebagai "tanggal hari ini" di timezone WIB
        // dekat tengah malam. Ambil komponen tanggal LOKAL langsung.
        function localDateStr(date) {
            return date.getFullYear() + '-' + pad2(date.getMonth() + 1) + '-' + pad2(date.getDate());
        }

        function roomState(room) {
            if (room.current) return 'berlangsung';
            if (room.next) return 'next';
            return 'idle';
        }

        function badgeHtml(state) {
            const map = {
                berlangsung: ['Sedang Operasi', 'state-berlangsung'],
                next: ['Operasi Berikutnya', 'state-next'],
                idle: ['Kosong', 'state-idle'],
            };
            const [label, cls] = map[state];
            return `<span class="room-badge ${cls}">${label}</span>`;
        }

        function renderRoomFilter() {
            const el = document.getElementById('roomFilter');
            if (roomStatusCache.length < 2) {
                el.innerHTML = '';
                return;
            }
            let html = `<button type="button" class="room-pill ${roomFilterAktif === null ? 'active' : ''}" onclick="pilihRuangan(null)">Semua Ruangan</button>`;
            roomStatusCache.forEach(r => {
                html += `<button type="button" class="room-pill ${roomFilterAktif === r.id ? 'active' : ''}" onclick="pilihRuangan(${r.id})">${r.nama}</button>`;
            });
            el.innerHTML = html;
        }

        window.pilihRuangan = function(id) {
            roomFilterAktif = id;
            roomPage = 1;
            renderRoomFilter();
            renderRoomGrid();
            if (calendar) calendar.refetchEvents();
        };

        function renderPager(totalPages) {
            const el = document.getElementById('roomPager');
            if (totalPages <= 1) {
                el.innerHTML = '';
                return;
            }
            let dots = '';
            for (let p = 1; p <= totalPages; p++) {
                dots += `<span class="dot-page ${p === roomPage ? 'active' : ''}"></span>`;
            }
            el.innerHTML = `<span>Halaman ${roomPage} / ${totalPages}</span><span class="dots">${dots}</span>`;
        }

        function roomsToShow() {
            return roomFilterAktif === null ?
                roomStatusCache :
                roomStatusCache.filter(r => r.id === roomFilterAktif);
        }

        function renderRoomGrid() {
            const grid = document.getElementById('roomGrid');

            if (!roomStatusCache.length) {
                grid.innerHTML = '<div class="empty-state">Belum ada ruangan operasi yang terdaftar.</div>';
                document.getElementById('roomPager').innerHTML = '';
                return;
            }

            const tampil = roomsToShow();
            const totalPages = Math.max(1, Math.ceil(tampil.length / ROOMS_PER_PAGE));
            if (roomPage > totalPages) roomPage = 1;
            const halamanIni = tampil.slice((roomPage - 1) * ROOMS_PER_PAGE, roomPage * ROOMS_PER_PAGE);

            renderPager(totalPages);

            grid.innerHTML = halamanIni.map(r => {
                const state = roomState(r);
                const kasus = r.current || r.next;
                const caseHtml = kasus ? `
                    <div class="room-case">
                        <div class="pasien">${kasus.pasien}</div>
                        <div class="meta">${kasus.nama_operasi} · ${kasus.dokter ?? '-'}</div>
                        <div class="meta">${kasus.jam_mulai} - ${kasus.jam_selesai}</div>
                    </div>
                    <div class="countdown-label">${state === 'berlangsung' ? 'Sisa Waktu Estimasi' : 'Dimulai Dalam'}</div>
                    <div class="countdown" data-room-id="${r.id}">--:--</div>
                ` : `<div class="room-case">Tidak ada jadwal operasi hari ini.</div>`;

                return `<div class="room-card state-${state}" onclick="openFocus(${r.id})">
                    ${badgeHtml(state)}
                    <div class="room-name">${r.nama}</div>
                    ${caseHtml}
                </div>`;
            }).join('');

            tickCountdowns();
        }

        function tickCountdowns() {
            const now = serverNow();

            document.querySelectorAll('.countdown[data-room-id]').forEach(el => {
                const room = roomStatusCache.find(r => r.id == el.dataset.roomId);
                if (!room) return;
                const kasus = room.current || room.next;
                if (!kasus) return;

                const target = room.current ?
                    parseJamTanggal(localDateStr(now), kasus.jam_selesai) :
                    parseJamTanggal(localDateStr(now), kasus.jam_mulai);

                const { text, overdue } = formatCountdown(target, now);
                el.textContent = text;
                el.classList.toggle('overdue', overdue);
            });

            if (focusedRoomId !== null) tickFocusCountdown();
        }
        setInterval(tickCountdowns, 1000);

        // ===== Fullscreen focus =====
        window.openFocus = function(roomId) {
            focusedRoomId = roomId;
            renderFocus();
            document.getElementById('focusOverlay').classList.add('show');
        };

        window.closeFocus = function() {
            focusedRoomId = null;
            document.getElementById('focusOverlay').classList.remove('show');
        };

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeFocus();
        });

        function renderFocus() {
            const room = roomStatusCache.find(r => r.id === focusedRoomId);
            if (!room) return closeFocus();

            const state = roomState(room);
            document.getElementById('focusRoomName').textContent = room.nama;
            document.getElementById('focusBadge').innerHTML = badgeHtml(state);

            const kasus = room.current || room.next;
            const content = document.getElementById('focusContent');
            const empty = document.getElementById('focusEmpty');

            if (!kasus) {
                content.style.display = 'none';
                empty.style.display = 'block';
                return;
            }
            content.style.display = 'block';
            empty.style.display = 'none';

            document.getElementById('focusPasien').textContent = kasus.pasien;
            document.getElementById('focusMeta').textContent = `${kasus.nama_operasi} · ${kasus.dokter ?? '-'} · ${kasus.jam_mulai} - ${kasus.jam_selesai}`;
            document.getElementById('focusCountdownLabel').textContent = state === 'berlangsung' ?
                'Sisa Waktu Estimasi Operasi' : 'Operasi Dimulai Dalam';

            tickFocusCountdown();
        }

        function tickFocusCountdown() {
            const room = roomStatusCache.find(r => r.id === focusedRoomId);
            if (!room) return;
            const kasus = room.current || room.next;
            if (!kasus) return;

            const now = serverNow();
            const target = room.current ?
                parseJamTanggal(localDateStr(now), kasus.jam_selesai) :
                parseJamTanggal(localDateStr(now), kasus.jam_mulai);

            const { text, overdue } = formatCountdown(target, now);
            const el = document.getElementById('focusCountdown');
            el.textContent = text;
            el.classList.toggle('overdue', overdue);
        }

        // ===== View switch =====
        function switchView(view) {
            activeView = view;
            document.getElementById('btnViewStatus').classList.toggle('active', view === 'status');
            document.getElementById('btnViewCalendar').classList.toggle('active', view === 'calendar');
            document.getElementById('roomGrid').classList.toggle('hide', view !== 'status');
            document.getElementById('calendarView').classList.toggle('show', view === 'calendar');
            document.getElementById('roomPager').style.display = view === 'status' ? 'flex' : 'none';

            if (view === 'calendar' && !calendar) initCalendar();
            if (view === 'calendar' && calendar) calendar.updateSize();
        }
        document.getElementById('btnViewStatus').addEventListener('click', () => switchView('status'));
        document.getElementById('btnViewCalendar').addEventListener('click', () => switchView('calendar'));

        function initCalendar() {
            const calendarEl = document.getElementById('opCalendar');
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                height: '100%',
                nowIndicator: true,
                events: function(info, successCallback, failureCallback) {
                    fetch(`{{ url('monitor/operasiData') }}?klienfk=${klienfk}&start=${info.startStr.slice(0,10)}&end=${info.endStr.slice(0,10)}`)
                        .then(res => res.json())
                        .then(data => {
                            const filtered = roomFilterAktif === null ?
                                data :
                                data.filter(e => e.fk_ruangan_operasi === roomFilterAktif);
                            successCallback(filtered);
                        })
                        .catch(failureCallback);
                }
            });
            calendar.render();
        }

        // ===== Data polling =====
        function loadRoomStatus() {
            fetch(`{{ url('monitor/operasiStatus') }}?klienfk=${klienfk}`)
                .then(res => res.json())
                .then(res => {
                    if (res.status !== 'ok') return;
                    clockOffsetMs = new Date(res.server_time).getTime() - Date.now();
                    roomStatusCache = res.data;
                    renderRoomFilter();
                    renderRoomGrid();
                    if (focusedRoomId !== null) renderFocus();
                })
                .catch(() => {});
        }

        // Ganti halaman room-grid otomatis — monitor ini tidak ada orang yang
        // klik next/prev, sama seperti Monitor Ruangan.
        setInterval(() => {
            if (focusedRoomId !== null || activeView !== 'status') return;
            const totalPages = Math.max(1, Math.ceil(roomsToShow().length / ROOMS_PER_PAGE));
            roomPage = roomPage >= totalPages ? 1 : roomPage + 1;
            renderRoomGrid();
        }, PAGE_CYCLE_MS);

        updateClock();
        loadRoomStatus();
        setInterval(loadRoomStatus, 15000);
    </script>
</body>

</html>

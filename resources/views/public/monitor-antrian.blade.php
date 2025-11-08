<!DOCTYPE html>
<html lang="id">
<?php
use App\Models\Setting;
// ys note: tidak semua user adalah employee

$logo = Setting::where('parameter', 'company_logo')->first()->value ?: url('/assets/logo.png');
// $app_name = Setting::where('parameter', 'app_name')->first()->value ?: 'AppName';
// $company_name = Setting::where('parameter', 'company_name')->first()->value ?: 'Company Name';
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JiviDent - Antrian</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <link href="{{ url('') }}/assets/favicon.ico" rel="icon" type="image/x-icon">
    <link href="{{ url('') }}/assets/favicon.ico" rel="shortcut icon" type="image/x-icon">
    <style>
        /*
         * STRATEGI FONT: Clamp()
         * Font responsif yang stabil di monitor besar.
         */
        html {
            font-size: clamp(10px, 1.2vw, 18px);
        }

        /* Reset Dasar */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: #333;
            overflow: hidden;
            height: 100vh;
            width: 100vw;
            background-color: #f0f4f8;
            animation: fadeIn 1s ease-out;
        }

        /* === ANIMASI BACKGROUND LOOPING === */
        .animated-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
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

        /* Layout CSS Grid */
        .grid-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            /* Kolom kiri 2/3, Kolom kanan 1/3 */
            height: 100vh;
            width: 100vw;
        }

        /* === KOLOM KIRI (ANTRIAN) === */
        .queue-section {
            display: flex;
            flex-direction: column;
            padding: 2.5rem;
            gap: 2rem;
            /* BARU: Pastikan tidak ada overflow */
            overflow: hidden;
        }

        .header {
            display: flex;
            align-items: center;
            border-bottom: 2px solid #eee;
            padding-bottom: 1.5rem;
        }

        .logo {
            height: 5rem;
            width: auto;
            margin-right: 1.5rem;
            border-radius: 8px;
        }

        .header h1 {
            font-size: 2.2rem;
            font-weight: 600;
            color: #29A0B6;
        }

        /* BARU: Jam dan Tanggal di Header (sesuai referensi) */
        .datetime {
            margin-left: auto;
            text-align: right;
            color: #29A0B6;
        }

        #clock {
            font-size: 2.5rem;
            font-weight: 700;
        }

        #date {
            font-size: 1.2rem;
            font-weight: 400;
        }

        /* Kartu Panggilan Utama */
        .now-serving {
            flex-grow: 1;
            /* Ambil sisa ruang */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: #ffffff;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 86, 179, 0.1);
            text-align: center;
            /* BARU: Pastikan tidak mengecil */
            min-height: 0;
        }

        .now-serving h2 {
            font-size: 2.5rem;
            font-weight: 300;
            color: #555;
        }

        .number-display {
            font-size: 10rem;
            font-weight: 700;
            color: #0A6E80;
            line-height: 1.1;
            margin: 1rem 0;
            text-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
            animation: textGlow 2.5s infinite alternate ease-in-out;
            transform: scale(1);
            transition: transform 0.3s ease;
        }

        .number-display.pop {
            animation: numberPop 0.5s ease-out;
        }

        .doctor-name {
            font-size: 2.2rem;
            font-weight: 600;
            color: #333;
            padding: 0.8rem 1.5rem;
            background-color: #e6f7ff;
            border-radius: 12px;
            display: inline-block;
            border: 1px solid #b3e0ff;
        }

        /* * PERUBAHAN BESAR: Bar Status Ruangan (menggantikan Riwayat)
         * Ini adalah bar horizontal di bawah, sesuai referensi.
         */
        .room-status-bar {
            background: #29A0B6;
            /* Warna gelap seperti referensi */
            border-radius: 15px;
            padding: 0.5rem;
            box-shadow: 0 10px 30px rgba(1, 175, 163, 0.2);
            display: flex;
            /* Hapus tinggi, biarkan konten yg menentukan */
        }

        .room-status-bar ul {
            list-style: none;
            display: flex;
            flex-direction: row;
            /* Horizontal */
            justify-content: space-between;
            /* Beri jarak merata */
            width: 100%;
        }

        /* Ini adalah "Loket" box */
        .room-status-bar li {
            flex-grow: 1;
            /* Bagi rata lebarnya */
            text-align: center;
            color: white;
            padding: 1rem 0.5rem;
            border-right: 2px solid #0A6E80;
            /* Garis pemisah */
        }

        .room-status-bar li:last-child {
            border-right: none;
        }

        /* Nama Ruangan (Loket 1, Loket 2) */
        .room-status-bar li .room-name {
            font-size: 1.3rem;
            /* Proporsional */
            font-weight: 400;
            color: #b3e0ff;
            /* Biru muda */
            display: block;
        }

        /* Nomor Antrian di bar bawah */
        .room-status-bar li .room-queue-num {
            font-size: 2.2rem;
            /* Proporsional */
            font-weight: 700;
            color: #ffffff;
            display: block;
        }

        .room-status-bar li.history-placeholder {
            font-size: 1.4rem;
            color: #999;
            justify-content: center;
            opacity: 0.7;
            font-style: italic;
        }

        /* === KOLOM KANAN (SIDEBAR) === */
        .sidebar-section {
            background-color: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            padding: 2.5rem;
            box-shadow: -10px 0 25px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 2rem;
            /* BARU: Pastikan tidak ada overflow */
            overflow: hidden;
        }

        .sidebar-section .video-container {
            width: 100%;
            flex-grow: 1;
            /* Biarkan video mengambil sisa ruang */
            min-height: 0;
        }

        .sidebar-section h3 {
            font-size: 1.6rem;
            margin-bottom: 1rem;
            color: #29A0B6;
        }

        .video-container iframe {
            width: 100%;
            height: 100%;
            /* Isi penuh container */
            aspect-ratio: 16 / 9;
            /* Jaga rasio jika tinggi tidak 100% */
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .info-box {
            background-color: #e6f7ff;
            border: 1px solid #b3e0ff;
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
        }

        .info-box h4 {
            font-size: 1.4rem;
            margin-bottom: 0.5rem;
        }

        .info-box p {
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .slogan-info {
            font-size: 1.2rem;
            font-weight: 600;
            color: #29A0B6;
        }

        /* Ticker tetap di paling bawah kanan */
        .ticker-wrap {
            width: 100%;
            overflow: hidden;
            background: linear-gradient(90deg, rgba(75, 227, 224, 1) 0%, rgba(14, 99, 110, 1) 100%);
            color: white;
            padding: 1rem 0;
            border-radius: 8px;
        }

        .ticker {
            display: inline-block;
            white-space: nowrap;
            animation: ticker-scroll 30s linear infinite;
        }

        .ticker-item {
            display: inline-block;
            padding: 0 2rem;
            font-size: 1.2rem;
            font-weight: 600;
        }

        /* === ANIMASI === */
        @keyframes textGlow {
            from {
                text-shadow: 0 0 10px #fff, 0 0 20px #0A6E80, 0 0 30px #0A6E80;
            }

            to {
                text-shadow: 0 0 20px #fff, 0 0 30px #00aaff, 0 0 40px #00aaff;
            }
        }

        @keyframes numberPop {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        @keyframes ticker-scroll {
            0% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <div class="animated-background"></div>

    <div class="grid-container">
        <!-- Kolom Kiri: Informasi Antrian -->
        <main class="queue-section">
            <header class="header">
                <img src="{{ $logo }}" alt="JiviDent" class="logo">


                <!-- BARU: Tampilan Jam & Tanggal -->
                <div class="datetime">
                    <div id="clock">00:00:00</div>
                    <div id="date">Memuat...</div>
                </div>
            </header>

            <!-- Kartu Panggilan Utama -->
            <section class="now-serving">
                <h2>Nomor Panggilan</h2>
                <div class="number-display" id="current-number">
                    A-101
                </div>
                <div class="doctor-name" id="current-doctor">
                    Ruang 01 - Drg. Amanda Sari
                </div>
            </section>

            <!-- BARU: Bar Status Ruangan (Horizontal) -->
            <section class="room-status-bar">
                <ul id="room-status-list-ul">
                    <!-- Ini akan diisi oleh JavaScript -->
                    <li class="history-placeholder">
                        Memuat status ruangan...
                    </li>
                </ul>
            </section>
        </main>

        <!-- Kolom Kanan: Video & Info -->
        <aside class="sidebar-section">
            <div class="video-container">
                <h3>Informasi & Hiburan</h3>
                <iframe width="560" height="315"
                    src="https://www.youtube.com/embed/1ZYbU82GVz4?autoplay=1&mute=1&loop=1&playlist=1ZYbU82GVz4&controls=0&showinfo=0"
                    title="YouTube video player" frameborder: 0,
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                </iframe>
            </div>

            <div class="info-box">
                <h4>Hai, selamat datang! 👋</h4>
                <p>Senang banget kamu di sini. Yuk, santai dulu — biar senyummu makin siap bersinar 😁</p>
                <div class="slogan-info">
                    <strong>JiviDent</strong> — tempat di mana senyum sehat dan happy dimulai!
                </div>
            </div>

            <div class="ticker-wrap">
                <div class="ticker">
                    <div class="ticker-item">Patuhi protokol kesehatan.</div>
                    <div class="ticker-item">Jaga kebersihan mulut Anda.</div>
                    <div class="ticker-item">Sikat gigi minimal 2x sehari.</div>
                    <div class="ticker-item">Promo scaling gigi hanya Rp250.000.</div>
                    <div class="ticker-item">Patuhi protokol kesehatan.</div>
                    <div class="ticker-item">Jaga kebersihan mulut Anda.</div>
                    <div class="ticker-item">Sikat gigi minimal 2x sehari.</div>
                    <div class="ticker-item">Promo scaling gigi hanya Rp250.000.</div>
                </div>
            </div>
        </aside>
    </div>

    <script>
        // === LOGIKA BARU: Status per Ruangan ===
        const doctors = [
            'Ruang 01 - Drg. Amanda Sari',
            'Ruang 02 - Drg. Budi Hartono',
            'Ruang 03 - Drg. Citra Lestari'
            // Tambahkan dokter/ruangan lain di sini
        ];

        // Buat state awal untuk setiap ruangan
        let roomStates = doctors.map((doc, index) => ({
            name: doc,
            shortName: doc.split(' - ')[0], // Misal: "Ruang 01"
            prefix: String.fromCharCode(65 + index) + '-', // A-, B-, C-
            number: 100 // Nomor awal
        }));

        let currentRoomIndex = 0; // Ruangan mana yang sedang memanggil

        // Ambil elemen dari HTML
        const currentNumberEl = document.getElementById('current-number');
        const currentDoctorEl = document.getElementById('current-doctor');
        const roomStatusListEl = document.getElementById('room-status-list-ul'); // ID yang benar

        // Elemen Jam & Tanggal
        const clockEl = document.getElementById('clock');
        const dateEl = document.getElementById('date');

        /**
         * ==========================================================
         * BARU: Fungsi Text-to-Speech (TTS)
         * ==========================================================
         */
        function speak(text) {
            // Hentikan suara sebelumnya (jika ada) agar tidak tumpang tindih
            window.speechSynthesis.cancel();

            if ('speechSynthesis' in window) {
                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = 'id-ID'; // Atur bahasa ke Indonesia
                utterance.rate = 0.9; // Sedikit pelan agar jelas
                window.speechSynthesis.speak(utterance);
            } else {
                console.log('Browser Anda tidak mendukung Text-to-Speech.');
            }
        }

        /**
         * PENTING: Browser modern memblokir audio otomatis.
         * Kita perlu "membuka" izin audio dengan interaksi pengguna pertama.
         * Cukup klik di mana saja pada halaman sekali untuk mengaktifkan suara.
         */
        document.body.addEventListener('click', () => {
            // Trik untuk mengaktifkan audio context setelah interaksi pengguna
            let testUtterance = new SpeechSynthesisUtterance(' ');
            window.speechSynthesis.speak(testUtterance);
            console.log('Speech synthesis diaktifkan.');
        }, {
            once: true
        }); // 'once: true' berarti event ini hanya berjalan sekali


        /**
         * BARU: Fungsi untuk update Jam dan Tanggal
         */
        function updateClock() {
            const now = new Date();

            // Format Waktu: 00:00:00
            const time = now.toTimeString().split(' ')[0];
            clockEl.textContent = time;

            // Format Tanggal: Jumat, 7 November 2025
            const dateOptions = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            dateEl.textContent = now.toLocaleDateString('id-ID', dateOptions);
        }

        /**
         * BARU: Fungsi untuk merender bar status bawah
         */
        function renderRoomStatus() {
            roomStatusListEl.innerHTML = ''; // Kosongkan

            for (const room of roomStates) {
                const li = document.createElement('li');

                const roomNameSpan = document.createElement('span');
                roomNameSpan.className = 'room-name';
                roomNameSpan.textContent = room.shortName; // "Ruang 01"

                const queueNumSpan = document.createElement('span');
                queueNumSpan.className = 'room-queue-num';
                queueNumSpan.textContent = room.prefix + room.number; // "A-100"

                li.appendChild(roomNameSpan);
                li.appendChild(queueNumSpan);
                roomStatusListEl.appendChild(li);
            }
        }

        /**
         * Fungsi untuk inisialisasi tampilan
         */
        function initializeDisplay() {
            updateClock(); // Set jam pertama kali

            // Set panggilan pertama saat memuat
            const initialCall = roomStates[0];
            currentNumberEl.textContent = initialCall.prefix + initialCall.number;
            currentDoctorEl.textContent = initialCall.name;

            // Render bar status awal
            renderRoomStatus();
        }

        /**
         * LOGIKA BARU: Fungsi untuk update antrian
         */
        function updateQueue() {
            // 1. Tentukan ruangan mana yang akan memanggil berikutnya
            currentRoomIndex = (currentRoomIndex + 1) % roomStates.length;

            // 2. Naikkan nomor antrian untuk ruangan tersebut
            roomStates[currentRoomIndex].number++;

            // 3. Update Panggilan Utama (Main Display)
            const currentCall = roomStates[currentRoomIndex];
            currentNumberEl.textContent = currentCall.prefix + currentCall.number;
            currentDoctorEl.textContent = currentCall.name;

            // 4. Beri animasi 'pop'
            currentNumberEl.classList.add('pop');
            setTimeout(() => {
                currentNumberEl.classList.remove('pop');
            }, 500); // Durasi animasi harus cocok dengan CSS

            // 5. Render ulang bar status di bawah
            renderRoomStatus();

            // 6. BARU: Panggil fungsi Text-to-Speech
            // Pisahkan prefix (huruf) dan nomor
            const spokenPrefix = currentCall.prefix.replace('-', ''); // "A"
            const spokenNumber = currentCall.number; // 101

            // Format teks yang akan diucapkan
            const textToSpeak = `Nomor antrian, ${spokenPrefix}, ${spokenNumber}, silakan ke ${currentCall.name}`;

            // Panggil fungsi speak
            speak(textToSpeak);
        }

        // Panggil fungsi updateQueue() setiap 7 detik (7000 ms)
        setInterval(updateQueue, 12000);

        // Panggil fungsi updateClock() setiap detik
        setInterval(updateClock, 1000);

        // Panggil sekali saat memuat halaman agar data langsung terisi
        initializeDisplay();
    </script>
</body>

</html>

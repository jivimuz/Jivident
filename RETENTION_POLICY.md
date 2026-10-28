# Kebijakan Retensi & Backup Rekam Medis

Dokumen ini mencatat kebijakan operasional retensi data rekam medis dan backup database Jivident. Ini **bukan** dokumen hukum resmi — ditulis berdasarkan riset web terhadap Permenkes 24/2022 (bukan teks resmi Kemenkes), jadi verifikasi ulang ke JDIH Kemenkes atau konsultan compliance sebelum dipakai sebagai bukti kepatuhan formal (misal saat akreditasi).

## Dasar Kebijakan

Permenkes 24/2022 tentang Rekam Medis Elektronik mewajibkan rekam medis pasien disimpan minimal **25 tahun** sejak tanggal kunjungan terakhir pasien tersebut. Karena rentang ini sangat panjang, kebijakan di bawah sengaja dibuat konservatif: **jangan pernah menghapus backup yang berisi data rekam medis**, daripada mencoba menghitung per-pasien kapan masing-masing data boleh dibuang.

## Mekanisme Saat Ini: Backup Manual

Jivident **belum** punya backup otomatis terjadwal — ini keputusan yang disengaja (lihat `TODO.md`), bukan kelalaian. Backup dilakukan manual lewat halaman **Setting → Download Backup Database**, yang mengunduh dump SQL lengkap (`app/Helpers/DatabaseBackupHelper.php`) langsung ke browser. Tidak ada file backup yang otomatis tersimpan di server.

Karena mekanismenya manual, **kebijakan ini bergantung penuh pada kedisiplinan orang yang menjalankannya** — tidak ada pengingat otomatis dari sistem. Kalau proses ini lebih sering lupa dijalankan daripada tidak, pertimbangkan kembali untuk mengaktifkan backup otomatis terjadwal (lihat bagian "Kalau Mau Upgrade" di bawah).

## Yang Harus Dilakukan

1. **Siapa:** Pemilik aplikasi/admin yang punya akses halaman Setting (`checkAccess:SET`).
2. **Seberapa sering:** Minimal **mingguan**. Untuk klinik dengan volume kunjungan tinggi, pertimbangkan harian.
3. **Download dari mana:** Tombol "Download Backup Database" di halaman Setting (`/setting`).
4. **Simpan ke mana:** **Jangan hanya di komputer/server yang sama dengan aplikasi** — kalau server/hosting bermasalah, backup yang disimpan di tempat yang sama bisa hilang bersamaan. Salin ke minimal satu lokasi terpisah, misalnya:
   - Cloud storage pribadi (Google Drive, Dropbox, dll — bukan yang dikelola penyedia hosting yang sama).
   - Hardisk eksternal fisik yang disimpan di lokasi berbeda.
5. **Penamaan file:** File yang di-download sudah otomatis bernama `backup-jivident-YYYY-MM-DD_HHMMSS.sql` — jangan diganti nama supaya tanggal backup tetap jelas.
6. **Berapa lama disimpan:** **Selamanya — jangan dihapus**, termasuk backup lama. Karena backup berisi snapshot seluruh database (termasuk rekam medis pasien lama), menghapus backup lama berisiko membuang data yang masih dalam periode wajib simpan 25 tahun. Storage untuk file `.sql` jauh lebih murah daripada risiko kehilangan data yang wajib disimpan secara hukum.
7. **Catatan log sederhana:** Simpan catatan (spreadsheet sederhana cukup) kapan backup terakhir dilakukan, oleh siapa, dan di mana disimpan — supaya ada jejak kalau suatu saat ditanya saat audit/akreditasi.

## Kalau Mau Upgrade ke Backup Otomatis

Kalau kedepannya kebijakan manual ini terasa berisiko (lupa dijalankan, atau volume data makin besar), aplikasi sudah punya fondasi untuk backup terjadwal: `routes/console.php` sudah punya pola `Schedule::command(...)` (dipakai untuk reminder email), dan `DatabaseBackupHelper` sudah di desain streaming (aman untuk tabel besar). Yang perlu ditambahkan saat itu: command Artisan baru yang menulis dump ke file di server (bukan ke HTTP response), dijadwalkan via `Schedule::command(...)`, dan proses terpisah untuk menyalin hasilnya ke storage eksternal (server itu sendiri tetap bukan tempat penyimpanan jangka panjang yang aman).

## Riwayat Keputusan

- 2026-10-28: Diputuskan tetap backup manual-only untuk saat ini; kebijakan ini didokumentasikan sebagai pengganti otomasi, bukan tambahan di atasnya.

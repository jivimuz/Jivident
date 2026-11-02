<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\JadwalOperasi;
use App\Models\RuanganOperasi;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

/**
 * Jadwal Operasi scheduling — room/doctor double-booking guards (same
 * interval-overlap check JadwalDokterController::findRoomConflict() already
 * uses for weekly shifts, applied here to a specific `tanggal` instead of a
 * recurring day-of-week) plus the data feed the FullCalendar view on
 * `/jadwal-operasi` renders.
 */
final class OperasiHelper
{
    private const ACTIVE_STATUSES = ['dijadwalkan', 'berlangsung', 'selesai'];

    /**
     * @return array<int, array{id:int,title:string,start:string,end:string,color:string,status:string,ruangan:?string,dokter:?string,pasien:?string}>
     */
    public function getCalendarEvents(int $klienfk, string $start, string $end): array
    {
        $rows = JadwalOperasi::with(['ruangan', 'dokter.employee', 'pasien'])
            ->where('klienfk', $klienfk)
            ->whereDate('tanggal', '>=', $start)
            ->whereDate('tanggal', '<=', $end)
            ->orderBy('jam_mulai')
            ->get();

        $colors = [
            'dijadwalkan' => '#0d6efd',
            'berlangsung' => '#fd7e14',
            'selesai' => '#198754',
            'dibatalkan' => '#6c757d',
        ];

        return $rows->map(function (JadwalOperasi $row) use ($colors) {
            $pasienNama = $row->pasien?->nama ?? '-';

            return [
                'id' => $row->id,
                'title' => "{$row->nama_operasi} — {$pasienNama}",
                'start' => "{$row->tanggal}T{$row->jam_mulai}",
                'end' => "{$row->tanggal}T{$row->jam_selesai}",
                'color' => $colors[$row->status] ?? '#6c757d',
                'status' => $row->status,
                'fk_ruangan_operasi' => $row->fk_ruangan_operasi,
                'ruangan' => $row->ruangan?->nama,
                'dokter' => $row->dokter?->employee?->fullname ?? $row->dokter?->display_name,
                'pasien' => $pasienNama,
            ];
        })->values()->all();
    }

    /**
     * Per-room "what's happening right now" — the CURRENT case (status
     * `berlangsung`, trusting the staff-set status rather than re-deriving
     * it from a time window, since that status is exactly what the
     * Pemeriksaan tab's "Mulai Berlangsung" button exists to set) and the
     * NEXT upcoming one today (`dijadwalkan`, soonest `jam_mulai` from now
     * onward). Powers the Monitor Operasi room cards + fullscreen focus
     * countdown — one call covers every room so the monitor doesn't need a
     * separate "list of rooms" endpoint.
     *
     * @return array<int, array{id:int,nama:string,status_ruangan:string,current:?array,next:?array}>
     */
    public function getRoomStatuses(int $klienfk): array
    {
        $now = now();
        $today = $now->toDateString();
        $nowTime = $now->format('H:i:s');

        $rooms = RuanganOperasi::where('klienfk', $klienfk)->orderBy('nama')->get();

        $todayJadwal = JadwalOperasi::with(['pasien', 'dokter.employee'])
            ->where('klienfk', $klienfk)
            ->where('tanggal', $today)
            ->whereIn('status', ['dijadwalkan', 'berlangsung'])
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('fk_ruangan_operasi');

        return $rooms->map(function (RuanganOperasi $room) use ($todayJadwal, $nowTime) {
            $jadwalRuangan = $todayJadwal->get($room->id, collect());

            $current = $jadwalRuangan->first(fn ($j) => $j->status === 'berlangsung');
            $next = $jadwalRuangan->first(fn ($j) => $j->status === 'dijadwalkan' && (string) $j->jam_mulai >= $nowTime);

            return [
                'id' => $room->id,
                'nama' => $room->nama,
                'status_ruangan' => $room->status,
                'current' => $current ? $this->formatJadwalForStatus($current) : null,
                'next' => $next ? $this->formatJadwalForStatus($next) : null,
            ];
        })->values()->all();
    }

    /**
     * @return array{nama_operasi:string,pasien:string,dokter:?string,jam_mulai:string,jam_selesai:string}
     */
    private function formatJadwalForStatus(JadwalOperasi $jadwal): array
    {
        return [
            'nama_operasi' => $jadwal->nama_operasi,
            'pasien' => $jadwal->pasien?->nama ?? '-',
            'dokter' => $jadwal->dokter?->employee?->fullname ?? $jadwal->dokter?->display_name,
            'jam_mulai' => substr((string) $jadwal->jam_mulai, 0, 5),
            'jam_selesai' => substr((string) $jadwal->jam_selesai, 0, 5),
        ];
    }

    public function create(array $data): JadwalOperasi
    {
        $validated = $this->validateTimeRange($data);

        $this->assertNoConflict($validated, null);

        return JadwalOperasi::create([
            'klienfk' => $data['klienfk'] ?? (Auth::user()?->klienfk ?? 1),
            'fk_ruangan_operasi' => $validated['fk_ruangan_operasi'],
            'fk_pasien' => $validated['fk_pasien'],
            'fk_dokter' => $validated['fk_dokter'],
            'fk_daftarpasien' => $data['fk_daftarpasien'] ?? null,
            'nama_operasi' => $validated['nama_operasi'],
            'tanggal' => $validated['tanggal'],
            'jam_mulai' => $validated['jam_mulai'],
            'jam_selesai' => $validated['jam_selesai'],
            'catatan' => $data['catatan'] ?? null,
            'status' => 'dijadwalkan',
            'created_by' => Auth::id(),
        ]);
    }

    public function reschedule(JadwalOperasi $jadwal, array $data): JadwalOperasi
    {
        if ($jadwal->status === 'dibatalkan') {
            throw new InvalidArgumentException('Jadwal operasi yang sudah dibatalkan tidak bisa diubah');
        }

        $validated = $this->validateTimeRange(array_merge([
            'fk_ruangan_operasi' => $jadwal->fk_ruangan_operasi,
            'fk_pasien' => $jadwal->fk_pasien,
            'fk_dokter' => $jadwal->fk_dokter,
            'nama_operasi' => $jadwal->nama_operasi,
            'tanggal' => $jadwal->tanggal->format('Y-m-d'),
            'jam_mulai' => $jadwal->jam_mulai,
            'jam_selesai' => $jadwal->jam_selesai,
        ], $data));

        $this->assertNoConflict($validated, $jadwal->id);

        $jadwal->update([
            'fk_ruangan_operasi' => $validated['fk_ruangan_operasi'],
            'fk_pasien' => $validated['fk_pasien'],
            'fk_dokter' => $validated['fk_dokter'],
            'nama_operasi' => $validated['nama_operasi'],
            'tanggal' => $validated['tanggal'],
            'jam_mulai' => $validated['jam_mulai'],
            'jam_selesai' => $validated['jam_selesai'],
            'catatan' => $data['catatan'] ?? $jadwal->catatan,
        ]);

        return $jadwal;
    }

    public function updateStatus(JadwalOperasi $jadwal, string $status): JadwalOperasi
    {
        if (! in_array($status, ['dijadwalkan', 'berlangsung', 'selesai'], true)) {
            throw new InvalidArgumentException('Status tidak valid');
        }
        if ($jadwal->status === 'dibatalkan') {
            throw new InvalidArgumentException('Jadwal operasi yang sudah dibatalkan tidak bisa diubah statusnya');
        }

        $jadwal->update(['status' => $status]);

        return $jadwal;
    }

    public function cancel(JadwalOperasi $jadwal): JadwalOperasi
    {
        if ($jadwal->status === 'dibatalkan') {
            throw new InvalidArgumentException('Jadwal operasi ini sudah dibatalkan sebelumnya');
        }
        if ($jadwal->status === 'selesai') {
            throw new InvalidArgumentException('Operasi yang sudah selesai tidak bisa dibatalkan');
        }

        $jadwal->update([
            'status' => 'dibatalkan',
            'cancelled_by' => Auth::id(),
            'cancelled_at' => now(),
        ]);

        return $jadwal;
    }

    private function validateTimeRange(array $data): array
    {
        foreach (['fk_ruangan_operasi', 'fk_pasien', 'fk_dokter', 'nama_operasi', 'tanggal', 'jam_mulai', 'jam_selesai'] as $field) {
            if (empty($data[$field])) {
                throw new InvalidArgumentException("{$field} wajib diisi");
            }
        }

        if ($data['jam_mulai'] >= $data['jam_selesai']) {
            throw new InvalidArgumentException('Jam mulai harus sebelum jam selesai');
        }

        return $data;
    }

    /**
     * Ruangan tidak boleh dipakai dua kasus sekaligus pada rentang jam yang
     * sama (tidak mungkin secara fisik); dokter juga tidak boleh dijadwalkan
     * di dua ruangan pada rentang jam yang tumpang tindih (tidak bisa hadir
     * di dua tempat sekaligus) — sama-sama memakai interval-overlap check
     * `mulai < lawan.selesai AND selesai > lawan.mulai`, hanya saja
     * di-scope ke `tanggal` tertentu, bukan hari-dalam-minggu berulang
     * seperti JadwalDokter.
     */
    private function assertNoConflict(array $data, ?int $ignoreId): void
    {
        $base = JadwalOperasi::where('klienfk', Auth::user()?->klienfk ?? 1)
            ->where('tanggal', $data['tanggal'])
            ->whereIn('status', self::ACTIVE_STATUSES)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('jam_mulai', '<', $data['jam_selesai'])
            ->where('jam_selesai', '>', $data['jam_mulai']);

        $roomConflict = (clone $base)->where('fk_ruangan_operasi', $data['fk_ruangan_operasi'])->first();
        if ($roomConflict) {
            throw new InvalidArgumentException('Ruangan sudah dipakai jadwal operasi lain pada jam ini');
        }

        $dokterConflict = (clone $base)->where('fk_dokter', $data['fk_dokter'])->first();
        if ($dokterConflict) {
            throw new InvalidArgumentException('Dokter sudah dijadwalkan operasi lain pada jam ini');
        }
    }
}

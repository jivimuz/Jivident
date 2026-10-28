<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\Appointment;
use App\Models\JadwalDokter;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

/**
 * Generates bookable time slots from a doctor's weekly schedule template
 * (JadwalDokter) and handles booking/cancellation with double-booking guards.
 */
final class AppointmentHelper
{
    /**
     * @return array<int, array{jam_mulai: string, jam_selesai: string, available: bool}>
     */
    public function getAvailableSlots(int $dokterId, int $ruangPraktekId, string $tanggal): array
    {
        $dayOfWeek = Carbon::parse($tanggal)->dayOfWeek; // 0=Minggu ... 6=Sabtu

        $templates = JadwalDokter::where('fk_dokter', $dokterId)
            ->where('fk_ruangpraktek', $ruangPraktekId)
            ->where('hari', $dayOfWeek)
            ->where('is_active', true)
            ->get();

        if ($templates->isEmpty()) {
            return [];
        }

        $bookedSlots = Appointment::where('fk_dokter', $dokterId)
            ->where('tanggal', $tanggal)
            ->where('status', '!=', 'cancelled')
            ->pluck('jam_mulai')
            ->map(fn ($t) => substr((string) $t, 0, 5))
            ->all();

        $slots = [];
        foreach ($templates as $template) {
            $cursor = Carbon::parse($template->jam_mulai);
            $end = Carbon::parse($template->jam_selesai);

            while ($cursor->copy()->addMinutes($template->durasi_slot)->lessThanOrEqualTo($end)) {
                $slotStart = $cursor->format('H:i');
                $slotEnd = $cursor->copy()->addMinutes($template->durasi_slot)->format('H:i');

                $slots[] = [
                    'jam_mulai' => $slotStart,
                    'jam_selesai' => $slotEnd,
                    'available' => ! in_array($slotStart, $bookedSlots, true),
                ];

                $cursor->addMinutes($template->durasi_slot);
            }
        }

        return $slots;
    }

    public function book(array $data): Appointment
    {
        foreach (['fk_dokter', 'fk_ruangpraktek', 'fk_pasien', 'tanggal', 'jam_mulai'] as $field) {
            if (empty($data[$field])) {
                throw new InvalidArgumentException("{$field} wajib diisi");
            }
        }

        $slots = $this->getAvailableSlots((int) $data['fk_dokter'], (int) $data['fk_ruangpraktek'], $data['tanggal']);
        $slot = collect($slots)->firstWhere('jam_mulai', $data['jam_mulai']);

        if (! $slot) {
            throw new InvalidArgumentException('Jam yang dipilih bukan bagian dari jadwal praktek dokter ini');
        }
        if (! $slot['available']) {
            throw new InvalidArgumentException('Slot jam ini sudah dibooking pasien lain');
        }

        return Appointment::create([
            // Nullsafe: book() is also called from the unauthenticated patient
            // portal (no staff Auth::user() session there).
            'klienfk' => $data['klienfk'] ?? (Auth::user()?->klienfk ?? 1),
            'fk_dokter' => $data['fk_dokter'],
            'fk_ruangpraktek' => $data['fk_ruangpraktek'],
            'fk_pasien' => $data['fk_pasien'],
            'tanggal' => $data['tanggal'],
            'jam_mulai' => $slot['jam_mulai'],
            'jam_selesai' => $slot['jam_selesai'],
            'notes' => $data['notes'] ?? null,
            'status' => 'booked',
            'created_by' => Auth::id(),
        ]);
    }

    public function cancel(int $appointmentId): Appointment
    {
        $appointment = Appointment::findOrFail($appointmentId);

        if ($appointment->status === 'cancelled') {
            throw new InvalidArgumentException('Appointment ini sudah dibatalkan sebelumnya');
        }

        $appointment->update([
            'status' => 'cancelled',
            'cancelled_by' => Auth::id(),
            'cancelled_at' => now(),
        ]);

        return $appointment;
    }
}

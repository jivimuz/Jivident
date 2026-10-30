<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\Employee;
use App\Models\Payments;
use App\Models\Setting;
use App\Models\SuratMandiri;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class SuratMandiriHelper
{
    public const JENIS_LIST = ['sehat', 'sakit', 'rujukan', 'umum'];

    private const JENIS_LABEL = [
        'sehat' => 'Surat Keterangan Sehat',
        'sakit' => 'Surat Keterangan Sakit',
        'rujukan' => 'Surat Rujukan',
        'umum' => 'Surat Keterangan Umum',
    ];

    public function issue(array $data): SuratMandiri
    {
        $jenis = $data['jenis'] ?? null;
        if (! in_array($jenis, self::JENIS_LIST, true)) {
            throw new InvalidArgumentException('Jenis surat tidak valid');
        }

        $nama = trim((string) ($data['nama'] ?? ''));
        if ($nama === '') {
            throw new InvalidArgumentException('Nama wajib diisi');
        }

        // m_employee tidak punya kolom klienfk (HR/payroll global antar cabang) — jangan di-scope.
        $dokter = Employee::find($data['fk_employee'] ?? null);
        if (! $dokter) {
            throw new InvalidArgumentException('Dokter penandatangan tidak ditemukan');
        }

        $payment = Payments::find($data['fk_payment'] ?? null);
        if (! $payment) {
            throw new InvalidArgumentException('Metode pembayaran tidak ditemukan');
        }

        $klienfk = $data['klienfk'] ?? (Auth::user()->klienfk ?? 1);
        $harga = (float) (Setting::where('parameter', 'patient_surat_keterangan_price')->value('value') ?? 0);
        $userhandlerId = $data['userhandler_id'] ?? Auth::id();

        return DB::transaction(function () use ($data, $jenis, $nama, $klienfk, $dokter, $payment, $harga, $userhandlerId) {
            $surat = SuratMandiri::create([
                'klienfk' => $klienfk,
                'jenis' => $jenis,
                'nama' => $nama,
                'jenis_kelamin' => $data['jenis_kelamin'] ?? null,
                'umur' => $data['umur'] ?? null,
                'alamat' => $data['alamat'] ?? null,
                'pekerjaan' => $data['pekerjaan'] ?? null,
                'keperluan' => $data['keperluan'] ?? null,
                'detail' => $data['detail'] ?? [],
                'fk_employee' => $dokter->id,
                'fk_payment' => $payment->id,
                'harga' => $harga,
                'userhandler_id' => $userhandlerId,
            ]);

            if ($harga > 0) {
                $transaction = app(TransactionHelper::class)->createTransaction([
                    'transaction_type' => 'in',
                    'typecode' => 'SRT',
                    'amount' => $harga,
                    'notes' => self::JENIS_LABEL[$jenis]." - {$nama}",
                    'userhandler_id' => $userhandlerId,
                ]);

                $surat->update(['transaction_id' => $transaction->id]);
            }

            return $surat;
        }, 3);
    }

    public function jenisLabel(string $jenis): string
    {
        return self::JENIS_LABEL[$jenis] ?? 'Surat Keterangan';
    }
}

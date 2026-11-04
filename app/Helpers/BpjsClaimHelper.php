<?php

namespace App\Helpers;

use App\Models\BpjsClaim;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class BpjsClaimHelper
{
    public const STATUSES = [
        'draft',
        'submitted',
        'verified',
        'pending',
        'disputed',
        'paid',
        'rejected',
    ];

    public static function create(array $data, int $klienfk, int $userId): BpjsClaim
    {
        if (($data['status'] ?? 'draft') !== 'draft') {
            throw new InvalidArgumentException('Klaim baru harus dibuat dengan status draft.');
        }

        return DB::transaction(function () use ($data, $klienfk, $userId) {
            return BpjsClaim::create([
                'klienfk' => $klienfk,
                'daftarpasien_id' => $data['daftarpasien_id'],
                'pasien_id' => $data['pasien_id'],
                'sep_no' => $data['sep_no'] ?? null,
                'claim_no' => $data['claim_no'] ?? null,
                'status' => 'draft',
                'amount' => $data['amount'] ?? 0,
                'service_date' => $data['service_date'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);
        });
    }

    public static function transition(BpjsClaim $claim, string $status, int $userId, array $data = []): BpjsClaim
    {
        if (! in_array($status, self::STATUSES, true)) {
            throw new InvalidArgumentException('Status klaim BPJS tidak valid.');
        }

        $allowed = [
            'draft' => ['submitted'],
            'submitted' => ['verified', 'pending', 'disputed', 'rejected'],
            'pending' => ['submitted', 'verified', 'disputed', 'rejected'],
            'disputed' => ['submitted', 'verified', 'rejected'],
            'verified' => ['paid'],
            'paid' => [],
            'rejected' => [],
        ];
        if (! in_array($status, $allowed[$claim->status] ?? [], true)) {
            throw new InvalidArgumentException("Klaim tidak dapat berpindah dari {$claim->status} ke {$status}.");
        }

        $claim->fill([
            'status' => $status,
            'claim_no' => $data['claim_no'] ?? $claim->claim_no,
            'notes' => array_key_exists('notes', $data) ? $data['notes'] : $claim->notes,
            'response_payload' => array_key_exists('response_payload', $data) ? $data['response_payload'] : $claim->response_payload,
            'updated_by' => $userId,
        ]);
        if ($status === 'submitted') {
            $claim->submitted_at = now();
        }
        if ($status === 'verified') {
            $claim->verified_at = now();
        }
        $claim->save();

        return $claim->refresh();
    }
}

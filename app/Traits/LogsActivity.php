<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Records created/updated/deleted events for a model into t_activitylogs.
 * Add `use LogsActivity;` to any Eloquent model whose changes need an audit trail.
 */
trait LogsActivity
{
    private const IGNORED_FIELDS = ['created_at', 'updated_at', 'deleted_at'];

    protected static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            static::recordActivity($model, 'created', static::stripIgnoredFields($model->getAttributes()));
        });

        static::updated(function ($model) {
            $changes = [];
            foreach (static::stripIgnoredFields($model->getChanges()) as $field => $new) {
                $changes[$field] = [
                    'old' => $model->getOriginal($field),
                    'new' => $new,
                ];
            }

            if (! empty($changes)) {
                static::recordActivity($model, 'updated', $changes);
            }
        });

        static::deleted(function ($model) {
            static::recordActivity($model, 'deleted', static::stripIgnoredFields($model->getAttributes()));
        });
    }

    private static function stripIgnoredFields(array $attributes): array
    {
        return array_diff_key($attributes, array_flip(self::IGNORED_FIELDS));
    }

    private static function recordActivity($model, string $action, array $payload): void
    {
        try {
            $user = Auth::user();

            ActivityLog::create([
                'klienfk' => $user->klienfk ?? null,
                'user_id' => $user->id ?? null,
                'action' => $action,
                'model_type' => get_class($model),
                'model_id' => $model->getKey(),
                'changes' => $payload,
                'url' => app()->bound('request') ? request()->fullUrl() : null,
                'ip_address' => app()->bound('request') ? request()->ip() : null,
            ]);
        } catch (Throwable $e) {
            // Audit logging must never break the actual business operation.
            Log::warning('Gagal mencatat activity log: '.$e->getMessage());
        }
    }
}

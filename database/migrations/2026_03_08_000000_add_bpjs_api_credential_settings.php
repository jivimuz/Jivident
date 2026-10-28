<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Credential placeholders for BPJS's "New Generation" API gateway. Each of
     * the three services (eligibilitas peserta/VClaim, P-Care, Antrean Online)
     * is registered separately on BPJS's side, so each gets its own cons_id/
     * secret_key/user_key/base_url — they are NOT guaranteed to share one set
     * of credentials. All seeded empty; nothing calls these yet, this just
     * reserves where to put them once the clinic registers with BPJS and
     * receives real values. Editable from the Setting page like `ppn_persen`.
     */
    public function up(): void
    {
        $services = [
            'vclaim' => 'BPJS Eligibilitas/VClaim',
            'pcare' => 'BPJS P-Care',
            'antrean' => 'BPJS Antrean Online',
        ];

        $fields = [
            'cons_id' => 'Consumer ID',
            'secret_key' => 'Secret Key',
            'user_key' => 'User Key',
            'base_url' => 'Base URL',
        ];

        foreach ($services as $serviceKey => $serviceLabel) {
            foreach ($fields as $fieldKey => $fieldLabel) {
                $parameter = "bpjs_{$serviceKey}_{$fieldKey}";
                if (! Setting::where('parameter', $parameter)->exists()) {
                    Setting::insert([
                        'name' => "{$serviceLabel} - {$fieldLabel}",
                        'parameter' => $parameter,
                        'value' => '',
                        'type' => 'text',
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::where('parameter', 'like', 'bpjs_vclaim_%')
            ->orWhere('parameter', 'like', 'bpjs_pcare_%')
            ->orWhere('parameter', 'like', 'bpjs_antrean_%')
            ->delete();
    }
};

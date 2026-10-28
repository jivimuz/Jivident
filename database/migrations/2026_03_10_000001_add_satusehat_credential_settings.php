<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * App-level SATUSEHAT credentials (OAuth2 client_credentials grant,
     * issued once per registered application on SATUSEHAT's developer
     * portal). Separate from `m_klien.ihs_id`, which identifies each
     * branch's own facility ("Organization" resource) — that's per-branch
     * data, this is the one app-wide secret used to authenticate against
     * the API. Seeded empty, same pattern as the bpjs_* settings: nothing
     * calls these yet, no sandbox/production credentials exist.
     */
    public function up(): void
    {
        $rows = [
            ['name' => 'SATUSEHAT - Client ID', 'parameter' => 'satusehat_client_id'],
            ['name' => 'SATUSEHAT - Client Secret', 'parameter' => 'satusehat_client_secret'],
            ['name' => 'SATUSEHAT - Base URL', 'parameter' => 'satusehat_base_url'],
        ];

        foreach ($rows as $row) {
            if (! Setting::where('parameter', $row['parameter'])->exists()) {
                Setting::insert([
                    'name' => $row['name'],
                    'parameter' => $row['parameter'],
                    'value' => '',
                    'type' => 'text',
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::whereIn('parameter', [
            'satusehat_client_id', 'satusehat_client_secret', 'satusehat_base_url',
        ])->delete();
    }
};

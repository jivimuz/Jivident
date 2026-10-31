<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Master list for odontogram diagnosis/kondisi-gigi — previously hardcoded
     * in two places: `DIAGNOSIS_COLOR_MAP` + a fixed <option> list inside
     * public/assets/js/3dmodel/adult.js & child.js (6 items: Karies, Calculus,
     * Fraktur, Abfraksi, Gingivitis, Pulpitis), and the `config` array inside
     * public/assets/js/3dmodel/2dglobal.js (15 items: Bersih, Belum Erupsi,
     * ..., Indikasi Cabut). Both lists get the same two columns the user
     * asked for (nama + warna) plus two columns only the 2D list needs
     * (`simbol` — the short code shown on the chart, e.g. "UE"/"PE"; `is_reset`
     * — the "Bersih" entry that clears a tooth surface instead of coloring
     * it). `kategori` keeps the two lists separate rather than merged, since
     * "Karies" has a different color convention in each context already
     * (blue in the 3D model, grey in the 2D chart) — forcing them into one
     * shared row would silently change one of the two.
     */
    public function up(): void
    {
        Schema::create('m_odontogram_diagnosis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('klienfk')->default(1);
            $table->enum('kategori', ['3d', '2d'])->default('3d');
            $table->string('nama');
            $table->string('warna', 9); // hex, e.g. #3b82f6
            $table->string('simbol', 20)->nullable();
            $table->boolean('is_reset')->default(false);
            $table->integer('urutan')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['klienfk', 'kategori']);
        });

        if (! Module::where('code', 'MOD')->exists()) {
            $masterGroup = Module::where('code', 'MTT')->first();
            Module::insert([
                'name' => 'Master Diagnosis Odontogram',
                'code' => 'MOD',
                'group_id' => $masterGroup->group_id ?? 2,
                'group_name' => $masterGroup->group_name ?? 'M',
                'list_no' => (int) (Module::where('group_id', $masterGroup->group_id ?? 2)->max('list_no')) + 1,
                'icon' => 'iconoir-palette',
                'route' => '/odontogram-diagnosis',
                'isheader' => false,
                'isactive' => true,
            ]);
        }

        if (DB::table('m_odontogram_diagnosis')->count() === 0) {
            $now = now();
            $rows3d = [
                ['nama' => 'Karies', 'warna' => '#3b82f6'],
                ['nama' => 'Calculus', 'warna' => '#bfb467'],
                ['nama' => 'Fraktur', 'warna' => '#59909c'],
                ['nama' => 'Abfraksi', 'warna' => '#9c4f6f'],
                ['nama' => 'Gingivitis', 'warna' => '#9c784f'],
                ['nama' => 'Pulpitis', 'warna' => '#9c504f'],
            ];

            $rows2d = [
                ['nama' => 'Bersih', 'warna' => '#ffffff', 'simbol' => '', 'is_reset' => true],
                ['nama' => 'Belum Erupsi', 'warna' => '#ffffff', 'simbol' => 'UE'],
                ['nama' => 'Erupsi Sebagian', 'warna' => '#ffffff', 'simbol' => 'PE'],
                ['nama' => 'Anomali Bentuk', 'warna' => '#ffffff', 'simbol' => '&nbsp;A'],
                ['nama' => 'Sisa Akar', 'warna' => '#ffffff', 'simbol' => '&nbsp;&radic;'],
                ['nama' => 'Gigi Hilang', 'warna' => '#ffffff', 'simbol' => '&nbsp;X'],
                ['nama' => 'Jembatan', 'warna' => '#ffffff', 'simbol' => '__'],
                ['nama' => 'Gigi Tiruan Lepas', 'warna' => '#ffffff', 'simbol' => '--'],
                ['nama' => 'Non Vital', 'warna' => '#ffffff', 'simbol' => '&nbsp;/'],
                ['nama' => 'Karies', 'warna' => '#808080', 'simbol' => ''],
                ['nama' => 'Tambalan Logam', 'warna' => '#ffc0cb', 'simbol' => ''],
                ['nama' => 'Tambalan Non Logam', 'warna' => '#5f9ea0', 'simbol' => ''],
                ['nama' => 'Mahkota Logam', 'warna' => '#006400', 'simbol' => ''],
                ['nama' => 'Mahkota Non Logam', 'warna' => '#00ffff', 'simbol' => ''],
                ['nama' => 'Indikasi Cabut', 'warna' => '#000000', 'simbol' => ''],
            ];

            $insert = [];
            foreach ($rows3d as $i => $row) {
                $insert[] = array_merge([
                    'klienfk' => 1,
                    'kategori' => '3d',
                    'simbol' => null,
                    'is_reset' => false,
                    'urutan' => $i,
                    'created_at' => $now,
                    'updated_at' => $now,
                ], $row);
            }
            foreach ($rows2d as $i => $row) {
                $insert[] = array_merge([
                    'klienfk' => 1,
                    'kategori' => '2d',
                    'simbol' => null,
                    'is_reset' => false,
                    'urutan' => $i,
                    'created_at' => $now,
                    'updated_at' => $now,
                ], $row);
            }

            DB::table('m_odontogram_diagnosis')->insert($insert);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Module::where('code', 'MOD')->delete();
        Schema::dropIfExists('m_odontogram_diagnosis');
    }
};

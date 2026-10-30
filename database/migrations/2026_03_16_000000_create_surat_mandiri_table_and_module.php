<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * "Surat Keterangan Mandiri" — issuing a letter (sehat/sakit/rujukan/umum)
     * for someone who is NOT going through an exam visit. Identity fields are
     * free text (no Patient/DaftarPasien record), charged a flat price from
     * the (previously unused) `patient_surat_keterangan_price` Setting, and
     * posted straight to the ledger via TransactionHelper with no
     * `daftarpasien_id` — same "ledger-only, no BillingTransaction" pattern
     * already used by the KSR cashier flow and POS sales.
     */
    public function up(): void
    {
        Schema::create('t_surat_mandiri', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('klienfk')->default(1);
            $table->string('jenis'); // sehat|sakit|rujukan|umum
            $table->string('nama');
            $table->string('jenis_kelamin')->nullable();
            $table->string('umur')->nullable();
            $table->text('alamat')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('keperluan')->nullable();
            // Field spesifik per jenis surat (lama_istirahat, tujuan_rujukan, isi_surat, dll) —
            // disimpan fleksibel di sini daripada bikin kolom sparse untuk tiap jenis.
            $table->json('detail')->nullable();
            $table->unsignedBigInteger('fk_employee'); // dokter penandatangan, m_employee tidak punya klienfk (lihat CLAUDE.md)
            $table->unsignedBigInteger('fk_payment');
            $table->decimal('harga', 15, 2)->default(0);
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->unsignedBigInteger('userhandler_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('klienfk');
            $table->index('jenis');
        });

        if (! Module::where('code', 'SRM')->exists()) {
            Module::insert([
                'name' => 'Surat Keterangan',
                'code' => 'SRM',
                'group_id' => 9,
                'group_name' => 'SR',
                'list_no' => 9,
                'icon' => 'iconoir-submit-document',
                'route' => '#',
                'isheader' => true,
                'isactive' => true,
            ]);
        }

        if (! Module::where('code', 'SRT')->exists()) {
            Module::insert([
                'name' => 'Surat Keterangan Mandiri',
                'code' => 'SRT',
                'group_id' => 9,
                'group_name' => 'SR',
                'list_no' => 1,
                'icon' => 'iconoir-submit-document',
                'route' => '/surat-mandiri',
                'isheader' => false,
                'isactive' => true,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Module::where('code', 'SRT')->delete();
        Module::where('code', 'SRM')->delete();
        Schema::dropIfExists('t_surat_mandiri');
    }
};

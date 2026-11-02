<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * "Biaya Tambahan" — an admin-only way to add an arbitrary charge
     * (e.g. "Biaya Operasi") directly onto a patient's visit bill. Unlike
     * `KSR` (manual cashier entry via `TransactionController`), this is
     * NOT ledger-only — it's tied to `fk_daftarpasien` and feeds into the
     * same outstanding-balance calculation as tindakan/diagnosa/lab/
     * radiologi/resep, so it shows up on that visit's tagihan and gets
     * paid through the normal Billing flow, not a separate untracked entry.
     */
    public function up(): void
    {
        Schema::create('t_biaya_tambahan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('klienfk')->default(1);
            $table->foreignId('daftarpasien_id');
            $table->string('nama');
            $table->decimal('jumlah', 15, 2);
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('klienfk');
            $table->index('daftarpasien_id');
        });

        $maxListNo = (int) Module::where('group_id', 5)->where('isheader', 0)->max('list_no');

        if (! Module::where('code', 'BTM')->exists()) {
            Module::insert([
                'name' => 'Biaya Tambahan',
                'code' => 'BTM',
                'group_id' => 5,
                'group_name' => 'KU',
                'list_no' => $maxListNo + 1,
                'icon' => 'iconoir-coins',
                'route' => '/biaya-tambahan',
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
        Module::where('code', 'BTM')->delete();
        Schema::dropIfExists('t_biaya_tambahan');
    }
};

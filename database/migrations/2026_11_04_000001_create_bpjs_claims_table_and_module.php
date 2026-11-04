<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_bpjs_claims', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('klienfk');
            $table->unsignedBigInteger('daftarpasien_id');
            $table->unsignedBigInteger('pasien_id');
            $table->string('sep_no', 40)->nullable();
            $table->string('claim_no', 40)->nullable();
            $table->string('status', 30)->default('draft');
            $table->decimal('amount', 15, 2)->default(0);
            $table->date('service_date')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('response_payload')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['klienfk', 'daftarpasien_id']);
            $table->index(['klienfk', 'status']);
            $table->index(['klienfk', 'service_date']);
        });

        $finance = Module::where('code', 'BL')->first();
        $groupId = $finance?->group_id ?? 5;
        $groupName = $finance?->group_name ?? 'KU';
        $listNo = (int) Module::where('group_id', $groupId)
            ->where('isheader', 0)
            ->max('list_no') + 1;

        Module::updateOrCreate(
            ['code' => 'BPJ'],
            [
                'name' => 'Klaim BPJS',
                'icon' => 'iconoir-shield-check',
                'route' => 'bpjs-claims',
                'group_id' => $groupId,
                'group_name' => $groupName,
                'list_no' => $listNo,
                'isheader' => 0,
                'isactive' => 1,
            ]
        );
    }

    public function down(): void
    {
        Module::where('code', 'BPJ')->delete();
        Schema::dropIfExists('t_bpjs_claims');
    }
};

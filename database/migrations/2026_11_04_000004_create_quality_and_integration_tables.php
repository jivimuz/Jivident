<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_quality_incidents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('klienfk');
            $table->string('incident_no', 40);
            $table->dateTime('occurred_at');
            $table->string('category', 80);
            $table->enum('severity', ['near_miss', 'mild', 'moderate', 'severe', 'death'])->default('near_miss');
            $table->text('description');
            $table->text('immediate_action')->nullable();
            $table->text('root_cause')->nullable();
            $table->text('corrective_action')->nullable();
            $table->enum('status', ['reported', 'investigating', 'resolved', 'closed'])->default('reported');
            $table->unsignedBigInteger('reported_by')->nullable();
            $table->unsignedBigInteger('closed_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['klienfk', 'incident_no']);
            $table->index(['klienfk', 'status']);
        });

        Schema::create('t_quality_indicators', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('klienfk');
            $table->string('code', 40);
            $table->string('name');
            $table->string('unit', 30);
            $table->decimal('target', 15, 4)->nullable();
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('value', 15, 4)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('recorded_by')->nullable();
            $table->timestamps();
            $table->unique(['klienfk', 'code', 'period_start', 'period_end']);
            $table->index(['klienfk', 'period_start', 'period_end']);
        });

        Schema::create('t_integration_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('klienfk');
            $table->string('system', 40);
            $table->string('resource', 80);
            $table->string('operation', 20);
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->enum('status', ['queued', 'sent', 'acknowledged', 'failed'])->default('queued');
            $table->unsignedSmallInteger('http_status')->nullable();
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->text('error_message')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();
            $table->index(['klienfk', 'system', 'status']);
            $table->index(['reference_type', 'reference_id']);
        });

        $report = Module::where('code', 'RP')->first();
        $groupId = $report?->group_id ?? 7;
        $groupName = $report?->group_name ?? 'RP';
        $listNo = (int) Module::where('group_id', $groupId)
            ->where('isheader', 0)
            ->max('list_no') + 1;

        Module::updateOrCreate(
            ['code' => 'QLT'],
            [
                'name' => 'Mutu & Insiden',
                'icon' => 'iconoir-shield-check',
                'route' => 'quality',
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
        Module::where('code', 'QLT')->delete();
        Schema::dropIfExists('t_integration_logs');
        Schema::dropIfExists('t_quality_indicators');
        Schema::dropIfExists('t_quality_incidents');
    }
};
